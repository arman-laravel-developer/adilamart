<template>
    <div>
<!--        <div class="shopping-cart">-->
<!--                <div class="cart-hide-icon cart-show-hide-btn">-->
<!--                    <button class="shopping-cart-close-icon">-->
<!--                        <i class="fas fa-chevron-right"></i>-->
<!--                    </button>-->
<!--                </div>-->
<!--                <div class="shopping-cart-items-count">-->
<!--                    <div class="left">-->
<!--                        <p class="shopping-cart-item-cont">-->
<!--                            <span>-->
<!--                                <i class="fas fa-shopping-bag"></i>-->
<!--                            </span>-->
<!--                            <span class="shopping-item-number">-->
<!--                                {{ this.totalQty }}-->
<!--                            </span>-->
<!--                            <span>items</span>-->
<!--                        </p>-->
<!--                    </div>-->
<!--                    <div class="right cart-show-hide-btn">-->
<!--                        <button class="shopping-cart-close-btn">-->
<!--                            close-->
<!--                        </button>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="shopping-delivery">-->
<!--                    <span>-->
<!--                        <i class="fas fa-truck"></i>-->
<!--                        Express Delivery-->
<!--                    </span>-->
<!--                </div>-->
<!--                <div class="shopping-order-items-wrapper">-->
<!--                    <div class="shopping-order-item-outer" v-for="(cart,index) in getAllCartProducts" :key="cart.id">-->
<!--                        <div class="order-quantity">-->
<!--                            <div>-->
<!--                                <i class="fas fa-chevron-up"></i>-->
<!--                            </div>-->
<!--                            <span>{{ index+1 }}</span>-->
<!--                            <div>-->
<!--                                <i class="fas fa-chevron-down"></i>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="shopping-order-item-image">-->
<!--                            <img :src="'/product/images/' + cart.product.image" height="40" width="40">-->
<!--                        </div>-->
<!--                        <div class="shopping-order-item-name">-->
<!--                            <h3 class="name">-->
<!--                                {{ cart.product.name }}-->
<!--                            </h3>-->
<!--                            <span class="sub-text">-->
<!--                                {{ cart.qty }} x {{ cart.price }}-->
<!--                            </span>-->
<!--                            =-->
<!--                            <span class="sub-text">-->
<!--                                ৳ {{ cart.qty * cart.price }}-->
<!--                            </span>-->
<!--                        </div>-->
<!--                        <div class="shopping-order-item-amount">-->
<!--                            <button class="shopping-order-item-delete-btn" @click="removeCartProduct(cart.id)">-->
<!--                                <i class="fas fa-times"></i>-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="shopping-cart-order-check">-->
<!--                    &lt;!&ndash; <a href="#" class="shopping-order-price">-->
<!--                        ৳ {{ this.totalPrice }}-->
<!--                    </a> &ndash;&gt;-->
<!--                    <a :href="'/checkout'" class="shopping-order-check">-->
<!--                        Checkout-->
<!--                    </a>-->
<!--&lt;!&ndash;                    <a :href="'/customer/login-form'" class="shopping-order-check" v-else>&ndash;&gt;-->
<!--&lt;!&ndash;                        Checkout&ndash;&gt;-->
<!--&lt;!&ndash;                    </a>&ndash;&gt;-->
<!--                    <a :href="'/checkout'" class="shopping-order-view-cart">-->
<!--                        View Cart-->
<!--                    </a>-->
<!--&lt;!&ndash;                    <a :href="'/customer/login-form'" class="shopping-order-view-cart" v-else>&ndash;&gt;-->
<!--&lt;!&ndash;                        View Cart&ndash;&gt;-->
<!--&lt;!&ndash;                    </a>&ndash;&gt;-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="add-cart-outer cart-show-hide-btn">-->
<!--                <div class="cart-item-count">-->
<!--                    <i class="fas fa-shopping-bag"></i>-->
<!--                    <p class="items">-->
<!--                        <span class="number">{{ this.totalQty }}</span>-->
<!--                        <span>ITEMS</span>-->
<!--                    </p>-->
<!--                </div>-->
<!--                &lt;!&ndash; <div class="items-total-price">-->
<!--                    <span>৳</span>-->
<!--                    <h3 class="price">-->
<!--                        {{ this.totalPrice }}-->
<!--                    </h3>-->
<!--                </div> &ndash;&gt;-->
<!--            </div>-->

        <div class="header-top-right-item dropdown">
            <div class="header-top-right-item-link">
                <span class="icon-outer">
                    <i class="far fa-heart"></i>
                    <span class="count-number">{{ this.totalQty }}</span>
                </span>
                Cart
            </div>
            <div class="cart-items-wrapper">
                <div class="cart-items-outer">
                    <div v-for="(cart,index) in getAllCartProducts" :key="cart.id">
                        <a href="#" class="cart-item-outer">
                            <div class="cart-product-image">
                                <img :src="'/product/images/' + cart.product.image" alt="product">
                            </div>
                            <div class="cart-product-name-price">
                                <h4 class="product-name">
                                    {{ cart.product.name }}
                                </h4>
                                <span class="product-price">
									৳ {{ cart.price }}
								</span>
                            </div>
                            <div class="cart-item-delete">
                                <button type="button" class="delete-btn" @click="removeCartProduct(cart.id)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="shopping-cart-footer">
                    <div class="shopping-cart-total">
                        <h4>
                            Total <span>৳ {{ this.totalPrice }}</span>
                        </h4>
                    </div>
                    <div class="shopping-cart-button">
                        <a :href="'/user/cart/products'" class="view-cart-link">View cart</a>
                        <a :href="'/checkout'" class="checkout-link">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
   export default {
       data(){
           return {
               getAllCartProducts:[],
               totalQty: '',
               totalPrice: '',
           }
       },

       mounted(){
           this.cartProducts();
           this.totalCartProducts();
           this.totalCartProductsPrice();
           Reload.$on('afterAddToCart', () => {
               this.cartProducts();
               this.totalCartProducts();
               this.totalCartProductsPrice();
           })
       },

       created(){

       },

       methods:{
           cartProducts(){
               axios.get('/cart/products')
                .then(response => {
                    console.log(response)
                    this.getAllCartProducts = response.data;
                    console.log(response)
                }).catch(error => {
                    console.log(error)
                })
           },
           totalCartProducts(){
               axios.get('/cart/products/count')
                .then(response => {
                    this.totalQty = response.data
                }).catch(error => {
                    console.log(error)
                })
           },
           totalCartProductsPrice(){
               axios.get('/cart/products/price')
                .then(response => {
                    this.totalPrice = response.data
                }).catch(error => {
                    console.log(error)
                })
           },

           removeCartProduct(cartId){
               axios.get('/remove/cart/product/' + cartId)
               .then(response => {
                   Reload.$emit('afterAddToCart')
                   flash('Product has been successfully remove to cart.', 'success');
               }).catch(error => {
                   console.log(error)
               })
           }
       }
   }
</script>

<style scoped>

</style>
