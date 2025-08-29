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
    public function carrinho(Request $request)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if($user->role === 'cliente' || $user->role === 'admin'){
            $carrinho = Carrinho::where('id_user', $user->id)->latest()->first();
            if(!$carrinho) {
                return response()->json(['message' => 'Carrinho vazio.'], 200);
            }

            $itens = json_decode($carrinho->itens_pedido, true);
            $copao = $carrinho->copao;
            $total = $carrinho->total;

            return response()->json([
                'carrinho' => [
                    'itens' => $itens,
                    'copao' => $copao,
                    'total' => $total,
                ]
            ], 200);
        }

        return response()->json(['error' => 'Acesso não autorizado!'], 401);
    }
    public function novoPedido(Request $request)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if($user->role === 'cliente' || $user->role === 'admin'){
            $energeticos = $request->input('energeticos', []);
            $bebidas     = $request->input('bebidas', []);
            $gelos       = $request->input('gelos', []);

            $qtd_energeticos = array_sum($energeticos);
            $qtd_bebidas     = array_sum($bebidas);
            $qtd_gelos       = array_sum($gelos);

            $quantidades = [$qtd_energeticos, $qtd_bebidas, $qtd_gelos];

            if(min($quantidades) < 1) {
                return response()->json(['error' => 'É necessário pelo menos 1 item de cada tipo.'], 400);
            }
            if(count(array_unique($quantidades)) !== 1) {
                return response()->json(['error' => 'Todos os itens (energético, bebida e gelo) devem ter a mesma quantidade.'], 400);
            }

            $copao = $qtd_energeticos;

            $total = 0;
            $itens_pedido = [];

            foreach ([$energeticos, $bebidas, $gelos] as $grupo) {
                foreach($grupo as $id_produto => $qtd) {
                    if($qtd > 0) {
                        $produto = Produto::find($id_produto);
                        $preco = Preco::where('produto_id', $id_produto)->latest()->first();

                        if(!$preco || $preco->valor === null || $preco->valor <= 0) {
                            return response()->json(['error' => "O produto '{$produto->nome}' está sem preço válido cadastrado."], 400);
                        }
                        $valor = $qtd * $preco->valor;
                        $total += $valor;

                        $itens_pedido[] = [
                            'id_produto'     => $id_produto,
                            'quantidade'     => $qtd,
                            'preco_unitario' => $preco->valor ?? 0,
                            'nome'           => $produto->nome,
                            'imagem'         => $produto->img_url,
                        ];
                    }
                }
            }
            $copaoProduto = Produto::where('tipo', 'copao')->first();
            if($copaoProduto && $copao > 0) {
                $preco = Preco::where('id_produto', $copaoProduto->id)->latest()->first();
                if(!$preco || $preco->valor === null || $preco->valor <= 0) {
                    return response()->json(['error' => "O produto '{$copaoProduto->nome}' está sem preço válido cadastrado."], 400);
                }
                $valor = $copao * $preco->valor;
                $total += $valor;

                $itens_pedido[] = [
                    'id_produto'     => $copaoProduto->id,
                    'quantidade'     => $copao,
                    'preco_unitario' => $preco->valor ?? 0,
                    'nome'           => $copaoProduto->nome,
                    'imagem'        => $copaoProduto->img_url,
                ];

                $valorAdicional = AdminSettings::first()->valor_adicional_pedido ?? 0;
                $total += $valorAdicional * $copao;

                Carrinho::create([
                    'id_user'      => $user->id,
                    'itens_pedido' => json_encode($itens_pedido),
                    'copao'        => $copao,
                    'total'        => $total,
                ]);

                return response()->json([
                    'message' => 'Carrinho salvo com sucesso, prosseguir para a confirmação do pedido!',
                ]);
            }
        }
    }
    public function confirmar(Request $request)
    {
        $user = Auth::user();
        if(!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if($user->role !== 'cliente' && $user->role !== 'admin'){
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $request->validate([
            'endereco'        => 'required|string|max:255',
            'forma_pagamento' => 'required|in:pix,dinheiro',
            'valor_troco'     => 'required_if:forma_pagamento,dinheiro|nullable|numeric|min:0',
        ]);

        $carrinho = Carrinho::where('id_user', $user->id)->latest()->first();

        $itens = $carrinho->itens_pedido;
        $copao = $carrinho->copao;
        $total = $carrinho->total;

        if(!$itens || count($itens) === 0) {
            return response()->json(['error' => 'Carrinho vazio.'], 400);
        }

        try{
            $totalItens = collect($itens)->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']);
            $troco = null;

            if($request->forma_pagamento === 'dinheiro')
            {
                $valorPago = $request->input('valor_troco', 0);
                if($valorPago < $total) {
                    return response()->json(['error' => 'O valor para troco deve ser maior ou igual ao total do pedido.'], 400);
                }
                $troco = $valorPago - $total;
            }

            $status = AdminSettings::first()->status_aberto;
            if($status === true) {
                $pedido = Pedido::create([
                    'id_user' => $user->id,
                    'copao' => $copao,
                    'total' => $total,
                    'status',
                    'endereco' => $request->input('endereco'),
                    'forma_pagamento' => $request->input('forma_pagamento'),
                    'troco' => $troco,
                ]);

                foreach ($itens as $item) {
                    ItemPedido::create([
                        'id_pedido' => $pedido->id,
                        'id_produto' => $item['produto_id'],
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $item['preco_unitario'],
                    ]);

                    $produto = Produto::find($item['produto_id']);
                    if ($produto) {
                        $sucesso = $produto->diminuirEstoque($item['quantidade']);
                        if(!$sucesso) {
                            return response()->json(['error' => "Estoque insuficiente para o produto '{$produto->nome}'."], 400);
                        }
                    }
                }
                $carrinho->delete();
                $pedido->user->notify(new PedidoFeito($request->user(), $pedido->id));
                return response()->json([
                    'id_pedido' => $pedido->id,
                    'message' => 'Pedido feito com sucesso!',
                ], 201);
            }
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

        $pedidos = Pedido::where('id_user', $user->id)
                    ->with('itensPedido.produto')
                    ->latest()
                    ->get();

        return response()->json([
            'user' => $user->username,
            'pedidos' => $pedidos
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

        if ($pedido->status === 'preparando') {
            $pedido->status = 'esperando_retirada';
            $pedido->save();
            $pedido->user->notify(new PedidoEsperandoRetirada($user->name, $pedido->id));
        }

        return response()->json([
            'message' => 'Status do pedido atualizado para esperando retirada.'
        ], 200);
    }
}
