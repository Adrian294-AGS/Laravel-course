<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        // For testing purposes, return a sample response
        return response()->json([
            'message' => 'Employees list (protected route)',
            'employees' => [
                ['id' => 1, 'name' => 'John Doe', 'position' => 'Developer'],
                ['id' => 2, 'name' => 'Jane Smith', 'position' => 'Designer']
            ]
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Employee created successfully (protected route)',
            'data' => $request->all()
        ], 201);
    }

    public function show($id)
    {
        return response()->json([
            'message' => "Employee {$id} details (protected route)",
            'employee' => ['id' => $id, 'name' => 'Sample Employee', 'position' => 'Manager']
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'message' => "Employee {$id} updated successfully (protected route)",
            'data' => $request->all()
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'message' => "Employee {$id} deleted successfully (protected route)"
        ]);
    }
}