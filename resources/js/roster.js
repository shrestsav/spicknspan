require('./bootstrap');

window.Vue = require('vue');

import index from './components/roster/index.vue';

const app = new Vue({
    el: '#roster',
    components:{index},
});
