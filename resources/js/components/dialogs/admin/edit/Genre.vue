<template>
    <edit-dialog
        @callToAction="saveGenre"
        @cancel="closeWindow"
        :loading="isLoading"
        editing="genre"
    >
        <template v-slot:body>
            <v-container class="pb-4">
                <v-row>
                    <v-col cols="auto">
                        <upload-image
                            @imageReady="imageReady($event)"
                            :source="editedGenre.cover || defaultCoverPath"
                        >
                            <template v-slot:icon v-if="showIcon">
                                <div
                                    class="content-wrapper content-wrapper__edit"
                                >
                                    <div
                                        class="genre-dark-layer dark-layer"
                                    ></div>
                                    <div class="content">
                                        <img
                                            class="icon"
                                            :src="svgIcon || editedGenre.icon"
                                            alt=""
                                        />
                                        <div class="content-text">
                                            {{ editedGenre.name }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </upload-image>
                    </v-col>
                    <v-col>
                        <v-text-field
                           :label="$t('Name')"
                            v-model="editedGenre.name"
                            :rules="nameRules"
                            @update:error="updateErrorState"
                        ></v-text-field>
                        <v-switch v-model="showIcon" :label="$t('Icon')"></v-switch>
                        <v-file-input
                           :label="$t('Icon file')  + '(svg)'"
                            class="mt-5"
                            filled
                            :rules="iconRules"
                            :disabled="!showIcon"
                            @change="svgFileChanged"
                            @update:error="updateErrorState"
                            accept="image/svg+xml"
                        >
                        </v-file-input>
                    </v-col>
                </v-row>
            </v-container>
        </template>
    </edit-dialog>
</template>
<script>
export default {
    props: ["genre"],
    data() {
        return {
            editedGenre: this.genre,
            isThereAnError: false,
            showIcon: this.genre.icon,
            defaultCoverPath: "/storage/defaults/images/genre_cover.png",
            isLoading: false,
            errors: {},
            progress: null,
            svgIcon: null,
            iconFile: null,
            firstCopy: JSON.parse(JSON.stringify(this.genre)),
            iconRules: [
                value =>
                    !value ||
                    value.size < 2000000 ||
                    "Svg size should be less than 2 MB!",
                value =>
                    !value ||
                    this.doesExtensionMatch(value.name, ["svg"]) ||
                    "Should be an SVG file"
            ],
            nameRules: [
                value => !!value || "Required.",
                value => value.length <= 15 || "Max 20 characters",
                value => value.length >= 2 || "Min 2 characters"
            ]
        };
    },
    methods: {
        imageReady(e) {
            this.editedGenre.cover = e;
        },
        svgFileChanged(file) {
            this.iconFile = file;
            if (file && !this.isThereAnError) {
                this.showIcon = true;
                var reader = new FileReader();
                var self = this;
                reader.onload = function(e) {
                    self.svgIcon = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        updateErrorState(event) {
            this.isThereAnError = event;
        },
        closeWindow() {
            let changed = false;
            if (this.editedGenre.name != this.firstCopy.name) {
                changed = true;
            }
            if (this.editedGenre.cover != this.firstCopy.cover) {
                changed = true;
            }
            if (this.svgIcon || changed) {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna quit without saving the changes ?")}`,
                    button: {
                        no: this.$t("Cancel"),
                        yes: this.$t("Discard")
                    },
                    callback: confirm => {
                        if (confirm) {
                            this.editedGenre.name = this.firstCopy.name;
                            this.editedGenre.cover = this.firstCopy.cover;
                            this.editedGenre.icon = this.firstCopy.icon;
                            this.$emit("close");
                        }
                    }
                });
            } else {
                this.$emit("close");
            }
        },
        saveGenre() {
            var formData = new FormData();
            formData.append("name", this.editedGenre.name || "");
            formData.append("slug", this.slug(this.editedGenre.name));
            this.isLoading = true;
            if (this.editedGenre.cover && this.editedGenre.cover.data) {
                // if cover was picked, the value is stored as an object from the CropImage component
                formData.append(
                    "cover",
                    this.editedGenre.cover.data,
                    this.editedGenre.cover.title
                );
            } else if (this.editedGenre.cover && !this.editedGenre.cover.data) {
                // no cover was picked, the value is stored as a string
                formData.append("cover", this.editedGenre.cover);
            } else {
                formData.append("cover", this.defaultCoverPath);
            }
            if (!this.showIcon) {
                formData.append("icon", "");
            } else if (this.iconFile) {
                formData.append("icon", this.iconFile);
            } else {
                formData.append("icon", this.editedGenre.icon || "");
            }
            if (this.editedGenre.new) {
                axios
                    .post("/api/admin/genres", formData, {
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
                        this.$emit("updated");
                    })
                    .catch(e => {
                        this.progress = 0;
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
                formData.append("id", this.editedGenre.id);
                formData.append("_method", "PUT");
                axios
                    .post(
                        "/api/admin/genres/" + this.editedGenre.id,
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
                    .then(() => {
                        this.$emit("updated");
                        this.isLoading = "Saved";
                    })
                    .catch(e => {
                        this.progress = 0;
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
    },
    watch: {
        showIcon(val) {
            if (val === false) {
                this.iconFile = null;
            }
        }
    }
};
</script>
