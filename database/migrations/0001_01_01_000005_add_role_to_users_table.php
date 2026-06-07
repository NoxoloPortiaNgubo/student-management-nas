<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'instructor', 'student'])
                  ->default('student')
                  ->after('email');
            $table->string('student_number', 20)->nullable()->unique()->after('role');
            $table->string('phone', 20)->nullable()->after('student_number');
            $table->date('date_of_birth')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'student_number', 'phone', 'date_of_birth']);
        });
    }
};