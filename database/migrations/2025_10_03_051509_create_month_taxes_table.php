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
        Schema::create('month_taxes', function (Blueprint $table) {
            $table->string('user_id', 10);
            $table->tinyInteger('month');
            $table->year('year');
            $table->decimal('salary', 15, 2);
            $table->decimal('tax', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->primary(['user_id', 'month', 'year']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('month_taxes');
    }
};
