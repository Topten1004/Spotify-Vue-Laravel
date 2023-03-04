<template>
    <div class="podcast-container elevate-page" v-if="podcast">
        <div class="podcast__header">
            <div class="podcast__info">
                <div class="podcast__cover">
                    <v-img
                        :src="podcast.cover"
                        class="podcast__cover__img"
                        aspect-ratio="1"
                    >
                        <template v-slot:placeholder>
                            <div class="fixed-area__image-skeleton">
                                <content-placeholders :rounded="true">
                                    <content-placeholders-img />
                                </content-placeholders>
                            </div>
                        </template>
                    </v-img>
                </div>
                <div class="podcast__text">
                    <div class="info-badge">{{ $t("Podcast") }}</div>
                    <div class="podcast__text__title">{{ podcast.title }}</div>
                    <div class="" v-if="podcast.artist && podcast.artist.id">
                        <router-link
                            class="router-link d-flex pt-2 pb-5 mb-3 align-center"
                            :to="{
                                name: 'artist',
                                params: { id: podcast.artist.id }
                            }"
                            target="_blank"
                        >
                            <div class="avatar mr-2">
                                <v-avatar size="35">
                                    <v-img :src="podcast.artist.avatar"></v-img>
                                </v-avatar>
                            </div>
                            <div class="artist-name">
                                {{ podcast.artist.displayname }}
                            </div>
                        </router-link>
                    </div>
                    <div class="podcast__description-lg" v-html="podcast.description">
                    </div>
                </div>
            </div>
            <div class="podcast__description-sm" v-html="podcast.description">
            </div>
            <div class="podcast__actions">
                <div class="play-button">
                    <v-btn
                        color="primary"
                        rounded
                        block
                        :disabled="!podcast.episodes.length"
                        @click="$store.dispatch('playPodcast', { podcast })"
                    >
                        <v-icon left>$vuetify.icons.play</v-icon>
                        {{ $t("Play") }}
                    </v-btn>
                </div>
            </div>
        </div>
        <div class="podcast__body">
            <div class="podcast__episodes" v-if="podcast.episodes.length">
                <div class="podcast__title py-4">
                    {{ $t("All Episodes") }}
                </div>
                <div class="episodes">
                    <v-card
                        class="episode"
                        v-for="episode in podcast.episodes"
                        :key="episode.id"
                        @click="
                            $store.dispatch('playEpisode', {
                                episode,
                                reset: true
                            })
                        "
                    >
                        <div class="justify-between">
                            <div class="episode__cover">
                                <v-img
                                    class="episode__cover__img"
                                    :src="episode.cover"
                                    width="100px"
                                    aspect-ratio="1"
                                >
                                    <div
                                        class="dark-layer hide-above-699"
                                        v-if="
                                            $store.getters.getCurrentAudio &&
                                                $store.getters.getCurrentAudio
                                                    .id === episode.id &&
                                                $store.getters.getCurrentAudio
                                                    .podcast
                                        "
                                    >
                                        <div
                                            class="icon icon-inside-cover absolute fill"
                                        >
                                            <div
                                                class="epico_music-is-playing-container"
                                            >
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <template v-slot:placeholder>
                                        <div class="fixed-area__image-skeleton">
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img />
                                            </content-placeholders>
                                        </div>
                                    </template>
                                </v-img>
                            </div>
                            <div class="share-button hide-above-699">
                                <v-btn
                                    color="primary"
                                    rounded
                                    small
                                    @click.stop="shareEpisode(episode)"
                                >
                                    <v-icon left>$vuetify.icons.share-variant</v-icon>
                                    {{ $t("Share") }}
                                </v-btn>
                            </div>
                        </div>

                        <div class="episode__body">
                            <div class="episode-header">
                                <div class="infos">
                                    <div class="title">
                                      <div class="icon hide-under-700">
                                          <template
                                            v-if="
                                              $store.getters.getCurrentAudio &&
                                              $store.getters.getCurrentAudio.id === episode.id &&
                                              $store.getters.getCurrentAudio.podcast
                                            "
                                          >
                                            <div class="epico_music-is-playing-container">
                                              <span></span>
                                              <span></span>
                                              <span></span>
                                            </div>
                                          </template>
                                          <template v-else>
                                            <v-icon class="icon__access-point" large
                                              >$vuetify.icons.access-point</v-icon
                                            >
                                            <v-icon class="icon__play" large>$vuetify.icons.play</v-icon>
                                          </template>
                                        </div>
                                        <div class="title__title">
                                            {{ episode.title }}
                                        </div>
                                    </div>
                                    <div class="times">
                                        <div class="created_at">
                                            {{
                                                formatEpisodeDate(
                                                    episode.created_at
                                                )
                                            }}
                                        </div>
                                        <div class="duration">
                                            {{
                                                formatEpisodeDuration(
                                                    episode.duration
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="share-button hide-under-700">
                                    <v-btn
                                        color="primary"
                                        rounded
                                        small
                                        @click.stop="shareEpisode(episode)"
                                    >
                                        <v-icon left>$vuetify.icons.share-variant</v-icon>
                                        {{ $t("Share") }}
                                    </v-btn>
                                </div>
                            </div>
                            <div class="description" v-html="episode.description">
                            </div>
                        </div>
                    </v-card>
                </div>
            </div>
            <empty-page
                v-else
                :headline="$t('No Episodes!')"
                :sub="$t('Looks like this podcast has no episodes yet.')"
                img="peep-34.png"
            />
        </div>
    </div>
    <empty-page
        v-else-if="!podcast && errorStatus == 404"
        :headline="$t('Not Available!')"
        :sub="$t('Podcast') + '' + $t('does no exist.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)">{{
                $t("Go Back")
            }}</v-btn>
        </template>
    </empty-page>
    <empty-page
        v-else-if="!podcast && errorStatus"
        :headline="$t('Something wrong happend!')"
        :sub="$t('Some server error has occurred. Try again later.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)">{{
                $t("Go Back")
            }}</v-btn>
        </template>
    </empty-page>
    <page-loading v-else />
</template>
<script>
import moment from "moment";
export default {
    metaInfo() {
        return {
            title: this.generateMetaInfos(
                this.podcast,
                this.$store.getters.getSettings.podcastPageTitle,
                "podcast"
            ),
            meta: [
                {
                    name: "description",
                    content: this.generateMetaInfos(
                        this.podcast,
                        this.$store.getters.getSettings.podcastPageDescription,
                        "podcast"
                    )
                }
            ]
        };
    },
    data() {
        return {
            podcast: null,
            isFollowed: null,
            errorStatus: null
        };
    },
    created() {
        axios
            .get("/api/podcast/" + this.$route.params.id)
            .then(res => {
                this.podcast = res.data;
                if (this.$route.query.episode) {
                    const episode = this.podcast.episodes.find(
                        ep => ep.id == this.$route.query.episode
                    );
                    this.$store.dispatch("playEpisode", {
                        episode,
                        reset: true
                    });
                }
            })
            .catch(e => (this.errorStatus = e.response.status));
    },
    methods: {
        shareEpisode(episode) {
            const appUrl = this.$store.getters.getSettings.appUrl;
            this.$store.commit("shareItem", {
                cover: this.podcast.cover,
                url: appUrl +
                    (appUrl.substring(appUrl.length - 1) === "/"
                        ? 'podcast' + "/"
                        : "/" + 'podcast' + "/") +
                    this.podcast.id + '?episode=' + episode.id,
                title: episode.title,
                type: 'episode',
                artist: this.getMainArtist(this.podcast)
            });
        },
        formatEpisodeDate(date) {
            return moment(date)
                .fromNow()
                .match(/year/)
                ? moment(date).format("MMM YYYY")
                : moment(date).format("DD MMM");
        },
        formatEpisodeDuration(secs) {
            var minutes = Math.floor(secs / 60);
            secs = secs % 60;
            return minutes + " min" + " " + secs + " secs";
        },
        // follow() {
        //     if (this.isFollowed) {
        //         this.$store
        //             .dispatch("unfollowPodcast", this.podcast.id)
        //             .then(() => {
        //                 this.isFollowed = false;
        //             })
        //             .catch(() => {});
        //     } else {
        //         this.$store
        //             .dispatch("followPodcast", this.podcast.id)
        //             .then(() => {
        //                 this.isFollowed = true;
        //             })
        //             .catch(() => {});
        //     }
        // }
    }
};
</script>
<style lang="scss" scoped>
@import "../../../../sass/variables";

.podcast-container {
    padding: 2em;
    @media (max-width: 700px) {
      padding: 0.2em;
    }
  }
.podcast {
    &__header {
    }
    &__info {
        display: flex;
    }
    &__description-sm {
        padding-top: 1em;
        @media (min-width: 1400px) {
            display: none;
        }
    }
    &__description-lg {
        @media (max-width: 1401px) {
            display: none;
        }
    }
    &__title {
        font-size: 1.3em;
        font-weight: bold;
    }
    &__text {
        padding: 0.4em 1em;
        .info-badge {
            font-size: 0.8em;
            letter-spacing: 2px;
        }
        &__title {
            font-size: 2em;
            line-height: 2.3;
            @media (max-width: 700px) {
                font-size: 1.3em;
            }
        }
    }
    &__actions {
        display: flex;
        padding: 2em 0;
        & > * {
            margin-right: 2em;
        }
    }
    &__body {
    }
    &__cover {
        min-width: 250px;
        &__img {
            border-radius: 7px;
        }
        @media (max-width: 700px) {
            min-width: 200px;
        }
        @media (max-width: 500px) {
            min-width: 150px;
        }
    }
    &__episodes {
        .episodes {
            .episode {
                display: flex;
                @media (max-width: 700px) {
                    flex-direction: column;
                }
                cursor: pointer;
                background-color: $light-theme-panel-bg-color;
                // border: 1px solid rgba(31, 41, 90, 0.322);
                margin: 0 1em 1.6em 0;
                padding: 0.7em;
                // border-radius: 15px;
                &__cover {
                    padding: 0.3em;
                    &__img {
                        border-radius: 6px;
                    }
                }
                &__body {
                    padding: 0.2em 0.5em;
                    flex-grow: 1;
                }
                &-header {
                    display: flex;
                    .infos {
                        flex-grow: 999;
                        .title {
                            display: flex;
                            align-items: center;
                            padding-left: 2em;
                            position: relative;
                            @media (max-width: 700px) {
                                padding-left: 0em;
                            }
                            &__title {
                                font-size: 1.18rem;
                                font-weight: bold;
                            }
                        }
                        .times {
                            display: flex;
                            padding: 0.6em 0;
                            .created_at {
                                opacity: 0.8;
                                font-size: 0.8em;
                                margin-right: 0.6em;
                            }
                            .duration {
                                font-size: 0.8em;
                            }
                        }
                    }
                    .icons {
                        .icon-download {
                            width: 1.4em;
                        }
                    }
                }
                &-body {
                    padding: 0.4em 0.2em;
                    .description {
                        color: rgba(141, 141, 141, 0.719);
                    }
                }
                .icon {
                    width: 2em;
                    left: 0;
                    position: absolute;
                    &__play {
                        display: none;
                    }
                    @media (max-width: 700px) {
                        display: none;
                    }
                }
                .icon-inside-cover {
                    width: 100%;
                    left: 0;
                    display: flex;
                    align-items: center;
                    position: absolute;
                    justify-content: center;
                    .epico_music-is-playing-container {
                        margin-left: unset;
                    }
                    .epico_music-is-playing-container > span {
                        width: 6px;
                        background-color: white;
                        margin-top: auto;
                        height: 80%;
                    }

                    @media (min-width: 699px) {
                        display: none;
                    }
                }
                &:hover {
                    .icon__access-point {
                        display: none;
                    }
                    .icon__play {
                        display: initial;
                    }
                }
            }
        }
    }
}

.player--dark {
    .podcast-main-wrapper {
        .episodes {
            .episode {
                background-color: $dark-theme-panel-bg-color;
            }
        }
    }
}

.hide-under-700 {
    @media (max-width: 700px) {
        display: none;
    }
}

.hide-above-699 {
    @media (min-width: 699px) {
        display: none;
    }
}
</style>
