<template>
  <div class="settings-container" v-if="settings">
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <v-sheet elevation="6">
           <v-tabs
              center-active
              grow
              show-arrows
            >
              <v-tab v-for="tab in tabs" :key="tab.routeName" :to="{ name: tab.routeName }">
                <v-icon class="mr-2">$vuetify.icons.{{ tab.icon }}</v-icon>
                <span>{{  $t(tab.text) }}</span>
              </v-tab>
           </v-tabs>
           </v-sheet>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <router-view
            :settings="settings"
            @save-settings="saveSettings"
            @reset-settings="resetSettings"
            :isLoading="isLoading"
            :isLoadingForButton="isLoadingForButton"
          ></router-view>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import { VSelect } from "vuetify/lib";
export default {
  components: {
    veutifySelect: VSelect,
  },
  created() {
    this.isLoading = true;
    axios
      .get("/api/admin/settings")
      .then((res) => {
        let settings = {};
        res.data.forEach((element) => {
          settings[element.key] =
            element.value == "0"
              ? false
              : element.value == "1"
              ? true
              : this.parseJSON(element.value);
        });
        this.settings = settings;
      })
      .finally(() => (this.isLoading = false));
  },
  data() {
    return {
      settings: {},
      isLoading: false,
      isLoadingForButton: false,
      tabs: [
        {
          text: 'General',
          icon: 'application-cog',
          routeName: 'settings.general'
        },
        {
          text: 'Providers',
          icon: 'api',
          routeName: 'settings.providers'
        },
        {
          text: 'Player',
          icon: 'motion-play',
          routeName: 'settings.player'
        },
        {
          text: 'Billing',
          icon: 'credit-card-outline',
          routeName: 'settings.billing'
        },
        {
          text: 'Analytics',
          icon: 'google-analytics',
          routeName: 'settings.analytics'
        },
        {
          text: 'Storage',
          icon: 'database',
          routeName: 'settings.storage'
        },
        {
          text: 'Auth',
          icon: 'account-key-outline',
          routeName: 'settings.auth'
        },
        {
          text: 'Email',
          icon: 'email-multiple',
          routeName: 'settings.mail'
        },
        {
          text: 'SEO',
          icon: 'text-box-search-outline',
          routeName: 'settings.seo'
        },
      ]
    };
  },
  methods: {
    saveSettings() {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna change to the current settings?")}`,
        button: {
          no: this.$t("Cancel"),
          yes: this.$t('Yes'),
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            if (
              this.settings.enableRealtime &&
              (!this.settings.pusherKey ||
                !this.settings.pusherCluster ||
                !this.settings.pusherSecret ||
                !this.settings.pusherAppId)
            ) {
              this.$notify({
                group: "foo",
                type: "error",
                title: this.$t("Oops!"),
                text:
                  "You need to add your Pusher credentials if you enable chatting.",
              });
              return;
            }
            if (
              this.settings.enableGoogleLogin &&
              !this.settings.google_oauth_client_id
            ) {
              this.$notify({
                group: "foo",
                type: "error",
                title: this.$t("Oops!"),
                text:
                  "You need to enter your Google OAuth client ID",
              });
              return;
            }
            // upcoming feature
            // if (
            //   this.settings.enableFacebookLogin &&
            //   !this.settings.facebook_oauth_client_id
            // ) {
            //   this.$notify({
            //     group: "foo",
            //     type: "error",
            //     title: this.$t("Oops!"),
            //     text:
            //       "You need to enter you facebook oauth client id if you want to enable facebook login",
            //   });
            //   return;
            // }
            this.isLoadingForButton = true;
            axios
              .post("/api/admin/save-settings", { settings: this.settings })
              .then(() => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Updated"),
                  text: this.$t('Settings') + ' ' + this.$t('updated successfully.'),
                });
                setTimeout(() => {
                  location.reload();
                }, 600);
              })
              .catch()
              .finally(() => (this.isLoadingForButton = false));
          }
        },
      });
    },
    resetSettings() {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna reset to the default settings?")}`,
        button: {
          no: this.$t("Cancel"),
          yes: this.$t("Reset"),
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            this.isLoadingForButton = true;
            axios
              .post("/api/admin/reset-settings")
              .then((res) => {
                let settings = {};
                res.data.forEach((element) => {
                  settings[element.key] =
                    element.value == "0"
                      ? false
                      : element.value == "1"
                      ? true
                      : element.value;
                });
                this.settings = settings;
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Reset"),
                  text: this.$t('Settings reset successfully.'),
                });
              })
              .catch()
              .finally(() => (this.isLoadingForButton = false));
          }
        },
      });
    },
  },
};
</script>
