<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if we're using PostgreSQL
        $isPostgreSQL = config('database.default') === 'pgsql';

        if ($isPostgreSQL) {
            // Add missing columns to employees table
            if (!Schema::hasColumn('employees', 'employment_status')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('employment_status', 20)->nullable();
                });
            }

            if (!Schema::hasColumn('employees', 'salary_currency')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('salary_currency', 10)->default('MVR');
                });
            }

            if (!Schema::hasColumn('employees', 'level')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('level', 10)->default('junior');
                });
            }

            if (!Schema::hasColumn('employees', 'company')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('company', 100)->default('RASHEED CARPENTRY AND CONSTRUCTION PVT LTD');
                });
            }

            // Add missing columns to leaves table
            if (!Schema::hasColumn('leaves', 'leave_type')) {
                Schema::table('leaves', function (Blueprint $table) {
                    $table->string('leave_type', 20);
                });
            }

            if (!Schema::hasColumn('leaves', 'status')) {
                Schema::table('leaves', function (Blueprint $table) {
                    $table->string('status', 20)->default('Pending');
                });
            }

            // Add missing columns to attendance table
            if (!Schema::hasColumn('attendance', 'status')) {
                Schema::table('attendance', function (Blueprint $table) {
                    $table->string('status', 20)->default('Absent');
                });
            }

            if (!Schema::hasColumn('attendance', 'check_in_status')) {
                Schema::table('attendance', function (Blueprint $table) {
                    $table->string('check_in_status', 20)->nullable();
                });
            }

            if (!Schema::hasColumn('attendance', 'check_out_status')) {
                Schema::table('attendance', function (Blueprint $table) {
                    $table->string('check_out_status', 20)->nullable();
                });
            }

            // Add missing columns to loans table
            if (!Schema::hasColumn('loans', 'loan_type')) {
                Schema::table('loans', function (Blueprint $table) {
                    $table->string('loan_type', 20);
                });
            }

            if (!Schema::hasColumn('loans', 'status')) {
                Schema::table('loans', function (Blueprint $table) {
                    $table->string('status', 20)->default('Pending');
                });
            }

            // Add missing columns to warnings table
            if (!Schema::hasColumn('warnings', 'warning_type')) {
                Schema::table('warnings', function (Blueprint $table) {
                    $table->string('warning_type', 20);
                });
            }

            if (!Schema::hasColumn('warnings', 'status')) {
                Schema::table('warnings', function (Blueprint $table) {
                    $table->string('status', 20)->default('Active');
                });
            }

            if (!Schema::hasColumn('warnings', 'severity_level')) {
                Schema::table('warnings', function (Blueprint $table) {
                    $table->string('severity_level', 20)->default('Medium');
                });
            }

            // Add missing columns to medical_records table
            if (!Schema::hasColumn('medical_records', 'medical_type')) {
                Schema::table('medical_records', function (Blueprint $table) {
                    $table->string('medical_type', 20);
                });
            }

            if (!Schema::hasColumn('medical_records', 'status')) {
                Schema::table('medical_records', function (Blueprint $table) {
                    $table->string('status', 20)->default('Active');
                });
            }

            // Add check constraints for data validation (PostgreSQL specific)
            $this->addCheckConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isPostgreSQL = config('database.default') === 'pgsql';

        if ($isPostgreSQL) {
            // Remove check constraints
            $this->removeCheckConstraints();

            // Remove columns
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn(['employment_status', 'salary_currency', 'level', 'company']);
            });

            Schema::table('leaves', function (Blueprint $table) {
                $table->dropColumn(['leave_type', 'status']);
            });

            Schema::table('attendance', function (Blueprint $table) {
                $table->dropColumn(['status', 'check_in_status', 'check_out_status']);
            });

            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn(['loan_type', 'status']);
            });

            Schema::table('warnings', function (Blueprint $table) {
                $table->dropColumn(['warning_type', 'status', 'severity_level']);
            });

            Schema::table('medical_records', function (Blueprint $table) {
                $table->dropColumn(['medical_type', 'status']);
            });
        }
    }

    /**
     * Add check constraints for data validation
     */
    private function addCheckConstraints(): void
    {
        // Employee constraints
        DB::statement("ALTER TABLE employees ADD CONSTRAINT check_employment_status 
            CHECK (employment_status IN ('Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'))");
        
        DB::statement("ALTER TABLE employees ADD CONSTRAINT check_salary_currency 
            CHECK (salary_currency IN ('MVR', 'USD'))");
        
        DB::statement("ALTER TABLE employees ADD CONSTRAINT check_level 
            CHECK (level IN ('senior', 'junior'))");

        // Leave constraints
        DB::statement("ALTER TABLE leaves ADD CONSTRAINT check_leave_type 
            CHECK (leave_type IN ('Annual', 'Sick', 'Emergency', 'Maternity', 'Paternity', 'Unpaid', 'Other'))");
        
        DB::statement("ALTER TABLE leaves ADD CONSTRAINT check_leave_status 
            CHECK (status IN ('Pending', 'Approved', 'Rejected'))");

        // Attendance constraints
        DB::statement("ALTER TABLE attendance ADD CONSTRAINT check_attendance_status 
            CHECK (status IN ('Present', 'Absent', 'Late', 'Early Departure', 'Half Day', 'Leave', 'Holiday', 'Weekend', 'Remote', 'Business Trip'))");
        
        DB::statement("ALTER TABLE attendance ADD CONSTRAINT check_check_in_status 
            CHECK (check_in_status IN ('On Time', 'Late', 'Early'))");
        
        DB::statement("ALTER TABLE attendance ADD CONSTRAINT check_check_out_status 
            CHECK (check_out_status IN ('On Time', 'Early', 'Late'))");

        // Loan constraints
        DB::statement("ALTER TABLE loans ADD CONSTRAINT check_loan_type 
            CHECK (loan_type IN ('Personal', 'Housing', 'Vehicle', 'Education', 'Emergency', 'Other'))");
        
        DB::statement("ALTER TABLE loans ADD CONSTRAINT check_loan_status 
            CHECK (status IN ('Pending', 'Active', 'Completed', 'Rejected'))");

        // Warning constraints
        DB::statement("ALTER TABLE warnings ADD CONSTRAINT check_warning_type 
            CHECK (warning_type IN ('Verbal', 'Written', 'Final', 'Suspension', 'Termination'))");
        
        DB::statement("ALTER TABLE warnings ADD CONSTRAINT check_warning_status 
            CHECK (status IN ('Active', 'Resolved', 'Expired'))");
        
        DB::statement("ALTER TABLE warnings ADD CONSTRAINT check_severity_level 
            CHECK (severity_level IN ('Low', 'Medium', 'High', 'Critical'))");

        // Medical records constraints
        DB::statement("ALTER TABLE medical_records ADD CONSTRAINT check_medical_type 
            CHECK (medical_type IN ('Fitness', 'Blood Test', 'Chest X-Ray', 'Eye Test', 'Dental', 'Other'))");
        
        DB::statement("ALTER TABLE medical_records ADD CONSTRAINT check_medical_status 
            CHECK (status IN ('Active', 'Expired', 'Pending'))");
    }

    /**
     * Remove check constraints
     */
    private function removeCheckConstraints(): void
    {
        $constraints = [
            'employees' => ['check_employment_status', 'check_salary_currency', 'check_level'],
            'leaves' => ['check_leave_type', 'check_leave_status'],
            'attendance' => ['check_attendance_status', 'check_check_in_status', 'check_check_out_status'],
            'loans' => ['check_loan_type', 'check_loan_status'],
            'warnings' => ['check_warning_type', 'check_warning_status', 'check_severity_level'],
            'medical_records' => ['check_medical_type', 'check_medical_status'],
        ];

        foreach ($constraints as $table => $tableConstraints) {
            foreach ($tableConstraints as $constraint) {
                try {
                    DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$constraint}");
                } catch (Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }
    }
}; 