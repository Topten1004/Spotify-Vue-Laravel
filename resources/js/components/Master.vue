<template>
    <v-app>
        <router-view></router-view>
        <vue-confirm-dialog></vue-confirm-dialog>
        <dialog-wrapper v-if="$store.getters.getDialog"></dialog-wrapper>
        <notifications group="foo" classes="muzzie-notification" />
    </v-app>
</template>
<script>
export default {
    created() {
        if (localStorage.getItem("dark-theme") == "true") {
            this.$vuetify.theme.dark = true;
        } else {
            if (localStorage.getItem("dark-theme") == "false") {
                this.$vuetify.theme.dark = false;
            } else {
                this.$vuetify.theme.dark =
                    this.$store.getters.getSettings.defaultTheme === "Dark";
            }
        }
    },
    mounted() {
        if (
            this.$store.getters.getSettings.enableGoogleLogin &&
            this.$store.getters.getSettings.google_oauth_client_id
        ) {
            let googleSDK = document.createElement("script");

            googleSDK.setAttribute(
                "src",
                "https://accounts.google.com/gsi/client"
            );
            googleSDK.setAttribute("async", "");
            googleSDK.setAttribute("defer", "");

            document.body.appendChild(googleSDK);

            let googleLoadElement = document.createElement("div");
            googleLoadElement.setAttribute("id", "g_id_onload");
            googleLoadElement.setAttribute(
                "data-client_id",
                this.$store.getters.getSettings.google_oauth_client_id
            );
            googleLoadElement.setAttribute("data-callback", "onSignIn");

            document.body.appendChild(googleLoadElement);
        }
    },
};
</script>
