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

`Post` 테이블에 `address`, `latitide`, `longitude` 컬럼이 있다고 가정하자.

app/post.php
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Searchable;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->address) {
                $data = json_decode($model->address);
                $model->address = $data->address;
                $model->latitude = $data->latitude;
                $model->longitude = $data->longitude;
            }

            return true;
        });

        static::updating(function ($model) {
            if ($model->address) {
                $data = json_decode($model->address);
                $model->address = $data->address;
                $model->latitude = $data->latitude;
                $model->longitude = $data->longitude;
            } else {
                $model->address = null;
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
    address: "주소",
    latitude: "위도",
    longitude: "경도"
}
```

해당 모델의 `creating`, 'updating` 이벤트 시 address 속성에 이 json 값이 들어 있개 되고,
이 값에서 실제 주소, 위도, 경도 값을 구해서 해당 속성을 갱신한다.

