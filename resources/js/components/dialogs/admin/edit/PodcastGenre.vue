<template>
  <edit-dialog
    @callToAction="saveGenre"
    @cancel="closeWindow"
    :loading="isLoading"
    editing="genre"
  >
    <template v-slot:body>
      <v-container class="px-3 pb-3">
        <v-row>
          <v-col cols="auto">
            <upload-image
              @imageReady="imageReady($event)"
              :source="editedGenre.cover || defaultCoverPath"
              ratio="2.5"
            />
          </v-col>
          <v-col>
            <v-text-field
              :label="$t('Name')"
              v-model="editedGenre.name"
              :rules="nameRules"
              @update:error="updateErrorState"
            ></v-text-field>
            <v-text-field
              v-if="$store.getters.getSettings.provider_listenNotes"
              :label="$t('Listen Notes Genre ID')"
              v-model="editedGenre.listen_notes_genre_id"
              v-bind:messages="
                $t(
                  'The ID of the genre from Listen Notes to fetch the best corresponding podcasts.'
                )
              "
            ></v-text-field>
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
      file: null,
      isLoading: false,
      errors: {},
      defaultCoverPath:
        "/storage/defaults/images/podcast_genres/podcast-genre_cover.png",
      progress: null,
      firstCopy: JSON.parse(JSON.stringify(this.genre)),
      nameRules: [
        (value) => !!value || "Required.",
        (value) => value.length <= 15 || "Max 20 characters",
        (value) => value.length >= 2 || "Min 2 characters",
      ],
    };
  },
  methods: {
    imageReady(e) {
      this.editedGenre.cover = e;
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
          message: `${this.$t(
            "Are you sure you wanna quit without saving the changes ?"
          )}`,
          button: {
            no: this.$t("Cancel"),
            yes: this.$t("Discard"),
          },
          callback: (confirm) => {
            if (confirm) {
              this.editedGenre.name = this.firstCopy.name;
              this.editedGenre.cover = this.firstCopy.cover;
              this.$emit("close");
            }
          },
        });
      } else {
        this.$emit("close");
      }
    },
    saveGenre() {
      var formData = new FormData();
      formData.append("name", this.editedGenre.name || "");
      formData.append(
        "listen_notes_genre_id",
        this.editedGenre.listen_notes_genre_id || ""
      );
      formData.append("slug", this.slug(this.editedGenre.name) || "");
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
      if (this.editedGenre.new) {
        axios
          .post("/api/admin/podcast-genres", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
            onUploadProgress: (progressEvent) => {
              let percentCompleted = Math.floor(
                (progressEvent.loaded * 100) / progressEvent.total
              );
              this.progress = percentCompleted;
            },
          })
          .then(() => {
            this.$emit("updated");
          })
          .catch((e) => {
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
          .post("/api/admin/podcast-genres/" + this.editedGenre.id, formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
            onUploadProgress: (progressEvent) => {
              let percentCompleted = Math.floor(
                (progressEvent.loaded * 100) / progressEvent.total
              );
              this.progress = percentCompleted;
            },
          })
          .then(() => {
            this.$emit("updated");
            this.isLoading = "Saved";
          })
          .catch((e) => {
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
    },
  },
};
</script>
