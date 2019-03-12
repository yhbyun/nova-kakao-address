<template>
    <default-field :field="field" :errors="errors" :full-width-content="true">
        <template slot="field">
            <div class="flex flex-wrap w-full">
                <div class="flex w-4/5">
                    <input :id="field.name" type="text"
                        class="w-full form-control form-input form-input-bordered"
                        :class="errorClasses"
                        :placeholder="field.name"
                        v-model="value"
                        v-on:keyup.enter="searchAddress"
                    />
                </div>
                <div class="flex w-1/5 pl-2">
                    <button :disabled='isSearchDisabled' @click.prevent="searchAddress" class="w-full btn btn-default btn-primary">주소검색</button>
                </div>
            </div>
            <div class="py-3">
                <label class="text-80 pt-2" for="use-address">위의 주소로 갱신</label>
                <input id="use-address"
                    type="checkbox"
                    v-model="useAddress"
                />
            </div>
            <div class="flex flex-wrap w-full pt-4">
                <div class="flex w-1/2">
                    <div class="w-1/6">
                        <label class="block text-80 pt-2" for="latitude">위도</label>
                    </div>
                    <div class="w-5/6 -ml-4">
                        <input id="latitude" type="text"
                            class="w-full form-control form-input form-input-bordered"
                            :class="errorClasses"
                            placeholder="long"
                            v-model="addressData.latitude"
                        />
                    </div>
                </div>
                <div class="flex w-1/2">
                    <div class="w-1/6">
                        <label class="block text-80 pt-2" for="longitude">경도</label>
                    </div>
                    <div class="w-5/6 -ml-4">
                        <input id="longitude" type="text"
                            class="w-full form-control form-input form-input-bordered"
                            :class="errorClasses"
                            placeholder="long"
                            v-model="addressData.longitude"
                        />
                    </div>
                </div>
            </div>
            <div class="py-3">
                <label class="text-80 pt-2" for="use-latlng">위의 위경도로 갱신</label>
                <input id="use-latlng"
                    type="checkbox"
                    v-model="useLatLng"
                />
            </div>

            <div class="kakao-map w-full" :id="mapName"></div>
            <div class="w-full py-4"><span class="text-80">주소:</span> {{ addressData.address }}</div>

            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>
        </template>
    </default-field>
</template>

<style scoped>
    .kakao-map {
        width: 100%;
        height: 300px;
        margin: 20px auto;
        background: gray;
        border:solid 1px #ccc;
    }
</style>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data: function () {
        return {
            mapName: this.name + "-map",
            address: '',
            addressData: {latitude: this.field.lat || 33.450701, longitude: this.field.lng || 126.570667, address: ''},
            useAddress: false,
            useLatLng: false,
            map: null,
            marker: null,
            geocoder: new daum.maps.services.Geocoder,
        }
    },

    mounted: function () {
        this.initMap();
    },

    methods: {
        initMap() {
            const element = document.getElementById(this.mapName);
            const center =  new daum.maps.LatLng(this.addressData.latitude, this.addressData.longitude)

            // setup map options
            const options = {
                level: this.field.level || 3,
                center: center
            };

            // initialize the map
            this.map = new daum.maps.Map(element, options);

            // adding initial marker
            this.marker = new daum.maps.Marker({
                position: center
            });
            this.marker.setMap(this.map);

            const mapTypeControl = new daum.maps.MapTypeControl();
            this.map.addControl(mapTypeControl, daum.maps.ControlPosition.TOPRIGHT);
            const zoomControl = new daum.maps.ZoomControl();
            this.map.addControl(zoomControl, daum.maps.ControlPosition.RIGHT);

            daum.maps.event.addListener(this.map, 'click', (event) => {
                const latlng = event.latLng;

                this.marker.setPosition(latlng);

                this.addressData.latitude = latlng.getLat();
                this.addressData.longitude = latlng.getLng();

                this.geocoder.coord2Address(latlng.getLng(), latlng.getLat(), (results, status) => {
                    if (status === daum.maps.services.Status.OK && results[0]) {
                        let address = !!results[0].road_address ? `[도로명 주소] ${results[0].road_address.address_name}, ` : '';
                        address += `[지번 주소] ${results[0].address.address_name}`;
                        this.addressData.address = address;
                    }
                });
            });
        },

        refreshMap() {
            if (this.map === null) {
                return;
            }

            const latlng = new daum.maps.LatLng(this.addressData.latitude, this.addressData.longitude)
            this.map.setCenter(latlng);
            this.marker.setPosition(latlng);
        },

        searchAddress() {
            if (!!!this.value) {
                window.alert('주소를 입력하세요.');
                return;
            }

            this.geocoder.addressSearch(this.value, (results, status) => {
                if (status === daum.maps.services.Status.OK) {
                    if (results[0]) {
                        this.addressData.latitude = results[0].y;
                        this.addressData.longitude = results[0].x;
                        this.addressData.address = results[0].address_name;
                        this.useAddress = true;
                        this.useLatLng = true;

                        this.refreshMap();
                    } else {
                        window.alert('주소 검색 결과가 없습니다.');
                    }
                } else {
                    window.alert(`주소 검색이 다음 이유로 실패했습니다. ${status}`);
                }
            });
        },

        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            const data = {
                address: this.useAddress ? this.value || '' : this.field.value,
                latitude: this.useLatLng ? this.addressData.latitude || '' : this.field.lat,
                longitude: this.useLatLng ? this.addressData.longitude || '' : this.field.lng,
            };

            formData.append(this.field.attribute, JSON.stringify(data));
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value
        },
    },

    computed: {
        isSearchDisabled() {
            return !this.value;
        }
    }
}
</script>
