<?php

namespace Yhbyun\NovaKakaoAddress;

use Laravel\Nova\Fields\Field;

class KakaoAddress extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'kakao-address';

    /**
     * Create a new field.
     *
     * @param string      $name
     * @param string|null $attribute
     * @param mixed|null  $resolveCallback
     *
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback ?? function ($value) {
            if ($value && $this->isJson($value)) {
                $address = json_decode($value);

                $this->withMeta([
                    'lat' => $address->latitude,
                    'lng' => $address->longitude,
                    'address_1level' => $address->address_1level,
                    'address_2level' => $address->address_2level,
                    'address_3level' => $address->address_3level,
                    'address_others' => $address->address_others,
                ]);

                return $address->address;
            }

            return $value;
        });
    }

    public function initLocation($latitude, $longitude)
    {
        return $this->withMeta([
            'lat' => $latitude,
            'lng' => $longitude,
        ]);
    }

    public function level($level)
    {
        return $this->withMeta([
            'level' => $level
        ]);
    }

    protected function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
