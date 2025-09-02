<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Illuminate\Support\Facades\Validator;

class ProdutoController extends Controller
{
    public function index()
    {
        // puxar todos os produtos, menos do tipo 'copao'
        $produtos = Produto::where('tipo', '!=', 'copao')->get();

        return response()->json([
            'produtos' => [
                'data' => $produtos->map(function ($produto) {
                    return [
                        'id'         => $produto->id,
                        'nome'       => $produto->nome,
                        'tipo'       => $produto->tipo,
                        'preco_base' => $produto->preco_base,
                        'estoque'    => $produto->estoque,
                        'imagem'     => url($produto->img_url),
                    ];
                }),
                'total' => $produtos->count(),
            ],
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'preco_base' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'imagem'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $imagemPath = null;

        if ($request->hasFile('imagem')) {
            $imagemPath = $request->file('imagem')->store('p', 'public');
        }else{
            $imagemPath = 'i/produto-padrao.png';
        }

        $produto = Produto::create([
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'preco_base' => $request->preco_base,
            'estoque' => $request->estoque,
            'imagem'   => $imagemPath,
        ]);

        return response()->json([
            'produto'  => [
                'id'       => $produto->id,
                'nome' => $produto->nome,
                'tipo' => $produto->tipo,
                'imagem'   => url($produto->img_url),
            ],
        ], 201);
    }
    public function show($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        return response()->json([
            'produto'  => [
                'id'       => $produto->id,
                'nome' => $produto->nome,
                'tipo' => $produto->tipo,
                'preco_base' => $produto->preco_base,
                'estoque' => $produto->estoque,
                'imagem'   => url($produto->img_url),
            ],
        ]);
    }
    public function update(Request $request, $id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|string|max:100',
            'preco_base' => 'sometimes|required|numeric|min:0',
            'estoque' => 'sometimes|required|integer|min:0',
            'imagem'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('imagem')) {
            $imagemPath = $request->file('imagem')->store('p', 'public');
            $produto->imagem = $imagemPath;
        }

        if ($request->has('nome')) {
            $produto->nome = $request->nome;
        }
        if ($request->has('tipo')) {
            $produto->tipo = $request->tipo;
        }
        if ($request->has('preco_base')) {
            $produto->preco_base = $request->preco_base;
        }
        if ($request->has('estoque')) {
            $produto->estoque = $request->estoque;
        }

        $produto->save();

        return response()->json([
            'produto'  => [
                'id'       => $produto->id,
                'nome' => $produto->nome,
                'tipo' => $produto->tipo,
                'imagem'   => url($produto->img_url),
            ],
        ], 200);
    }
    public function destroy($id) {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto deletado com sucesso.'], 200);
    }
}
