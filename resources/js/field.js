Nova.booting((Vue, router) => {
    Vue.component('index-kakao-address', require('./components/IndexField'));
    Vue.component('detail-kakao-address', require('./components/DetailField'));
    Vue.component('form-kakao-address', require('./components/FormField'));
})
