<template>
    <div class="playlists-wrapper">
        <div class="header">
            <div class="content-text">
                <div class="title">
                    <selectComp :options="options" @select="filter($event)" />
                </div>
            </div>
            <div class="buttons">
                <v-btn
                    class="primary"
                    small
                    rounded
                    @click="createPlaylist = true"
                >
                    {{$t('Create Playlist')}}
                </v-btn>
            </div>
        </div>
        <div class="content content-list-wrapper">
            <template v-if="$store.getters.getPlaylists">
                <ul v-if="currentPagePlaylists && currentPagePlaylists.length" class="item-list">
                    <li
                        v-for="(playlist, i) in currentPagePlaylists"
                        :key="i"
                        @click="
                            $router.push({
                                name: 'playlist',
                                params: { id: playlist.id }
                            })
                        "
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
                        <div
                            class="options"
                            v-if="playlist.user.id == $store.getters.getUser.id"
                        >
                            <div
                                class="private"
                                v-if="hasPermission('Publish playlists')"
                                @click.stop="changePrivacy(playlist)"
                            >
                                <button
                                    class="button-with-img-inside warning"
                                    v-if="!playlist.public"
                                >
                                    <div class="image">
                                        <img
                                            src="/svg/lock-rounded.svg"
                                            alt=""
                                            class="svg-image"
                                            :title="$t('Make Private')"
                                        />
                                    </div>
                                    <div class="content-text">
                                        {{$t('Make Public')}}
                                    </div>
                                </button>
                                <button
                                    class="button-with-img-inside success"
                                    v-else
                                >
                                    <div class="image">
                                        <img
                                            src="/svg/lock-rounded-open.svg"
                                            alt=""
                                            class="svg-image"
                                            :title="$t('make public')"
                                        />
                                    </div>
                                    <div class="content-text">
                                        {{$t('Make Private')}}
                                    </div>
                                </button>
                            </div>
                            <div
                                class="delete-button"
                                @click.stop="
                                    deletePlaylist(playlist.id, playlist.title)
                                "
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
                    </li>
                </ul>
                <empty-page
                    v-else
                    :headline="$t('No Playlists!')"
                    :sub="$t('This page is empty.')
                    "
                    img="peep-68.png"
                >
                    <template
                        v-slot:button
                        v-if="page != 'My followed Playlists'"
                    >
                        <v-btn
                            rounded
                            small
                            class="primary"
                            @click="createPlaylist = true"
                        >
                            {{$t('Create Playlist')}}
                        </v-btn>
                    </template>
                </empty-page>
            </template>
            <template v-else>
                <ul class="skeleton-list">
                    <div class="skeleton" v-for="n in 10" :key="n">
                        <content-placeholders :rounded="true">
                            <content-placeholders-img />
                        </content-placeholders>
                        <content-placeholders :rounded="true">
                            <content-placeholders-img />
                        </content-placeholders>
                    </div>
                </ul>
            </template>
        </div>
        <add-playlist
            v-if="createPlaylist"
            @success="playlistCreated"
            @cancel="createPlaylist = false"
        ></add-playlist>
    </div>
</template>
<script>
import addPlaylist from "../../../../dialogs/Createplaylist";
import SelectComp from "../../../../elements/Select";
export default {
    props: ["createNewPlaylist"],
    components: {
        addPlaylist,
        SelectComp
    },
    created() {
        this.$store
            .dispatch("fetchPlaylists")
            .then(() => (this.mutatedPlaylists = this.playlists));
        if (this.createNewPlaylist) {
            this.createPlaylist = true;
        }
    },
    data() {
        return {
            createPlaylist: false,
            mutatedPlaylists: null,
            options: [
                {
                    value: "My Playlists",
                    text: this.$t("My Playlists")
                },
                {
                    value: "My followed Playlists",
                    text: this.$t("My followed Playlists")
                }
            ],
            page: "My Playlists"
        };
    },
    computed: {
        followedPlaylists() {
            return this.$store.getters.getPlaylists.followed;
        },
        ownPlaylists() {
            return this.$store.getters.getPlaylists.own;
        },
        currentPagePlaylists() {
            if (this.page === "My followed Playlists") {
                return this.$store.getters.getPlaylists.followed;
            } else if (this.page === "My Playlists") {
                return this.$store.getters.getPlaylists.own;
            }
        }
    },
    methods: {
        filter(option) {
            this.page = option;
        },
        deletePlaylist(playlist_id, playlist_title) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this playlist?")}`,
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
                            .dispatch("delete_user_playlist", playlist_id)
                            .then(() => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text: this.$t("Playlist") + " " + this.$t("Deleted") +  "."
                                });
                            });
                    }
                }
            });
        },
        playlistCreated() {
            this.createPlaylist = false;
        },
        changePrivacy(playlist) {
            if (!playlist.public) {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna make this playlist public? This means that it's gonna be visible on your profile.")}`,
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
                            this.$store.dispatch(
                                "make_playlist_public",
                                playlist.id
                            );
                        }
                    }
                });
            } else {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna make this playlist private? This means that it's gonna be visible only for you.")}`,
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
                            this.$store.dispatch(
                                "make_playlist_private",
                                playlist.id
                            );
                        }
                    }
                });
            }
        }
    }
};
</script>
<style lang="scss" scoped>
.playlists-wrapper {
    padding: 0 1rem;
    @media screen and( max-width: 600px) {
        padding-top: 2em;
    }
}
</style>
