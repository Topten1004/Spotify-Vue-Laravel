<template>
    <div class="new-artist-account">
        <div class="header_and_sub_title">
            <div class="header_and_sub_title__header">
                {{ $t("Welcome to your artist account") }}
            </div>
            <div class="header_and_sub_title__sub">
                {{ $t("Firstly, fill your information") }}
            </div>
        </div>
        <v-card class="p-3 mt-5">
            <v-row class="px-4">
                <v-col lg="3" md="4" x-sm="6" class="p-2">
                    <upload-image
                        @imageReady="imageReady($event, 'artist')"
                        :id="'artist-image'"
                        :source="
                            artist.avatar ||
                                '/storage/defaults/images/artist_avatar.png'
                        "
                    />
                </v-col>
                <v-col lg="9" sm="6">
                    <v-text-field
                        :label="$t('Firstname')"
                        v-model="artist.firstname"
                    ></v-text-field>
                    <v-text-field
                        :label="$t('Lastname')"
                        v-model="artist.lastname"
                    ></v-text-field>
                    <v-text-field
                        :label="$t('Displayname')"
                        message="This name will be displayed on your profile."
                        v-model="artist.displayname"
                    ></v-text-field>
                    <v-select
                        :label="$t('Country')"
                        :items="countriesList"
                        v-model="artist.country"
                    ></v-select>
                    <v-text-field
                        :label="$t('Address')"
                        v-model="artist.address"
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
                        @soundcloud_link="artist.soundcloud_link = $event"
                        @itunes_link="artist.itunes_link = $event"
                        @deezer_link="artist.deezer_link = $event"
                        :expanded="true"
                    />
                </v-col>
                <v-col cols="12">
                    <v-btn
                        class="success ml-auto d-flex"
                        @click="save"
                        :loading="loading"
                        :disabled="loading"
                    >
                        <template v-slot:loader>
                            <span class="custom-loader">
                                <v-icon light>$vuetify.icons.cached</v-icon>
                            </span> </template
                        >{{ $t("Save") }}</v-btn
                    >
                </v-col>
            </v-row>
        </v-card>
    </div>
</template>

<script>
import countriesList from "../../../data/coutries";

export default {
    data() {
        return {
            artist: {},
            loading: false,
            countriesList,
            defaultCoverPath: "/storage/defaults/images/artist_avatar.png"
        };
    },
    methods: {
        imageReady(e) {
            this.artist.avatar = e;
        },
        save() {
            var formData = new FormData();
            this.loading = true;
            formData.append("firstname", this.artist.firstname || "");
            formData.append("lastname", this.artist.lastname || "");
            formData.append("country", this.artist.country || "");
            formData.append("phone", this.artist.phone || "");
            formData.append("email", this.artist.email || "");
            formData.append("address", this.artist.address || "");
            formData.append("spotify_link", this.artist.spotify_link || "");
            formData.append("youtube_link", this.artist.youtube_link || "");
            formData.append(
                "soundcloud_link",
                this.artist.soundcloud_link || ""
            );
            formData.append("itunes_link", this.artist.itunes_link || "");
            formData.append("displayname", this.artist.displayname || "");
            if (this.artist.avatar && this.artist.avatar.data) {
                formData.append(
                    "avatar",
                    this.artist.avatar.data,
                    this.artist.avatar.title
                );
            } else {
                formData.append("avatar", this.defaultCoverPath);
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

<style lang="scss">
.new-artist-account {
    max-width: 800px;
    margin: 5em auto;
}
.header_and_sub_title {
    text-align: center;
    &__header {
        font-size: 1.3em;
        font-weight: bold;
    }
    &__sub {
        opacity: 0.75;
    }
}
</style>
