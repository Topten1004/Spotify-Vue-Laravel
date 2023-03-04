<template>
    <v-app>
        <div class="installer-container">
            <div class="center-element">
                <welcome-card v-if="!installationBegan" @startInstall="installationBegan = true"></welcome-card>
                <installation-steps v-else></installation-steps>
            </div>
        </div>
    </v-app>
</template>

<script>
import InstallationSteps from "./InstallationSteps.vue";
import WelcomeCard from "./WelcomeCard.vue";
export default {
    components: {
        InstallationSteps,
        WelcomeCard
    },
    data() {
        return {
            installationBegan: false
        };
    }
};
</script>
