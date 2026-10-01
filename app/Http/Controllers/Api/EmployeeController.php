<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json([
            'employees' => Employee::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
        ]);

        $employee = Employee::create($validated);

        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => $employee,
        ], 201);
    }

    public function show($id)
    {
        return response()->json([
            'employee' => Employee::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $employee->update($validated);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully',
        ]);
    }
}