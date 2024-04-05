<?php

namespace App\Casts;

use Illuminate\Support\Str;
use App\Traits\Base\BaseTrait;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class LinkToUploads implements CastsAttributes
{
    use BaseTrait;

    private $prefix;

    public function __construct()
    {
        $this->prefix = URL::to('/').'/uploads/';
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
        if( is_null($value) ) {

            return null;

        }else if( Str::startsWith($value, $this->prefix) ) {

            return $value;

        }else{

            return Str::start($value, $this->prefix);

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
        if( is_null($value) ) {

            return null;

        }else if( Str::startsWith($value, $this->prefix) ) {

            return Str::replaceFirst($this->prefix, '', $value);

        }else{

            return $value;

        }
    }
}
