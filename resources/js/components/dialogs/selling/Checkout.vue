<template>
    <v-card class="edit-dialog page-dialog-wrapper" :class="{  'dark-backround' : $vuetify.theme.dark }">
        <div class="edit-dialog__header d-flex" v-if="!paymentSuccessful">
            <div class="edit-dialog__header__title">{{ $t("Checkout") }}</div>
            <v-card-actions class="edit-dialog__header__actions">
                <slot name="header-actions"></slot>
                <v-btn
                    text
                    small
                    @click="$store.commit('purchase/setCheckoutDialog', false)"
                >
                    {{ $t("Cancel") }}
                </v-btn>
            </v-card-actions>
        </div>
        <div class="body" v-if="!paymentSuccessful">
            <div class="items">
                <v-card-text v-if="cart.items.length > 0">
                    <v-list :class="{  'dark-backround' : $vuetify.theme.dark }">
                        <v-list-item v-for="(product, idx) in cart.items" :key="idx">
                            <v-list-item-avatar
                                width="85"
                                height="85"
                                rounded="0"
                                class="asset-shadow"
                            >
                                <img :src="product.item.cover" />
                            </v-list-item-avatar>
                            <v-list-item-content>
                                <v-list-item-title>{{
                                    product.item.title
                                }}</v-list-item-title>
                                <v-list-item-subtitle>{{
                                   getMainArtist(product.item)
                                }}</v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-action>
                                <div class="price">{{ price(product.price.amount) }} {{ product.price.currency_symbol }}</div>
                            </v-list-item-action>
                        </v-list-item>
                    </v-list>
                </v-card-text>
            </div>
            <div class="total space-between">
                <div class="title">{{ $t('Total') }}</div>
                <div class="total__price">{{ price(cart.total) }} {{ defaultCurrency.symbol }}</div>
            </div>
            <div class="checkout  p-4">
                <v-card class="payment-gateway" :class="{  'dark-backround' : $vuetify.theme.dark }"> 
                    <v-tabs v-model="paymentGateway" center-active grow :class="{  'dark-backround' : $vuetify.theme.dark }">
                        <v-tab v-if="$store.getters.getSettings.stripeGateway" :class="{  'dark-backround' : $vuetify.theme.dark }">
                            {{ $t("Credit Card") }}
                        </v-tab>
                        <v-tab v-if="$store.getters.getSettings.paypalGateway" :class="{  'dark-backround' : $vuetify.theme.dark }">
                            {{ $t("PayPal") }}
                        </v-tab>
                    </v-tabs>
                    <v-tabs-items v-model="paymentGateway" :class="{  'dark-backround' : $vuetify.theme.dark }">
                        <v-tab-item v-if="$store.getters.getSettings.stripeGateway">
                            <v-alert
                                v-if="error"
                                color="red"
                                class="py-2"
                                dense
                                type="error"
                            >
                                {{ error }}</v-alert
                            >
                            <CardCheckout
                                :isPurchaseLoading="isPurchaseLoading"
                                :cart="cart"
                                :error="error"
                                @error="error = $event"
                                @isPurchaseLoading="
                                   isPurchaseLoading =  $event
                                "
                                @successfulPayment="paymentSuccessfulHandler"
                            />
                        </v-tab-item>
                        <v-tab-item>
                            <v-alert
                                v-if="error"
                                color="red"
                                class="py-2"
                                dense
                                type="error"
                            >
                                {{ error }}</v-alert
                            >
                            <PayPalCheckout
                                :error="error"
                                :cart="cart"
                                @error="error = $event"
                                @successfulPayment="paymentSuccessfulHandler"
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-card>
                <!-- <v-card-actions >
                    <v-btn color="primary" max-width="150">
                        {{ $t('Pay') }}
                    </v-btn>
                </v-card-actions> -->
            </div>
        </div>
        <div flat class="successful-payment pt-4" v-else> 
        <div class="successful-payment__icon">
            <v-icon x-large color="success">$vuetify.icons.checkbox-marked-circle</v-icon>
        </div>
        <div class="successful-payment__text success--text">
            {{$t("Congratulation! Payment was successful.")}}
        </div>

        <div class="successful-payment__action my-5  text-center">
           <v-btn color="primary" @click="$store.commit('purchase/setCheckoutDialog', false)">
               {{ $t('Exit') }}
           </v-btn>
        </div>
        </div>
    </v-card>
</template>

<script>
import BillingMixin from '../../../mixins/billing/billing';
export default {
    components: {
        CardCheckout: () => import("./CardCheckout.vue"),
        PayPalCheckout: () => import("./PayPalCheckout.vue"),
        AcceptedPaymentMethods: () =>
            import("../../subscription/AcceptedPaymentMethods.vue")
    },
    mixins: [
        BillingMixin
    ],
    data() {
        return {
            error: "",
            isPurchaseLoading: false,
            paymentSuccessful: false,
            paymentGateway: 0,
        };
    },
    computed: {
        cart() {
            return this.$store.getters['purchase/getCart']
        }
    },
    methods: {
        paymentSuccessfulHandler() {
            this.paymentSuccessful = true
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Success"),
                text: this.$t('Payment proceeded successfully.')
            })
            if( this.$store.getters.getSettings.ga4 && this.$store.getters.getSettings.analytics_purchase_event ) {
                emitAnalyticsEvent({
                    action: 'purchase',
                    category: 'item_purchase',
                    label: this.$store.getters.getUser.email
                })
            }
            this.$store.dispatch("fetchPurchases");
            this.$store.dispatch('purchase/emptyCart');
        }
    }
};
</script>

<style lang="scss">
.total {
    display: flex;
    align-items: center;
    font-weight: bold !important;
    justify-content: space-between;
    padding: 1em 2em;
    &__price {
        font-size: 2em;
    }

}

.asset-shadow {
    box-shadow: 2px 5px 5px rgb(0 0 0 / 30%);
}

.price {
    font-size: 1.2em;
    font-weight: bold;
}
.successful-payment {
    background-image: url('/images/background.svg');
    background-size: cover;
}
</style>
