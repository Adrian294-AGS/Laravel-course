<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Departments retrieved successfully.',
            'data' => Department::query()->orderBy('name')->get(['id', 'name', 'code']),
            'errors' => null,
        ]);
    }
}
