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
        $this->publishes([
            __DIR__ . '/config/nova-kakao-address.php' => config_path('nova-kakao-address.php'),
        ], 'nova-kakao-address');

        $this->mergeConfigFrom(
            __DIR__ . '/config/nova-kakao-address.php', 'nova-kakao-address'
        );

        Nova::serving(function (ServingNova $event) {
            $key = config('nova-kakao-address.kakao-api-key');
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
