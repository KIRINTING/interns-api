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
            // Status tracking
            $table->enum('status', ['pending', 'officer_approved', 'dean_approved', 'rejected'])
                ->default('pending')
                ->after('notes');

            // PDF document path
            $table->string('pdf_path')->nullable()->after('status');

            // Officer approval tracking
            $table->timestamp('officer_approved_at')->nullable()->after('pdf_path');
            $table->unsignedBigInteger('officer_approved_by')->nullable()->after('officer_approved_at');

            // Dean approval tracking
            $table->timestamp('dean_approved_at')->nullable()->after('officer_approved_by');
            $table->string('dean_signature_path')->nullable()->after('dean_approved_at');

            // Rejection reason
            $table->text('rejection_reason')->nullable()->after('dean_signature_path');

            // Foreign key (optional - if officers table exists)
            // $table->foreign('officer_approved_by')->references('id')->on('officers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'pdf_path',
                'officer_approved_at',
                'officer_approved_by',
                'dean_approved_at',
                'dean_signature_path',
                'rejection_reason'
            ]);
        });
    }
};
