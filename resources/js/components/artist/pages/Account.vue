<template>
    <div class="new-artist-account" v-if="artist && artist.id">
        <v-container fluid>
            <v-row>
                <v-col cols="12" max-width="800px">
                    <v-card class="p-3">
                        <v-card-title>
                            <v-icon color="primary" x-large class="mr-3"
                                >$vuetify.icons.account-music</v-icon
                            >
                            {{ $t("Edit Personal Information") }}
                        </v-card-title>
                        <v-row class="px-4">
                            <v-col cols="auto" class="p-2">
                                <upload-image
                                    @imageReady="imageReady($event)"
                                    :source="artist.avatar || null"
                                />
                            </v-col>
                            <v-col>
                                <v-text-field
                                    :label="$t('Firstname')"
                                    v-model="artist.firstname"
                                    :message="
                                        $t('This is a private information.')
                                    "
                                ></v-text-field>
                                <v-text-field
                                    :label="$t('Lastname')"
                                    :message="
                                        $t('This is a private information.')
                                    "
                                    v-model="artist.lastname"
                                ></v-text-field>
                                <v-text-field
                                    :label="$t('Displayname')"
                                    :message="
                                        $t(
                                            'This name will be displayed on your profile.'
                                        )
                                    "
                                    v-model="artist.displayname"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    :label="$t('Country')"
                                    readonly
                                    v-model="artist.country"
                                ></v-text-field>
                                <v-text-field
                                    :label="$t('Address')"
                                    v-model="artist.lastname"
                                ></v-text-field>
                                <v-text-field
                                    :label="$t('Email')"
                                    v-model="artist.email"
                                ></v-text-field>
                                <v-text-field
                                    :label="$t('Phone Number')"
                                    v-model="artist.phone"
                                    hint="+xxxxxxxxxx"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <edit-external-links
                                    :item="artist"
                                    @spotify_link="artist.spotify_link = $event"
                                    @youtube_link="artist.youtube_link = $event"
                                    @soundcloud_link="
                                        artist.soundcloud_link = $event
                                    "
                                    @itunes_link="artist.itunes_link = $event"
                                    @deezer_link="artist.deezer_link = $event"
                                    :expanded="true"
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-btn
                                    class="success ml-auto d-flex"
                                    @click="save"
                                    :disabled="loading"
                                    :loading="loading"
                                    >{{ $t("Save") }}</v-btn
                                >
                            </v-col>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
    <page-loading v-else />
</template>
<script>
export default {
    created() {
        axios
            .get("/api/artist")
            .then(res => (this.artist = res.data))
            .catch(e => (this.artist = {}));
    },
    data() {
        return {
            artist: null,
            loading: false
        };
    },
    methods: {
        imageReady(e) {
            this.artist.avatar = e;
        },
        save() {
            var formData = new FormData();
            this.loading = true;
            formData.append("id", this.artist.id);
            formData.append("firstname", this.artist.firstname);
            formData.append("lastname", this.artist.lastname);
            formData.append("displayname", this.artist.displayname);

            //external links
            formData.append("spotify_link", this.artist.spotify_link || "");
            formData.append("youtube_link", this.artist.youtube_link || "");
            formData.append(
                "soundcloud_link",
                this.artist.soundcloud_link || ""
            );
            formData.append("itunes_link", this.artist.itunes_link || "");
            formData.append("deezer_link", this.artist.deezer_link || "");

            if (this.artist.avatar && this.artist.avatar.data) {
                formData.append(
                    "avatar",
                    this.artist.avatar.data,
                    this.artist.avatar.title
                );
            }
            axios
                .post("/api/artist/save-personal-infos", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(() => {
                    this.loading = false;
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Success"),
                        text:
                            this.$t("Artist account") +
                            " " +
                            this.$t("updated successfully.")
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                })
                .catch(e => {
                    this.loading = false;
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: Object.values(e.response.data.errors).join(
                            "<br />"
                        )
                    });
                });
        }
    }
};
</script>
