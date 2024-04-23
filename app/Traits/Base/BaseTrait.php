<?php

namespace App\Traits\Base;

use Carbon\Carbon;
use Illuminate\Support\Str;
use stdClass;

trait BaseTrait
{
    /**
     * @param string $startDate e.g 2022-04-09
     * @param string $startTime e.g 06:00
     * @return Carbon
     */
    public function combineDateAndTime($date, $time)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        /**
         *  The copy method essentially creates a new Carbon object which you can
         *  apply the changes to without affecting the original $date variable
         *
         *  Reference: https://stackoverflow.com/questions/34413877/php-carbon-class-changing-my-original-variable-value
         */
        return $date->copy()->addHours(Str::before($time, ':'))->addMinutes(Str::after($time, ':'));
    }

    public function convertToMoneyFormat($value) {

        $symbol = config('app.CURRENCY_SYMBOL');
        $money = number_format($value, 2, '.', ',');

        $obj = new stdClass();
        $obj->amount = $value;
        $obj->amount_without_currency = $money;
        $obj->amount_with_currency = $symbol . $money;

        return $obj;
    }
}
