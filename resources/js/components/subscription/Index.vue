<template>
    <div class="upgrade-plan-page-container">
        <div class="text-center py-4">
            <h1>{{ $t("Subscribe") }}</h1>
        </div>
        <div class="upgrade-plan-page-container__dialog">
            <v-container>
                <v-stepper v-model="stepper_step">
                    <v-stepper-header>
                        <v-stepper-step :complete="stepper_step > 1" step="1">
                            {{$t('Choose your plan')}}
                        </v-stepper-step>
                        <v-divider></v-divider>
                        <v-stepper-step :complete="stepper_step > 2" step="2">
                            {{$t('Payment')}}
                        </v-stepper-step>
                        <v-divider></v-divider>
                        <v-stepper-step :complete="stepper_step == 3" step="3">
                            {{$t('Complete')}}
                        </v-stepper-step>
                    </v-stepper-header>
                    <v-stepper-items>
                        <v-stepper-content step="1">
                            <StepOne :isPageLoading="isPageLoading" @planChosen="proceedSubscription($event)"/>
                        </v-stepper-content>
                        <v-stepper-content step="2">
                           <StepTwo v-if="chosenPlan" :error="error" :isSubscriptionLoading="isSubscriptionLoading" :chosenPlan="chosenPlan" @isSubscriptionLoading="isSubscriptionLoading = $event" @error="error = $event" @dec-stepper="stepper_step--" @paymentStatusChanged="paymentStatus = $event" @createSubscription="createSubscription($event)"/>
                        </v-stepper-content>
                        <v-stepper-content step="3">
                            <StepThree v-if="paymentStatus === 'success'" :paymentStatus="paymentStatus" />
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>
            </v-container>
        </div>
    </div>
</template>

<script>
export default {
        metaInfo() {
        return {
            title:   this.$t('Subscribe') +  ' ' +  '|'  +  ' ' + this.$store.getters.getSettings.appName,
            meta: [
                {
                    name: "description",
                    content: this.$t('Explore & subscribe to our amazing plans')
                }
            ]
        };
    },
    components: {
        StepOne : () => import('./steps/Step_1'),
        StepTwo : () => import('./steps/Step_2'),
        StepThree: () => import('./steps/Step_3')
    },
    data() {
        return {
            isPageLoading: false,
            // reloadProgress: null,
            stepper_step: 1,
            chosenPlan: null,
            paymentStatus: null,
            isSubscriptionLoading: false,
            error: false
        };
    },
    methods: {
        proceedSubscription(plan) {
            this.chosenPlan = plan;
            if (plan.free) {
                this.isPageLoading = true
                this.createSubscription({ gateway: 'local' })
                .finally(() => this.isPageLoading = false)
            } else {
                this.stepper_step++;
            }
        },
        async createSubscription({gateway, paymentMethod, paypal_subscription_id }) {
            const requestData = {};
            requestData.plan = this.chosenPlan;
            requestData.gateway = gateway;
            if( paymentMethod ) {
                requestData.paymentMethod = paymentMethod.id
            }
            if( paypal_subscription_id ) {
                requestData.paypal_subscription_id = paypal_subscription_id
            }
            try {
                await axios.post("/api/subscribe", requestData)
                this.isSubscriptionLoading = false
                this.paymentStatus = "success";
                this.stepper_step = 3;
                this.isSubscriptionLoading = false;
                if( this.$store.getters.getSettings.ga4 &&  this.$store.getters.getSettings.analytics_logout_event ) {
                    this.emitAnalyticsEvent({
                        action: 'subcription',
                        category: 'User subscription',
                        label: this.$store.getters.getUser.email
                    })
                }
            } catch(e) {
                this.isSubscriptionLoading = false;
            }

        }
    }
};
</script>

<style lang="scss" scoped>
.upgrade-plan-page-container {
    // display: flex;
    // align-items: center;
    // justify-content: center;
    // position: relative;
    // width: 100%;
    // height: 100%;
    // overflow: hidden;

    &__dialog {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        .v-stepper__items {
            height: 65vh;
            background-image: url("/images/background.svg");
            background-size: cover;
            // background-color: rgba(117, 107, 177, 0.15);
            padding: 0.5em;
        }
    }
}
</style>
