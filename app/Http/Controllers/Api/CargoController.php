<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCargoRequest;
use App\Http\Requests\UpdateCargoRequest;
use App\Http\Resources\CargoResource;
use App\Models\Cargo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Cargo::with('funcionesCargo');

        if ($request->filled('nombre')) {
            $query->where('nombre_cargo', 'LIKE', "%{$request->nombre}%");
        }

        if ($request->filled('ids')) {
            $query->whereIn('id_cargo', explode(',', $request->ids));
        }

        $cargos = $query->paginate(10);

        return response()->json([
            'data' => CargoResource::collection($cargos),
            'meta' => [
                'total' => $cargos->total(),
                'per_page' => $cargos->perPage(),
                'current_page' => $cargos->currentPage(),
                'last_page' => $cargos->lastPage(),
                'from' => $cargos->firstItem(),
                'to' => $cargos->lastItem(),
            ],
        ]);
    }

    public function store(StoreCargoRequest $request): JsonResponse
    {
        $cargo = Cargo::create($request->validated());

        return response()->json([
            'data' => new CargoResource($cargo),
        ], 201);
    }

    public function show(Cargo $cargo): JsonResponse
    {
        $cargo->load('funcionesCargo');

        return response()->json([
            'data' => new CargoResource($cargo),
        ]);
    }

    public function update(UpdateCargoRequest $request, Cargo $cargo): JsonResponse
    {
        $cargo->update($request->validated());

        return response()->json([
            'data' => new CargoResource($cargo),
        ]);
    }

    public function destroy(Cargo $cargo): JsonResponse
    {
        $cargo->delete();

        return response()->json([
            'message' => 'Cargo eliminado correctamente.',
        ]);
    }
}
