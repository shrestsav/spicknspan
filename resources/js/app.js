require('./bootstrap');

window.Vue = require('vue');

import Vue from 'vue'
import VueRouter from 'vue-router'


import reportlist from './components/reports/list.vue';
import wagesfilter from './components/reports/wages/filter.vue';
import attendancereport from './components/reports/attendance/render.vue';

Vue.use(VueRouter)

const routes = [
  { path: '/wages', component: wagesfilter },
  { path: '/attendance', component: attendancereport }
]

const router = new VueRouter({
  routes // short for `routes: routes`
})

const app = new Vue({
    el: '#app',
    router,
    components:{reportlist},
});
