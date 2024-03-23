<?php

namespace App\Enums;

enum BillingTransactionFailureType:string {
    case InactiveAccount = 'Inactive Account';
    case InsufficientFunds = 'Insufficient Funds';
    case TokenGenerationFailed = 'Token Generation Failed';
    case ProductInventoryRetrievalFailed = 'Product Inventory Retrieval Failed';
    case UsageConsumptionRetrievalFailed = 'Usage Consumption Retrieval Failed';
}
