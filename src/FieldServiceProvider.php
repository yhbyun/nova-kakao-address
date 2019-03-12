<?php

namespace Yhbyun\NovaKakaoAddress;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            $key = env('KAKAO_API_KEY');
            Nova::script('kakao-maps', "https://dapi.kakao.com/v2/maps/sdk.js?appkey={$key}&libraries=services");
            Nova::script('kakao-address', __DIR__ . '/../dist/js/field.js');
            Nova::style('kakao-address', __DIR__ . '/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
