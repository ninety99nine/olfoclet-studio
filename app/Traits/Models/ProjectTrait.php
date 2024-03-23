<?php

namespace App\Traits\Models;

trait ProjectTrait
{
    public function hasSmsCredentials()
    {
        return !empty($this->settings['sms_sender_name']) &&
               !empty($this->settings['sms_sender_number']) &&
               !empty($this->settings['sms_client_credentials']);
    }

    public function hasBillingCredentials()
    {
        return !empty($this->settings['auto_billing_client_id']) &&
               !empty($this->settings['auto_billing_client_secret']);
    }
}
