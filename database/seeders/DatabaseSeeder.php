<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'College API Administrator',
            'password' => 'CollegeAdmin123!',
        ]);

        $departments = [
            ['name' => 'Administration', 'code' => 'ADMIN'],
            ['name' => 'Admissions', 'code' => 'ADMS'],
            ['name' => 'Information Technology', 'code' => 'IT'],
        ];

        foreach ($departments as $attributes) {
            Department::updateOrCreate(['code' => $attributes['code']], $attributes);
        }

        $administration = Department::where('code', 'ADMIN')->firstOrFail();
        $admissions = Department::where('code', 'ADMS')->firstOrFail();

        Employee::updateOrCreate(['employee_number' => 'EMP-1001'], [
            'department_id' => $administration->id,
            'first_name' => 'Amina',
            'last_name' => 'Patel',
            'email' => 'amina.patel@example.edu',
            'position' => 'Office Coordinator',
            'employment_status' => 'Active',
        ]);

        Employee::updateOrCreate(['employee_number' => 'EMP-1002'], [
            'department_id' => $admissions->id,
            'first_name' => 'Marcus',
            'last_name' => 'Reed',
            'email' => 'marcus.reed@example.edu',
            'position' => 'Admissions Officer',
            'employment_status' => 'Active',
        ]);
    }
}
