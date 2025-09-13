<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Pedido;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function atualizarStatusPedido(Request $request, $id_pedido)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 401);
        }

        // Somente admins podem mudar o status do pedido
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Permissão negada.'
            ], 403);
        }

        // Buscar o pedido
        $pedido = Pedido::find($id_pedido);
        if (!$pedido) {
            return response()->json([
                'message' => 'Pedido não encontrado.'
            ], 404);
        }

        // Validar novo status
        $novoStatus = $request->input('status');
        $statusValidos = ['preparando', 'esperando_retirada', 'saiu_para_entrega', 'entregue', 'cancelado'];

        if (!in_array($novoStatus, $statusValidos)) {
            return response()->json([
                'message' => 'Status inválido.'
            ], 400);
        }

        // Atualizar e salvar
        $pedido->status = $novoStatus;
        $pedido->save();

        return response()->json([
            'message' => 'Status atualizado com sucesso!',
            'pedido' => $pedido
        ]);
    }
    public function atualizarRole(Request $request, $id_user)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 401);
        }

        // Somente admins podem mudar roles
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Permissão negada.'
            ], 403);
        }

        // Impede que o próprio usuário altere sua role
        if ($user->id == $id_user) {
            return response()->json([
                'message' => 'Você não pode alterar a sua própria role.'
            ], 403);
        }

        $targetUser = User::find($id_user);
        if (!$targetUser) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        $novaRole = $request->input('role');
        $rolesValidas = ['admin', 'entregador', 'cliente'];

        if (!in_array($novaRole, $rolesValidas)) {
            return response()->json([
                'message' => 'Role inválida.'
            ], 400);
        }

        $targetUser->role = $novaRole;
        $targetUser->save();

        return response()->json([
            'message' => 'Role atualizado com sucesso!',
            'user' => $targetUser
        ]);
    }
    public function deleteUser($id_user)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 401);
        }

        // Somente admins podem deletar usuários
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Permissão negada.'
            ], 403);
        }

        // Impede que o próprio usuário se delete
        if ($user->id == $id_user) {
            return response()->json([
                'message' => 'Você não pode deletar a si mesmo.'
            ], 403);
        }

        $targetUser = User::find($id_user);
        if (!$targetUser) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        try {
            $targetUser->delete();
            return response()->json([
                'message' => 'Usuário deletado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar usuário.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function settings()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 401);
        }

        $settings = AdminSettings::first();

        if (!$settings) {
            $settings = AdminSettings::create();
        }

        return response()->json($settings);
    }
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 401);
        }

        $settings = AdminSettings::updateOrCreate(
            ['id' => optional(AdminSettings::first())->id],
            [
                'valor_adicional_pedido' => $request->input('valor_adicional_pedido'),
                'status_aberto'          => $request->input('status_aberto'),
                'horario_abertura'       => $request->input('horario_abertura'),
                'horario_fechamento'     => $request->input('horario_fechamento'),
            ]
        );

        return response()->json([
            'message'  => 'Configurações atualizadas com sucesso!',
            'settings' => $settings,
        ]);
    }
    public function todosClientes()
    {
        $user = Auth::user();

        if ($user->role == "admin") {
            $users = User::where('role', 'cliente')
                ->latest()
                ->get();

            return response()->json([
                'users' => [
                    'data' => $users->map(function ($user) {
                        return [
                            'id'         => $user->id,
                            'nome'       => $user->name,
                            'username'   => $user->username,
                            'email'      => $user->email,
                            'telefone'   => $user->telefone,
                            'role'       => $user->role,
                            'created_at' => $user->created_at->format('d/m/Y H:i'),
                        ];
                    }),
                    'total' => $users->count(),
                ],
            ]);
        }

        return response()->json([
            'message' => 'Acesso negado.'
        ], 401);
    }
    public function todosEntregadores()
    {
        $user = Auth::user();

        if ($user->role == "admin") {
            $users = User::where('role', 'entregador')
                ->latest()
                ->get();

            return response()->json([
                'users' => [
                    'data' => $users->map(function ($user) {
                        return [
                            'id'         => $user->id,
                            'nome'       => $user->name,
                            'username'   => $user->username,
                            'email'      => $user->email,
                            'telefone'   => $user->telefone,
                            'role'       => $user->role,
                            'created_at' => $user->created_at->format('d/m/Y H:i'),
                        ];
                    }),
                    'total' => $users->count(),
                ],
            ]);
        }

        return response()->json([
            'message' => 'Acesso negado.'
        ], 401);
    }
    public function todosAdmins()
    {
        $user = Auth::user();

        if ($user->role == "admin") {
            $users = User::where('role', 'admin')
                ->latest()
                ->get();

            return response()->json([
                'users' => [
                    'data' => $users->map(function ($user) {
                        return [
                            'id'         => $user->id,
                            'nome'       => $user->name,
                            'username'   => $user->username,
                            'email'      => $user->email,
                            'telefone'   => $user->telefone,
                            'role'       => $user->role,
                            'created_at' => $user->created_at->format('d/m/Y H:i'),
                        ];
                    }),
                    'total' => $users->count(),
                ],
            ]);
        }

        return response()->json([
            'message' => 'Acesso negado.'
        ], 401);
    }
}
