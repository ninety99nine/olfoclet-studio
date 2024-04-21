<?php

namespace App\Enums;

enum UpdateDeliveryStatusFailureType:string {
    case InternalFailure = 'Internal Failure';
    case TokenGenerationFailed = 'Token Generation Failed';
    case DeliveryStatusRequestFailed = 'Delivery Status Request Failed';
}
