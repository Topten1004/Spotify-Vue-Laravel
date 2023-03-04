<template>
    <div class="content-list-wrapper">
        <ul v-if="songs">
            <li v-if="headers">
                <div class="rank" v-if="ranked">
                    #
                </div>

                <div class="list-item-cover mr-5" v-if="songs.length">
                    {{$t('Title')}}
                </div>
            </li>
            <li
                class="item-list__item relative"
                v-for="(song, i) in songs"
                :key="i"
                @click="$store.dispatch('playSong', { song, reset: true })"
                @contextmenu.prevent.stop="
                    $store.commit('setSongContextMenu', compid + i)
                "
            >
                <abs-menu
                    v-if="$store.getters.getSongContextMenu == compid + i"
                    :style="{ top: '24px', right: 0 }"
                >
                    <song-menu
                        :song="song"
                        @close="$store.commit('setSongContextMenu', null)"
                    ></song-menu>
                </abs-menu>
                <div class="rank" v-if="ranked">
                    <v-icon class="play-icon" small>$vuetify.icons.play</v-icon>
                    <span class="rank__rank">{{ i + 1 }}</span>
                </div>
                <div class="item-cover">
                    <v-img :src="song.cover" class="img" aspect-ratio="1">
                        <template v-slot:placeholder>
                            <v-row
                                class="fill-height ma-0"
                                align="center"
                                justify="center"
                            >
                                <content-placeholders
                                    :rounded="true"
                                    style="width: 100%"
                                >
                                    <content-placeholders-img />
                                </content-placeholders>
                            </v-row>
                        </template>
                    </v-img>
                </div>
                <div class="item-info">
                    <div
                        class="item-title router-link"
                        @click.stop="
                            $router.push({
                                name: 'song',
                                params: { id: song.id }
                            })
                        "
                    >
                        {{ song.title }}
                        <v-icon v-if="song.youtube_id" small
                            >$vuetify.icons.youtube</v-icon
                        >
                    </div>
                    <div
                        class="epico_music-is-playing-container"
                        v-if="
                            $store.getters.getCurrentAudio &&
                                $store.getters.getCurrentAudio.id === song.id
                        "
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div class="item-artist">
                    <router-link
                        class="router-link"
                        :to="{ name: 'artist', params: { id: song.artist_id } }"
                        v-if="song.artist_id"
                    >
                        {{ song.artist_name }}
                    </router-link>
                    <span v-else>{{ song.artist_name }}</span>
                </div>
                <div class="duration small mr-3">
                    <template v-if="!song.youtube_id">
                        {{ mmss(song.duration) }}
                    </template>
                    <template v-else>
                        --:--
                    </template>
                </div>
                <div class="custom-options">
                    <div
                        class="delete-button"
                        :title="$t('Remove song from playlist')"
                        @click.stop="detachSong(song.id, song.title)"
                        v-if="mine"
                    >
                        <v-icon color="danger" class="pointer" small
                            >$vuetify.icons.delete</v-icon
                        >
                    </div>
                    <div class="like-button">
                        <div
                            class="button-svg-container"
                            v-if="isLiked(song.id)"
                            @click.stop="like(song.id)"
                        >
                            <v-icon color="primary" class="pointer" small
                                >$vuetify.icons.heart</v-icon
                            >
                        </div>
                        <div
                            class="button-svg-container"
                            v-else
                            @click.stop="like(song.id)"
                        >
                            <v-icon class="pointer" small
                                >$vuetify.icons.heart-outline</v-icon
                            >
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <ul v-else>
            <div class="skeleton" v-for="n in 10" :key="n">
                <content-placeholders :rounded="true">
                    <content-placeholders-img />
                </content-placeholders>
                <content-placeholders :rounded="true">
                    <content-placeholders-img />
                </content-placeholders>
            </div>
        </ul>
    </div>
</template>
<script>
export default {
    props: ["songs", "playlist_id", "mine", "ranked", "headers"],
    computed: {
        isLiked() {
            return function(id) {
                return this.$store.getters.isLiked(id);
            };
        }
    },
    data() {
        return {
            compid: Math.floor(Math.random(50 * 50) * 5000)
        };
    },
    methods: {
        like(song_id) {
            if (this.isLiked(song_id)) {
                this.$store.dispatch("dislike", song_id);
            } else {
                this.$store.dispatch("like", song_id);
            }
        },
        detachSong(song_id, song_title) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna remove this song from this playlist?")}`,
                button: {
                    no: this.$t('No'),
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        this.$store
                            .dispatch("detachFromPlaylist", {
                                song_id,
                                playlist_id: this.playlist_id
                            })
                            .then(res => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Song Removed"),
                                    text:
                                        song_title + " " + 
                                        this.$t("has been removed from playlist successfully.")
                                });
                                this.$emit("deleted", song_id);
                            });
                    }
                }
            });
        },
        changePrivacy(song) {
            if (song.private == 1) {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna make this song public? This means that it's gonna be visible on your profile.")}`,
                    button: {
                        no: this.$t('No'),
                        yes: this.$t("Yes")
                    },
                    /**
                     * Callback Function
                     * @param {Boolean} confirm
                     */
                    callback: confirm => {
                        if (confirm) {
                            this.$store.dispatch("make_song_public", song.id);
                        }
                    }
                });
            } else {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna make this song private? This means that it's gonna be visible only for you.")}`,
                    button: {
                        no: this.$t('No'),
                        yes: this.$t("Yes")
                    },
                    /**
                     * Callback Function
                     * @param {Boolean} confirm
                     */
                    callback: confirm => {
                        if (confirm) {
                            this.$store.dispatch("make_song_private", song.id);
                        }
                    }
                });
            }
        }
    }
};
</script>
