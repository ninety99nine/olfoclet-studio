<?php

namespace App\Casts;

use stdClass;
use App\Traits\Base\BaseTrait;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Money implements CastsAttributes
{
    use BaseTrait;

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

            return $this->convertToMoneyFormat($value);

        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string|null
    {
        return $model->getRawOriginal($key);
    }
}
