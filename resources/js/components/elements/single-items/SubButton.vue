<template>
    <div v-if="$store.getters.getPlans && $store.getters.getPlans.length > 0">
        <v-btn
            dark
            small
            :color="
                $store.getters.getSettings.subscriptionButtonColor || 'primary'
            "
            :outlined="$store.getters.getSettings.subscriptionButtonOutlined"
            depressed
            v-if="isUpgradable"
            @click="$router.push({ name: 'subscription' })"
        >
            <v-icon left>
                mdi-{{ $store.getters.getSettings.subscriptionButtonIcon }}
            </v-icon>
            {{ $t($store.getters.getSettings.subscriptionButtonText) }}
        </v-btn>
        <v-btn
            dark
            color="primary"
            outlined
            depressed
            small
            v-else-if="userPlan"
            @click="$router.push({ name: 'subscription' })"
        >
            <v-icon left v-if="userPlan.badge">
                mdi-{{ userPlan.badge }}
            </v-icon>
            {{ userPlan.name }}
        </v-btn>
    </div>
</template>

<script>
export default {
    computed: {
        userPlan() {
            return this.$store.getters.getUser.plan
        }
    }
};
</script>
