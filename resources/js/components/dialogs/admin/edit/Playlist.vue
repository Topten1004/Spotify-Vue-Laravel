<template>
    <edit-dialog
        @callToAction="savePlaylist"
        @cancel="closeWindow"
        :loading="isLoading"
        editing="Playlist"
    >
        <template v-slot:body>
            <v-container>
                <v-row>
                    <v-col cols="auto">
                        <upload-image
                            @imageReady="imageReady($event)"
                            :id="'playlist' + editedPlaylist.id"
                            :source="editedPlaylist.cover || defaultCoverPath"
                        />
                    </v-col>
                    <v-col>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                       :label="$t('Title')"
                                        v-model="editedPlaylist.title"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-switch
                                       :label="$t('Public')"
                                        v-model="editedPlaylist.public"
                                    ></v-switch>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-col>
                </v-row>
            </v-container>
            <v-container class="songs" v-if="!editedPlaylist.new">
                <div class="songs-grid">
                    <v-card outlined>
                        <v-card-title>
                            <v-icon color="primary" x-large
                                >$vuetify.icons.music-note</v-icon
                            >
                            <div class="large-icon-text tertiary--text">
                                {{$t('Songs')}}
                            </div>
                            <v-btn
                                class="mx-2 ml-3"
                                dark
                                small
                                color="primary"
                                @click="addSong = true"
                            >
                                <v-icon dark>$vuetify.icons.link</v-icon>
                                {{$t('Attach')}}
                            </v-btn>
                        </v-card-title>
                        <v-data-table
                            :no-data-text="$t('No data available')"
                            :loading-text="$t('Fetching data') + '...'"
                            :headers="episodeTableHeaders"
                            :items="editedPlaylist.songs"
                            hide-default-footer
                            :items-per-page="10000"
                            :loading="!editedPlaylist"
                        >
                            <template v-slot:item.cover="{ item }">
                                <div class="img-container py-2">
                                    <v-img
                                        :src="item.cover"
                                        :alt="item.title"
                                        class="user-avatar playlist-cover"
                                        width="50"
                                        height="50"
                                    >
                                    </v-img>
                                </div>
                            </template>

                            <template v-slot:item.detach="{ item }">
                                <v-btn
                                    class="mx-2"
                                    @click="detachSong(item)"
                                    x-small
                                    fab
                                    dark
                                    color="error"
                                >
                                    <v-icon>$vuetify.icons.link-off</v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                    </v-card>
                </div>
            </v-container>
        </template>
        <template v-slot:dialogs>
            <v-dialog v-model="addSong" max-width="500px">
                <attach-song-dialog
                    v-if="addSong"
                    @songSelected="attachSong($event)"
                ></attach-song-dialog>
            </v-dialog>
        </template>
    </edit-dialog>
</template>

<script>
import attachSongDialog from "../../../dialogs/AttachSong";
export default {
    props: ["playlist"],
    components: {
        attachSongDialog
    },
    data() {
        return {
            editedPlaylist: this.playlist,
            firstCopy: JSON.parse(JSON.stringify(this.playlist)),
            isLoading: false,
            errors: {},
            addSong: false,
            defaultCoverPath: "/storage/defaults/images/playlist_cover.png",
            episodeTableHeaders: [
                { text: this.$t('Cover'), value: "cover", align: "center" },
                { text: this.$t('Title'), value: "title", align: "center" },
                { text: this.$t('Detach'), value: "detach", align: "center" }
            ]
        };
    },
    methods: {
        closeWindow() {
            let changed = false;
            if (this.editedPlaylist.title != this.firstCopy.title) {
                changed = true;
            }

            if (this.editedPlaylist.cover != this.firstCopy.cover) {
                changed = true;
            }
            if (changed) {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna quit without saving the changes ? maybe consider just hiding the window.")}`,
                    button: {
                        no: this.$t("Cancel"),
                        yes: this.$t("Discard")
                    },
                    /**
                     * Callback Function
                     * @param {Boolean} confirm
                     */
                    callback: confirm => {
                        if (confirm) {
                            this.editedPlaylist.title = this.firstCopy.title;
                            this.editedPlaylist.cover = this.firstCopy.cover;
                            this.editedPlaylist.public = this.firstCopy.public;
                            this.$emit("close");
                        }
                    }
                });
            } else {
                this.$emit("close");
            }
        },
        imageReady(e) {
            this.editedPlaylist.cover = e;
        },
        attachSong(song) {
            axios
                .post("/api/attach-to-playlist", {
                    playlist_id: this.editedPlaylist.id,
                    song_id: song.id,
                    song_origin: song.origin
                })
                .then(res => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Added"),
                        text: this.$t('Song added to playlist!')
                    });
                    this.editedPlaylist.songs.push(song);
                    this.addSong = false;
                });
        },
        detachSong(song) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna detach this song ?")}`,
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
                        let index = this.editedPlaylist.songs.findIndex(
                            song => song.id === song.id
                        );
                        this.editedPlaylist.songs.splice(index, 1);
                        axios
                            .post("/api/detach-from-playlist", {
                                playlist_id: this.editedPlaylist.id,
                                song_id: song.id,
                                song_origin: song.origin
                            })
                            .then(() => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text: this.$t('Song')  + " " + this.$t('Deleted') + "."
                                });
                            })
                            .catch();
                    }
                }
            });
        },
        savePlaylist() {
            var formData = new FormData();
            this.isLoading = true;
            if (this.editedPlaylist.cover && this.editedPlaylist.cover.data) {
                // if cover was picked, the value is stored as an object from the CropImage component
                formData.append(
                    "cover",
                    this.editedPlaylist.cover.data,
                    this.editedPlaylist.cover.title
                );
            } else if (
                this.editedPlaylist.cover &&
                !this.editedPlaylist.cover.data
            ) {
                // no cover was picked, the value is stored as a string
                formData.append("cover", this.editedPlaylist.cover);
            } else {
                formData.append("cover", this.defaultCoverPath);
            }
            if (this.editedPlaylist.title) {
                formData.append("title", this.editedPlaylist.title);
            }

            formData.append("public", this.editedPlaylist.public ? 1 : 0);
            if (this.editedPlaylist.new) {
                formData.append("created_by", "admin");
                axios
                    .post("/api/admin/playlists", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        },
                        onUploadProgress: progressEvent => {
                            let percentCompleted = Math.floor(
                                (progressEvent.loaded * 100) /
                                    progressEvent.total
                            );
                            this.progress = percentCompleted;
                        }
                    })
                    .then(() => {
                        this.$emit("created");
                    })
                    .catch(e => {
                        this.progress = 0;
                        this.errors = e.response.data.errors;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    })
                    .finally(() => this.isLoading = false)
            } else {
                formData.append("playlist_id", this.editedPlaylist.id);
                formData.append("_method", "PUT");
                axios
                    .post(
                        "/api/admin/playlists/" + this.editedPlaylist.id,
                        formData,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            },
                            onUploadProgress: progressEvent => {
                                let percentCompleted = Math.floor(
                                    (progressEvent.loaded * 100) /
                                        progressEvent.total
                                );
                                this.progress = percentCompleted;
                            }
                        }
                    )
                    .then(res => {
                        this.$emit("updated");
                    })
                    .catch(e => {
                        this.progress = 0;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    })
                    .finally(() => this.isLoading = false)
            }
        }
    }
};
</script>
