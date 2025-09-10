<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:30|unique:users,username',
            'email'    => 'nullable|string|email|unique:users,email',
            'telefone' => 'nullable|string|unique:users,telefone',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'telefone' => $request->telefone,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        if($user->role == "cliente"){
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user'  => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'role'     => $user->role,
                ],
                'token' => $token,
            ], 201);
        }else{
            return response()->json([
                'user'  => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'role'     => $user->role,
                ]
            ], 201);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($login_type, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email/Username or Password does not match.',
            ], 401);
        }

        $token = $user->createToken('LOGIN TOKEN')->plainTextToken;

        return response()->json([
            'user'  => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'role'     => $user->role,
            ],
            'token' => $token,
        ], 200);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:30|unique:users,username,' . $user->id,
            'email'    => 'sometimes|string|email|unique:users,email,' . $user->id,
            'telefone' => 'sometimes|string|unique:users,telefone,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $fields = ['name', 'username', 'email', 'telefone'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $user->$field = $request->input($field);
            }
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso.',
            'data' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'telefone' => $user->telefone,
            ],
        ]);
    }
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $user->delete();

        return response()->json([
            'message' => 'Usuário excluído com sucesso.',
        ]);
    }
    public function userLogado(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        return response()->json([
            'data' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'telefone' => $user->telefone,
                'role'     => $user->role,
            ],
        ], 200);
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
}
