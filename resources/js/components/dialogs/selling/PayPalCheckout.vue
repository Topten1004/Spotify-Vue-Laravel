<template>
    <v-card
        flat
        light
        v-show="!isScriptLoading"
        :class="{ 'dark-backround': $vuetify.theme.dark }"
    >
        <v-card-text>
            <div id="paypal-button" v-show="!isScriptLoading"></div>
            <page-loading
                height="20vh"
                :size="50"
                :width="5"
                v-show="isScriptLoading"
            />
        </v-card-text>
    </v-card>
</template>
<script>
import Loader from "../../../services/Loader";
import PageLoading from "../../elements/single-items/PageLoading.vue";
import Billing from "@mixins/billing/billing";
export default {
    components: { PageLoading },
    mixins: [Billing],
    props: ["cart"],
    data() {
        return {
            isScriptLoading: true
        };
    },
    mounted() {
        var self = this;
        const loader = new Loader();
        loader
            .loadAsset(
                "https://www.paypal.com/sdk/js?client-id=" +
                    this.$store.getters.getSettings.paypalClientID +
                    "&currency=" +
                    this.defaultCurrency.value,
                { id: "paypal-sdk-script" }
            )
            .then(() => {
                this.isScriptLoading = false;
                paypal
                    .Buttons({
                        style: {
                            shape: "rect",
                            color: "gold",
                            layout: "horizontal",
                            label: "paypal"
                        },
                        createOrder: function(data, actions) {
                            return actions.order.create({

                                purchase_units: [
                                    {
                                        amount: {
                                            currency_code: self.defaultCurrency.value,
                                            value: self.price(self.cart.total)
                                        }
                                    }
                                ]
                            });
                        },
                        onApprove: function(data, actions) {
                            return actions.order
                                .capture()
                                .then(function(details) {
                                    axios
                                        .post("/api/user/checkout-with-paypal")
                                        .then(() => {
                                            self.$emit("successfulPayment");
                                        });

                                    // This function shows a transaction success message to your buyer.
                                    alert(
                                        "Transaction completed by " +
                                            details.payer.name.given_name
                                    );
                                });
                        },
                        onError: function(err) {
                            self.$emit("error", err);
                        }
                    })
                    .render("#paypal-button");
            });
    }
};
</script>

<style></style>
