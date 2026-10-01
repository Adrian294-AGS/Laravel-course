<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'employment_status' => ['sometimes', 'required', Rule::in(['Active', 'Inactive'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
        ]);

        $employees = Employee::query()->with('department')->when(
            $request->filled('search'),
            function ($query) use ($request): void {
                $term = '%'.mb_strtolower((string) $request->query('search')).'%';

                // Normalize both sides so matching does not depend on database collation.
                $query->where(function ($query) use ($term): void {
                    foreach (['employee_number', 'first_name', 'last_name', 'email'] as $column) {
                        $query->orWhereRaw("LOWER($column) LIKE ?", [$term]);
                    }

                    $query->orWhereHas('department', fn ($departmentQuery) => $departmentQuery->whereRaw('LOWER(name) LIKE ?', [$term]));
                });
            }
        )->when(
            isset($validated['employment_status']),
            fn ($query) => $query->where('employment_status', $validated['employment_status'])
        )->orderBy('id')->paginate($validated['per_page'] ?? 15);

        return response()->json([
            'message' => 'Employees retrieved successfully.',
            'data' => EmployeeResource::collection($employees->getCollection()),
            'errors' => null,
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
        ]);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());
        $employee->load('department');

        return response()->json([
            'message' => 'Employee created successfully.',
            'data' => new EmployeeResource($employee),
            'errors' => null,
        ], 201);
    }

    public function show(Employee $employee)
    {
        return response()->json([
            'message' => 'Employee retrieved successfully.',
            'data' => new EmployeeResource($employee->load('department')),
            'errors' => null,
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->validated());

        return response()->json([
            'message' => 'Employee updated successfully.',
            'data' => new EmployeeResource($employee->fresh()->load('department')),
            'errors' => null,
        ]);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->noContent();
    }
}
