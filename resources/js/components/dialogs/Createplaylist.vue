<template>
    <dialog-wrapper>
        <div class="create-playlist-wrapper">
            <div class="create-playlist-wrapper__header">
                <div class="title">
                    {{$t('Create Playlist')}}
                </div>
                <div class="buttons">
                    <v-btn
                        class="primary"
                        rounded
                        small
                        :disabled="loading"
                        @click="validateAndCreatePlaylist()"
                    >
                        {{$t('Create')}}
                    </v-btn>
                    <v-btn
                        rounded
                        small
                        class="secondary ml-2"
                        :disabled="loading"
                        @click="$emit('cancel')"
                    >
                        {{$t('Cancel')}}
                    </v-btn>
                </div>
            </div>
            <div class="create-playlist-wrapper__body">
                <v-container>
                    <v-row>
                        <v-col cols="auto" class="max-width-175">
                            <upload-image
                                @imageReady="imageUploaded($event)"
                                :source="playlist.cover || defaultCoverPath"
                            />
                        </v-col>
                        <v-col>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                    v-model="playlist.title"
                                   :label="$t('Name')"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12">
                                      <v-switch
                                        v-model="playlist.isPublic"
                                       :label="$t('Public')"
                                    ></v-switch>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-container>
            </div>
        </div>
    </dialog-wrapper>
</template>

<script>
export default {
    data() {
        return {
            playlist: {
                title: "",
                isPublic: false
            },
            loading: false,
            defaultCoverPath: "/storage/defaults/images/playlist_cover.png"
        };
    },
    methods: {
        validateAndCreatePlaylist() {
            if (this.playlist.title) {
                this.loading = true;
                var formData = new FormData();
                formData.append("title", this.playlist.title);
                formData.append("created_by", "user");
                if (this.playlist.cover && this.playlist.cover.data) {
                    // if cover was picked, the value is stored as an object from the CropImage component
                    formData.append(
                        "cover",
                        this.playlist.cover.data,
                        this.playlist.cover.title
                    );
                } else {
                    formData.append("cover", this.defaultCoverPath);
                }
                formData.append("public", this.playlist.isPublic ? 1 : 0);
                axios
                    .post("/api/user/playlists", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    })
                    .then(() => {
                        this.$notify({
                            group: "foo",
                            type: "success",
                            title: this.$t("Created"),
                            text: this.$t('Playlist created successfully.')
                        });
                        this.$store.dispatch("fetchPlaylists");
                        this.$emit("success");
                    })
                    .catch(() => (this.progress = 0))
                    .finally(() => (this.loading = false));
            } else {
                this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Oops!"),
                    text: this.$t('Please give your playlist a title')
                });
            }
        },
        imageUploaded(e) {
            this.playlist.cover = e;
        }
    }
};
</script>
<style lang="scss">
.max-width-175 {
    max-width: 175px;
}
</style>
