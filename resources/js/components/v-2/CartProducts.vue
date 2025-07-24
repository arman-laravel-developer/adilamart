<template>
    <div>
        <section class="cart-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="cart-table-wrapper">
                            <a :href="'/'" class="continue-shopping-btn">
                                <i class="fas fa-long-arrow-alt-left"></i>
                                Continue Shopping
                            </a>
                            <div class="product-cart-item-title">
                                <div class="row">
                                    <div class="col-md-2">
                                        <h4 class="title">image</h4>
                                    </div>
                                    <div class="col-md-2">
                                        <h4 class="title">product name</h4>
                                    </div>
                                    <div class="col-md-2">
                                        <h5 class="title">
                                            price
                                        </h5>
                                    </div>
                                    <div class="col-md-2">
                                        <h5 class="title">
                                            quantity
                                        </h5>
                                    </div>
                                    <div class="col-md-2">
                                        <h5 class="title">
                                            remove
                                        </h5>
                                    </div>
                                    <div class="col-md-2">
                                        <h5 class="title">
                                            total
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <cart-row v-for="product in cardProducts" :product="product" :key="product.id"></cart-row>
                            </div>
                        </div>
                        <div class="text-center">
                            <a :href="'/checkout'" class="process-checkout-btn">
                                Proceed To CheckOut
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="releted-product-section">
            <div class="container">
                <div class="section-title-outer">
                    <h1 class="title">
                        You may also like this Products
                    </h1>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-sm-6" v-for="relatedProduct in relatedProducts">
                        <div class="product-item-wrapper">
                            <div class="product-image-outer">
                                <a :href="'/product/' + relatedProduct.products.slug" class="product-imgae">
                                    <img :src="'/product/images/' + relatedProduct.products.image" class="main-image" alt="product image">
                                    <img :src="'/product/images/' + relatedProduct.products.image" class="hover-image" alt="product image">
                                </a>
                                <div class="product-action">
                                    <a class="action-btn" href="#" @click="addToCart(relatedProduct.products)">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </div>
                                <div class="product-badges hot">
                                    <span style="text-transform: capitalize">
                                        {{ relatedProduct.products.product_type }}
                                    </span>
                                </div>
                            </div>
                            <div class="product-content-outer">
                                <a :href="'/product/' + relatedProduct.products.slug" class="product-name">
                                    {{ relatedProduct.products.name }}
                                </a>
                                <div class="product-item-bottom">
                                    <div class="product-price" v-if="relatedProduct.products.discount_price == null">
                                        <span>{{ relatedProduct.products.regular_price }} Tk.</span>
                                    </div>
                                    <div class="product-price" v-else>
                                        <span class="old-price">{{ relatedProduct.products.discount_price }} Tk.</span>
                                    </div>
                                    <div class="add-cart">
                                        <button class="add-cart-btn" @click="addToCart(relatedProduct.products)">
                                            <i class="fas fa-shopping-cart"></i>
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios'
import cartRow from './CartRow.vue'
    export default {
		props:['auth_user'],
        data() {
            return {
                cardProducts: [],
                relatedProducts: [],
				totalPrice: '',
				totalQty: '',
                qty: this.auth_user.qty,
            }
        },
		components: {
			cartRow,
		},

		mounted() {
			this.authUserProducts();
			this.totalCartProductsPrice();
			this.totalCartProducts();
            this.getComboProducts();
			Reload.$on('afterAddToCart', () => {
			   this.authUserProducts();
               this.totalCartProductsPrice();
			   this.totalCartProducts();
           })
		},

		methods: {
            getComboProducts(){
                axios.get('/get/combo/products')
                    .then(response => {
                        //console.log(response.data)
                        this.relatedProducts = response.data
                    }).catch(error => {
                        console.log(error)
                })
            },
			authUserProducts(){
				axios.get('/get/customer/products/' + this.auth_user)
					.then(response => {
						this.cardProducts = response.data
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
            },

            cartIncrement(id){
                axios.post('/cart-product-update/' + id, {
                    qty: this.qty
                }).then(response => {
                    Reload.$emit('afterAddToCart')
                    flash('Product cart has been updated.', 'success');
                }).catch(error => {
                    console.log(error)
                })
            },
            addToCart(related){
                if(related.discount_price == null){
                    axios.post('/add/to/cart/' + related.id,{
                        product_id: related.id,
                        qty:1,
                        user_id:this.$authUserId,
                        price: related.regular_price,
                    }).then(response => {
                        if(response.status == 200){
                            this.$swal({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Product added to cart',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            Reload.$emit('afterAddToCart');
                            window.location.href = '/checkout'
                            //flash('Product has been successfully added to cart.', 'success');
                        }
                        //console.log(response)
                    }).catch(error => {
                        console.log(error)
                    })
                }else{
                    axios.post('/add/to/cart/' + related.id,{
                        product_id: related.id,
                        qty:1,
                        user_id:this.$authUserId,
                        price: related.discount_price,
                    }).then(response => {
                        if(response.status == 200){
                            this.$swal({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Product added to cart',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            Reload.$emit('afterAddToCart');
                            window.location.href = '/checkout'
                            //flash('Product has been successfully added to cart.', 'success');
                        }
                        //console.log(response)
                    }).catch(error => {
                        console.log(error)
                    })
                }
            },
		},
    }
</script>

<style scoped>
.product-item-content .title{
        font-size: 14px;
    }
    .product-section-item img{
        width: 100%;
        height: 170px;
    }
    .price-cart-btn-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 5px;
    }
    .product-item-image-outer {
        margin-bottom: 5px!important;
    }
    .add-to-card-btn {
        margin: 0;
        padding: 5px 10px;
        background: #fff;
    }
    .single-product-rating {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 5px;
    }
    .rating-outer ul {
        padding-left: 0;
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }
    .rating-outer ul li i {
        color: #154360;
        font-size: 14px;
    }
    .total-rating span {
        font-size: 14px;
        font-family: 'Poppins';
        font-weight: 500;
    }
</style>
