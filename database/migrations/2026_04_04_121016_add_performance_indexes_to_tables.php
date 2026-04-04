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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'type', 'transaction_date'], 'tx_summary_index');
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->index('is_settled');
            $table->index('direction');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->index(['user_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('tx_summary_index');
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->dropIndex(['is_settled']);
            $table->dropIndex(['direction']);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'start_date']);
        });
    }
};
