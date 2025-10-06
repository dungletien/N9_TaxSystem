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
        Schema::table('month_taxes', function (Blueprint $table) {
            // Add auto-increment ID column first
            $table->id()->first();
        });

        // Then drop the old primary key and add unique constraint
        Schema::table('month_taxes', function (Blueprint $table) {
            // Drop existing primary key
            $table->dropPrimary(['user_id', 'month', 'year']);

            // Add unique constraint for the composite key
            $table->unique(['user_id', 'month', 'year'], 'month_taxes_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('month_taxes', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('month_taxes_unique');

            // Drop the id column
            $table->dropColumn('id');
        });

        // Add back the original primary key
        Schema::table('month_taxes', function (Blueprint $table) {
            $table->primary(['user_id', 'month', 'year']);
        });
    }
};
