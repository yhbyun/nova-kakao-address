<template>
    <div class="flex border-b border-40">
        <div class="w-1/4 py-4">
            <h4 class="font-normal text-80">{{ field.name }}</h4>
        </div>
        <div class="w-3/4 py-4">
            <p class="text-90">{{ field.value }}
                <div v-if="field.lat">
                    <div class="kakao-map w-full" :id="mapName"></div>
                    <div class="w-full py-4"><span class="text-80">위도:{{ field.lat }}, 경도:{{ field.lng }}:</span></div>
                </div>
            </p>
        </div>
    </div>
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
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    data: function () {
        const id = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });

        return {
            mapName: id + "-map",
        }
    },

    mounted: function () {
        if (!!this.field.lat && !!this.field.lng) {
            this.initMap();
        }
    },

    methods: {
        initMap() {
            const element = document.getElementById(this.mapName);
            const center =  new daum.maps.LatLng(this.field.lat, this.field.lng)

            // setup map options
            const options = {
                level: this.field.level || 3,
                center: center
            };

            // initialize the map
            const map = new daum.maps.Map(element, options);

            // adding initial marker
            const marker = new daum.maps.Marker({
                position: center
            });
            marker.setMap(map);

            const mapTypeControl = new daum.maps.MapTypeControl();
            map.addControl(mapTypeControl, daum.maps.ControlPosition.TOPRIGHT);
            const zoomControl = new daum.maps.ZoomControl();
            map.addControl(zoomControl, daum.maps.ControlPosition.RIGHT);
        },
    },
}
</script>
