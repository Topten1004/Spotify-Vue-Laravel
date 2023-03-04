import Vue from "vue";
import { i18n } from '../../i18n-setup.js'
const state = {
    purchaseDialog: false,
    sellingAsset: null,
    checkoutDialog: false,
    purchases: [],
    cart: []
};
const getters = {
    getCart(state) {
        return state.cart;
    },
    getCheckoutDialog(state) {
        return state.checkoutDialog;
    },
    getPurchaseDialog(state) {
        return state.purchaseDialog;
    },
    getSellingAsset(state) {
        return state.sellingAsset;
    },
    getPurchases(state) {
        return state.purchases;
    } 
};


const mutations = {
    setCart(state, cart) {
        state.cart = cart
    },
    setPurchaseDialog(state, purchaseDialog) {
        state.purchaseDialog = purchaseDialog;
    },
    async setSellingAsset(state, sellingAsset) {
        if( sellingAsset ){
            state.purchaseDialog = true
        }
        state.sellingAsset = sellingAsset;
    },
    setCheckoutDialog(state, checkoutDialog) {
        state.checkoutDialog = checkoutDialog;
    },
    setPurchases(state, purchases) {
        state.purchases = purchases;
    }
};

const actions = {

    updateCart(context, { product, price }) {
        axios.post('/api/user/update-cart', {
            product, price
        })
        .then((res) => {
            state.purchaseDialog = false
            state.sellingAsset = null;
            Vue.notify({
                type: 'success',
                title: "Added",
                group: "foo",
                text: i18n.t("Asset added to card successfully.")
            });
            context.commit('setCart', res.data)
        })
        .catch((e) => {
            Vue.notify({
                type: 'warning',
                title: "Oops",
                group: "foo",
                text: e.response.data
            });
        })
    },
    removeFromCart(context, { id }) {
        axios.post('/api/user/remove-from-cart', {
            id
        })
        .then((res) => {
            context.commit('setCart', res.data)
        })
    },
    emptyCart(context) {
        context.commit('setCart', {
            items: [],
            total: 0
        })
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
