<template>
    <edit-dialog
        @callToAction="saveArtist"
        @cancel="closeWindow"
        :loading="isLoading"
        editing="artist"
    >
        <template v-slot:body>
            <v-container>
                <v-row>
                    <v-col cols="auto">
                        <upload-image
                            @imageReady="imageReady($event)"
                            :source="artist.avatar || defaultAvatarPath"
                        />
                    </v-col>
                    <v-col>
                        <v-container>
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        :label="$t('Firstname')"
                                        :readonly="!artist.new"
                                        v-model="editedArtist.firstname"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        :label="$t('Lastname')"
                                        :readonly="!artist.new"
                                        v-model="editedArtist.lastname"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        :label="$t('Displayname')"
                                        :readonly="!artist.new"
                                        v-model="editedArtist.displayname"
                                    ></v-text-field
                                ></v-col>
                                <v-col cols="12" sm="6">
                                    <v-select
                                        :label="$t('Country')"
                                        :readonly="!artist.new"
                                        :items="countriesList"
                                        v-model="editedArtist.country"
                                    ></v-select
                                ></v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        :label="$t('Address')"
                                        :readonly="!artist.new"
                                        v-model="editedArtist.address"
                                    ></v-text-field
                                ></v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        :label="$t('Phone')"
                                        :readonly="!artist.new"
                                        v-model="editedArtist.phone"
                                    ></v-text-field
                                ></v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        :label="$t('Email')"
                                        :readonly="!artist.new"
                                        v-model="editedArtist.email"
                                    ></v-text-field
                                ></v-col>
                            </v-row>
                        </v-container>
                    </v-col>
                    <v-col cols="12" v-if="editedArtist.user">
                        <v-card>
                            <v-card-title>
                                {{ $t("Storage Space") }}
                            </v-card-title>
                            <v-divider></v-divider>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="9">
                                            <v-text-field
                                                :label="$t('Size')"
                                                type="number"
                                                v-model="available_disk_space"
                                                :messages="
                                                    editedArtist.used_disk_space
                                                        ? (
                                                              editedArtist.used_disk_space /
                                                              1024 /
                                                              1024
                                                          ).toFixed(1) +
                                                          ' MB ' +
                                                          this.$t(
                                                              'used already.'
                                                          )
                                                        : ''
                                                "
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="3">
                                            <veutify-select
                                                type="number"
                                                :items="['MB', 'GB', 'KB']"
                                                v-model="
                                                    available_disk_space_unit
                                                "
                                            ></veutify-select>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row>
                    <edit-external-links
                        :item="editedArtist"
                        @spotify_link="editedArtist.spotify_link = $event"
                        @youtube_link="editedArtist.youtube_link = $event"
                        @soundcloud_link="editedArtist.soundcloud_link = $event"
                        @itunes_link="editedArtist.itunes_link = $event"
                        @deezer_link="editedArtist.deezer_link = $event"
                    />
                </v-row>
                <v-row v-if="$store.getters.getSettings.saas && !editedArtist.new">
                    <ArtistEarningDetails :artist="artist" />
                </v-row>
            </v-container>
        </template>
    </edit-dialog>
</template>

<script>
import ArtistEarningDetails from "../../../artist/EarningDetails.vue";
import countriesList from "../../../../data/coutries";
export default {
    props: ["artist"],
    components: {
        ArtistEarningDetails
    },
    data() {
        return {
            editedArtist: this.artist,
            countriesList,
            firstCopy: JSON.parse(JSON.stringify(this.artist)),
            available_disk_space:
                this.artist.available_disk_space ||
                this.$store.getters.getSettings.availableArtistDiskSpace,
            available_disk_space_unit: "MB",
            isLoading: false,
            defaultAvatarPath: "/storage/defaults/images/artist_avatar.png"
        };
    },
    methods: {
        imageReady(e) {
            this.artist.avatar = e;
        },
        closeWindow() {
            let changed = false;
            if (
                JSON.stringify(this.editedArtist) !=
                JSON.stringify(this.firstCopy)
            ) {
                changed = true;
            }
            if (changed) {
                this.$confirm({
                    message: `${this.$t(
                        "Are you sure you wanna quit without saving the changes?"
                    )}`,
                    button: {
                        no: this.$t("Cancel"),
                        yes: this.$t("Discard")
                    },
                    callback: confirm => {
                        if (confirm) {
                            this.editedArtist = this.firstCopy;
                            this.$emit("close");
                        }
                    }
                });
            } else {
                this.$emit("close");
            }
        },
        saveArtist() {
            var formData = new FormData();
            this.isLoading = true;
            formData.append("firstname", this.editedArtist.firstname || "");
            formData.append("lastname", this.editedArtist.lastname || "");
            formData.append("displayname", this.editedArtist.displayname || "");
            formData.append("country", this.editedArtist.country || "");
            formData.append("phone", this.editedArtist.phone || "");
            formData.append("email", this.editedArtist.email || "");
            formData.append("address", this.editedArtist.address || "");
            formData.append("spotify_link", this.editedArtist.spotify_link || "");
            formData.append("youtube_link", this.editedArtist.youtube_link || "");
            formData.append("soundcloud_link", this.editedArtist.soundcloud_link || "");
            formData.append("itunes_link", this.editedArtist.itunes_link || "");

            if (this.available_disk_space_unit === "GB") {
                var available_disk_space = this.available_disk_space * 1024;
            } else if (this.available_disk_space_unit === "KB") {
                var available_disk_space = this.available_disk_space / 1024;
            } else {
                var available_disk_space = this.available_disk_space;
            }
            this.editedArtist.available_disk_space = available_disk_space;
            formData.append("available_disk_space", available_disk_space);
            if (this.editedArtist.avatar && this.editedArtist.avatar.data) {
                formData.append(
                    "avatar",
                    this.editedArtist.avatar.data,
                    this.editedArtist.avatar.title
                );
            } else if (
                this.editedArtist.avatar &&
                !this.editedArtist.avatar.data
            ) {
                // no avatar was picked, the value is stored as a string
                formData.append("avatar", this.editedArtist.avatar);
            } else {
                formData.append("avatar", this.defaultAvatarPath);
            }
            if (this.editedArtist.new) {
                axios
                    .post("/api/admin/artists", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    })
                    .then(res => {
                        this.$emit("updated");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.errors = e.response.data.errors;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    });
            } else {
                formData.append("_method", "PUT");
                axios
                    .post(
                        "/api/admin/artists/" + this.editedArtist.id,
                        formData,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            }
                        }
                    )
                    .then(() => {
                        this.$emit("updated");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.errors = e.response.data.errors;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    });
            }
        }
    }
};
</script>
