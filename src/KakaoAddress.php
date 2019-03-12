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
}
