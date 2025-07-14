<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Employee related enums
        $this->createEnum('employment_status_enum', [
            'Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'
        ]);
        
        $this->createEnum('salary_currency_enum', ['MVR', 'USD']);
        
        $this->createEnum('employee_level_enum', ['senior', 'junior']);
        
        $this->createEnum('company_enum', [
            'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            'NAZRASH COMPANY PVT LTD',
            '',
            ''
        ]);

        // Leave related enums
        $this->createEnum('leave_type_enum', [
            'Annual', 'Sick', 'Emergency', 'Maternity', 'Paternity', 'Unpaid', 'Other'
        ]);
        
        $this->createEnum('leave_status_enum', ['Pending', 'Approved', 'Rejected']);
        
        $this->createEnum('leave_record_status_enum', [
            'Pending', 'Approved', 'Rejected', 'Pending Leave Arrival', 'Arrived', 'Departed'
        ]);
        
        $this->createEnum('gender_restriction_enum', ['None', 'Male', 'Female']);

        // Attendance related enums
        $this->createEnum('attendance_status_enum', [
            'Present', 'Absent', 'Late', 'Early Departure', 'Half Day', 'Leave', 'Holiday', 'Weekend', 'Remote', 'Business Trip'
        ]);
        
        $this->createEnum('check_status_enum', ['On Time', 'Late', 'Early']);

        // Loan related enums
        $this->createEnum('loan_type_enum', [
            'Personal', 'Housing', 'Vehicle', 'Education', 'Emergency', 'Other'
        ]);
        
        $this->createEnum('loan_status_enum', ['Pending', 'Active', 'Completed', 'Rejected']);

        // Project related enums
        $this->createEnum('project_status_enum', ['Active', 'Completed', 'On Hold']);

        // Warning related enums
        $this->createEnum('warning_type_enum', [
            'Verbal', 'Written', 'Final', 'Suspension', 'Termination'
        ]);
        
        $this->createEnum('warning_status_enum', ['Active', 'Resolved', 'Expired']);
        
        $this->createEnum('severity_level_enum', ['Low', 'Medium', 'High', 'Critical']);

        // Medical related enums
        $this->createEnum('medical_type_enum', [
            'Fitness', 'Blood Test', 'Chest X-Ray', 'Eye Test', 'Dental', 'Other'
        ]);
        
        $this->createEnum('medical_status_enum', ['Active', 'Expired', 'Pending']);
        
        $this->createEnum('medical_examination_status_enum', [
            'Pending', 'Medical Center Visited', 'Uploaded', 'Incomplete', 'Completed'
        ]);

        // Bank and card related enums
        $this->createEnum('bank_status_enum', ['Pending', 'Scheduled', 'Completed']);
        
        $this->createEnum('print_type_enum', ['Work Permit Card', 'Access Card']);
        
        $this->createEnum('card_status_enum', ['Pending', 'Printed', 'Handed Over']);
        
        $this->createEnum('payment_status_enum', ['Not Received', 'Received']);

        // Communication related enums
        $this->createEnum('sender_receiver_type_enum', ['employee', 'hr']);
        
        $this->createEnum('role_enum', ['HRM', 'HOD', 'Management']);
        
        $this->createEnum('send_type_enum', ['automatic', 'manual']);
        
        $this->createEnum('mail_status_enum', ['sent', 'failed']);

        // Visa and passport related enums
        $this->createEnum('visa_status_enum', [
            'Pending', 'Pending Approval', 'Ready for Submission', 'Ready for Collection', 'Completed'
        ]);
        
        $this->createEnum('visa_renewal_status_enum', [
            'Expiring Soon', 'Pending', 'Pending Approval', 'Ready for Submission', 
            'Ready for Collection', 'Completed', 'Expired'
        ]);
        
        $this->createEnum('collection_status_enum', [
            'Pending', 'Collection Created', 'Paid', 'Completed'
        ]);
        
        $this->createEnum('direction_enum', ['IN', 'OUT']);
        
        $this->createEnum('passport_status_enum', [
            'Pending', 'Scheduled', 'Went to embassy', 'Applied', 'Rejected', 
            'Incomplete', 'Approved', 'Received new passport'
        ]);

        // Ticket related enums
        $this->createEnum('ticket_type_enum', [
            'Annual Leave', 'Business Travel', 'Emergency Leave', 'Other'
        ]);
        
        $this->createEnum('ticket_status_enum', [
            'Pending', 'Reservation Sent', 'Ticket Received', 'Departed', 
            'Pending Arrival', 'Arrived'
        ]);

        // Contract related enums
        $this->createEnum('contract_type_enum', [
            'Offer Letter', 'Employment Contract', 'Renewal', 'Termination', 'Other'
        ]);
        
        $this->createEnum('contract_status_enum', ['Active', 'Expired', 'Revoked']);
        
        $this->createEnum('contract_variant_enum', ['renewal', 'new']);

        // Payroll related enums
        $this->createEnum('payroll_status_enum', ['pending', 'generated', 'issued']);
        
        $this->createEnum('payroll_approval_status_enum', [
            'Pending', 'HRM Approved', 'Management Approved', 'Rejected'
        ]);

        // Employee management related enums
        $this->createEnum('allowance_type_enum', ['Fixed', 'Variable']);

        // General status enums
        $this->createEnum('general_status_enum', ['Pending', 'Resolved', 'Approved']);
        
        $this->createEnum('active_inactive_enum', ['Active', 'Inactive']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all enum types
        $enumTypes = [
            'employment_status_enum', 'salary_currency_enum', 'employee_level_enum', 'company_enum',
            'leave_type_enum', 'leave_status_enum', 'leave_record_status_enum', 'gender_restriction_enum',
            'attendance_status_enum', 'check_status_enum',
            'loan_type_enum', 'loan_status_enum',
            'project_status_enum',
            'warning_type_enum', 'warning_status_enum', 'severity_level_enum',
            'medical_type_enum', 'medical_status_enum', 'medical_examination_status_enum',
            'bank_status_enum', 'print_type_enum', 'card_status_enum', 'payment_status_enum',
            'sender_receiver_type_enum', 'role_enum', 'send_type_enum', 'mail_status_enum',
            'visa_status_enum', 'visa_renewal_status_enum', 'collection_status_enum',
            'direction_enum', 'passport_status_enum',
            'ticket_type_enum', 'ticket_status_enum',
            'contract_type_enum', 'contract_status_enum', 'contract_variant_enum',
            'payroll_status_enum', 'payroll_approval_status_enum',
            'allowance_type_enum',
            'general_status_enum', 'active_inactive_enum'
        ];

        foreach ($enumTypes as $enumType) {
            $this->dropEnum($enumType);
        }
    }

    /**
     * Create an ENUM type in PostgreSQL
     */
    private function createEnum(string $name, array $values): void
    {
        $valuesString = "'" . implode("', '", array_filter($values)) . "'";
        DB::statement("CREATE TYPE {$name} AS ENUM ({$valuesString})");
    }

    /**
     * Drop an ENUM type in PostgreSQL
     */
    private function dropEnum(string $name): void
    {
        DB::statement("DROP TYPE IF EXISTS {$name} CASCADE");
    }
}; 