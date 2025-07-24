/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

//support vuex
import Vuex from 'vuex'
Vue.use(Vuex)
import storeData from "./store/index"

const store = new Vuex.Store(
    storeData
)

import VueResource from 'vue-resource';
Vue.use(VueResource);

import VueLoading from 'vuejs-loading-plugin'

// using default options
Vue.use(VueLoading)

// overwrite defaults
// Vue.use(VueLoading, {
//     dark: true, // default false
//     text: 'Ladataan', // default 'Loading'
//     loading: true, // default false
//     customLoader: myVueComponent, // replaces the spinner and text with your own
//     background: 'rgb(255,255,255)', // set custom background
//     classes: ['myclass'] // array, object or string
// })


window.events = new Vue();
window.Reload = new Vue();

window.flash = function(message) {
    window.events.$emit('flash', message);
}

import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

// Vue.prototype.$userName = document.querySelector("meta[name='user_name']").getAttribute('content');
// Vue.prototype.$authUserId = document.querySelector("meta[name='user_id']").getAttribute('content');



Vue.component('search', require('./components/v-2/Search.vue').default);
Vue.component('feature-products', require('./components/v-2/FeatureProducts.vue').default);
Vue.component('hot-products', require('./components/v-2/HotProducts.vue').default);
Vue.component('discount-products', require('./components/v-2/DiscountProducts.vue').default);
Vue.component('new-arrival-products', require('./components/v-2/NewArrivalProducts.vue').default);

Vue.component('flash', require('./components/Flash.vue').default);
Vue.component('add-cart', require('./components/v-2/Cart.vue').default);
Vue.component('cart-products', require('./components/v-2/CartProducts.vue').default);
Vue.component('related-product', require('./components/v-2/RelatedProduct.vue').default);
Vue.component('combo-product', require('./components/v-2/ComboProduct.vue').default);
Vue.component('product-details', require('./components/v-2/ProductDetails.vue').default);

Vue.component('category-products-list', require('./components/v-2/CategoryProducts.vue').default);
Vue.component('shop-products', require('./components/v-2/ShopProducts.vue').default);
Vue.component('subcategory-products-list', require('./components/v-2/SubcategoryProducts.vue').default);
Vue.component('page-products-list', require('./components/v-2/PageProducts.vue').default);
Vue.component('return-product-form', require('./components/v-2/ReturnProduct.vue').default);

//Backend component
Vue.component('purchase-input-field', require('./components/v-2/Purchase.vue').default);

//Infinity scroll
Vue.component('InfiniteLoading', require('vue-infinite-loading'));

/*Supplier dashboard component here*/
Vue.component('supplier-dashboard', require('./components/supplier/Dashboard.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Vue from 'vue';
import VueSweetalert2 from 'vue-sweetalert2';

// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

Vue.use(VueSweetalert2);

const app = new Vue({
    el: '#app',
    store
});
const search = new Vue({
    el: '#search',
});
const search1 = new Vue({
    el: '#search1',
});
const cart = new Vue({
    el: '#cart',
});
