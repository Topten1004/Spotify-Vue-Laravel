<template>
    <v-card class="page" v-if="!isLoading">
        <v-card-title>
            <div class="page__title">
                <v-icon color="primary" x-large
                    >$vuetify.icons.motion-play</v-icon
                >
                {{ $t("Player") }}
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
        <v-container>
            <v-row>
                <v-col>
                    <v-switch
                        :label="$t('Hide Video')"
                        v-model="settings.disableVideo"
                    ></v-switch>
                </v-col>
                <v-col>
                    <v-switch
                        :label="$t('Hide Download Button')"
                        v-model="settings.hideDownloadButton"
                    ></v-switch>
                </v-col>
                <v-col>
                    <v-slider
                        v-model="settings.playerVolume"
                        :label="$t('Default Volume')"
                        thumb-color="primary"
                        hide-details=""
                        :thumb-label="true"
                    ></v-slider>
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <v-divider></v-divider>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <v-switch
                        :label="$t('Auto Play')"
                        :messages="
                            $t(
                                'Shuffle songs automatically based on the settings you provide.'
                            )
                        "
                        v-model="settings.autoPlay"
                    ></v-switch>
                    <v-container v-if="settings.autoPlay">
                        <v-row>
                            <div class="pt-5">
                                {{
                                    $t(
                                        "Select the order in which the songs should be shuffled to the user based on the song he selected."
                                    )
                                }}
                            </div>
                            <v-col cols="12">
                                <draggable v-model="shuffleOptions">
                                    <div
                                        class="shuffle-option"
                                        v-for="option in shuffleOptions"
                                        :key="option.id"
                                    >
                                        <div class="drag-icon pr-2">
                                            <v-icon>$vuetify.icons.menu</v-icon>
                                        </div>
                                        <div class="option-text">
                                            {{ option.text }}
                                        </div>
                                    </div>
                                </draggable>
                            </v-col>
                            <!-- <v-col cols="12">
                                <v-switch
                                    disabled
                                    :label="$t('Crossfade') + '(Coming soon)'"
                                    :messages="
                                        $t(
                                            'Makes a smooth transition between two songs.'
                                        )
                                    "
                                    v-model="settings.crossfade"
                                ></v-switch>
                            </v-col> -->
                        </v-row>
                    </v-container>
                </v-col>
            </v-row>
                <!-- <v-row>
                <v-col cols="12">
                    <v-switch
                        :label="$t('Default Playlist')"
                        :messages="
                            $t(
                                'Playlist to load when the user visits the home page.'
                            )
                        "
                        v-model="settings.defaultPlaylist"
                    ></v-switch>
                    <v-container v-if="settings.defaultPlaylist">
                        <v-row>
                            <v-col>
                                <List
                                    :contentLoading="contentLoading"
                                    :content="editedSection.content"
                                    :isOnSectionEdit="true"
                                    @removeContentItem="removeContentItem($event)"
                                    @attachAsset="attachAsset($event)"
                                />
                            </v-col>
                        </v-row>
                    </v-container>
                </v-col>
            </v-row> -->
        </v-container>
        <!-- <v-dialog v-model="attachAssetDialogBoolean" max-width="500">
            <attach-asset-dialog
                v-if="assetIndex !== null"
                class="p-3"
                @asset="updateContent($event)"
            />
        </v-dialog> -->
    </v-card>
    
    <page-loading v-else />
</template>
<script>
import draggable from "vuedraggable";
// import AttachAssetDialog from "../../../dialogs/admin/AttachAssetDialog.vue";
export default {
    components: {
        draggable,
        // AttachAssetDialog
    },
    props: ["settings", "isLoading"],
    data() {
        return {
            isLoadingForButton: false,
            // shuffleOptions: [
            //     {
            //         id: 1,
            //         value: "album",
            //         text: "Same Album"
            //     },
            //     {
            //         id: 1,
            //         value: "artists",
            //         text: "Same Artists"
            //     },
            //     {
            //         id: 1,
            //         value: "genres",
            //         text: "Same Genres"
            //     }
            // ]
            shuffleOptions: this.$store.getters.getSettings.shuffleOrder
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

            formData.append("autoPlay", this.settings.autoPlay ? 1 : 0);
            formData.append("crossfade", this.settings.crossfade ? 1 : 0);
            formData.append("disableVideo", this.settings.disableVideo ? 1 : 0);
            formData.append("hideDownloadButton", this.settings.hideDownloadButton ? 1 : 0);
            formData.append(
                "downloadButton",
                this.settings.downloadButton ? 1 : 0
            );
            formData.append("playerVolume", this.settings.playerVolume);

            formData.append(
                "shuffleOrder",
                JSON.stringify(this.shuffleOptions)
            );

            axios
                .post("/api/admin/save-player-settings", formData)
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
        },
        updateContent(event) {
            this.$set(this.defaultPlaylist.content, this.assetIndex, event);
            this.attachAssetDialogBoolean = false;
            this.assetIndex = null;
        }
    }
};
</script>

<style lang="scss" scoped>
.shuffle-option {
    display: flex;
    padding: 0.5em 1em;
    font-size: 1.2em;
}
</style>
