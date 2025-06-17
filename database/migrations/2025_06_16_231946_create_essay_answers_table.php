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
        Schema::create('essay_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('essay_questions_id')->constrained()->onDelete('cascade');
            $table->string('user_ic'); // Define the column first
            $table->foreign('user_ic')->references('ic')->on('users')->onDelete('cascade');
            $table->foreignId('quiz_category_id')->constrained()->onDelete('cascade');
            $table->text('answer');
            $table->integer('mark')->nullable(); // cikgu nilai manual
            $table->text('comment')->nullable(); // optional komen cikgu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essay_answers');
    }
};
