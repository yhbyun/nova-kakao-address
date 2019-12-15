# Nova Kakao Address Field

주소 입력시 주소에 해당하는 위도, 경도를 카카오 지도을 이용하여 입력할 수 있게
해주는 Laravel Nova Field

## Installation

```sh
$ composer require yhbyun/nova-kakao-address
```

`.env` 파일에 카카오 API 키 추가

[카카오 API 키 발급](https://developers.kakao.com/apps)

```
KAKAO_API_KEY=############################
```

## 사용방법

데이타베이스 `post` 테이블에 `address`, `latitide`, `longitude`, `address_1level`, `address_2level`, `address_3level`, `address_others`  컬럼이 있다고 가정하자.

app/post.php
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->address) {
                $data = json_decode($model->address);
                $model->address = $data->address;
                $model->address_1level = $data->address_1level;
                $model->address_2level = $data->address_2level;
                $model->address_3level = $data->address_3level;
                $model->address_others = $data->address_others;
                $model->latitude = $data->latitude;
                $model->longitude = $data->longitude;
            }

            return true;
        });

        static::updating(function ($model) {
            if ($model->address) {
                $data = json_decode($model->address);
                $model->address = $data->address;
                $model->address_1level = $data->address_1level;
                $model->address_2level = $data->address_2level;
                $model->address_3level = $data->address_3level;
                $model->address_others = $data->address_others;
                $model->latitude = $data->latitude;
                $model->longitude = $data->longitude;
            } else {
                $model->address = null;
                $model->address_1level = null;
                $model->address_2level = null;
                $model->address_3level = null;
                $model->address_others = null
                $model->latitude = null;
                $model->longitude = null;
            }

            return true;
        });
    }
...
```

app\Nova\Post.php
```php
<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Yhbyun\NovaKakaoAddress\KakaoAddress;

class Post extends Resource
{
     public static $model = 'App\Post';

     public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            KakaoAddress::make('Address')
                ->rules('required')
                ->initLocation($this->latitude, $this->longitude),
        ];
    }
...
```

## 동작원리

Laravel Nova 필드는 한개의 폼 값 만을 반환하기 때문에 다음과 같은 주소 정보를 갖는 json
객체를 반환하도록 구현했다.

```json
{
    "address": "전체주소",
    "address_1level": "시도",
    "address_2level": "구",
    "address_3level": "동",
    "address_others": "나머지 주소",
    "latitude": "위도",
    "longitude": "경도"
}
```

해당 모델의 `creating`, 'updating` 이벤트 시 address 속성에 이 json 데이터가 들어 있게 되고,
이 데이터로부터 전체주소, 시도, 구, 동, 나머지 주소, 위도, 경도 값을 구해서 해당 속성을 갱신한다.


## 화면켭쳐

![](https://github.com/yhbyun/resources/raw/master/nova-kakao-address/map2.png)