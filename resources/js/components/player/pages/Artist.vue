<template>
    <fixed-left class="artist" v-if="artist">
        <template v-slot:img>
            <div class="img-cover">
                <v-img :src="artist.avatar" class="img" aspect-ratio="1">
                    <template v-slot:placeholder>
                        <div class="fixed-area__image-skeleton">
                            <content-placeholders :rounded="true">
                                <content-placeholders-img />
                            </content-placeholders>
                        </div>
                    </template>
                </v-img>
            </div>
        </template>
        <template v-slot:infos>
            <div class="info-wrapper">
                <div class="title">
                    <div class="info-badge">{{ $t("ARTIST") }}</div>
                    <div>{{ artist.displayname }}</div>
                </div>
            </div>
            <!-- <div class="stats">
                <span class="followers"
                    >{{ artist.nb_followers }} {{ $t("Followers") }}
                </span>
            </div> -->
        </template>
        <template v-slot:buttons>
            <div class="buttons">
                <div class="play-button">
                    <v-btn
                        class="primary"
                        rounded
                        small
                        @click="
                            $store.dispatch('updateQueue', {
                                content: artist.top_tracks,
                                reset: true
                            })
                        "
                    >
                        <v-icon left>$vuetify.icons.play</v-icon>
                        {{ $t("Play") }}
                    </v-btn>
                </div>
                <div class="follow-button" v-if="isFollowed !== null">
                    <v-btn
                        class="secondary"
                        rounded
                        small
                        @click="follow()"
                        v-if="isFollowed"
                    >
                        {{ $t("Following") }}
                    </v-btn>
                    <v-btn
                        class="primary"
                        rounded
                        small
                        @click="follow()"
                        v-else
                    >
                        {{ $t("Follow") }}
                    </v-btn>
                </div>
            </div>
            <div class="pt-4">
                <external-links :content="artist"></external-links>
            </div>
        </template>
        <template v-slot:main>
            <div
                class="profile-main-content"
                v-if="artist.top_tracks || artist.albums || artist.latest"
            >
                <div class="latest" v-if="artist.latest">
                    <div class="card-title-medium">
                        {{ $t("Latest Song") }}
                    </div>
                    <div class="content">
                        <song-encap
                            :song="artist.latest"
                            @song="$store.commit('updatePlaylist', [$event])"
                        ></song-encap>
                    </div>
                </div>
                <div
                    class="popular-songs"
                    v-if="artist.top_tracks && artist.top_tracks.length"
                >
                    <div class="card-title-medium">
                        {{ $t("Popular Songs") }}
                    </div>
                    <List
                        :isAlbum="true"
                        :content="artist.top_tracks"
                        class="song-list"
                        headers="true"
                        ranked="true"
                    />
                    <!-- <song-list :songs="popularSpliced"></song-list> -->
                    <!-- <div class="more-or-less">
                        <div
                            class="more"
                            @click="nPopular += 5"
                            v-if="nPopular < artist.top_tracks.length"
                        >
                            {{ $t("More") }}
                        </div>
                    </div> -->
                </div>
                <div
                    class="albums"
                    v-if="artist.albums && artist.albums.length"
                >
                    <div class="card-title-medium">
                        {{ $t("Albums") }}
                    </div>
                    <div class="content">
                        <swiper-section
                            :content="artist.albums"
                            :_breakpoints="albumsSwiperBreakpoints"
                        ></swiper-section>
                    </div>
                </div>
                <!-- <div class="singles" v-if="artist.songs.length">
                    <div class="card-title-medium">
                        {{$t('Singles')}}
                    </div>
                    <div class="content">
                        <song-list :songs="singlesSpliced"></song-list>
                    </div>
                    <div class="more-or-less">
                        <div
                            class="more"
                            @click="nSingles += 5"
                            v-if="nSingles < artist.songs.length"
                        >
                            {{$t('More')}}
                        </div>
                    </div>
                </div> -->
            </div>
            <template v-else>
                <empty-page
                    img="peep-68.png"
                    :headline="$t('This artist profile is empty.')"
                    :sub="$t('Looks like this account is still new!')"
                >
                </empty-page>
            </template>
        </template>
    </fixed-left>
    <empty-page
        v-else-if="!artist && errorStatus == 404"
        :headline="$t('Not Available!')"
        sub="Artist does not exist!"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" small rounded @click="$router.go(-1)">
                {{ $t("Go back") }}
            </v-btn>
        </template>
    </empty-page>
    <empty-page
        v-else-if="!artist && errorStatus"
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
import SwiperSection from "../../elements/sections/SwiperSection.vue";
export default {
    components: {
        SwiperSection
    },
    metaInfo() {
        return {
            title:
                this.title.slice(0, 1).toUpperCase() +
                this.title.slice(1, this.title.length),
            meta: [
                {
                    name: "description",
                    content:
                        this.description.slice(0, 1).toUpperCase() +
                        this.description.slice(1, this.description.length)
                }
            ]
        };
    },
    data() {
        return {
            artist: null,
            isFollowed: null,
            nPopular: 5,
            nSingles: 5,
            errorStatus: null,
            albumsSwiperBreakpoints: {
                1500: {
                    slidesPerView: 5,
                    slidesPerGroup: 5,
                    spaceBetweenSlides: 20
                },
                1230: {
                    slidesPerView: 4,
                    slidesPerGroup: 4,
                    spaceBetweenSlides: 20
                },
                1050: {
                    slidesPerView: 3,
                    slidesPerGroup: 3,
                    spaceBetweenSlides: 10
                },
                500: {
                    slidesPerView: 2,
                    slidesPerGroup: 2,
                    spaceBetweenSlides: 10
                }
            }
        };
    },
    computed: {
        popularSpliced() {
            if (this.artist.top_tracks !== 5) {
                return this.artist.top_tracks.slice(0, this.nPopular);
            } else {
                return this.artist.top_tracks;
            }
        },
        singlesSpliced() {
            if (this.artist.songs !== 5) {
                return this.artist.songs.slice(0, this.nSingles);
            } else {
                return this.artist.songs;
            }
        },
        title() {
            if (this.artist) {
                return this.$store.getters.getSettings.artistPageTitle
                    .replace(
                        "%site_name",
                        this.$store.getters.getSettings.appName
                    )
                    .replace("%artist_name", this.artist.displayname)
                    .replace(
                        "%creation_date",
                        this.moment(this.artist.created_at).format("ll")
                    )
                    .replace(/(—|-)\W+/, function(c) {
                        if (c.length >= 3) return c.replace(/(—|-)/, "");
                        else return c;
                    });
            } else {
                return ''
            }
        },
        description() {
            if (this.artist) {
                return this.$store.getters.getSettings.artistPageDescription
                    .replace(
                        "%site_name",
                        this.$store.getters.getSettings.appName
                    )
                    .replace("%artist_name", this.artist.displayname)
                    .replace(
                        "%creation_date",
                        this.moment(this.artist.created_at).format("ll")
                    )
                    .replace(/(—|-)\W+/, function(c) {
                        if (c.length >= 3) return c.replace(/(—|-)/, "");
                        else return c;
                    });
            } else {
                return ''
            }
        }
    },
    created() {
        axios
            .get("/api/artist/" + this.$route.params.id)
            .then(res => (this.artist = res.data))
            .then(() => {
                this.$store
                    .dispatch("isArtistFollowed", this.artist.id)
                    .then(res => (this.isFollowed = res));
            })
            .catch(e => (this.errorStatus = e.response.status));
    },
    methods: {
        follow() {
            if (this.isFollowed) {
                this.$store
                    .dispatch("unfollow", this.artist)
                    .then(() => {
                        this.isFollowed = false;
                        this.artist.nb_followers--;
                    })
                    .catch(() => {});
            } else {
                this.$store
                    .dispatch("follow", this.artist)
                    .then(() => {
                        this.isFollowed = true;
                        this.artist.nb_followers++;
                    })
                    .catch(() => {});
            }
        }
    }
};
</script>
<style lang="scss">
.singles img {
    max-width: 50px;
}
.latest > * {
    max-width: 11rem;
}

.singles,
.latest,
.popular-songs,
.albums {
    margin-bottom: 2rem;
}
</style>
