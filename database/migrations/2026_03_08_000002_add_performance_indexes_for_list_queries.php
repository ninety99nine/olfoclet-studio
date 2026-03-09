<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes to support fast filtering, sorting, date ranges, and search
     * for subscribers, subscriptions, billing_transactions, billing_reports,
     * subscriber_messages, sms_campaign_schedules, and auto_billing_schedules.
     *
     * @return void
     */
    public function up(): void
    {
        // Subscribers: date filters, default sort (created_at), list by project
        Schema::table('subscribers', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['project_id', 'created_at']);
        });

        // Subscriptions: date filters, sort (created_at, start_at, end_at), active/inactive scope
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('start_at');
            $table->index('end_at');
            $table->index('cancelled_at');
            $table->index(['project_id', 'created_at']);
            $table->index(['project_id', 'end_at', 'cancelled_at']);
        });

        // Billing transactions: date filters, status, sort, analytics (is_successful, created_using_auto_billing)
        // subscription_id already indexed via FK
        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('is_successful');
            $table->index('created_using_auto_billing');
            $table->index(['project_id', 'created_at']);
            $table->index(['project_id', 'is_successful']);
        });

        // Billing reports: search (name), date filters, sort (name, month, year, gross_revenue, total_transactions)
        Schema::table('billing_reports', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('name');
            $table->index(['project_id', 'created_at']);
            $table->index(['project_id', 'year', 'month']);
        });

        // Subscriber messages: date filters, status (is_successful), type, sort
        Schema::table('subscriber_messages', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('is_successful');
            $table->index('type');
            $table->index(['project_id', 'created_at']);
            $table->index(['project_id', 'is_successful']);
        });

        // SMS campaign schedules: list by project, sort by created_at
        Schema::table('sms_campaign_schedules', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['project_id', 'created_at']);
        });

        // Auto billing schedules: list by project, lookup by (subscriber_id, pricing_plan_id)
        Schema::table('auto_billing_schedules', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['project_id', 'created_at']);
            $table->index(['subscriber_id', 'pricing_plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'created_at']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['start_at']);
            $table->dropIndex(['end_at']);
            $table->dropIndex(['cancelled_at']);
            $table->dropIndex(['project_id', 'created_at']);
            $table->dropIndex(['project_id', 'end_at', 'cancelled_at']);
        });

        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_successful']);
            $table->dropIndex(['created_using_auto_billing']);
            $table->dropIndex(['project_id', 'created_at']);
            $table->dropIndex(['project_id', 'is_successful']);
        });

        Schema::table('billing_reports', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['name']);
            $table->dropIndex(['project_id', 'created_at']);
            $table->dropIndex(['project_id', 'year', 'month']);
        });

        Schema::table('subscriber_messages', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_successful']);
            $table->dropIndex(['type']);
            $table->dropIndex(['project_id', 'created_at']);
            $table->dropIndex(['project_id', 'is_successful']);
        });

        Schema::table('sms_campaign_schedules', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'created_at']);
        });

        Schema::table('auto_billing_schedules', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'created_at']);
            $table->dropIndex(['subscriber_id', 'pricing_plan_id']);
        });
    }
};
