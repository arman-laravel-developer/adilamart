import axios from "axios";

export default {
    state: {
        category: [],
        subcategory: [],
        product: [],
    },

    getters: {
        getCategoryFormGetters(state){
            return state.category
        },

        getSubcategoryFormGetters(state){
            return state.subcategory
        },
        getProductFormGetters(state){
            return state.product
        },
    },

    actions: {
        getAllCategoryFromDatabase(context){
            axios.get('/api/get/categories')
                .then(response => {
                    context.commit('categories', response.data.categories);
                }).catch(error => {
                console.log(error)
            })
        },
        getAllSubcategoryFromDatabase(context){
            axios.get('/api/get/subcategories')
                .then(response => {
                    context.commit('subcategories', response.data.subcategories)
                }).catch(error => {
                console.log(error)
            })
        },

        shopAllProductsFromDatabase(context){
            axios.get('/api/shop-products')
                .then(response => {
                    context.commit('products', response.data)
                }).catch(error => {
                console.log(error)
            })
        },
    },

    mutations: {
        categories(state, data){
            return state.category = data;
        },
        subcategories(state, data){
            return state.subcategory = data;
        },
        products(state, data){
            return state.product = data;
        },
    }
}
