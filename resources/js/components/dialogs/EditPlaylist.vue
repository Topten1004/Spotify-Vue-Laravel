<template>
    <dialog-wrapper>
        <div class="create-playlist-wrapper">
            <div class="create-playlist-wrapper__header">
                <div class="title">
                    {{ $t("Edit Playlist") }}
                </div>
                <div class="buttons">
                    <v-btn
                        class="primary create"
                        small
                        rounded
                        :disabled="isLoading"
                        @click="validateAndCreatePlaylist()"
                    >
                        {{ $t("Save") }}
                    </v-btn>
                    <v-btn
                        class="secondary cancel ml-2"
                        small
                        rounded
                        @click="$emit('cancel')"
                    >
                        {{ $t("Cancel") }}
                    </v-btn>
                </div>
            </div>
            <div class="create-playlist-wrapper__body">
                <v-container>
                    <v-row>
                        <v-col cols="auto" class="max-width-175">
                            <upload-image
                                @imageReady="imageUploaded($event)"
                                :source="this.editedPlaylist.cover"
                            />
                        </v-col>
                        <v-col>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="editedPlaylist.title"
                                        :label="$t('Name')"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-switch
                                        v-if="hasPermission('Publish playlists')"
                                        v-model="editedPlaylist.public"
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
    props: ["playlist"],
    data() {
        return {
            editedPlaylist: JSON.parse(JSON.stringify(this.playlist)),
            isLoading: false
        };
    },
    methods: {
        validateAndCreatePlaylist() {
            if (this.editedPlaylist.title) {
                this.isLoading = true;
                var formData = new FormData();
                formData.append("playlist_id", this.editedPlaylist.id);
                formData.append("title", this.editedPlaylist.title);
                if (
                    this.editedPlaylist.cover &&
                    this.editedPlaylist.cover.data
                ) {
                    formData.append(
                        "cover",
                        this.editedPlaylist.cover.data,
                        this.editedPlaylist.cover.title
                    );
                }
                formData.append("public", this.editedPlaylist.public ? 1 : 0);
                formData.append("_method", "PUT");
                axios
                    .post(
                        "/api/user/playlists/" + this.editedPlaylist.id,
                        formData,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            }
                        }
                    )
                    .then(() => {
                        this.$notify({
                            type: "success",
                            group: "foo",
                            title: this.$t("Updated"),
                            text:
                                this.$t("Playlist") +
                                " " +
                                this.$t("updated successfully.")
                        });
                        this.$emit("updated");
                    })
                    .catch()
                    .finally(() => (this.isLoading = false));
            }
        },
        imageUploaded(e) {
            this.editedPlaylist.cover = e;
        }
    }
};
</script>
