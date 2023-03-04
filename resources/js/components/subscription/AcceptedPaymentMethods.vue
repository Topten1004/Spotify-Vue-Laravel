<template>
    <v-card class="mt-2">
        <div class="header py-2 px-3">
            {{$t('Accepted Payment Methods')}}
        </div>
        <div class="content align-center py-3 px-3">
            <template v-if="$store.getters.getSettings.paypalGateway">
                <v-img
                    max-width="60"
                    src="/storage/defaults/images/billing/paypal-logo.png"
                ></v-img>
            </template>
            <template v-if="$store.getters.getSettings.stripeGateway">
                <div class="credit-card">
                    <v-img
                        src="/storage/defaults/images/billing/visa-blue.png"
                    ></v-img>
                </div>
                <div class="credit-card">
                    <v-img
                        src="/storage/defaults/images/billing/mastercard.png"
                    ></v-img>
                </div>
                <div class="credit-card">
                    <v-img
                        src="/storage/defaults/images/billing/jcb.png"
                    ></v-img>
                </div>
                <div class="credit-card">
                    <v-img
                        src="/storage/defaults/images/billing/american_express.png"
                    ></v-img>
                </div>
                <div class="credit-card">
                    <v-img
                        src="/storage/defaults/images/billing/discover.png"
                    ></v-img>
                </div>
                <div class="credit-card">
                    <v-img
                        src="/storage/defaults/images/billing/unionpay.png"
                    ></v-img>
                </div>
            </template>
        </div>
    </v-card>
</template>
<script>
export default {};
</script>

<style lang="scss" scoped>
.credit-card {
    width: 40px;
}
</style>
