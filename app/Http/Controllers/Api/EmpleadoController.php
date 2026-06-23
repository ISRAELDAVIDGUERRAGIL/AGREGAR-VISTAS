<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmpleadoRequest;
use App\Http\Resources\EmpleadoResource;
use App\Models\Empleado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Empleado::with('cargo');

        if ($request->filled('nombre')) {
            $query->where('nombres', 'LIKE', "%{$request->nombre}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('cargo')) {
            $query->where('id_cargo', $request->cargo);
        }

        if ($request->filled('cargos')) {
            $query->whereIn('id_cargo', explode(',', $request->cargos));
        }

        if ($request->filled('salario_min') && $request->filled('salario_max')) {
            $query->whereBetween('salario', [$request->salario_min, $request->salario_max]);
        }

        $empleados = $query->paginate(10);

        return response()->json([
            'data' => EmpleadoResource::collection($empleados),
            'meta' => [
                'total' => $empleados->total(),
                'per_page' => $empleados->perPage(),
                'current_page' => $empleados->currentPage(),
                'last_page' => $empleados->lastPage(),
                'from' => $empleados->firstItem(),
                'to' => $empleados->lastItem(),
            ],
        ]);
    }

    public function store(EmpleadoRequest $request): JsonResponse
    {
        $empleado = Empleado::create($request->validated());
        $empleado->load('cargo');

        return response()->json([
            'data' => new EmpleadoResource($empleado),
        ], 201);
    }

    public function show(Empleado $empleado): JsonResponse
    {
        $empleado->load('cargo');

        return response()->json([
            'data' => new EmpleadoResource($empleado),
        ]);
    }

    public function update(EmpleadoRequest $request, Empleado $empleado): JsonResponse
    {
        $empleado->update($request->validated());
        $empleado->load('cargo');

        return response()->json([
            'data' => new EmpleadoResource($empleado),
        ]);
    }

    public function destroy(Empleado $empleado): JsonResponse
    {
        $empleado->delete();

        return response()->json([
            'message' => 'Empleado eliminado correctamente.',
        ]);
    }
}
