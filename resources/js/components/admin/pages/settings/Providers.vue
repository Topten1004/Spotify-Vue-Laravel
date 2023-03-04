<template>
    <v-card class="page" v-if="!isLoading">
        <v-card-title>
            <div class="page__title">
                <v-icon color="primary" x-large>$vuetify.icons.api</v-icon>
                {{ $t("Providers") }}
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
            <v-row>
            <v-col cols="12">
                <v-switch v-model="settings.provider_spotify" label="Spotify API"></v-switch>
            </v-col>
            <template v-if="settings.provider_spotify">
            <v-col cols="6">
                <v-text-field dense hide-details="" outlined v-model="settings.spotifyClientID" label="Spotify Client ID"></v-text-field>
            </v-col>
            <v-col cols="6">
                <v-text-field dense hide-details="" outlined v-model="settings.spotifyClientSecret" label="Spotify Client Secret"></v-text-field>
            </v-col>
             <v-col cols="12">
                <v-switch v-model="settings.spotify_search" :label="$t('Search')" :messages="$t('Includes search results from Spotify engine ( search bar of the player )')"></v-switch>
            </v-col>
             <v-col cols="12">
                <v-switch v-model="settings.show_external_link_spotify" :label="$t('Show Spotify external links')" :messages="$t('A Button shows on Spotify content pages that takes to the official page')"></v-switch>
            </v-col>
            </template>
            </v-row>
            <v-row>
            <v-divider class="py-3"></v-divider>
            </v-row>
            <v-row>
            <v-col cols="12">
                <v-switch v-model="settings.provider_listenNotes" label="Listen Notes API" messages="The most comprehensive podcast database online."></v-switch>
            </v-col>
            <template v-if="settings.provider_listenNotes">
            <v-col cols="6">
                <v-text-field dense outlined v-model="settings.listenNotesClientID" label="Listen Notes API Token"></v-text-field>
            </v-col>
            <v-col cols="12">
                <v-switch v-model="settings.listenNotes_search" :label="$t('Search')" :messages="$t('Includes search results from Listen Notes engine ( search bar of the player )')"></v-switch>
            </v-col>

            </template>
            </v-row>
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
                this.settings.provider_spotify &&
                (!this.settings.spotifyClientID ||
                    !this.settings.spotifyClientSecret)
            ) {
                this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Oops!"),
                    text: this.$t("You need to add your Spotify keys.")
                });
                return;
            }
            this.isLoadingForButton = true;
            const formData = new FormData();
            formData.append("provider_spotify", this.settings.provider_spotify ? 1 : 0);
            formData.append("provider_listenNotes", this.settings.provider_listenNotes ? 1 : 0);
            formData.append("show_external_link_spotify", this.settings.show_external_link_spotify ? 1 : 0);
            formData.append("spotify_search", this.settings.spotify_search ? 1 : 0);
            formData.append("listenNotes_search", this.settings.listenNotes_search ? 1 : 0);

            formData.append("spotifyClientID", this.settings.spotifyClientID);
            formData.append("listenNotesClientID", this.settings.listenNotesClientID);
            formData.append("spotifyClientSecret", this.settings.spotifyClientSecret);
            axios
                .post("/api/admin/save-providers", formData)
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
                    // this.$notify({
                    //     group: "foo",
                    //     type: "error",
                    //     title: this.$t("Error"),
                    //     text: Object.values(e.response.data.errors).join(
                    //         "<br />"
                    //     )
                    // });
                })
                .finally(() => {
                    this.isLoadingForButton = false;
                });
        }
    }
};
</script>
