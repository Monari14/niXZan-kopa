<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produto;
use App\Models\Preco;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Notifications\PedidoFeito;
use App\Models\AdminSettings;
use App\Models\Carrinho;
use App\Notifications\PedidoCancelado;
use App\Notifications\PedidoEsperandoRetirada;

class PedidoController extends Controller
{
    public function getarCarrinho(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if (!in_array($user->role, ['cliente', 'admin'])) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $carrinho = Carrinho::where('id_user', $user->id)->latest()->first();

        if (!$carrinho) {
            return response()->json(['message' => 'Carrinho vazio.'], 200);
        }

        return response()->json([
            'carrinho' => [
                'id'    => $carrinho->id,
                'itens' => $carrinho->itens_pedido,
                'total' => $carrinho->total,
            ]
        ], 200);
    }
    public function novoPedido(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        // Recebe o array de itens do JS
        $itensCarrinho = $request->input('itens', []);

        if (empty($itensCarrinho)) {
            return response()->json(['error' => 'Carrinho vazio.'], 400);
        }

        // Buscar todos os produtos de uma vez
        $produtoIds = array_column($itensCarrinho, 'id');
        $produtos = Produto::whereIn('id', $produtoIds)->get()->keyBy('id');

        $itensPedido = [];
        $total = 0;

        $quantidades_por_tipo = [
            'energetico' => 0,
            'bebida'     => 0,
            'gelo'       => 0,
        ];

        foreach ($itensCarrinho as $item) {
            $produto = $produtos[$item['id']] ?? null;
            if (!$produto) continue;

            $preco = Preco::where('id_produto', $produto->id)->latest()->first();
            if (!$preco || $preco->valor <= 0) {
                return response()->json(['error' => "Produto '{$produto->nome}' sem preço válido."], 400);
            }

            $quantidade = $item['quantidade'];
            $valor = $preco->valor * $quantidade;
            $total += $valor;

            $itensPedido[] = [
                'id_produto'     => $produto->id,
                'nome'           => $produto->nome,
                'imagem'         => $produto->img_url,
                'tipo'           => $produto->tipo,
                'quantidade'     => $quantidade,
                'preco_unitario' => $preco->valor,
            ];

            // Atualiza contagem por tipo
            if (isset($quantidades_por_tipo[$produto->tipo])) {
                $quantidades_por_tipo[$produto->tipo] += $quantidade;
            }
        }

        if (empty($itensPedido)) {
            return response()->json(['error' => 'Nenhum produto válido no carrinho.'], 400);
        }

        $numero_copoes = min($quantidades_por_tipo);

        $valor_adicional = $numero_copoes * (AdminSettings::first()->valor_adicional_pedido ?? 0);
        $total_final = $total + $valor_adicional;

        Carrinho::create([
            'id_user'      => $user->id,
            'itens_pedido' => $itensPedido,
            'total'        => $total_final,
        ]);

        return response()->json([
            'message' => 'pedido_finalizado',
            'total'   => $total_final,
            'itens'   => $itensPedido,
            'numero_copoes' => $numero_copoes
        ]);
    }
    public function confirmar(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if ($user->role !== 'cliente' && $user->role !== 'admin') {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $request->validate([
            'endereco'        => 'required|string|max:255',
            'forma_pagamento' => 'required|in:pix,dinheiro',
            'valor_troco'     => 'required_if:forma_pagamento,dinheiro|numeric|min:0',
        ]);

        $carrinho = Carrinho::where('id_user', $user->id)->latest()->first();

        if (!$carrinho) {
            return response()->json(['error' => 'Carrinho não encontrado.'], 400);
        }

        $itens = $carrinho->itens_pedido;
        $totalItens = $carrinho->total;

        if (!$itens || count($itens) === 0) {
            return response()->json(['error' => 'Carrinho vazio.'], 400);
        }

        try {
            $troco = null;

            if ($request->forma_pagamento === 'dinheiro') {
                $valorPago = floatval($request->input('valor_troco', 0));

                if ($valorPago < $totalItens) {
                    return response()->json(['error' => 'O valor para troco deve ser maior ou igual ao total do pedido.'], 400);
                }
                $troco = $valorPago - $totalItens;
            }

            $status = AdminSettings::first()->status_aberto;

            if ($status == true) {
                // Cria o pedido
                $pedido = Pedido::create([
                    'id_user' => $user->id,
                    'id_entregador' => null,
                    'endereco' => $request->input('endereco'),
                    'forma_pagamento' => $request->input('forma_pagamento'),
                    'troco' => $troco,
                    'total' => $totalItens,
                    'itens_pedido' => json_encode($carrinho->itens_pedido),
                    'status' => 'preparando',
                ]);

                // Cria os itens do pedido e diminui estoque
                foreach ($itens as $item) {
                    $produto = Produto::find($item['id_produto']);
                    if ($produto) {
                        $sucesso = $produto->diminuirEstoque($item['quantidade']);
                        if (!$sucesso) {
                            return response()->json(['error' => "Estoque insuficiente para o produto '{$produto->nome}'."], 400);
                        }
                    }
                }

                // Deleta o carrinho
                $carrinho->delete();

                // Notificação
                $pedido->user->notify(new PedidoFeito($request->user(), $pedido->id));

                return response()->json([
                    'message' => 'pedido_realizado',
                    'id_pedido' => $pedido->id,
                ], 201);
            } else {
                return response()->json([
                    'status' => 'A loja está fechada no momento!'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['error' => 'Erro ao processar o pedido. Tente novamente mais tarde.'], 500);
        }
    }
    public function meusPedidos(Request $request)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if($user->role !== 'cliente' && $user->role !== 'admin'){
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }
        $perPage = intval($request->input('per_page', 10));
        $pedidos = Pedido::where('id_user', $user->id)
            ->latest()
            ->paginate($perPage);

        $pedidosFormatados = $pedidos->map(function($pedido) {
            $itens = json_decode($pedido->itens_pedido, true);
            $created_at = $pedido->created_at->format('d/m/Y H:i');
            $itensSelecionados = array_map(function($item) {
                return [
                    'nome' => $item['nome'],
                    'quantidade' => $item['quantidade'],
                    'tipo' => $item['tipo'],
                ];
            }, $itens);

            return [
                'id_pedido' => $pedido->id,
                'endereco' => $pedido->endereco,
                'forma_pagamento' => $pedido->forma_pagamento,
                'troco' => $pedido->troco,
                'total' => $pedido->total,
                'status' => $pedido->status,
                'itens_pedido' => $itensSelecionados,
                'created_at' => $created_at,
            ];
        });

        return response()->json([
            'id_user' => $user->id,
            'info_pedidos' => $pedidosFormatados,
            'pagination' => [
                'current_page' => $pedidos->currentPage(),
                'last_page' => $pedidos->lastPage(),
                'per_page' => $pedidos->perPage(),
                'total' => $pedidos->total(),
            ],
        ], 200);
    }
    public function cancelar(Request $request, $id_pedido)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if($user->role !== 'cliente' && $user->role !== 'admin'){
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $pedido = Pedido::where('id_user', $user->id)
                        ->where('id', $id_pedido)
                        ->with('itensPedido.produto')
                        ->first();

        if(!$pedido) {
            return response()->json(['error' => 'Pedido não encontrado.'], 404);
        }

        if($pedido->status !== 'pendente') {
            return response()->json(['error' => 'Somente pedidos com status "pendente" podem ser cancelados.'], 400);
        }

        foreach ($pedido->itensPedido as $item) {
            $produto = Produto::find($item->id_produto);
            if ($produto) {
                $produto->aumentarEstoque($item->quantidade);
            }
        }

        $pedido->status = 'cancelado';
        $pedido->save();
        $pedido->user->notify(new PedidoCancelado($request->user(), $pedido->id));

        return response()->json([
            'message' => "Pedido #{$pedido->id} cancelado com sucesso.",
        ], 200);

    }
    public function refazer(Request $request, $id_pedido)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if($user->role !== 'cliente' && $user->role !== 'admin'){
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $request->validate([
            'id_pedido' => 'required|integer|exists:pedidos,id',
        ]);

        $pedidoAntigo = Pedido::where('id_user', $user->id)
                        ->where('id', $request->input('id_pedido'))
                        ->with('itensPedido.produto')
                        ->first();

        if(!$pedidoAntigo) {
            return response()->json(['error' => 'Pedido não encontrado.'], 404);
        }

        $itens_pedido = [];
        foreach ($pedidoAntigo->itensPedido as $item) {
            $itens_pedido[] = [
                'id_produto'     => $item->id_produto,
                'quantidade'     => $item->quantidade,
                'preco_unitario' => $item->preco_unitario,
                'nome'           => $item->produto->nome,
                'imagem'       => $item->produto->img_url,
            ];
        }

        Carrinho::create([
            'id_user'      => $user->id,
            'itens_pedido' => json_encode($itens_pedido),
            'copao'        => $pedidoAntigo->copao,
            'total'        => $pedidoAntigo->total,
        ]);

        return response()->json([
            'message' => 'Carrinho atualizado com os itens do pedido selecionado. Prossiga para a confirmação do pedido!',
        ], 200);
    }
    public function tempoExpiradoParaCancelar($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $pedido = Pedido::find($id);
        if(!$pedido)
        {
            return response()->json(['error' => 'Pedido não encontrado.'], 404);
        }

        if ($pedido->status === 'pendente') {
            $pedido->status = 'preparando';
            $pedido->save();
            $pedido->user->notify(new PedidoEsperandoRetirada($user->name, $pedido->id));
        }

        return response()->json([
            'message' => 'Status do pedido atualizado para esperando retirada.'
        ], 200);
    }
    public function todosPedidos(Request $request)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if($user->role !== 'admin'){
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $perPage = intval($request->input('per_page', 10));
        $pedidos = Pedido::latest()->paginate($perPage);

        $pedidosFormatados = $pedidos->getCollection()->map(function($pedido) {
            $itens = json_decode($pedido->itens_pedido, true);
            $created_at = $pedido->created_at->format('d/m/Y H:i');
            $itensSelecionados = array_map(function($item) {
                return [
                    'nome' => $item['nome'],
                    'quantidade' => $item['quantidade'],
                    'tipo' => $item['tipo'],
                ];
            }, $itens);

            return [
                'id_pedido' => $pedido->id,
                'id_cliente' => $pedido->id_user,
                'nome_cliente' => $pedido->user->name,
                'endereco' => $pedido->endereco,
                'forma_pagamento' => $pedido->forma_pagamento,
                'troco' => $pedido->troco,
                'total' => $pedido->total,
                'status' => $pedido->status,
                'itens_pedido' => $itensSelecionados,
                'created_at' => $created_at,
            ];
        });

        return response()->json([
            'id_user' => $user->id,
            'info_pedidos' => $pedidosFormatados,
            'pagination' => [
                'current_page' => $pedidos->currentPage(),
                'last_page' => $pedidos->lastPage(),
                'per_page' => $pedidos->perPage(),
                'total' => $pedidos->total(),
            ],
        ], 200);
    }
}
