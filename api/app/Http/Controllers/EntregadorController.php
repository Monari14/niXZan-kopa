<?php

namespace App\Http\Controllers;

use App\Notifications\PedidoEntregue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pedido;
use App\Notifications\PedidoSaiuParaEntrega;

class EntregadorController extends Controller
{
    public function pedidosEsperandoRetirada(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if ($user->role !== 'entregador' && $user->role !== 'admin') {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $pedidos = Pedido::where('status', 'esperando_retirada')
            ->with('itens.produto', 'usuario')
            ->latest()
            ->get();

        $pedidosFormatados = $pedidos->map(function ($pedido) {
            return [
                'id_pedido' => $pedido->id,
                'cliente'   => $pedido->usuario->name,
                'endereco'  => $pedido->endereco,
                'total'     => $pedido->total,
            ];
        });

        return response()->json([
            'entregador' => $user->username,
            'pedidos'    => $pedidosFormatados
        ], 200);
    }
    public function aceitarEntrega(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if ($user->role !== 'entregador' && $user->role !== 'admin') {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $request->validate([
            'id_pedido' => 'required|integer|exists:pedidos,id',
        ]);

        $pedido = Pedido::where('status', 'esperando_retirada')
            ->where('id', $request->input('id_pedido'))
            ->first();

        if (!$pedido) {
            return response()->json(['error' => 'Pedido não encontrado ou não está aguardando retirada.'], 404);
        }

        $pedido->status = 'saiu_para_entrega';
        $pedido->id_entregador = $user->id;
        $pedido->save();

        $pedido->usuario->notify(new PedidoSaiuParaEntrega(
            $user->name,
            $pedido->usuario->name,
            $pedido->id
        ));

        return response()->json([
            'message' => "Pedido #{$pedido->id} aceito para entrega com sucesso.",
        ], 200);
    }
    public function finalizarEntrega(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if ($user->role !== 'entregador' && $user->role !== 'admin') {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $request->validate([
            'id_pedido' => 'required|integer|exists:pedidos,id',
        ]);

        $pedido = Pedido::where('status', 'saiu_para_entrega')
            ->where('id', $request->input('id_pedido'))
            ->where('id_entregador', $user->id)
            ->first();

        if (!$pedido) {
            return response()->json(['error' => 'Pedido não encontrado ou não está em entrega para você.'], 404);
        }

        $pedido->status = 'entregue';
        $pedido->save();

        $pedido->usuario->notify(new PedidoEntregue(
            $user->name,
            $pedido->usuario->name,
            $pedido->id
        ));

        return response()->json([
            'message' => "Pedido #{$pedido->id} entregue!",
        ], 200);
    }
    public function todasMinhasEntregas(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        if ($user->role !== 'entregador' && $user->role !== 'admin') {
            return response()->json(['error' => 'Acesso não autorizado!'], 401);
        }

        $pedidos = Pedido::where('id_entregador', $user->id)
            ->with('itens.produto', 'usuario')
            ->latest()
            ->get();

        $pedidosFormatados = $pedidos->map(function ($pedido) {
            return [
                'id_pedido' => $pedido->id,
                'cliente'   => $pedido->usuario->name,
                'endereco'  => $pedido->endereco,
                'total'     => $pedido->total,
                'status'    => $pedido->status,
            ];
        });

        return response()->json([
            'entregador' => $user->username,
            'pedidos'    => $pedidosFormatados
        ], 200);
    }
}
