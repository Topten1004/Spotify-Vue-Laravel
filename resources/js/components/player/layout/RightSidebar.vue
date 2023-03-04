<template>
    <v-card
        id="right-sidebar-container"
        ref="rightSidebar"
        v-if="!hideRightSidebar"
    >
        <div class="right-sidebar-toggle" @click="$emit('toggle')">
            <v-icon small class="mr-1">$vuetify.icons.menu</v-icon>
        </div>
        <div class="sticky">
            <template v-if="$store.getters.getSettings.enableAds">
                <div
                    class="ad-slot"
                    v-if="
                        parseAd($store.getters.getSettings.square_ad)
                            .position == 'trs' &&
                            parseAd($store.getters.getSettings.square_ad).code
                    "
                >
                    <ad
                        v-if="!hasPermission('No ads')"
                        :ad_code="
                            parseAd($store.getters.getSettings.square_ad).code
                        "
                        @click="
                        $store.getters.getSettings.ga4 && 
                        $store.getters.getSettings.analytics_adClick_event ? 
                        emitAnalyticsEvent({
                            action: 'ad_click',
                            category: 'square_ad',
                            label: 'top right-sidebar ad'
                        })
                        : ''
                        "
                    ></ad>
                </div>
            </template>
            <div
                class="card-inside highlights"
                flat
                v-if="highlights && highlights.length"
            >
                <div class="card-inside__title">{{ $t("Live Radio") }}</div>
                <div class="card-inside__body">
                    <div
                        class="radio-station"
                        v-for="radioStation in highlights"
                        :key="radioStation.id"
                        @click="
                            $store.dispatch('playRadioStation', {
                                radioStation
                            })
                        "
                    >
                        <div class="radio-station__cover">
                            <v-img
                                class="bordered-small-img img"
                                :src="radioStation.cover"
                                width="45"
                                height="45"
                            ></v-img>
                        </div>
                        <div class="radio-station__details">
                            {{ radioStation.name }}
                            <div class="live-animation ml-2">
                                <div class="align-center">
                                    <svg
                                        height="20"
                                        width="13"
                                        class="blinking"
                                    >
                                        <circle
                                            cx="5"
                                            cy="10"
                                            r="3"
                                            fill="red"
                                        ></circle>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <friend-list
                class="card-inside"
                v-if="
                    $store.getters.getUser &&
                        $store.getters.getFriends &&
                        $store.getters.getSettings.enableFriendshipSystem
                "
            />
            <template v-if="$store.getters.getSettings.enableAds">
                <div
                    class="ad-slot"
                    v-if="
                        parseAd($store.getters.getSettings.square_ad)
                            .position == 'brs' &&
                            parseAd($store.getters.getSettings.square_ad).code
                    "
                >
                    <ad
                        v-if="!hasPermission('No ads')"
                        :ad_code="
                            parseAd($store.getters.getSettings.square_ad).code
                        "
                        @click="
                        $store.getters.getSettings.ga4 && 
                        $store.getters.getSettings.analytics_adClick_event ? 
                        emitAnalyticsEvent({
                            action: 'ad_click',
                            category: 'square_ad',
                            label: 'bottom right-sidebar ad'
                        }) : ''
                        "
                    ></ad>
                </div>
            </template>
            <div class="links">
                <div class="link" v-for="page in pages" :key="page.id">
                    <router-link
                        class="router-link"
                        :to="{ path: page.path }"
                    >
                        {{ $t(page.name) }}
                    </router-link>
                </div>
                <div
                    class="link"
                    v-if="
                        $store.getters.getPlans &&
                            $store.getters.getPlans.length > 1
                    "
                >
                    <router-link
                        class="router-link"
                        :to="{ name: 'subscription' }"
                    >
                        {{ $t("Plans") }}
                    </router-link>
                </div>

                <!-- <div
                    class="link"
                    @click="aboutUsDialog = true"
                    v-if="$store.getters.getSettings.aboutUs.length"
                >
                    {{ $t("About Us") }}
                </div> -->
            </div>
            <div class="copyrights">
                © {{ moment().year() }} {{ $store.getters.getSettings.appName }}. {{ $t('All rights reserved.') }}
            </div>
            <div class="external-link mt-4">
                <v-btn class="mx-1" x-small :href="$store.getters.getSettings.twitter_link" rel="noreferrer" target="_blank" v-if="$store.getters.getSettings.twitter_link" icon> 
                    <v-icon>$vuetify.icons.twitter</v-icon>
                </v-btn>
                <v-btn class="mx-1" x-small :href="$store.getters.getSettings.facebook_link" rel="noreferrer" target="_blank" v-if="$store.getters.getSettings.facebook_link" icon> 
                    <v-icon>$vuetify.icons.facebook</v-icon>
                </v-btn>
                <v-btn class="mx-1" x-small :href="$store.getters.getSettings.youtube_link" rel="noreferrer" target="_blank" v-if="$store.getters.getSettings.youtube_link" icon> 
                    <v-icon>$vuetify.icons.youtube</v-icon>
                </v-btn>
                <v-btn class="mx-1" x-small :href="$store.getters.getSettings.instagram_link" rel="noreferrer" target="_blank" v-if="$store.getters.getSettings.instagram_link" icon> 
                    <v-icon>$vuetify.icons.instagram</v-icon>
                </v-btn>
                <v-btn class="mx-1" x-small :href="$store.getters.getSettings.spotify_link" rel="noreferrer" target="_blank" v-if="$store.getters.getSettings.spotify_link" icon> 
                    <v-icon>$vuetify.icons.spotify</v-icon>
                </v-btn>
                <v-btn class="mx-1" x-small :href="$store.getters.getSettings.soundcloud_link" rel="noreferrer" target="_blank" v-if="$store.getters.getSettings.soundcloud_link" icon> 
                    <v-icon>$vuetify.icons.soundcloud</v-icon>
                </v-btn>
            </div>
        </div>
        <!-- <v-dialog v-model="aboutUsDialog" width="500">
            <v-card v-if="aboutUsDialog">
                <v-card-title>
                    {{ $t("About Us") }}
                    <v-spacer></v-spacer>
                    <v-spacer></v-spacer>
                    <v-card-actions>
                        <v-btn icon @click="aboutUsDialog = false">
                            <v-icon>$vuetify.icons.close</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card-title>
                <div class="body py-1 px-2">
                    <div
                        class="body__content"
                        v-html="$store.getters.getSettings.aboutUs"
                    ></div>
                </div>
            </v-card>
        </v-dialog> -->
        <v-dialog v-model="contactUsDialog" max-width="600" >
            <v-card v-if="$store.getters.getSettings.allowGuestsToContact">
                <v-card-title
                    >{{ $t("Contact Us") }}
                    <v-spacer></v-spacer>
                    <v-spacer></v-spacer>
                    <v-card-actions>
                        <v-btn icon @click="contactUsDialog = false">
                            <v-icon>$vuetify.icons.close</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card-title>
                <div class="body">
                    <contact-us @emailSent="contactUsDialog = false"></contact-us>
                </div>
            </v-card>
        </v-dialog>
    </v-card>
</template>
<script>
import ContactUs from "../../dialogs/ContactUs.vue";
export default {
    components: {
        ContactUs,
        FriendList: () => import("../chat/FriendList.vue")
    },
    created() {
        this.fetchHighlights();
        this.fetchPages();
    },
    methods: {
        fetchHighlights() {
            axios.get("/api/highlights").then(res => {
                this.highlights = res.data;
                // this.$store.dispatch('updateQueue', { content: [this.highlights[0]], force: false })
            });
        },
        fetchPages() {
            axios.get('/api/pages')
            .then((res) => {
                this.pages = res.data
            })
        }
    },
    data() {
        return {
            aboutUsDialog: false,
            contactUsDialog: false,
            highlights: [],
            pages: []
        };
    },
    computed: {
        hideRightSidebar() {
            return this.$store.getters.getSettings.hideRightSidebar;
        }
    }
};
</script>

<style lang="scss" scoped>
.card-inside {
    background-color: var(--light-theme-bg-color);
    border-radius: 0.5em;
    padding: 1em 0;
    -webkit-border-radius: 0.5em;
    -moz-border-radius: 0.5em;
    -ms-border-radius: 0.5em;
    -o-border-radius: 0.5em;
    padding: 0.2em;
    display: block;
    width: 100%;
    margin-top: 1em;
    &__title {
        font-weight: bold;
        font-size: 1.1em;
        padding: 0.5em;
    }
}
.sticky {
    position: sticky;
    top: 4em;
}
.links {
    display: flex;
    flex-wrap: wrap;
    max-width: 100%;
    min-width: 200px;
    margin-top: .5em;
    padding: 0.5em 0.2em;
    .link {
        padding: 0.1em 0.4em;
        cursor: pointer;
        font-size: .75em;
        &:hover {
            text-decoration: underline;
        }
    }
}

.player--dark {
    .card-inside {
        background-color: var(--dark-theme-bg-color);
    }
}
.copyrights {
    opacity: 0.7;
    padding-left: 0.5em;
    margin-top: 1.5em;
    font-size: .7em;
}

.highlights {
    padding: 0.7em 0.3em;
    .radio-station {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 0.35em 0.6em;
        &:hover {
            background-color: rgba(223, 223, 223, 0.226);
        }
        &__cover {
            margin-right: 0.5em;
            .img {
                border-radius: 5px;
            }
        }
        &__details {
            display: flex;
            align-items: center;
        }
    }
}
</style>
