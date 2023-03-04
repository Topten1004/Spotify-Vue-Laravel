<template>
    <v-container class="payment-container">
        <v-row>
            <v-col cols="12" sm="5" md="5" lg="3">
                <Plan
                    :plan="chosenPlan"
                    :chosen="true"
                    class="px-2 plan__chosen"
                    :isSubscriptionLoading="isSubscriptionLoading"
                    @callToAction="$emit('dec-stepper')"
                />
            </v-col>
            <v-col>
                <v-card class="payment-gateways">
                    <v-tabs v-model="paymentGateway" center-active grow>
                        <v-tab
                            :disabled="
                                !$store.getters.getSettings.stripeGateway
                            "
                        >
                            {{$t('Credit Card')}}
                        </v-tab>
                        <v-tab
                            :disabled="
                                !$store.getters.getSettings.paypalGateway
                            "
                        >
                            {{$t('PayPal')}}
                        </v-tab>
                    </v-tabs>
                    <v-tabs-items v-model="paymentGateway">
                        <v-tab-item>
                            <CardCheckout
                                :chosenPlan="chosenPlan"
                                :isSubscriptionLoading="isSubscriptionLoading"
                                :error="error"
                                @error="$emit('error', $event)"
                                @createSubscription="
                                    $emit('createSubscription', $event)
                                "
                                @isSubscriptionLoading="
                                    $emit('isSubscriptionLoading', $event)
                                "
                            />
                        </v-tab-item>
                        <v-tab-item>
                            <PayPalCheckout
                                :chosenPlan="chosenPlan"
                                :error="error"
                                @error="$emit('error', $event)"
                                @createSubscription="
                                    $emit('createSubscription', $event)
                                "
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-card>
                <v-alert
                    v-if="error"
                    color="red"
                    class="py-2"
                    dense
                    type="error"
                >
                    {{ error }}</v-alert
                >
                <AcceptedPaymentMethods />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    components: {
        CardCheckout: () => import("../CardCheckoutSubscription.vue"),
        PayPalCheckout: () => import("../PayPalCheckout"),
        AcceptedPaymentMethods: () => import("../AcceptedPaymentMethods"),
        Plan: () => import("../Plan")
    },
    props: ["chosenPlan", "error", "isSubscriptionLoading"],
    data() {
        return {
            paymentGateway: null
        };
    }
};
</script>

<style>
.payment-container {
    overflow-y: hidden;
}
</style>
