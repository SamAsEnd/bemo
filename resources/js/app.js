window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Vue = require('vue').default;

Vue.component('columns', require('./components/ColumnsComponent.vue').default);

let VModal = require('vue-js-modal').default;
Vue.use(VModal);

const app = new Vue({
    el: '#app',
});
