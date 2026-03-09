<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add cached total_spend_amount to subscribers for fast sorting/filtering by "Most spend".
     * Avoids expensive correlated subqueries over 100k+ rows.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->decimal('total_spend_amount', 10, 2)->default(0)->after('metadata');
        });

        // Backfill from billing_transactions (successful only)
        DB::statement('
            UPDATE subscribers s
            SET total_spend_amount = COALESCE((
                SELECT SUM(bt.amount)
                FROM billing_transactions bt
                WHERE bt.subscriber_id = s.id AND bt.is_successful = 1
            ), 0)
        ');

        Schema::table('subscribers', function (Blueprint $table) {
            $table->index(['project_id', 'total_spend_amount']);
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'total_spend_amount']);
            $table->dropColumn('total_spend_amount');
        });
    }
};
