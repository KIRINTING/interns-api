<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('interns', function (Blueprint $table) {
            // Training evidence fields
            $table->decimal('total_training_hours', 8, 2)->nullable()->after('rejection_reason');
            $table->integer('absence_days')->default(0)->after('total_training_hours');
            $table->integer('leave_days')->default(0)->after('absence_days');
            $table->decimal('calculated_hours', 8, 2)->nullable()->after('leave_days');

            // Training status: pending, passed, failed
            $table->enum('training_status', ['pending', 'passed', 'failed'])
                ->default('pending')
                ->after('calculated_hours');

            // Training period
            $table->date('start_date')->nullable()->after('training_status');
            $table->date('end_date')->nullable()->after('start_date');

            // Evidence submission timestamp
            $table->timestamp('evidence_submitted_at')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropColumn([
                'total_training_hours',
                'absence_days',
                'leave_days',
                'calculated_hours',
                'training_status',
                'start_date',
                'end_date',
                'evidence_submitted_at'
            ]);
        });
    }
};
