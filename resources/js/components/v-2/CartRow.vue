<template>
    <div>
        <div class="cart-section-item-wrapper">
            <div class="row">
                <div class="col-md-2 col-sm-2">
                    <div class="cart-sec item-image">
                        <img :src="'/product/images/' + product.product.image" height="76" width="130">
                    </div>
                </div>
                <div class="col-md-2 col-sm-2">
                    <div class="cart-sec item-title">
                        <h3 class="item-name">
                            {{ product.product.name }}
                        </h3>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2">
                    <div class="cart-sec item-price">
                        <h3 class="item-price" v-if="product.product.discount_price == null">
                            ৳ {{ product.product.regular_price }}
                        </h3>
                        <h3 class="item-price" v-else>
                            ৳ {{ product.product.discount_price }}
                        </h3>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2">
                    <div class="cart-sec item-quantity">
                        <button v-if="product.qty < product.product.qty" @click.prevent="cartIncrement(product.id)" title="Increment" class="increment-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button v-else disabled title="Increment" class="btn btn-sm btn-success">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                        <input type="number" readonly name="qty" :value="product.qty" :min="1" />
                        <button v-if="product.qty >= 1" @click.prevent="cartDecrement(product.id)" title="Decrement" class="decrement-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button v-else disabled title="Decrement" class="btn btn-sm btn-danger">
                            <i class="fas fa-eye-slash"></i>
                        </button>
<!--                        <span class="quantity">-->
<!--                            <input type="number" name="qty" v-model="qty" min="1" @click="cartIncrement(product.id)">-->
<!--                        </span>-->
                    </div>
                </div>
                <div class="col-md-2 col-sm-2">
                    <div class="cart-sec item-remove-btn">
                        <button class="remove-product" @click="removeCartProduct(product.id)">Remove</button>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2">
                    <div class="cart-sec item-total">
                        <span class="total-price">
                            ৳ {{ product.product.discount_price != null ? product.product.discount_price * product.qty : product.product.regular_price * product.qty }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="cart-responsive-item-wrapper">
            <div class="cart-responsive-product-image">
                 <img :src="'/product/images/' + product.product.image" height="70" width="100">
            </div>
            <div class="cart-responsive-product-content">
                <div class="cart-responsive-product-name">
                    <div class="item-title">
                        <h3>
                            {{ product.product.name }}
                        </h3>
                    </div>
                    <div class="item-price">
                        <h3 v-if="product.product.discount_price == null">
                            ৳ {{ product.product.regular_price }}
                        </h3>
                        <h3 class="item-price" v-else>
                            ৳ {{ product.product.discount_price }}
                        </h3>
                    </div>
                </div>
                <div class="cart-responsive-product-total">
                    <div class="item-quantity">
                        <span class="quantity">
                            <button v-if="product.qty < product.product.qty" @click.prevent="cartIncrement(product.id)" title="Increment" class="increment-btn">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button v-else disabled title="Increment" class="btn btn-sm btn-success">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                            <input type="number" readonly name="qty" :value="product.qty" :min="1" />
                            <button v-if="product.qty >= 1" @click.prevent="cartDecrement(product.id)" title="Decrement" class="decrement-btn">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button v-else disabled title="Decrement" class="btn btn-sm btn-danger">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </span>
                    </div>
                    <div class="item-remove-btn">
                        <button class="remove-product" @click="removeCartProduct(product.id)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="item-total">
                        <span class="total-price">
                            ৳ {{ product.product.discount_price != null ? product.product.discount_price * product.qty : product.product.regular_price * product.qty }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
    export default {
        props:['product'],
        data() {
            return {
                qty: this.product.qty,
            }
        },

        methods: {
            removeCartProduct(cartId){
               axios.get('/remove/cart/product/' + cartId)
               .then(response => {
                   Reload.$emit('afterAddToCart')
               }).catch(error => {
                   console.log(error)
               })
           },

           cartIncrement(id){
               axios.get('/cart-product-update/' + id)
                   .then(response => {
                   Reload.$emit('afterAddToCart')
               }).catch(error => {
                   console.log(error)
               })
           },
            cartDecrement(id){
                axios.get('/cart-product-decrement/' + id)
                    .then(response => {
                    Reload.$emit('afterAddToCart')
                }).catch(error => {
                    console.log(error)
                })
           }
        },
    }
</script>

<style scoped>
.cart-sec.item-quantity {
    display: flex;
    align-items: center;
    justify-content: center;
}
.increment-btn,.decrement-btn {
    background: transparent;
    border: 1px solid #f85506;
    font-size: 14px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    color: #f85506;
}
.cart-sec.item-quantity input[type="number"] {
    background: #fff;
    border: 1px solid #f85506 !important;
    font-size: 20px;
    font-weight: 700;
    color: #f85506;
    border-radius: 5px;
    padding: 8px 5px;
    max-width: 70px;
    height: 35px;
    margin: 0 10px;
}
.cart-sec.item-quantity button:hover {
    background: #f85506;
    color: #fff;
}
</style>