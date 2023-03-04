<template>
    <div class="advertising-wrapper">
        <v-card outlined>
            <v-card-title>
                <v-icon color="primary" x-large>$vuetify.icons.google-ads</v-icon>
                <div class="advertising-wrapper__title">
                    {{ $t("Advertisements") }}
                </div>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="save" :disabled="isLoading">{{
                    $t("Save")
                }}</v-btn>
            </v-card-title>
            <v-container>
                <v-row>
                    <v-col cols="21">
                        <v-switch
                            :label="$t('Enable Ads')"
                            v-model="$store.getters.getSettings.enableAds"
                        ></v-switch>
                    </v-col>
                </v-row>
                <row>
                    <!-- <v-card>
                        <v-card-title>
                            In-Player Ads
                            <v-divider></v-divider>
                            <div class="sub">Play ads between tracks</div>
                        </v-card-title>
                        <div>
                            <v-switch :label="$t('Include Banner')"></v-switch>
                            <v-file-input
                                accept="audio/*"
                                outlined
                                :error="error"
                                v-model="inPlayerAD"
                                hide-details=""
                                :label="
                                    editedSong.file_name
                                        ? editedSong.file_name
                                        : $t('Attach Audio File')
                                "
                                @change="loadSongMetadata($event)"
                            >

                            </v-file-input>
                        </div>
                    </v-card> -->
                </row>
                <v-row v-if="$store.getters.getSettings.enableAds">
                    <v-col cols="12">
                        <v-card outlined>
                            <v-card-title>
                                <v-icon color="primary" x-large></v-icon>
                                Leaderboard Ad
                                <v-spacer></v-spacer>
                                <v-spacer></v-spacer>
                                <v-btn
                                    class="mx-2"
                                    fab
                                    dark
                                    x-small
                                    color="info"
                                    @click="rect_ad.showInfoDialog = true"
                                >
                                    <v-icon dark>
                                        $vuetify.icons.help
                                    </v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-container>
                                <v-row>
                                    <v-col cols="12"
                                        ><v-textarea
                                            :label="$t('Ad code')"
                                            outlined
                                            v-model="rect_ad.code"
                                            messages=""
                                        ></v-textarea
                                    ></v-col>
                                    <v-col cols="12">
                                        <v-select
                                            :label="$t('position')"
                                            :items="rect_ad.positions"
                                            v-model="rect_ad.position"
                                            item-text="text"
                                            item-value="value"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card>
                    </v-col>
                    <v-col cols="12">
                        <v-card outlined>
                            <v-card-title>
                                <v-icon color="primary" x-large></v-icon>
                                Square Ad
                                <v-spacer></v-spacer>
                                <v-spacer></v-spacer>
                                <v-btn
                                    class="mx-2"
                                    fab
                                    dark
                                    x-small
                                    color="info"
                                    @click="square_ad.showInfoDialog = true"
                                >
                                    <v-icon dark>
                                        $vuetify.icons.help
                                    </v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" class="p-2">
                                        <v-textarea
                                            :label="$t('Ad code')"
                                            outlined
                                            v-model="square_ad.code"
                                        ></v-textarea>
                                        <v-select
                                            :label="$t('position')"
                                            :items="square_ad.positions"
                                            v-model="square_ad.position"
                                            item-text="text"
                                            item-value="value"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
        <v-dialog v-model="rect_ad.showInfoDialog" max-width="950">
            <v-card>
                <v-img
                    src="/storage/defaults/images/ads/ad_leaderboard_preview.png"
                    alt="ad_leaderboard_preview"
                    width="100%"
                />
            </v-card>
        </v-dialog>
        <v-dialog v-model="square_ad.showInfoDialog" max-width="950">
            <v-card>
                <v-img
                    src="/storage/defaults/images/ads/ad_square_preview.png"
                    alt="ad_square_preview"
                    width="100%"
                />
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
import { VSelect } from "vuetify/lib";
export default {
    components: {
        VuetifySelect: VSelect
    },
    data() {
        return {
            isLoading: false,
            rect_ad: {
                code: this.parseAd(this.$store.getters.getSettings.rect_ad)
                    .code,
                position: this.parseAd(this.$store.getters.getSettings.rect_ad)
                    .position,
                positions: [
                    {
                        text: this.$t("Top of content pages"),
                        value: "tcp"
                    },
                    {
                        text: this.$t("Bottom of content pages"),
                        value: "bcp"
                    }
                ],
                showInfoDialog: false
            },
            square_ad: {
                code: this.parseAd(this.$store.getters.getSettings.square_ad)
                    .code,
                position: this.parseAd(
                    this.$store.getters.getSettings.square_ad
                ).position,
                positions: [
                    {
                        text: this.$t("Top of the right-sidebar"),
                        value: "trs"
                    },
                    {
                        text: this.$t("Bottom of right-sidebar"),
                        value: "brs"
                    }
                ],
                showInfoDialog: false
            }
        };
    },
    methods: {
        save() {
            this.isLoading = true;
            const enableAds = this.$store.getters.getSettings.enableAds;
            const rect_ad = {
                code: this.rect_ad.code,
                position: this.rect_ad.position
            };
            const square_ad = {
                code: this.square_ad.code,
                position: this.square_ad.position
            };
            axios
                .post("/api/admin/save-ads", {
                    enableAds,
                    rect_ad,
                    square_ad
                })
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Saved"),
                        text: this.$t("Saved successfully.")
                    });
                    location.reload();
                })
                .finally(() => (this.isLoading = false));
        }
    }
};
</script>

<style lang="scss">
.advertising-wrapper {
    &__title {
        font-weight: bold;
        font-size: 1.2em;
        margin-left: 0.5em;
    }
}
.ad-banner {
    width: 100%;
    &__rect {
        min-width: 300px;
    }
    &__square {
        max-width: 300px;
        min-width: 140px;
    }
}
</style>
