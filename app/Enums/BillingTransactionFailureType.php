<?php

namespace App\Enums;

enum BillingTransactionFailureType:string {
    case InternalFailure = 'Internal Failure';
    case InactiveAccount = 'Inactive Account';
    case DeductFeeFailed = 'Deduct Fee Failed';
    case InsufficientFunds = 'Insufficient Funds';
    case TokenGenerationFailed = 'Token Generation Failed';
    case MissingMainBalanceInformation = 'Missing Main Balance Information';
    case ProductInventoryRetrievalFailed = 'Product Inventory Retrieval Failed';
    case UsageConsumptionRetrievalFailed = 'Usage Consumption Retrieval Failed';
}