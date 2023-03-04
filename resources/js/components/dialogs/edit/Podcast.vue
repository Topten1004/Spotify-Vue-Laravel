<template>
  <edit-dialog
    @callToAction="savePodcast(true)"
    @cancel="closeWindow"
    :loading="isLoading"
    :fullscreen="!editedPodcast.new ? true : false"
    editing="Podcast"
  >
    <template v-slot:body>
      <v-container>
        <v-row>
          <v-col cols="auto">
            <upload-image
              @imageReady="imageReady($event)"
              :id="'podcast' + editedPodcast.id"
              :source="editedPodcast.cover || defaultCoverPath"
            />
          </v-col>
          <v-col>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    :label="$t('Title')"
                    outlined
                    hide-details
                    dense
                    v-model="editedPodcast.title"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" v-if="$route.path.match('admin/')">
                  <select-artists
                    :artists="
                      editedPodcast.artist && editedPodcast.artist.id
                        ? editedPodcast.artist
                        : ''
                    "
                    @artists="editedPodcast.artist = $event"
                    :multiple="false"
                  />
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    v-model="editedPodcast.genres"
                    :items="genres"
                    :label="$t('Genres')"
                    item-text="name"
                    return-object
                    :placeholder="$t('Select Genres')"
                    multiple
                    outlined
                  >
                    <template v-slot:selection="{ item }">
                      <v-chip close @click:close="remove(item.id)">
                        {{ item.name }}
                      </v-chip>
                    </template>
                    <template v-slot:item="{ item }">
                      <template>
                        <v-list-item-content>
                          <v-list-item-title
                            v-html="item.name"
                          ></v-list-item-title>
                          <!-- <v-list-item-subtitle v-html="item.group"></v-list-item-subtitle> -->
                        </v-list-item-content>
                      </template>
                    </template>
                  </v-autocomplete>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    outlined=""
                    :label="$t('Description')"
                    v-model="editedPodcast.description"
                    hint="A general description of the podcast."
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-container>
            <v-container class="episodes mb-5" v-if="!editedPodcast.new">
              <div class="episodes-wrapper">
                <v-card outlined>
                  <v-card-title>
                    <v-icon color="primary" x-large
                      >$vuetify.icons.music-note</v-icon
                    >
                    <div class="large-icon-text tertiary--text">
                      {{ $t("Episodes") }}
                    </div>
                    <v-btn
                      class="mx-2 ml-3"
                      dark
                      small
                      color="primary"
                      @click="editEpisode('new')"
                    >
                      <v-icon
                        :dark="
                          $store.state.darkTheme ||
                          $store.getters.getSettings.defaultTheme == 'dark'
                        "
                        >$vuetify.icons.plus</v-icon
                      >
                      {{ $t("New") }}
                    </v-btn>
                  </v-card-title>
                  <v-data-table
                    :no-data-text="$t('No data available')"
                    :loading-text="$t('Fetching data') + '...'"
                    :headers="episodeTableHeaders"
                    :items="editedPodcast.episodes"
                    hide-default-footer
                    :items-per-page="10000"
                    :loading="!editedPodcast.episodes"
                  >
                    <template v-slot:item.created_at="{ item }">
                      {{ moment(item.created_at).format("ll") }}
                    </template>
                    <template v-slot:item.cover="{ item }">
                      <div class="img-container py-2">
                        <v-img
                          :src="editedPodcast.cover"
                          :alt="editedPodcast.title"
                          class="user-avatar podcast-cover"
                          width="50"
                          height="50"
                        >
                          <div
                            class="upload-percentage"
                            v-if="item.progress != null && item.progress < 100"
                          >
                            <div class="content-text">{{ item.progress }}%</div>
                          </div>
                        </v-img>
                      </div>
                    </template>
                    <template v-slot:item.duration="{ item }">
                      {{ item.duration ? mmss(item.duration) : "" }}
                    </template>
                    <template v-slot:item.operations="{ item }">
                      <v-btn
                        class="mx-2"
                        @click="editEpisode(item.id)"
                        x-small
                        fab
                        dark
                        color="info"
                      >
                        <v-icon
                          :dark="
                            $store.state.darkTheme ||
                            $store.getters.getSettings.defaultTheme == 'dark'
                          "
                          >$vuetify.icons.pencil</v-icon
                        >
                      </v-btn>
                      <v-btn
                        class="mx-2"
                        @click="deleteEpisode(item.id)"
                        x-small
                        fab
                        dark
                        color="error"
                      >
                        <v-icon>$vuetify.icons.delete</v-icon>
                      </v-btn>
                    </template>
                  </v-data-table>
                </v-card>
              </div>
            </v-container>
          </v-col>
        </v-row>
        <v-row>
          <edit-external-links
            :item="editedPodcast"
            @spotify_link="editedPodcast.spotify_link = $event"
            @youtube_link="editedPodcast.youtube_link = $event"
            @soundcloud_link="editedPodcast.soundcloud_link = $event"
            @itunes_link="editedPodcast.itunes_link = $event"
            @deezer_link="editedPodcast.deezer_link = $event"
          />
        </v-row>
      </v-container>
    </template>
    <template v-slot:dialogs>
      <v-dialog
        v-model="editDialog"
        persistent
        scrollable=""
        max-width="950"
        @click:outside="hideAllepisodes"
      >
        <template v-for="episode in editedPodcast.episodes">
          <edit-episode-dialog
            :key="episode.id"
            v-if="episode.isActive"
            v-show="isShowing(episode.id)"
            @updated="episodeEdited(episode.id)"
            @progress="updateProgress($event, episode.id)"
            @created="episodeCreated"
            @close="closeEpisode(episode.id)"
            @sleep="sleepEpisode(episode.id)"
            @wake="wakeEpisode(episode.id)"
            :episode="episode"
            :uploader="creator"
            :podcast_id="editedPodcast.id"
          />
        </template>
      </v-dialog>
    </template>
  </edit-dialog>
</template>

<script>
import { VSelect } from "vuetify/lib";
import editEpisodeDialog from "./Episode";
export default {
  props: ["podcast", "creator"],
  components: {
    veutifySelect: VSelect,
    editEpisodeDialog,
  },
  computed: {
    isShowing() {
      return (episode_id) =>
        this.editedPodcast.episodes[
          this.editedPodcast.episodes.findIndex(
            (episode) => episode.id === episode_id
          )
        ].isShowing;
    },
  },
  created() {
    axios.get("/api/podcast-genres").then((res) => (this.genres = res.data));
  },
  data() {
    return {
      editedPodcast: this.podcast,
      firstCopy: JSON.parse(JSON.stringify(this.podcast)),
      artistsFocused: false,
      editDialog: false,
      search: null,
      defaultCoverPath: "/storage/defaults/images/podcast_cover.png",
      genres: [],
      editingEpisode: null,
      genresFocused: false,
      artists: [],
      isLoading: false,
      errors: {},
      progress: null,
      episodeTableHeaders: [
        { text: this.$t("Cover"), value: "cover", align: "center" },
        { text: this.$t("Title"), value: "title" },
        { text: this.$t("Likes"), value: "nb_likes", align: "center" },
        { text: this.$t("Duration"), value: "duration", align: "center" },
        {
          text: this.$t("Number of plays"),
          value: "nb_plays",
          align: "center",
        },
        { text: this.$t("Created At"), value: "created_at", align: "center" },
        { text: this.$t("Operations"), value: "operations", align: "center" },
      ],
    };
  },
  beforeDestroy() {
    this.$emit("beforeDestroy");
  },
  methods: {
    closeWindow() {
      let changed = false;
      if (
        JSON.stringify(this.editedPodcast) != JSON.stringify(this.firstCopy)
      ) {
        changed = true;
      }
      if (changed) {
        this.$confirm({
          message: `${this.$t(
            "Are you sure you wanna quit without saving the changes ? maybe consider just hiding the window."
          )}`,
          button: {
            no: this.$t("Cancel"),
            yes: this.$t("Discard"),
          },
          /**
           * Callback Function
           * @param {Boolean} confirm
           */
          callback: (confirm) => {
            if (confirm) {
              this.editedPodcast.title = this.firstCopy.title;
              this.editedPodcast.genres = this.firstCopy.genres;
              this.editedPodcast.artist = this.firstCopy.artist;
              this.editedPodcast.description = this.firstCopy.description;
              this.editedPodcast.cover = this.firstCopy.cover;
              this.$emit("close");
            }
          },
        });
      } else {
        this.$emit("close");
      }
    },
    updateProgress(progress, episode_id) {
      let index = this.editedPodcast.episodes.findIndex(
        (episode) => episode.id === episode_id
      );
      this.$set(this.editedPodcast.episodes[index], "progress", progress);
    },
    cancelUpload(episode_id) {
      let index = this.editedPodcast.episodes.findIndex(
        (episode) => episode.id === episode_id
      );
      this.editedPodcast.episodes[index].requestSource.cancel();
      this.editEpisode(episode_id);
    },
    sleepEpisode(episode_id) {
      this.editDialog = false;
      let index = this.editedPodcast.episodes.findIndex(
        (episode) => episode.id === episode_id
      );
      this.$set(this.editedPodcast.episodes[index], "isShowing", false);
    },
    wakeEpisode(episode_id) {
      this.editDialog = true;
      let index = this.editedPodcast.episodes.findIndex(
        (episode) => episode.id === episode_id
      );
      this.$set(this.editedPodcast.episodes[index], "isShowing", true);
    },
    hideAllepisodes() {
      for (let i = 0; i < this.editedPodcast.episodes.length; i++) {
        this.$set(this.editedPodcast.episodes[i], "isShowing", false);
      }
      this.editDialog = false;
    },
    imageReady(e) {
      this.editedPodcast.cover = e;
    },
    deleteEpisode(episode_id) {
      this.$confirm({
        message: `${
          this.$t("Are you sure you wanna delete this") +
          " " +
          this.$t("episode") +
          "?"
        }`,
        button: {
          no: this.$t("No"),
          yes: this.$t("Yes"),
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            let index = this.editedPodcast.episodes.findIndex(
              (episode) => episode.id === episode_id
            );
            if (this.editedPodcast.episodes[index].requestSource) {
              this.editedPodcast.episodes[index].requestSource.cancel();
            }
            this.editedPodcast.episodes.splice(index, 1);
            axios
              .delete("/api/episodes/" + episode_id)
              .then(() => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Deleted"),
                  text: this.$t("Episode") + " " + this.$t("Deleted") + ".",
                });
              })
              .catch((e) => {});
          }
        },
      });
    },
    editEpisode(episode_id) {
      if (episode_id === "new") {
        this.editedPodcast.episodes.unshift({
          id: Math.floor(Math.random() * (100000 - 5000) + 100000),
          new: true,
          genres: [],
          isActive: true,
          isShowing: true,
          source: "",
          nb_likes: 0,
          nb_plays: 0,
          source_format: "file",
        });
        this.editDialog = true;
      } else {
        this.editedPodcast.episodes[
          this.editedPodcast.episodes.findIndex(
            (episode) => episode.id === episode_id
          )
        ].isActive = true;
        this.editedPodcast.episodes[
          this.editedPodcast.episodes.findIndex(
            (episode) => episode.id === episode_id
          )
        ].isShowing = true;
        this.editDialog = true;
      }
    },
    closeEpisode(episode_id) {
      let index = this.editedPodcast.episodes.findIndex(
        (episode) => episode.id === episode_id
      );
      if (this.editedPodcast.episodes[index].isShowing) {
        this.editDialog = false;
      }
      if (this.editedPodcast.episodes[index].new) {
        this.editedPodcast.episodes.splice(index, 1);
      }
      this.$set(this.editedPodcast.episodes[index], "isShowing", false);
      this.$set(this.editedPodcast.episodes[index], "isActive", false);
      this.$forceUpdate();
    },
    episodeEdited(episode_id) {
      let index = this.editedPodcast.episodes.findIndex(
        (episode) => episode.id === episode_id
      );
      this.$set(this.editedPodcast.episodes[index], "progress", 0);
      this.$set(this.editedPodcast.episodes[index], "isActive", false);
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Saved"),
        text: this.$t("Episode") + " " + this.$t("Updated") + ".",
      });
    },
    episodeCreated() {
      this.savePodcast();
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Created"),
        text: this.$t("Episode") + " " + this.$t("Created") + ".",
      });
    },
    fetchArtists(search, loading) {
      if (search) {
        loading(true),
          axios
            .get("/api/match-artists/" + search)
            .then(
              (res) =>
                (this.artists = res.data.map((artist) => ({
                  id: artist.id,
                  displayname: artist.displayname,
                  avatar: artist.avatar,
                })))
            )
            .then(() => loading(false))
            .catch(() => loading(false));
      }
    },
    savePodcast(emit) {
      var formData = new FormData();
      this.isLoading = true;
      if (this.editedPodcast.cover && this.editedPodcast.cover.data) {
        // if cover was picked, the value is stored as an object from the CropImage component
        formData.append(
          "cover",
          this.editedPodcast.cover.data,
          this.editedPodcast.cover.title
        );
      } else if (this.editedPodcast.cover && !this.editedPodcast.cover.data) {
        // no cover was picked, the value is stored as a string
        formData.append("cover", this.editedPodcast.cover);
      } else {
        formData.append("cover", this.defaultCoverPath);
      }
      if (this.editedPodcast.genres.length) {
        formData.append("genres", JSON.stringify(this.editedPodcast.genres));
      }
      if (this.editedPodcast.title) {
        formData.append("title", this.editedPodcast.title);
      }

      // links
      if (this.editedPodcast.spotify_link) {
        formData.append("spotify_link", this.editedPodcast.title);
      }
      if (this.editedPodcast.youtube_link) {
        formData.append("youtube_link", this.editedPodcast.youtube_link);
      }
      if (this.editedPodcast.soundcloud_link) {
        formData.append("soundcloud_link", this.editedPodcast.soundcloud_link);
      }
      if (this.editedPodcast.itunes_link) {
        formData.append("itunes_link", this.editedPodcast.itunes_link);
      }
      if (this.editedPodcast.deezer_link) {
        formData.append("deezer_link", this.editedPodcast.deezer_link);
      }

      if (this.creator === "admin") {
        if (this.editedPodcast.artist) {
          formData.append("artist_id", this.editedPodcast.artist.id);
        } else {
          formData.append("artist_id", "");
        }
      } else if (this.creator === "artist") {
        formData.append("artist_id", this.$store.getters.getArtist.id);
      } else {
        formData.append("artist_id", "");
      }
      if (this.editedPodcast.description) {
        formData.append("description", this.editedPodcast.description);
      }

      if (this.editedPodcast.new) {
        axios
          .post("/api/podcasts", formData, {
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
            this.$emit("created");
            this.isLoading = false;
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
        formData.append("podcast_id", this.editedPodcast.id);
        formData.append("_method", "PUT");
        axios
          .post("/api/podcasts/" + this.editedPodcast.id, formData, {
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
            if (emit) {
              this.$emit("updated");
            }
            this.isLoading = false;
          })
          .catch((e) => {
            this.progress = 0;
            this.isLoading = false;
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
