<template>
    <v-expansion-panels class="p-2" :value="1">
        <v-expansion-panel>
            <v-expansion-panel-header>
                <div>
                    <v-icon class="mr-2">$vuetify.icons.link</v-icon>
                {{ $t("External Links") }}
                </div>
            </v-expansion-panel-header>
            <v-expansion-panel-content>
                <v-container>
                    <v-row align="center">
                        <v-col cols="auto">
                            <v-sheet width="150" class="pt-2">
                                <v-icon>$vuetify.icons.spotify</v-icon>
                                Spotify
                            </v-sheet>
                        </v-col>
                        <v-col>
                            <v-text-field
                                label="URL"
                                v-model="links.spotify_link"
                                :value="item.spotify_link"
                                hide-details=""
                                :readonly="readonly"
                                @change="emitLink('spotify_link')"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row align="center">
                        <v-col cols="auto">
                            <v-sheet width="150" class="pt-2"></v-sheet>
                            <v-icon>$vuetify.icons.youtube</v-icon>
                            Youtube
                        </v-col>
                        <v-col>
                            <v-text-field
                                label="URL"
                                v-model="links.youtube_link"
                                :value="item.youtube_link"
                                hide-details=""
                                                                :readonly="readonly"
                                @change="emitLink('youtube_link')"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row align="center">
                        <v-col cols="auto">
                            <v-sheet width="150" class="pt-2"></v-sheet>
                            <v-icon>$vuetify.icons.soundcloud</v-icon>
                            Soundcloud
                        </v-col>
                        <v-col>
                            <v-text-field
                                label="URL"
                                v-model="links.soundcloud_link"
                                :value="item.soundcloud_link"
                                hide-details=""
                                                                :readonly="readonly"
                                @change="emitLink('soundcloud_link')"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row align="center">
                        <v-col cols="auto">
                            <v-sheet width="150" class="pt-2"></v-sheet>
                            <div class="align-center">
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true"
                                focusable="false"
                                width="28"
                                height="28"
                                style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);"
                                preserveAspectRatio="xMidYMid meet"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M7.85 17.07c-.82.1-4.35.6-3.79 3.19c.63 3.04 5.81 2.33 5.77-1.26c-.02-2.43 0-9.8 0-9.8s-.07-.67.6-.81l7.76-1.6s.64-.14.64.5v6.91s.07.63-.71.8c-.78.12-4.21.4-3.93 3c.31 3.07 5.81 2.65 5.81-.93V2.61s.04-.99-1.1-.74L9.5 3.78s-.84.18-.84.99v11.34s0 .85-.81.96z"
                                    fill="#626262"
                                />
                            </svg>
                            <div class="mr-2">
                                itunes
                            </div>
                            </div>
                        </v-col>
                        <v-col>
                            <v-text-field
                                label="URL"
                                v-model="links.itunes_link"
                                :value="item.itunes_link"
                                hide-details=""
                                                                :readonly="readonly"
                                @change="emitLink('itunes_link')"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-container>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
export default {
    props: ['item', 'expanded', 'readonly'],
    data() {
        return {
            links: this.item
        };
    },
    methods: {
        emitLink(type) {
            this.$emit(type, this.links[type]);
        }
    }
};
</script>