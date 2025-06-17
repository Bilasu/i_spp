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
        Schema::create('quizresults', function (Blueprint $table) {
            $table->id();
            $table->string('user_ic', 12);
            $table->foreign('user_ic')->references('ic')->on('users')->onDelete('cascade');
            $table->foreignId('quiz_category_id')->constrained()->onDelete('cascade');
            $table->integer('correct');
            $table->integer('wrong');
            $table->integer('total');
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizresults');
    }
};
