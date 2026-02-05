<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('interns', 'student_id')) {
            Schema::table('interns', function (Blueprint $table) {
                $table->string('student_id')->nullable()->after('id');
            });
        }

        // Update existing records to set student_id = student_code if student_id is null or empty
        DB::table('interns')
            ->whereNull('student_id')
            ->orWhere('student_id', '')
            ->update([
                'student_id' => DB::raw('student_code')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('interns', 'student_id')) {
            Schema::table('interns', function (Blueprint $table) {
                $table->dropColumn('student_id');
            });
        }
    }
};
