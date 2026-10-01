<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_employees(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');

        $this->postJson('/api/employees', [
            'name' => 'Ada Lovelace',
            'position' => 'Developer',
        ])
            ->assertCreated()
            ->assertJsonPath('employee.name', 'Ada Lovelace')
            ->assertJsonPath('employee.position', 'Developer');

        $employee = Employee::firstOrFail();

        $this->getJson('/api/employees')
            ->assertOk()
            ->assertJsonCount(1, 'employees');

        $this->getJson("/api/employees/{$employee->id}")
            ->assertOk()
            ->assertJsonPath('employee.id', $employee->id);

        $this->putJson("/api/employees/{$employee->id}", [
            'name' => 'Ada Byron',
            'position' => 'Lead Developer',
        ])
            ->assertOk()
            ->assertJsonPath('employee.name', 'Ada Byron')
            ->assertJsonPath('employee.position', 'Lead Developer');

        $this->deleteJson("/api/employees/{$employee->id}")
            ->assertOk();

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }
}