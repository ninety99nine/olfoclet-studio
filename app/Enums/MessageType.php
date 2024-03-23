<?php

namespace App\Enums;

enum MessageType:string {
    case Content = 'Content';
    case PaymentConfirmation = 'Payment Confirmation';
    case AutoBillingReminder = 'Auto Billing Reminder';
}
