<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFuncionCargoRequest;
use App\Http\Requests\UpdateFuncionCargoRequest;
use App\Http\Resources\FuncionCargoResource;
use App\Models\FuncionCargo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FuncionCargoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FuncionCargo::with('cargo');

        if ($request->filled('id_cargo')) {
            $query->where('id_cargo', $request->id_cargo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $funciones = $query->paginate(10);

        return response()->json([
            'data' => FuncionCargoResource::collection($funciones),
            'meta' => [
                'total' => $funciones->total(),
                'per_page' => $funciones->perPage(),
                'current_page' => $funciones->currentPage(),
                'last_page' => $funciones->lastPage(),
                'from' => $funciones->firstItem(),
                'to' => $funciones->lastItem(),
            ],
        ]);
    }

    public function store(StoreFuncionCargoRequest $request): JsonResponse
    {
        $funcion = FuncionCargo::create($request->validated());
        $funcion->load('cargo');

        return response()->json([
            'data' => new FuncionCargoResource($funcion),
        ], 201);
    }

    public function show(FuncionCargo $funcionCargo): JsonResponse
    {
        $funcionCargo->load('cargo');

        return response()->json([
            'data' => new FuncionCargoResource($funcionCargo),
        ]);
    }

    public function update(UpdateFuncionCargoRequest $request, FuncionCargo $funcionCargo): JsonResponse
    {
        $funcionCargo->update($request->validated());
        $funcionCargo->load('cargo');

        return response()->json([
            'data' => new FuncionCargoResource($funcionCargo),
        ]);
    }

    public function destroy(FuncionCargo $funcionCargo): JsonResponse
    {
        $funcionCargo->delete();

        return response()->json([
            'message' => 'Función eliminada correctamente.',
        ]);
    }
}
