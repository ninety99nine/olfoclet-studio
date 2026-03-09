<?php

namespace App\Observers;

use App\Models\BillingTransaction;
use App\Models\Subscriber;

class BillingTransactionObserver
{
    /**
     * After a billing transaction is created or updated, refresh the subscriber's cached total_spend_amount.
     *
     * @param  \App\Models\BillingTransaction  $billingTransaction
     * @return void
     */
    public function saved(BillingTransaction $billingTransaction): void
    {
        $this->refreshSubscriberTotalSpend($billingTransaction->subscriber_id);
    }

    /**
     * After a billing transaction is deleted, refresh the subscriber's cached total_spend_amount.
     *
     * @param  \App\Models\BillingTransaction  $billingTransaction
     * @return void
     */
    public function deleted(BillingTransaction $billingTransaction): void
    {
        $this->refreshSubscriberTotalSpend($billingTransaction->subscriber_id);
    }

    /**
     * Recalculate and update total_spend_amount for the given subscriber.
     *
     * @param  int  $subscriberId
     * @return void
     */
    private function refreshSubscriberTotalSpend(int $subscriberId): void
    {
        $total = BillingTransaction::query()
            ->where('subscriber_id', $subscriberId)
            ->where('is_successful', true)
            ->sum('amount');

        Subscriber::where('id', $subscriberId)->update(['total_spend_amount' => $total]);
    }
}
