<?php

namespace App\Traits\Models;

use Carbon\Carbon;

trait BillingReportTrait
{
    public static function getNameFromDate(Carbon $date)
    {
        return $date->format('M Y');
    }
}
