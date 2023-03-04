<template>
    <div class="content-list-wrapper">
        <ul v-if="songs">
            <li v-if="headers">
                <div class="rank" v-if="ranked">
                    #
                </div>

                <div class="" v-if="songs.length">
                    {{$t('Title')}}
                </div>
            </li>
            <li
                v-for="(song, i) in songs"
                :key="i"
                class="relative list-li"
                @click="$store.dispatch('playSong', { song, reset: true })"
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

                                <div class="img-container py-2 relative">
                    <v-img
                        :src="song.cover"
                        :alt="song.title"
                        class="table-cover"
                        width="50"
                        height="50"
                    >
                        <span
                            class="play-icon"
                            :class="{
                                hidden: $store.getters.isCurrentlyPlaying(song)
                            }"
                        >
                            <v-icon dark class="i"
                                >$veutify.icons.play-circle</v-icon
                            >
                        </span>
                        <div
                            class="dark-layer absolute fill justify-align-center"
                            v-if="$store.getters.isCurrentlyPlaying(song)"
                        >
                            <div
                                class="epico_music-is-playing-container white-bars"
                            >
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </v-img>
                </div>

                <div class="item-info ml-4 relative">
                    <div
                        class="song-title router-link  text-overflow-ellipsis pointer"
                        @click.stop="
                            $router.push({
                                name: 'song',
                                params: { id: song.id }
                            })
                        "
                    >
                        {{ song.title }}
                        <v-icon
                            small
                            v-if="
                                song.youtube_id &&
                                    $store.getters.getSettings.allowVideos
                            "
                            >$vuetify.icons.youtube</v-icon
                        >
                    </div>
                </div>
                <div
                class="menu-button"
                @click="$store.commit('setSongMenu', 'song-' + song.id)"
            >
                <song-menu
                    :item="song"
                    :closeOnContentClick="true"
                    @close="$store.commit('setSongMenu', null)"
                ></song-menu>
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
                <div class="options">
                    <div class="like-button" v-if="options.includes('like')">
                        <div
                            class="button-svg-container"
                            v-if="isLiked(song.id)"
                            @click.stop="like(song)"
                        >
                            <v-icon color="primary" class="pointer" small
                                >$vuetify.icons.heart</v-icon
                            >
                        </div>
                        <div
                            class="button-svg-container"
                            v-else
                            @click.stop="like(song)"
                        >
                            <v-icon class="pointer" small
                                >$vuetify.icons.heart-outline</v-icon
                            >
                        </div>
                    </div>
                        <div class="custom-options">
                            <div
                                v-if="hasPermission('Publish songs') && options.includes('privacy')"
                                class="private"
                                @click.stop="changePrivacy(song)"
                            >
                                <button
                                    class="button-with-img-inside warning"
                                    v-if="!song.public"
                                    :title="$t('Make Public')"
                                >
                                    <div class="image">
                                        <img
                                            src="/svg/lock-rounded.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="content-text">
                                        {{$t('Private')}}
                                    </div>
                                </button>
                                <button
                                    class="button-with-img-inside success"
                                    :title="$t('Make Private')"
                                    v-else
                                >
                                    <div class="image">
                                        <img
                                            src="/svg/lock-rounded-open.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="content-text">
                                        {{$t('Public')}}
                                    </div>
                                </button>
                            </div>
                            <div
                                v-if="
                                    hasPermission('Download songs') &&
                                    (song.source_format === 'file'  || song.source_format === 'audio_url' )&&
                                    options.includes('download') 
                                "
                                class="download"
                                @click.stop="download(song)"
                            >
                                <button
                                    class="button-with-img-inside primary"
                                    :disabled="isLoading === song.id"
                                >
                                    <div class="image">
                                        <v-progress-circular
                                            indeterminate
                                            :size="15"
                                            class="progress-circle"
                                            :width="3"
                                            color="primary"
                                            v-if="isLoading === song.id"
                                        ></v-progress-circular>
                                        <img
                                            v-else
                                            src="/svg/download.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="content-text">
                                        {{$t('Download')}}
                                    </div>
                                </button>
                            </div>
                            <div
                                class="delete-button"
                                v-if="options.includes('delete')"
                                @click.stop="deleteSong(song.id, song.title)"
                            >
                                <button class="button-with-img-inside danger">
                                    <div class="image">
                                        <img
                                            src="/svg/trash-can.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="content-text">
                                        {{$t('Delete')}}
                                    </div>
                                </button>
                            </div>
                        </div>
                </div>
            </li>
        </ul>
        <ul class="skeleton-list" v-else>
            <div class="skeleton" v-for="n in 7" :key="n">
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
    props: ["songs", "options", "ranked", "headers"],
    data() {
        return {
            compid: Math.floor(Math.random(50 * 50) * 5000),
            isLoading: null
        };
    },
    methods: {
        isLiked(song_id) {
            return (this.$store.getters.getLikedSongs || []).some(x =>
                x ? x.id === song_id : false
            );
        },
        like(song) {
            if (this.isLiked(song.id)) {
                this.$store.dispatch("dislike", song);
            } else {
                this.$store.dispatch("like", song);
            }
        },
        download(song) {
            this.isLoading = song.id;
            this.$store
                .dispatch("downloadAudio", song)
                .finally(() => {
                    this.isLoading = null;
                });
        },
        deleteSong(song_id) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this") + " " + this.$t("Song") + "?"}`,
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
                            .dispatch("delete_user_song", song_id)
                            .then(res => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Song Deleted"),
                                    text: this.$t("Song has been deleted successfully.")
                                });
                            });
                    }
                }
            });
        },
        changePrivacy(song) {
            if (song.public == 1) {
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
            } else {
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
            }
        }
    }
};
</script>
