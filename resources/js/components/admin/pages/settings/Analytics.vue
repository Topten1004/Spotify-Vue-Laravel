<template>
    <v-card class="page" v-if="!isLoading">
        <v-card-title>
            <div class="page__title">
                <v-icon color="primary" x-large>$vuetify.icons.google-analytic</v-icon>
                {{ $t("Analytics") }}
            </div>
            <div class="page__options">
                <v-btn
                    small
                    class="success"
                    @click="save"
                    :disabled="isLoadingForButton"
                    :loading="isLoadingForButton"
                    >{{ $t("Save") }}
                    <template v-slot:loader>
                        <span class="custom-loader">
                            <v-icon light>$vuetify.icons.cached</v-icon>
                        </span>
                    </template>
                </v-btn>
            </div>
        </v-card-title>
        <v-container fluid>
            <v-row class="pl-4">
                <v-col cols="12" sm="6">
                    <v-switch
                        v-model="settings.ga4"
                        :label="$t('Google Analytics 4(GA4)')"
                    ></v-switch>
                </v-col>
            </v-row>
            <template v-if="settings.ga4">
                <v-row>
                    <!-- <template v-if="settings.mailMailer === 'smtp'"> -->
                    <v-col md="6" cols="12">
                        <v-text-field
                            :label="$t('Measurement ID')"
                            outlined
                            placeholder="G-**********"
                            v-model="settings.ga4MID"
                        ></v-text-field
                    ></v-col>
                    <v-col cols="12">
                            <div class="title">{{ $t("Events") }}</div>
                            <v-divider></v-divider>
                            <div class="sub">Enable the events you want to report</div>
                    </v-col>
                    <v-col cols="12">
                        <v-row>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_play_event"
                                    :label="$t('Play')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_like_event"
                                    :label="$t('Like')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_download_event"
                                    :label="$t('File Download')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_adClick_event"
                                    :label="$t('Ad Click')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_fileUpload_event"
                                    :label="$t('User Upload')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_chat_event"
                                    :label="$t('Chat Message')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_login_event"
                                    :label="$t('Login')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_purchase_event"
                                    :label="$t('Purchase')"
                                ></v-switch>
                            </v-col>
                            <!-- <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_logout_event"
                                    :label="$t('Logout')"
                                ></v-switch>
                            </v-col> -->
                             <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="settings.analytics_subscription_event"
                                    :label="$t('Subscription')"
                                ></v-switch>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </template>
        </v-container>
    </v-card>
    <page-loading v-else />
</template>
<script>
export default {
    props: ["settings", "isLoading"],
    data() {
        return {
            isLoadingForButton: false
        };
    },
    methods: {
        save() {
            if (
                this.settings.ga4 &&
                !this.settings.ga4MID 
            ) {
                this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Oops!"),
                    text: this.$t("Please enter your valid GA4 measurement ID.")
                });
                return;
            }
            
            this.isLoadingForButton = true;

            const formData = new FormData();

            formData.append('ga4', this.settings.ga4 ? 1 : 0)
            formData.append('ga4MID', this.settings.ga4MID)
            formData.append('analytics_play_event', this.settings.analytics_play_event ? 1 : 0)
            formData.append('analytics_like_event', this.settings.analytics_like_event ? 1 : 0)
            formData.append('analytics_download_event', this.settings.analytics_download_event ? 1 : 0)
            formData.append('analytics_adClick_event', this.settings.analytics_adClick_event ? 1 : 0)
            formData.append('analytics_fileUpload_event', this.settings.analytics_fileUpload_event ? 1 : 0)
            formData.append('analytics_chat_event', this.settings.analytics_chat_event ? 1 : 0)
            formData.append('analytics_login_event', this.settings.analytics_login_event ? 1 : 0)
            formData.append('analytics_purchase_event', this.settings.analytics_purchase_event ? 1 : 0)
            // formData.append('analytics_logout_event', this.settings.analytics_logout_event ? 1 : 0)
            formData.append('analytics_subscription_event', this.settings.analytics_subscription_event ? 1 : 0)

            axios
                .post("/api/admin/save-analytics-settings", formData)
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Updated"),
                        text:
                            this.$t("Settings") +
                            " " +
                            this.$t("updated successfully.")
                    });
                })
                .catch(() => {
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: Object.values(e.response.data.errors).join(
                            "<br />"
                        )
                    });
                })
                .finally(() => {
                    this.isLoadingForButton = false;
                });
        }
    }
};
</script>
