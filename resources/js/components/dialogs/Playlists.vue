<template>
    <dialog-wrapper @end="$emit('end')">
        <div class="add-playlist-wrapper">
            <div class="add-playlist-wrapper__header">
                <div class="card-title-medium">
                    {{$t('Choose Playlist')}}
                </div>
                <div class="buttons">
                    <v-btn
                        class="primary create"
                        small
                        rounded
                        @click="
                            $emit('end');
                            $router.push({
                                name: 'library.playlists',
                                params: { createNewPlaylist: true }
                            });
                        "
                    >
                        {{$t('New Playlist')}}
                    </v-btn>
                    <v-btn
                        class="secondary"
                        small
                        rounded
                        @click="$emit('end')"
                    >
                        {{$t('Cancel')}}
                    </v-btn>
                </div>
            </div>
            <div class="playlists-wrapper content-list-wrapper">
                <ul v-if="playlists && playlists.length">
                    <li
                        class="pointer"
                        v-for="(playlist, i) in playlists"
                        :key="i"
                        @click="addToPlaylist(playlist)"
                    >
                        <div class="item-cover">
                            <v-img
                                :src="playlist.cover"
                                class="img"
                                aspect-ratio="1"
                            >
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
                        <div class="item-title">{{ playlist.title }}</div>
                        <div class="checked options" v-if="playlist.checked">
                            <v-icon color="success">$vuetify.icons.check-circle</v-icon>
                        </div>
                    </li>
                </ul>

                <ul class="skeleton-list" v-else-if="!playlists">
                    <div class="skeleton" v-for="n in 5" :key="n">
                        <content-placeholders :rounded="true">
                            <content-placeholders-img />
                        </content-placeholders>
                        <content-placeholders :rounded="true">
                            <content-placeholders-img />
                        </content-placeholders>
                    </div>
                </ul>
                <empty-page
                    v-else
                    :headline="$t('No Playlists!')"
                    :sub="$t('Looks like you do not have any playlists yet.')"
                >
                    <template v-slot:button>
                        <v-btn
                            class="primary"
                            rounded
                            small
                            @click="
                                $emit('end');
                                $router.push({
                                    name: 'library.playlists',
                                    params: { createNewPlaylist: true }
                                });
                            "
                        >
                            {{$t('New Playlist')}}
                        </v-btn>
                    </template>
                </empty-page>
            </div>
        </div>
    </dialog-wrapper>
</template>
<script>
export default {
    created() {
        this.$store.dispatch("fetchPlaylists");
    },
    computed: {
        playlists() {
            return this.$store.getters.getPlaylists
                ? this.$store.getters.getPlaylists.own.map(playlist => {
                      return {
                          id: playlist.id,
                          cover: playlist.cover,
                          title: playlist.title,
                          checked: playlist.songs.some(
                              song =>
                                  song.id ==
                                  this.$store.getters.getAddSongToPlaylist
                          )
                      };
                  })
                : null;
        }
    },
    methods: {
        addToPlaylist(playlist) {
            if (playlist.checked) {
                this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Already There!"),
                    text: this.$t('Song already exists on this playlist!')
                });
            } else {
                this.$store
                    .dispatch("attachToPlaylist", {
                        playlist_id: playlist.id,
                        song_id: this.$store.getters.getAddSongToPlaylist.id,
                        song_origin: this.$store.getters.getAddSongToPlaylist.origin
                    })
                    .then(() => {
                        this.$notify({
                            group: "foo",
                            type: "success",
                            title: this.$t("Added"),
                            text: this.$t('Song added to playlist successfully.')
                        });
                        this.$store.dispatch("fetchPlaylists");
                        this.$emit("end");
                    });
            }
        }
    }
};
</script>
