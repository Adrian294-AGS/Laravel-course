<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('employee_number', 30)->nullable()->unique();
            $table->string('first_name', 80)->nullable();
            $table->string('last_name', 80)->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('position', 100)->change();
            $table->enum('employment_status', ['Active', 'Inactive'])->default('Active');
        });

        $departmentId = DB::table('departments')->value('id');

        if ($departmentId === null) {
            $departmentId = DB::table('departments')->insertGetId([
                'name' => 'General',
                'code' => 'GEN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Backfill legacy rows before the new API fields become required.
        DB::table('employees')->orderBy('id')->chunkById(100, function ($employees) use ($departmentId): void {
            foreach ($employees as $employee) {
                $legacyName = trim((string) $employee->name);
                $nameParts = preg_split('/\s+/', $legacyName, 2) ?: [];

                DB::table('employees')->where('id', $employee->id)->update([
                    'department_id' => $departmentId,
                    'employee_number' => 'LEGACY-'.$employee->id,
                    'first_name' => mb_substr($nameParts[0] ?? 'Employee', 0, 80),
                    'last_name' => mb_substr($nameParts[1] ?? 'Staff', 0, 80),
                    'email' => 'employee-'.$employee->id.'@example.invalid',
                ]);
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable(false)->change();
            $table->string('employee_number', 30)->nullable(false)->change();
            $table->string('first_name', 80)->nullable(false)->change();
            $table->string('last_name', 80)->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->foreign('department_id')->references('id')->on('departments')->cascadeOnDelete();
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropUnique(['employee_number']);
            $table->dropUnique(['email']);
            $table->string('name')->nullable();
        });

        // Rebuild the old display name before removing the normalized name fields.
        DB::table('employees')->orderBy('id')->chunkById(100, function ($employees): void {
            foreach ($employees as $employee) {
                DB::table('employees')->where('id', $employee->id)->update([
                    'name' => trim($employee->first_name.' '.$employee->last_name),
                ]);
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['department_id', 'employee_number', 'first_name', 'last_name', 'email', 'employment_status']);
            $table->string('name')->nullable(false)->change();
            $table->string('position')->change();
        });

        Schema::dropIfExists('departments');
    }
};
