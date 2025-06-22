<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjective_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('mark_total')->default(0);
            $table->text('question');
            $table->foreignId('quiz_category_id')->constrained()->onDelete('cascade');
            $table->string('created_by'); // Use the correct datatype (string if IC is string)
            $table->foreign('created_by')->references('ic')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjective_questions');
    }
};
