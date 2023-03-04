<template>
    <div class="song-page-container elevate-page" v-if="song">
        <div class="cover-img">
            <div class="overlay"></div>
            <img :src="song.cover" alt="" class="cover-background" />
            <div class="song-info">
                <v-img :src="song.cover" aspect-ratio="1" class="cover">
                    <template v-slot:placeholder>
                        <div class="song-expo-skeleton fill-height">
                            <content-placeholders :rounded="true">
                                <content-placeholders-img />
                            </content-placeholders>
                        </div>
                    </template>
                </v-img>
                <div
                    class="main-genre align-center"
                    v-if="song.genres && song.genres.length"
                >
                    <router-link
                        class="router-link"
                        :to="{
                            name: 'genre-page',
                            params: { genre_name: slug(song.genres[0].name), id: song.genres[0].id }
                        }"
                    >
                        <div class="genre">
                            {{ song.genres[0].name }} {{ $t("Song") }}
                        </div>
                    </router-link>
                    <div class="badges align-center">
                        <div
                            class="exclusive mr-2"
                            :title="$t('Exclusive')"
                            v-if="song.isExclusive"
                        >
                            <v-btn x-small dense depressed color="primary"
                                >Exclusive</v-btn
                            >
                        </div>
                        <div
                            class="explicit mr-2"
                            :title="$t('Explicit')"
                            v-if="song.isExplicit"
                        >
                            <div class="explicit__sign">
                                E
                            </div>
                        </div>
                    </div>
                </div>
                <div class="song-title">
                    {{ song.title }}
                </div>
                <div class="song-infos">
                    <h3>
                        <template v-if="song.album">
                            <small>{{ $t("From") }}</small>
                            <router-link
                                class="router-link router-link__white"
                                :to="{
                                    name: 'album',
                                    params: { id: song.album.id }
                                }"
                            >
                                {{ song.album.title }}
                            </router-link></template
                        >

                        <template v-if="song.artists && song.artists.length">
                            <small>{{ $t("By") }}</small>
                            <artists
                                :artists="song.artists"
                                :textColor="'white'"
                            ></artists>
                        </template>
                    </h3>
                </div>
                <!-- <div class="song-artists">
                    <h3></h3>
                </div> -->
            </div>
            <div class="actions">
                <purchase-button :size="'large'" :item="song"></purchase-button>
                <external-links :content="song" />
                <div
                    class="like-button"
                    v-if="!$store.getters.getSettings.disableRegistration"
                >
                    <div
                        class="button-svg-container button-heart"
                        v-if="isLiked"
                        @click="like(song)"
                    >
                        <button class="heart-button">
                            <v-icon color="primary"
                                >$vuetify.icons.heart</v-icon
                            >
                        </button>
                    </div>
                    <div
                        class="button-svg-container button-heart"
                        v-else
                        @click="like(song)"
                    >
                        <button class="heart-button">
                            <v-icon color="primary"
                                >$vuetify.icons.heart-outline</v-icon
                            >
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="main">
            <div
                class="play-button relative pointer"
                @click="$store.dispatch('playSong', { song, reset: true })"
            >
                <div class="absolute fill align-justify-center">
                    <div class="back-layer"></div>
                </div>
                <img
                    src="/svg/play-round.svg"
                    alt=""
                    class="svg-image"
                    v-if="$vuetify.theme.dark"
                />
                <img
                    src="/svg/play-round-purple.svg"
                    alt=""
                    class="svg-image"
                    v-else
                />
            </div>
            <div class="other-actions">
                <div class="share-btn">
                    <v-btn small fab @click="share" color="secondary">
                        <v-icon>$vuetify.icons.share-variant</v-icon>
                    </v-btn>
                </div>
            </div>
            <template v-if="$store.getters.getSettings.enableAds">
                <div class="ad-slot">
                    <ad
                        :ad_code="
                            parseAd($store.getters.getSettings.rect_ad).code
                        "
                        @click="
                            $store.getters.getSettings.ga4 &&
                            $store.getters.getSettings.analytics_adClick_event
                                ? emitAnalyticsEvent({
                                      action: 'ad_click',
                                      category: 'leaderboard_ad',
                                      label: 'Song page'
                                  })
                                : ''
                        "
                    ></ad>
                </div>
            </template>
            <div class="sections pt-5">
                <div
                    class="section-more-from-album"
                    v-if="moreFromAlbum && moreFromAlbum.length"
                >
                    <swiper-section
                        :content="moreFromAlbum"
                        rank="1"
                        :title="
                            $t('More from') +
                                ' ' +
                                '&quot;' +
                                song.album.title +
                                '&quot;' +
                                ' ' +
                                $t('Album')
                        "
                    />
                </div>
                <div
                    class="section-more-from-artist"
                    v-if="moreFromArtists && moreFromArtists.length"
                >
                    <swiper-section
                        :content="moreFromArtists"
                        rank="1"
                        :title="$t('More from the same aritsts')"
                    />
                </div>
                <div
                    class="section-more-from-genre"
                    v-if="moreFromGenre && moreFromGenre.length"
                >
                    <swiper-section
                        :content="moreFromGenre"
                        rank="2"
                        :title="
                            $t('More') +
                                ' ' +
                                song.genres[0].name +
                                ' ' +
                                $t('Music')
                        "
                    />
                </div>
            </div>
        </div>
    </div>
    <empty-page
        v-else-if="!song && errorStatus == 404"
        :headline="$t('Not Available!')"
        :sub="
            $t(
                'Song does not exist or maybe it is not available for public access. Who knows!'
            )
        "
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)">
                {{ $t("Go back") }}
            </v-btn>
        </template>
    </empty-page>
    <empty-page
        v-else-if="!song && errorStatus"
        :headline="$t('Something wrong happend!')"
        :sub="$t('Some server error has occurred. Try again later.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)">
                {{ $t("Go back") }}
            </v-btn>
        </template>
    </empty-page>
    <page-loading v-else />
</template>
<script>
import PurchaseButton from '../../elements/other/ProductBtn.vue';
export default {
    components: {PurchaseButton},
    metaInfo() {
        return {
            title: this.generateMetaInfos(
                this.song,
                this.$store.getters.getSettings.songPageTitle,
                "song"
            ),
            meta: [
                {
                    name: "description",
                    content: this.generateMetaInfos(
                        this.song,
                        this.$store.getters.getSettings.songPageDescription,
                        "song"
                    )
                }
            ]
        };
    },
    created() {
        axios
            .get("/api/song/" + this.$route.params.id)
            .then(res => {
                this.song = res.data;
                axios
                    .get(
                        "/api/more-from-album?id=" +
                            this.song.id +
                            "&origin=" +
                            this.song.origin
                    )
                    .then(
                        res =>
                            (this.moreFromAlbum = res.data.filter(
                                song => song.id !== this.song.id
                            ))
                    );
                axios
                    .get(
                        "/api/more-from-artist?id=" +
                            this.song.id +
                            "&origin=" +
                            this.song.origin
                    )
                    .then(
                        res =>
                            (this.moreFromArtists = res.data.filter(
                                song => song.id !== this.song.id
                            ))
                    );
                if (this.song.genres.length) {
                    axios
                        .get("/api/more-from-genre/" + this.song.genres[0].id)
                        .then(
                            res =>
                                (this.moreFromGenre = res.data.filter(
                                    song => song.id !== this.song.id
                                ))
                        );
                }
            })
            .catch(e => {
                if (e.response.status === 404) {
                    this.errorStatus = 404;
                } else {
                    this.errorStatus = 500;
                }
            });
    },
    computed: {
        isLiked() {
            if (this.song) {
                return (this.$store.getters.getLikedSongs || []).some(
                    x => x.id == this.song.id
                );
            }
        }
    },
    data() {
        return {
            song: null,
            moreFromArtists: null,
            moreFromAlbum: null,
            moreFromGenre: null,
            allowedToWrite: null,
            errorStatus: null
        };
    },
    methods: {
        share() {
            this.$store.commit("shareItem", {
                cover: this.song.cover,
                url: this.getItemURL(this.song),
                title: this.song.title,
                type: this.song.type,
                artist: this.getMainArtist(this.song)
            })
        },
        deleteSong() {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this") +
                    " " +
                    this.$t("Song") +
                    "?"}`,
                button: {
                    no: this.$t("No"),
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        this.$store
                            .dispatch("delete_user_song", this.song.id)
                            .then(res => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Song Deleted"),
                                    text:
                                        this.song.title +
                                        " " +
                                        this.$t(
                                            "has been deleted successfully."
                                        )
                                });
                                this.$router.push("/library/my-songs");
                            });
                    }
                }
            });
        },
        async like(song) {
            if (this.isLiked) {
                this.$store.dispatch("dislike", song);
            } else {
                this.$store.dispatch("like", song);
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.delete {
    display: flex;
    justify-content: center;
    padding-top: 1.2em;
}
.song-page-container {
    margin: -12px;
    padding-bottom: 3em;
    .other-actions {
        position: absolute;
        right: 3em;
        top: 0;
        z-index: 3;
        transform: translateY(-50%);
    }
    .main {
        position: relative;
        padding: 2em 1em;
        padding-top: 3em;
        .stats {
            display: flex;
            font-size: 2em;
            opacity: 0.8;
            justify-content: center;
            .plays {
                margin: 0 2em;
            }
        }
        .links {
            justify-content: center;
            margin-top: -2em;
            .link {
                margin: 0 1em;
            }
        }
        .play-button {
            width: 8%;
            min-width: 4.5em;
            position: absolute;
            max-width: 8em;
            left: 3em;
            top: 0;
            z-index: 3;
            transform: translateY(-50%);
            .svg-image {
                width: 100%;
                z-index: 1;
            }
            .back-layer {
                width: 5em;
                height: 5em;
                z-index: -1;
                border-radius: 50%;
                background-color: white;
                @media (max-width: 1400px) {
                    width: 3em;
                    height: 3em;
                }
            }
        }
    }
    .cover-img {
        height: 50vh;
        overflow: hidden;
        position: relative;
        .song-info {
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            bottom: 0;
            top: 0;
            left: 0;
            padding-left: 3em;
            z-index: 2;
            color: white;

            right: 0;
            .genre {
                background: #fff;
                color: #000;
                display: inline-block;
                text-transform: uppercase;
                border-radius: 0.2rem;
                font-weight: 600;
                margin-right: 1em;
                font-size: 0.8em;
                padding: 0 0.6em;
            }
            .song-title {
                font-weight: bold;
                font-size: 4.5em;
                @media screen and (max-width: 1400px) {
                    font-size: 3em;
                }
            }
            .song-artist {
                font-weight: 400;
                font-size: 3.5em;
            }
        }
        .actions {
            position: absolute;
            top: 5%;
            z-index: 4;
            right: 4%;
            display: flex;
            align-items: center;
            & > * {
                padding: 0 0.3em;
            }
        }
        .overlay {
            position: absolute;
            bottom: 0;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1;
            background: rgb(0, 0, 0);
            background: linear-gradient(
                0deg,
                rgba(0, 0, 0, 0.9023984593837535) 0%,
                rgba(0, 0, 0, 0.6502976190476191) 33%,
                rgba(0, 0, 0, 0.3253676470588235) 100%
            );
        }
        .cover-background {
            width: 100%;
            transform: translateY(-20%);
            filter: blur(2px);
            @media screen and (max-width: 1000px) {
                transform: translateY(-3%);
            }
        }
    }
    .cover {
        width: 100px;
        border-radius: 15px;
        top: 20px;
        position: absolute;
        @media screen and (max-width: 550px) {
            display: none;
        }
    }
}
.player--dark {
    .song-page-container {
        .main {
            .play-button {
                .back-layer {
                    background-color: black;
                }
            }
        }
    }
}
</style>
