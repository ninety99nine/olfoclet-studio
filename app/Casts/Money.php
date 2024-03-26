<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Money implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return stdClass|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): stdClass|null
    {
        if(is_null($value)) {

            return $value;

        }else{

            $symbol = config('app.CURRENCY_SYMBOL');
            $money = number_format($value, 2, '.', ',');

            $obj = new stdClass();
            $obj->amount = $value;
            $obj->amount_without_currency = $money;
            $obj->amount_with_currency = $symbol . $money;

            return $obj;

        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return $value instanceof stdClass ? $value->amount : $value;
    }
}
