<template>
    <div class="songs-wrapper">
        <v-card outlined>
            <v-card-title>
                <v-icon color="primary" x-large>$vuetify.icons.music-note</v-icon>
                <v-btn
                    class="mx-2"
                    dark
                    small
                    color="primary"
                    @click="editSong('new')"
                >
                    <v-icon>$vuetify.icons.plus</v-icon>
                    {{ $t("New") }}
                </v-btn>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <div class="admin-search-bar">
                    <v-text-field
                        v-model="search"
                        append-icon="mdi-magnify"
                        :label="$t('Search')"
                        single-line
                        hide-details
                    ></v-text-field>
                </div>
            </v-card-title>
            <v-data-table
                :no-data-text="$t('No data available')"
                :loading-text="$t('Fetching data') + '...'"
                :headers="headers"
                :items="songs || []"
                :loading="!songs"
                :items-per-page="25"
                :search="search"
                class="elevation-1"
            >
                <template v-slot:item.cover="{ item }">
                    <div class="img-container py-2">
                        <v-img
                            :src="
                                (item.cover && item.cover.image) || item.cover
                            "
                            :alt="item.title"
                            class="user-avatar song-cover"
                            width="50"
                            height="50"
                        >
                            <div
                                class="upload-percentage"
                                v-if="
                                    item.progress != null && item.progress < 100
                                "
                            >
                                <div class="content-text">
                                    <template v-if="item.progress < 99">
                                        {{ item.progress }}%
                                    </template>
                                    <template v-else>
                                        <v-progress-circular
                                            :size="15"
                                            :width="3"
                                            color="grey"
                                            indeterminate
                                        ></v-progress-circular>
                                    </template>
                                </div>
                            </div>
                        </v-img>
                    </div>
                </template>
                <template v-slot:item.title="{ item }">
                    <router-link
                        class="router-link"
                        :to="{ name: 'song', params: { id: item.id } }"
                        target="_blank"
                    >
                        {{ item.title }}<v-icon x-small class="mb-1 ml-1">$vuetify.icons.open-in-new</v-icon>
                    </router-link>
                </template>
                <template v-slot:item.operations="{ item }">
                    <v-btn
                        @click="editSong(item)"
                        x-small
                        fab
                        dark
                        color="info"
                    >
                        <v-icon
                            :dark="
                                $store.state.darkTheme ||
                                    $store.getters.getSettings.defaultTheme ==
                                        'dark'
                            "
                            >$vuetify.icons.pencil</v-icon
                        >
                    </v-btn>
                    <v-btn
                        @click="deleteSong(item.id)"
                        x-small
                        fab
                        dark
                        color="error"
                    >
                        <v-icon>$vuetify.icons.delete</v-icon>
                    </v-btn>
                </template>
                <template v-slot:item.artists="{ item }">
                    <artists :showLinker="true" :artists="item.artists"></artists>
                </template>
                <template v-slot:item.artist="{ item }">
                    <template v-if="item.artist">
                        <router-link
                            class="router-link"
                            :to="{
                                name: 'artist',
                                params: { id: item.artist.id }
                            }"
                            target="_blank"
                        >
                            <div class="avatar">
                                <v-avatar size="35">
                                    <v-img :src="item.artist.avatar"></v-img>
                                </v-avatar>
                            </div>
                            <div class="artist-name">
                                {{ item.artist.displayname }} <v-icon x-small class="mb-1 ml-1">$vuetify.icons.open-in-new</v-icon>
                            </div>
                        </router-link>
                    </template>
                    <template v-else> - </template>
                </template>
                <template v-slot:item.created_at="{ item }">
                    {{ moment(item.created_at).format("ll") }}
                </template>
            </v-data-table>
        </v-card>
        <v-dialog
            persistent
            v-model="editDialog"
            max-width="950"
            @click:outside="hideAllsongs"
        >
            <template v-for="song in songs">
                <edit-song-dialog
                    :key="song.id"
                    uploader="admin"
                    v-if="song.isActive"
                    v-show="isShowing(song.id)"
                    @updated="songEdited(song.id)"
                    @progress="updateProgress($event, song.id)"
                    @created="songCreated"
                    @close="closeSong(song.id)"
                    @sleep="sleepSongDialog(song.id)"
                    @wake="wakeSongDialog(song.id)"
                    :song="song"
                />
            </template>
        </v-dialog>
    </div>
</template>
<script>
import editSongDialog from "../../dialogs/edit/Song";
export default {
    components: {
        editSongDialog
    },
    computed: {
        isShowing() {
            return song_id =>
                this.songs[this.songs.findIndex(song => song.id === song_id)]
                    .isShowing;
        }
    },
    data() {
        return {
            songs: null,
            search: "",
            headers: [
                {
                    text: this.$t("Cover"),
                    align: "start",
                    sortable: false,
                    value: "cover"
                },
                { text: this.$t("Title"), value: "title" },
                {
                    text: this.$t("Artist") + "(" + this.$t("Account") + ")",
                    value: "artist",
                    align: "center"
                },
                { text: this.$t("Artists"), value: "artists" },
                { text: this.$t("Plays"), value: "nb_plays", align: "center" },
                {
                    text: this.$t("Downloads"),
                    value: "nb_downloads",
                    align: "center"
                },
                { text: this.$t("Likes"), value: "nb_likes", align: "center" },
                { text: this.$t("Created At"), value: "created_at" },
                {
                    text: this.$t("Operations"),
                    value: "operations",
                    align: "center"
                }
            ],
            editDialog: false,
            editingSong: null
        };
    },
    created() {
        this.fetchSongs();
    },
    methods: {
        fetchSongs() {
            axios.get("/api/admin/songs").then(res => {
                this.songs = res.data;
            });
        },
        wakeSongDialog(song_id) {
            this.editDialog = true;
            let index = this.songs.findIndex(song => song.id === song_id);
            this.$set(this.songs[index], "isShowing", true);
        },
        sleepSongDialog(song_id) {
            this.editDialog = false;
            let index = this.songs.findIndex(song => song.id === song_id);
            this.$set(this.songs[index], "isShowing", false);
            this.$forceUpdate();
        },
        closeSong(song_id) {
            let index = this.songs.findIndex(song => song.id === song_id);
            if (this.songs[index].isShowing) {
                this.editDialog = false;
            }
            if (this.songs[index].new) {
                this.songs.splice(index, 1);
            }
            this.$set(this.songs[index], "isActive", false);
            this.$set(this.songs[index], "isShowing", false);
            this.$forceUpdate();
        },
        hideAllsongs() {
            for (let i = 0; i < this.songs.length; i++) {
                this.$set(this.songs[i], "isShowing", false);
            }
            this.editDialog = false;
        },
        updateProgress(progress, song_id) {
            let index = this.songs.findIndex(song => song.id === song_id);
            this.$set(this.songs[index], "progress", progress);
        },
        deleteSong(song_id) {
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
                        let index = this.songs.findIndex(
                            song => song.id === song_id
                        );
                        if (this.songs[index].requestSource) {
                            this.songs[index].requestSource.cancel();
                        }
                        axios
                            .delete("/api/songs/" + song_id, {
                                song_id
                            })
                            .then(() => {
                                this.songs.splice(index, 1)
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text:
                                        this.$t("Song") +
                                        " " +
                                        this.$t("Deleted") +
                                        "."
                                });
                            })
                            .catch(() => {})
                    }
                }
            });
        },
        editSong(song) {
            if (song === "new") {
                this.songs.unshift({
                    id: Math.floor(Math.random() * (100000 - 5000) + 100000),
                    new: true,
                    artists: [],
                    cover: "/storage/defaults/images/song_cover.png",
                    genres: [],
                    isActive: true,
                    isShowing: true,
                    nb_plays: 0,
                    nb_likes: 0,
                    public: true,
                    source: "",
                    source_format: "file"
                });
                this.editDialog = true;
            } else {
                this.songs[
                    this.songs.findIndex(t => t.id === song.id)
                ].isActive = true;
                this.songs[
                    this.songs.findIndex(t => t.id === song.id)
                ].isShowing = true;
                this.editDialog = true;
            }
        },
        songEdited(song_id) {
            let index = this.songs.findIndex(song => song.id === song_id);
            this.$set(this.songs[index], "progress", 0);
            this.$set(this.songs[index], "isActive", false);
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Saved"),
                text: this.$t("Song") + " " + this.$t("Updated") + "."
            });
        },
        songCreated() {
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Created"),
                text: this.$t("Song") + " " + this.$t("Created") + "."
            });
        }
    }
};
</script>
