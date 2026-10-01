<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_employee_search_and_status_filter_can_be_combined(): void
    {
        $department = Department::create(['name' => 'Information Technology', 'code' => 'IT']);
        $matching = $this->createEmployee($department, [
            'employee_number' => 'EMP-101',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.edu',
        ]);
        $this->createEmployee($department, [
            'employee_number' => 'EMP-102',
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'grace@example.edu',
            'employment_status' => 'Inactive',
        ]);

        $this->getJson('/api/employees?search=INFORMATION&employment_status=Active')
            ->assertOk()
            ->assertJsonPath('data.0.id', $matching->id)
            ->assertJsonPath('data.0.department.name', 'Information Technology')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_departments_are_public_and_missing_employee_returns_sanitized_not_found(): void
    {
        Department::create(['name' => 'Student Services', 'code' => 'STSV']);

        $this->getJson('/api/departments')
            ->assertOk()
            ->assertJsonPath('data.0.code', 'STSV');

        $this->getJson('/api/employees/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'The requested resource was not found.')
            ->assertJsonStructure(['message', 'data', 'errors']);
    }

    public function test_write_routes_require_authentication_and_support_employee_crud(): void
    {
        $department = Department::create(['name' => 'Administration', 'code' => 'ADMIN']);
        $payload = [
            'department_id' => $department->id,
            'employee_number' => 'EMP-201',
            'first_name' => 'Amina',
            'last_name' => 'Patel',
            'email' => 'amina@example.edu',
            'position' => 'Office Coordinator',
            'employment_status' => 'Active',
        ];

        $this->postJson('/api/employees', $payload)
            ->assertUnauthorized()
            ->assertJsonPath('data', null);

        $this->actingAs(User::factory()->create(), 'sanctum');
        $created = $this->postJson('/api/employees', $payload)
            ->assertCreated()
            ->assertJsonPath('data.employee_number', 'EMP-201')
            ->assertJsonPath('data.department.code', 'ADMIN');
        $employeeId = $created->json('data.id');

        $this->putJson("/api/employees/{$employeeId}", $payload)
            ->assertOk()
            ->assertJsonPath('data.email', 'amina@example.edu');

        $this->patchJson("/api/employees/{$employeeId}", ['employment_status' => 'Inactive'])
            ->assertOk()
            ->assertJsonPath('data.employment_status', 'Inactive');

        $this->deleteJson("/api/employees/{$employeeId}")->assertNoContent();
        $this->assertDatabaseMissing('employees', ['id' => $employeeId]);
    }

    public function test_duplicate_employee_values_are_rejected_but_updates_ignore_current_record(): void
    {
        $department = Department::create(['name' => 'Admissions', 'code' => 'ADMS']);
        $employee = $this->createEmployee($department);
        $this->actingAs(User::factory()->create(), 'sanctum');

        $this->patchJson("/api/employees/{$employee->id}", ['email' => 'staff@example.edu'])
            ->assertOk()
            ->assertJsonPath('data.email', 'staff@example.edu');

        $this->postJson('/api/employees', [
            'department_id' => $department->id,
            'employee_number' => $employee->employee_number,
            'first_name' => 'Other',
            'last_name' => 'Person',
            'email' => 'other@example.edu',
            'position' => 'Advisor',
            'employment_status' => 'Active',
        ])->assertUnprocessable()->assertJsonStructure(['message', 'data', 'errors']);
    }

    public function test_login_issues_a_token_and_logout_revokes_it(): void
    {
        $user = User::factory()->create(['email' => 'api-user@example.edu', 'password' => 'secret-password']);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertOk()->assertJsonStructure(['message', 'data' => ['token', 'user'], 'errors']);

        $this->assertArrayNotHasKey('password', $login->json('data.user'));
        $token = $login->json('data.token');

        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->withToken($token)->postJson('/api/employees', [])->assertUnauthorized();
    }

    private function createEmployee(Department $department, array $attributes = []): Employee
    {
        return Employee::create(array_merge([
            'department_id' => $department->id,
            'employee_number' => 'EMP-100',
            'first_name' => 'Sample',
            'last_name' => 'Employee',
            'email' => 'staff@example.edu',
            'position' => 'Coordinator',
            'employment_status' => 'Active',
        ], $attributes));
    }
}
