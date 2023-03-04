<template>
    <v-card flat>
        <v-card-text v-show="!isScriptLoading">
            <div id="card-element" v-show="!hasCard"></div>
            <div
                class="existing-card d-flex align-center justify-space-between"
                v-if="hasCard"
            >
                <div class="existing-card__details d-flex align-center">
                    <div class="credit-card mr-2">
                        <v-img
                            v-if="$store.getters.getUser.card_brand == 'visa'"
                            src="/storage/defaults/images/billing/visa-blue.png"
                        ></v-img>
                        <v-img
                            v-if="
                                $store.getters.getUser.card_brand ==
                                    'mastercard'
                            "
                            src="/storage/defaults/images/billing/mastercard.png"
                        ></v-img>
                        <v-img
                            v-if="$store.getters.getUser.card_brand == 'jcb'"
                            src="/storage/defaults/images/billing/jcb.png"
                        ></v-img>
                        <v-img
                            v-if="$store.getters.getUser.card_brand == 'amex'"
                            src="/storage/defaults/images/billing/american_express.png"
                        ></v-img>
                        <v-img
                            v-if="
                                $store.getters.getUser.card_brand == 'discover'
                            "
                            src="/storage/defaults/images/billing/discover.png"
                        ></v-img>
                        <v-img
                            v-if="
                                $store.getters.getUser.card_brand == 'unionpay'
                            "
                            src="/storage/defaults/images/billing/unionpay.png"
                        ></v-img>
                    </div>
                    <div class="existing-card__digits">
                        <strong>****</strong>
                        <strong>{{
                            $store.getters.getUser.card_last_four
                        }}</strong>
                    </div>
                </div>
                <div class="exsiting-card__options">
                    <v-btn icon @click="newCard = true">
                        <v-icon color="error" :title="$t('Remove card')"
                            >$vuetify.icons.credit-card-refresh</v-icon
                        >
                    </v-btn>
                </div>
            </div>
            <v-btn
                class="primary mt-5"
                @click="subscribeWithStripe"
                :disabled="isSubscriptionLoading || !validCardInput"
                :loading="isSubscriptionLoading"
            >
                <template v-slot:loader>
                    <span class="custom-loader">
                        <v-icon light>$vuetify.icons.cached</v-icon>
                    </span>
                </template>

                {{ $t("Subscribe") }}
            </v-btn>
        </v-card-text>
        <page-loading
            height="20vh"
            :size="50"
            :width="5"
            v-show="isScriptLoading"
        />
    </v-card>
</template>

<script>
import { loadStripe } from "@stripe/stripe-js";
export default {
    props: ["chosenPlan", "isSubscriptionLoading"],
    async mounted() {
        this.isScriptLoading = true;
        this.stripe = await loadStripe(
            this.$store.getters.getSettings.stripeClientID
        );
        const elements = this.stripe.elements();
        this.cardElement = elements.create("card");
        this.cardElement.on(
            "change",
            function(event) {
                if (event.complete) {
                    this.validCardInput = true;
                } else if (event.error) {
                    this.validCardInput = false;
                    this.$emit("error", event.error.message);
                }
            }.bind(this)
        );
        setTimeout(() => {
            this.isScriptLoading = false;
        }, 1000);
        this.cardElement.mount("#card-element");
    },
    data() {
        return {
            newCard: false,
            cardElement: null,
            validCardInput: true,
            stripe: null,
            isScriptLoading: true
        };
    },
    computed: {
        hasCard() {
            return (
                this.user.card_brand &&
                this.user.card_last_four &&
                !this.newCard
            );
        },
        user() {
            return this.$store.getters.getUser;
        }
    },
    methods: {
        async subscribeWithStripe() {
            this.$emit("isSubscriptionLoading", true);
            this.$emit("error", false);
            try {
                var paymentMethod = null;

                if (!this.hasCard && !this.chosenPlan.free) {
                    paymentMethod = await this.createPaymentMethod();
                }
                this.$emit("createSubscription", {
                    gateway: "stripe",
                    paymentMethod
                });
            } catch (e) {
                if (!this.error) {
                    this.$emit(
                        "error",
                        this.$t("Oops! Some error occurred. Try again later.")
                    );
                    this.$emit("isSubscriptionLoading", false);
                }
            }
        },
        async createPaymentMethod() {
            const {
                paymentMethod,
                error
            } = await this.stripe.createPaymentMethod(
                "card",
                this.cardElement,
                {
                    billing_details: {}
                }
            );
            if (error) {
                if (!this.error) {
                    this.$emit(
                        "error",
                        this.$t("Oops! Some error occurred. Try again later.")
                    );
                    this.$emit("isSubscriptionLoading", false);
                }
            } else {
                return paymentMethod;
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.loading-circle-button {
    width: 30px;
    height: 16px;
}
#card-elements {
    padding: 11.4px 12px;
}
.credit-card {
    max-width: 40px;
}
.StripeElement {
    border: 1px solid rgba(43, 43, 43, 0.15);
    border-radius: 5px;
    padding: 1em;
    &--focus {
        border: 2px solid var(--color-primary);
    }
    &--invalid {
        border: 2px solid rgb(248, 74, 74);
    }
}
</style>
