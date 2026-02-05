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
        Schema::create('daily_work_logs', function (Blueprint $table) {
            $table->id();
            $table->string('student_code');
            $table->unsignedBigInteger('intern_id')->nullable();
            $table->date('log_date');
            $table->text('work_description');
            $table->decimal('hours_worked', 5, 2);
            $table->boolean('is_weekend')->default(false);
            $table->string('photo_path')->nullable();
            $table->timestamps();

            $table->index('student_code');
            $table->index('log_date');
            $table->foreign('intern_id')->references('id')->on('interns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_work_logs');
    }
};
