<template>
    <div class="playlist-box-container content-item" v-if="playlist">
        <div class="content-item__header" @click="playPlaylist">
            <div class="cover-layer">
                <v-img :src="playlist.cover" aspect-ratio="1" class="img">
                    <template v-slot:placeholder>
                        <div class="song-expo-skeleton fill-height">
                            <content-placeholders :rounded="true">
                                <content-placeholders-img />
                            </content-placeholders>
                        </div>
                    </template>
                </v-img>
            </div>
            <slot name="control-layer" @click.stop="" v-if="admin"></slot>
            <div class="control-layer" v-else>
                <div class="buttons">
                    <button
                        class="button button-play mx-3"
                        @click.stop="playPlaylist"
                    >
                        <img
                            src="/svg/play-round.svg"
                            alt="next"
                            class="svg-image"
                        />
                    </button>
                </div>
            </div>
        </div>
        <div class="content-item__body">
            <router-link
                class="content-item__body__title__container"
                :to="{ name: 'playlist', params: { id: playlist.id } }"
            >
                <div class="content-item__body__type">
                    {{$t('Playlist')}}
                </div>
                <div class="content-item__body__title max-2-lines">
                    {{ playlist.title }}
                </div>
            </router-link>
            <div
                class="content-item__body__sub artist"
                v-if="playlist.user && playlist.created_by == 'user'"
            >
                {{$t('By')}}
                <router-link
                    :key="playlist.user.id"
                    :to="{ name: 'profile', params: { id: playlist.user.id } }"
                >
                    {{ playlist.user.name }}
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["playlist", "admin"],
    data() {
        return {
            showMenu: false
        };
    },
    methods: {
        playPlaylist() {
            if (this.playlist.songs.length) {
                this.$store.dispatch('playPlaylist', { playlist: this.playlist });
            } else {
                this.$notify({
                    group: "foo",
                    type:"warning",
                    title: this.$t("Empty!"),
                    text: this.$t("This playlist has no songs.")
                });
            }
        }
    }
};
</script>
