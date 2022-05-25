/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require('./bootstrap');

 window.Vue = require('vue').default;

import Vue from 'vue';
import VueSweetalert2 from 'vue-sweetalert2';
import DatePicker from 'vue2-datepicker';
import Vuetify from 'vuetify'

import 'sweetalert2/dist/sweetalert2.min.css';
import 'vue2-datepicker/index.css';
import 'material-icons/iconfont/material-icons.css';
import 'vue2-datepicker/locale/es/es';

Vue.use(Vuetify);
Vue.use(VueSweetalert2);
Vue.use(DatePicker);
 
 /**
  * The following block of code may be used to automatically register your
  * Vue components. It will recursively scan this directory for the Vue
  * components and automatically register them with their "basename".
  *
  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
  */
 
 // const files = require.context('./', true, /\.vue$/i)
 // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
 
 Vue.component('example-component', require('./components/ExampleComponent.vue').default);
 Vue.component('horarios', require('./components/Horarios.vue').default);
 Vue.component('pacientes', require('./components/Pacientes.vue').default);
 Vue.component('dentistas', require('./components/Dentistas.vue').default);
 Vue.component('servicios', require('./components/Servicios.vue').default);
 Vue.component('cita-form', require('./components/citas/CitasForm.vue').default);
 Vue.component('cita-list', require('./components/citas/CitasList.vue').default);
 Vue.component('reservas-user', require('./components/ReservasUser.vue').default);
 
 
 /**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */
 
 const app = new Vue({
     el: '#app',
     vuetify: new Vuetify()
 });
 