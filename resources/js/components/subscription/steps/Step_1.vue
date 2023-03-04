<template>
    <div class="step-one-container">
        <template v-if="plans && !isPageLoading">
            <div class="subscription-interval">
                <v-container
                    class="py-5"
                    v-if="
                        plans.length &&
                            plans.filter(
                                plan => plan.interval !== plans[0].interval.value
                            )[0]
                    "
                >
                    <v-row justify="center">
                        <v-btn
                            v-show="
                                plans.some(plan => plan.interval === interval.value)
                            "
                            v-for="(interval, i) in intervals"
                            :key="i"
                            @click="subscriptionInterval = interval.value"
                            :outlined="subscriptionInterval !== interval.value"
                            class="mx-2"
                            color="primary"
                            >{{ $t(interval.text) }}</v-btn
                        >
                    </v-row>
                </v-container>
            </div>
            <Plans
                class="step_1_plan"
                :plans="plansWithIntervalFilter"
                @plan="choosePlan($event)"
            />
        </template>
        <template v-else>
            <page-loading />
        </template>
    </div>
</template>

<script>
import Plans from '../Plans'
import helpers from '../../../helpers';
export default {
    components: {
        Plans
    },
    props: ["isPageLoading"],
    data() {
        return {
            subscriptionInterval: "month",
            intervals: [
                {  
                    text: this.$t("Daily"),
                    value: 'day'
                },
                {  
                    text: this.$t("Weekly"),
                    value: 'week'
                },
                {  
                    text: this.$t("Monthly"),
                    value: 'month'
                },
                {  
                    text: this.$t("Yearly"),
                    value: 'year'
                },
            ]
        };
    },
    computed: {
        plans() {
            var plans = this.$store.getters.getPlans || []; 
            if( !this.$store.getters.getSettings.enableBilling ) {
                plans = plans.filter(plan => plan.free)
            }
            return plans;
        },
        plansWithIntervalFilter() {
            return this.plans.filter(
                plan => plan.interval === this.subscriptionInterval
            );
        }
    },
    methods: {
        async choosePlan(plan) {
            if (!this.$store.getters.isLogged) {
            // if the user is not logged
                await helpers.loginOrCancel();
            }
            if (
                this.userPlan &&
                !this.userPlan.free &&
                !this.userPlan.upgradable
            ) {
                this.$confirm({
                    message: `${this.$t("You already have a subscription. Do you want to cancel the current one and switch to a new subscription?")}`,
                    button: {
                        no: this.$t('No'),
                        yes: this.$t("Yes")
                    },
                    /**
                     * Callback Function
                     * @param {Boolean} confirm
                     */
                    callback: confirm => {
                        if (confirm) {
                            this.$emit("planChosen", plan);
                        }
                    }
                });
            } else {
                this.$emit("planChosen", plan);
            }
        }
    }
};
</script>