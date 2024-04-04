<?php

namespace App\Casts;

use App\Traits\Base\BaseTrait;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use stdClass;

class JsonToArrayMoney implements CastsAttributes
{
    use BaseTrait;

    /**
     *  Indication of whether this can return null
     */
    private $returnType;

    public function __construct($type = 'array')
    {
        //  Set the return type
        $this->returnType = $type;
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        $output = (new JsonToArray($this->returnType))->get($model, $key, $value, $attributes);

        if(is_array($output)) {

            foreach($output as $costName => $costAmount) {

                $output[$costName] = $this->convertToMoneyFormat($costAmount);

            }

            return $output;

        }else{

            return $output;

        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return $model->getRawOriginal($key);
    }
}
