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
        Schema::table('students', function (Blueprint $table) {
            $table->string('name_th')->nullable()->after('surname');
            $table->string('name_en')->nullable()->after('name_th');
            $table->string('email')->nullable()->after('name_en');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->decimal('gpa', 3, 2)->nullable()->after('address');
            $table->string('faculty')->nullable()->after('gpa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'name_th',
                'name_en',
                'email',
                'phone',
                'address',
                'gpa',
                'faculty'
            ]);
        });
    }
};
