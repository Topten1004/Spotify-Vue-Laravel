<template>
    <v-card flat light>
        <v-card-text>
            <div id="paypal-button" v-show="!isScriptLoading"></div>
            <page-loading height="20vh" :size="50" :width="5"  v-show="isScriptLoading"/>
        </v-card-text>
    </v-card>
</template>
<script>
import Loader from '../../services/Loader';
import PageLoading from '../elements/single-items/PageLoading.vue';
export default {
  components: { PageLoading },
    props: ["chosenPlan"],
    data() {
        return {
            isScriptLoading: true
        }
    },
    mounted() {
        var self = this;
        const loader = new Loader();
        loader.loadAsset("https://www.paypal.com/sdk/js?client-id=" + this.$store.getters.getSettings.paypalClientID +"&vault=true&intent=subscription", { id: "paypal-sdk-script" })
        .then(() => {
            this.isScriptLoading = false
             paypal
            .Buttons({
                style: {
                    shape: "rect",
                    color: "gold",
                    layout: "horizontal",
                    label: "paypal"
                },
                createSubscription: function(data, actions) {
                    return actions.subscription.create({
                        plan_id: self.chosenPlan.paypal_id
                    });
                },
                onApprove: function(data, actions) {
                    self.$emit("createSubscription", {
                        gateway: "paypal",
                        paypal_subscription_id: data.subscriptionID
                    });
                }
            })
            .render("#paypal-button");
        })
       
    }
};
</script>

<style></style>
