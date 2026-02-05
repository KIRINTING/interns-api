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
        Schema::create('interns', function (Blueprint $table) {
            $table->id();
            $table->string('intern_id')->unique();

            // Student Information (fields 1-6)
            $table->string('student_code');
            $table->string('title'); // คำนำหน้าชื่อ
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('class_group'); // หมู่เรียน
            $table->string('registration_status'); // สถานะการลงทะเบียนเรียน

            // Company/Organization Information (fields 7-11)
            $table->string('company_name');
            $table->string('position'); // ตำแหน่งงานที่ให้ฝึกฯ
            $table->text('job_description'); // รายละเอียดของงาน/หน้าที่
            $table->text('company_address'); // ที่อยู่ของหน่วยงาน
            $table->string('company_phone');

            // Coordinator Information (fields 12-14)
            $table->string('coordinator_name');
            $table->string('coordinator_position');
            $table->string('coordinator_phone');

            // Approver Information (fields 15-16)
            $table->string('approver_name');
            $table->string('approver_position');

            // Location & Photo (fields 17-18)
            $table->string('google_map_coordinates')->nullable(); // พิกัด google map
            $table->string('photo_path')->nullable(); // รูปถ่ายนักศึกษากับสถานที่ฝึก

            // Additional (field 21)
            $table->text('notes')->nullable(); // หมายเหตุ

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interns');
    }
};
