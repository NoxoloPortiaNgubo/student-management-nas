<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->enum('grade', ['A+','A','A-','B+','B','B-','C+','C','C-','D','F'])->nullable();
            $table->decimal('mark', 5, 2)->nullable()->comment('Percentage mark 0-100');
            $table->date('enrolled_at')->useCurrent();
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'course_id']);
            $table->index('status');
            $table->index('grade');

            $table->foreign('student_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();

            $table->foreign('course_id')
                  ->references('id')->on('courses')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrolments');
    }
};