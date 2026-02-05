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
        Schema::create('internship_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('student_code');
            $table->decimal('gpa', 3, 2)->nullable();
            $table->integer('credits_completed')->default(0);
            $table->boolean('required_courses_completed')->default(false);
            $table->boolean('has_advisor_approval')->default(false);
            $table->boolean('is_eligible')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('student_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_criteria');
    }
};
