<template>
  <edit-dialog
    @callToAction="saveSong"
    @cancel="closeWindow"
    :loading="isLoading"
    :hideCallToAction="true"
    editing="Song"
  >
    <template v-slot:header-actions>
      <v-btn
        v-if="$store.getters.getSettings.provider_spotify"
        @click="importFromSpotifyDialog = true"
        small
        color="#1DB954"
        dark
      >
        <v-icon left>$vuetify.icons.spotify</v-icon>
        {{ $t("From Spotify") }}
      </v-btn>
      <v-btn @click="$emit('sleep')" v-if="uploader !== 'user'" small>
        {{ $t("Hide") }}
      </v-btn>
    </template>
    <template v-slot:body>
      <v-container>
        <v-row>
          <v-col cols="auto">
            <upload-image
              @imageReady="imageReady($event)"
              :source="editedSong.cover || defaultCoverPath"
              :id="'song-' + editedSong.id"
            />
            <div>
              <div v-if="isLoading && progress" class="button-upload">
                <button
                  class="
                    uploading
                    my-2
                    v-btn v-btn--block v-btn--contained
                    theme--dark
                    v-size--default
                  "
                  color="transparent"
                >
                  <div class="content-text">
                    {{ $t("Saving... ") }}{{ progress }}%
                  </div>
                  <div
                    class="percentage success"
                    :style="{ width: progress + '%' }"
                  ></div>
                </button>
                <v-btn
                  class="cancel my-2"
                  block
                  color="error"
                  @click="cancelRequest()"
                >
                  {{ $t("Cancel") }}
                </v-btn>
              </div>
              <v-btn
                v-else
                block
                class="my-2 success"
                :disabled="error"
                @click.stop="saveSong"
                >{{ $t("Save") }}
              </v-btn>
            </div>
          </v-col>
          <v-col>
            <v-container>
              <v-row>
                <v-col cols="5">
                  <v-select
                    outlined
                    :items="sourceFormats"
                    @change="resetSource"
                    :label="$t('Source Format')"
                    item-value="value"
                    hide-details
                    v-model="editedSong.source_format"
                  >
                    <template
                      v-slot:item="{ item }"
                      :disabled="
                        item.value == 'video' &&
                        !$store.getters.getSettings.allowVideos
                      "
                    >
                      <div class="align-center p-1">
                        <v-icon class="mr-2" color="primary"
                          >$vuetify.icons.{{ item.icon }}</v-icon
                        >
                        {{ item.text }}
                      </div>
                    </template>
                    <template v-slot:selection="{ item }">
                      <div class="align-center p-1">
                        <v-icon class="mr-2" color="primary"
                          >$vuetify.icons.{{ item.icon }}</v-icon
                        >
                        {{ item.text }}
                      </div>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="7">
                  <v-file-input
                    v-if="editedSong.source_format === 'file'"
                    :rules="[rules.fileSize]"
                    accept="audio/*"
                    show-size
                    outlined
                    :error="error"
                    v-model="songFile"
                    @update:error="error = $event"
                    :label="
                      editedSong.file_name
                        ? editedSong.file_name
                        : $t('Attach Audio File')
                    "
                    :loading="songSourceLoading"
                    @change="loadSongMetadata($event)"
                  ></v-file-input>
                  <template v-else-if="editedSong.source_format === 'yt_video'">
                    <div class="align-center">
                      <v-text-field
                        :label="$t('Video URL')"
                        :hint="
                          $t('You can add the video ID, but It must be valid.')
                        "
                        v-model="editedSong.source"
                        hide-details=""
                        outlined
                      ></v-text-field>
                      <!-- <v-btn color="#FF0000" light class="ml-2" dark>
                                            <v-icon left
                                                >$vuetify.icons.youtube</v-icon
                                            >
                                            Search
                                        </v-btn> -->
                    </div>
                  </template>

                  <template
                    v-else-if="editedSong.source_format === 'audio_url'"
                  >
                    <div class="align-center">
                      <v-text-field
                        :label="$t('URL')"
                        v-model="editedSong.source"
                        hide-details=""
                        outlined
                      ></v-text-field>
                      <v-switch
                        v-model="saveFileFromURL"
                        hide-details
                        class="ml-2"
                        :label="$t('Save file')"
                      ></v-switch>
                    </div>
                  </template>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    :label="$t('Title')"
                    outlined
                    :rules="titleRules"
                    hide-details="auto"
                    v-model="editedSong.title"
                  ></v-text-field>
                </v-col>
                <!-- <v-row justify="center">
                                <v-col cols="6" sm="8">
                                    <v-text-field
                                        :label="$t('Duration')+'(s)'"
                                        :placeholder="$t('Duration in seconds')"
                                        type="number"
                                        outlined
                                        dense
                                        hide-details
                                        v-model="editedSong.duration"
                                        :disabled="autoDuration"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col cols="6" sm="4">
                                    <v-switch
                                        v-model="autoDuration"
                                        dense
                                        hide-details
                                        @change="
                                            $event
                                                ? loadSongMetadata(
                                                      songFile ||
                                                          editedSong.source
                                                  )
                                                : ''
                                        "
                                        :label="$t('Auto Duration')"
                                        :disabled="
                                            editedSong.source_format ===
                                                'yt_video'
                                        "
                                    >
                                    </v-switch>
                                </v-col>
                            </v-row> -->
                <v-col cols="12">
                  <select-genres
                    v-if="genresComp"
                    :genres="editedSong.genres"
                    @genres="editedSong.genres = $event"
                  />
                </v-col>
              </v-row>
            </v-container>
          </v-col>
          <v-col cols="12">
            <select-artists
              v-if="artistsComp"
              :artists="editedSong.artists"
              @artists="editedSong.artists = $event"
              :multiple="true"
            />
          </v-col>
          <v-col
            cols="12"
            sm="4"
            v-if="uploader === 'admin' || uploader === 'artist'"
          >
            <v-switch
              v-model="editedSong.isExclusive"
              :label="$t('Exclusive')"
            ></v-switch>
          </v-col>
          <v-col
            cols="12"
            sm="4"
            v-if="uploader === 'admin' || uploader === 'artist'"
          >
            <v-switch
              v-model="editedSong.isExplicit"
              :label="$t('Explicit')"
            ></v-switch>
          </v-col>
          <v-col
            cols="12"
            sm="4"
            v-if="uploader === 'admin' || uploader === 'artist'"
          >
            <v-switch
              v-model="editedSong.status"
              :label="$t('Status')"
            ></v-switch>
          </v-col>
        </v-row>
        <v-row v-if="uploader === 'admin'">
          <edit-external-links
            :item="editedSong"
            @spotify_link="editedSong.spotify_link = $event"
            @youtube_link="editedSong.youtube_link = $event"
            @soundcloud_link="editedSong.soundcloud_link = $event"
            @itunes_link="editedSong.itunes_link = $event"
            @deezer_link="editedSong.deezer_link = $event"
          />
        </v-row>
        <v-row
          v-if="
            $store.getters.getSettings.enable_selling &&
            canBeProduct &&
            !album_id &&
            !editedSong.album
          "
        >
          <v-col cols="12">
            <div class="title">{{ $t("Product") }}</div>
            <v-divider></v-divider>
          </v-col>
          <v-col>
            <v-switch
              :label="
                'Provide as product' +
                ($store.getters.getSettings.enableBilling
                  ? ''
                  : ' (Enable Billing first)')
              "
              :disabled="!$store.getters.getSettings.enableBilling"
              v-model="editedSong.isProduct"
            ></v-switch>
          </v-col>
          <v-col cols="12" v-if="editedSong.isProduct">
            <div class="d-flex justify-space-between">
              <div class="title">{{ $t("Licenses") }}</div>
              <div></div>
              <v-btn small class="primary" @click="licenseDialog = true">{{
                $t("Add License")
              }}</v-btn>
            </div>
            <div class="py-3">
              <v-divider></v-divider>
            </div>
            <template>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        {{ $t("Name") }}
                      </th>
                      <th class="text-left">
                        {{ $t("Price") }}
                      </th>
                      <th class="text-left">
                        {{ $t("Description") }}
                      </th>
                      <th class="text-left">
                        {{ $t("Delete") }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(license, n) in licenses" :key="n">
                      <td>{{ license.name }}</td>
                      <td>
                        {{ price(license.amount) + defaultCurrency.symbol }}
                      </td>
                      <td>{{ license.description }}</td>
                      <td>
                        <v-btn
                          @click="licenses.splice(n, 1)"
                          x-small
                          fab
                          dark
                          color="error"
                        >
                          <v-icon>$vuetify.icons.delete</v-icon>
                        </v-btn>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </template>
          </v-col>
        </v-row>
      </v-container>
    </template>
    <template v-slot:dialogs>
      <v-dialog
        v-model="attachArtistDialog"
        max-width="500"
        v-if="uploader === 'admin' || uploader === 'artist'"
      >
        <search-artist @attachArtist="attachArtist($event)"></search-artist>
      </v-dialog>
      <v-dialog
        v-model="licenseDialog"
        v-if="canBeProduct && licenseDialog"
        max-width="500"
      >
        <edit-dialog
          @callToAction="addLicense($event)"
          @cancel="(licenseDialog = false), (selectedLicense = null)"
          callToActionText="Add"
          :loading="false"
          :title="$t('Add License')"
        >
          <template v-slot:body>
            <v-container>
              <v-select
                :items="previousPrices"
                v-model="selectedLicense"
                item-value="id"
                item-text="name"
                :label="$t('Choose License')"
              >
                <template v-slot:item="{ item }">
                  {{ item.name }}
                  {{ price(item.amount) + " " + item.currency_symbol }}
                </template>
                <template v-slot:selection="{ item }">
                  {{ item.name }}
                  {{ price(item.amount) + " " + item.currency_symbol }}
                </template>
              </v-select>
              <template v-if="!selectedLicense">
                <v-text-field
                  :label="$t('Name')"
                  v-model="editedLicense.name"
                ></v-text-field>
                <v-text-field
                  :label="$t('Description')"
                  v-model="editedLicense.description"
                ></v-text-field>
                <div class="d-flex align-center">
                  <v-text-field
                    :label="$t('Amount')"
                    hint="Important: the amount should be in cents ( 1$ = 100 )"
                    :value="0"
                    class="pr-2"
                    v-model="editedLicense.amount"
                  ></v-text-field>
                  <div>
                    <div class="plan__price">
                      <div class="plan__price__currency">
                        {{ defaultCurrency.symbol }}
                      </div>
                      <div class="plan__price__amount">
                        {{ price(editedLicense.amount) }}
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </v-container>
          </template>
        </edit-dialog>
      </v-dialog>
      <v-dialog v-model="importFromSpotifyDialog" max-width="500">
        <attach-asset-dialog
          class="p-3"
          type="song"
          engine="spotify"
          v-if="importFromSpotifyDialog"
          @asset="addSpotifyData($event)"
        />
      </v-dialog>
    </template>
  </edit-dialog>
</template>

<script>
import AttachAssetDialog from "../../dialogs/admin/AttachAssetDialog.vue";
import BillingMixin from "../../../mixins/billing/billing";
export default {
  components: {
    AttachAssetDialog,
  },
  mixins: [BillingMixin],
  props: ["song", "album_id", "uploader"],
  created() {
    if (this.uploader !== "user") {
      axios.get("/api/prices").then((res) => {
        this.previousPrices = this.previousPrices = res.data;
      });
    }
    // remove the YouTube link option if the Youtube Plugin was disabled
    if( !this.$store.getters.getSettings.youtubePlugin ) {
      this.sourceFormats.splice(2, 1)
    }
  },
  data() {
    return {
      firstCopy: JSON.parse(JSON.stringify(this.song)),
      editedSong: this.song,
      artistsComp: true,
      genresComp: true,
      selectedLicense: 0,
      saveFileFromURL: false,
      previousPrices: [],
      songFile: null,
      defaultCoverPath: "/storage/defaults/images/song_cover.png",
      songSourceLoading: false,
      artistsFocused: false,
      attachArtistDialog: false,
      selectedArtist: null,
      fileSize: null,
      autoDuration: true,
      genresFocused: false,
      sourceFormats: [
        {
          value: "file",
          text: this.$t("Audio File"),
          icon: "file-chart",
        },
        {
          value: "audio_url",
          text: this.$t("Audio URL"),
          icon: "link",
        },
        {
          value: "yt_video",
          text: this.$t("YouTube Video"),
          icon: "youtube",
        },
      ],
      rules: {
        fileSize: (file) =>
          file
            ? file.size <
                parseInt(this.$store.getters.getSettings.maxFileSize) *
                  1024 *
                  1024 ||
              this.$t("Max file size is") +
                " " +
                this.$store.getters.getSettings.maxFileSize +
                " " +
                this.$t("MB")
            : true,
      },
      titleRules: [(v) => !!v || this.$t("Please give a title")],
      genres: [],
      progress: null,
      error: false,
      isLoading: false,
      importFromSpotifyDialog: false,
      licenseDialog: false,
      licenses: (this.song.product ? this.song.product.prices : []) || [],
      editedLicense: {},
    };
  },
  computed: {
    sourceMissing() {
      return !this.editedSong.source && !this.songFile;
    },
    canBeProduct() {
      return (
        (this.uploader === "admin" || this.uploader === "artist") &&
        this.editedSong.source_format === "file"
      );
    },
  },
  watch: {
    // workaround
    importFromSpotifyDialog() {
      this.artistsComp = false;
      setTimeout(() => {
        this.artistsComp = true;
      }, 10);
    },
  },
  methods: {
    addLicense() {
      if (this.selectedLicense) {
        var selectedLicense = this.previousPrices.find(
          (wx) => wx.id === this.selectedLicense
        );
      } else {
        // validation
        if (!this.editedLicense.name) {
          return this.$notify({
            group: "foo",
            type: "error",
            title: this.$t("Oops!"),
            text: this.$t("Please provide a name for the license."),
          });
        }
        if (!this.editedLicense.amount || isNaN(this.editedLicense.amount)) {
          return this.$notify({
            group: "foo",
            type: "error",
            title: this.$t("Oops!"),
            text: this.$t("Please enter a correct amount."),
          });
        } else if (
          !isNaN(this.editedLicense.amount) &&
          this.editedLicense.amount < 50
        ) {
          return this.$notify({
            group: "foo",
            type: "error",
            title: this.$t("Oops!"),
            text: this.$t(
              "Please enter a value above 50 on the amount (minimum charge for Stripe)"
            ),
          });
        }

        var selectedLicense = this.editedLicense;
      }

      this.licenses.push({
        ...selectedLicense,
      });

      this.licenseDialog = false;
      this.selectedLicense = 0;
      this.editedLicense = {};
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Added"),
        text: this.$t("License added!"),
      });
    },
    imageReady(e) {
      this.editedSong.cover = e;
    },
    getYoutubeVideoID(string = "") {
      if (string) {
        if (string.length === 11) {
          return string;
        }
        var regExp =
          /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
        var match = string.match(regExp);
        return match && match[7].length == 11 ? match[7] : "";
      }
      return "";
    },
    closeWindow() {
      if (
        this.songFile ||
        JSON.stringify(this.firstCopy) !== JSON.stringify(this.editedSong)
      ) {
        this.$confirm({
          message: `${this.$t(
            "Are you sure you wanna quit without saving the changes?"
          )}`,
          button: {
            no: this.$t("Cancel"),
            yes: this.$t("Discard"),
          },
          callback: (confirm) => {
            if (confirm) {
              if (this.editedSong.requestSource) {
                this.editedSong.requestSource.cancel();
              }
              this.editedSong.title = this.firstCopy.title;
              this.editedSong.genres = this.firstCopy.genres;
              this.editedSong.artists = this.firstCopy.artists;
              this.editedSong.youtube_id = this.firstCopy.youtube_id;
              this.editedSong.cover = this.firstCopy.cover;
              this.editedSong.artist = this.firstCopy.artist;
              this.editedSong.duration = this.firstCopy.duration;
              this.$emit("close");
            }
          },
        });
      } else {
        this.$emit("close");
      }
    },
    resetSource($event) {
      this.editedSong.source = null;
      if ($event === "yt_video") {
        this.autoDuration = false;
      } else if ($event === "file") {
        this.autoDuration = true;
      }
    },
    fetchGenres(search, loading) {
      if (search.length >= 1) {
        loading(true),
          axios
            .get("/api/match-genres/" + search)
            .then(
              (res) =>
                (this.genres = res.data.map((genre) => ({
                  id: genre.id,
                  name: genre.name,
                })))
            )
            .then(() => loading(false));
      }
    },
    cancelRequest() {
      this.editedSong.progress = null;
      this.editedSong.requestSource.cancel("upload canceled");
    },
    addSpotifyData(data) {
      this.editedSong.cover = data.cover;
      this.editedSong.title = data.title;
      this.editedSong.artists = data.artists;

      this.importFromSpotifyDialog = false;
    },
    loadSongMetadata(file) {
      if (!file) {
        return (this.songFile = null), (this.editedSong.source = null);
      }
      this.songSourceLoading = true;
      this.error = false;
      return new Promise((res, rej) => {
        this.getAudioMetadata(
          file ? file : this.editedSong.source,
          function (audioMetadata) {
            if (!audioMetadata) {
              this.$notify({
                group: "foo",
                type: "error",
                title: this.$t("File corrupted!"),
                text: this.$t(
                  "The source file you are trying to upload is corrupted."
                ),
              });
              this.error = true;
              this.songSourceLoading = false;
              rej();
              return;
            }
            this.fileSize = this.editedSong.file_size = file.size;
            this.editedSong.duration = Math.floor(audioMetadata.duration);
            this.songSourceLoading = false;
            res();
          }.bind(this)
        );
      });
    },
    async saveSong() {
      var formData = new FormData();
      if (!this.editedSong.title) {
        return this.$notify({
          group: "foo",
          type: "error",
          title: this.$t("Error"),
          text: this.$t("Please give a title"),
        });
      }
      if (this.editedSong.cover && this.editedSong.cover.data) {
        // if cover was picked, the value is stored as an object from the CropImage component
        formData.append(
          "cover",
          this.editedSong.cover.data,
          this.editedSong.cover.title
        );
      } else if (this.editedSong.cover && !this.editedSong.cover.data) {
        // no cover was picked, the value is stored as a string
        formData.append("cover", this.editedSong.cover);
      } else {
        formData.append("cover", this.defaultCoverPath);
      }
      if (this.editedSong.title) {
        formData.append("title", this.editedSong.title);
      }

      // links
      if (this.editedSong.spotify_link) {
        formData.append("spotify_link", this.editedSong.title);
      }
      if (this.editedSong.youtube_link) {
        formData.append("youtube_link", this.editedSong.youtube_link);
      }
      if (this.editedSong.soundcloud_link) {
        formData.append("soundcloud_link", this.editedSong.soundcloud_link);
      }
      if (this.editedSong.itunes_link) {
        formData.append("itunes_link", this.editedSong.itunes_link);
      }
      if (this.editedSong.deezer_link) {
        formData.append("deezer_link", this.editedSong.deezer_link);
      }

      if (this.editedSong.public) {
        formData.append("public", 1);
      } else {
        formData.append("public", 0);
      }
      if (this.editedSong.isExclusive) {
        formData.append("isExclusive", 1);
      } else {
        formData.append("isExclusive", 0);
      }
      if (this.editedSong.isExplicit) {
        formData.append("isExplicit", 1);
      } else {
        formData.append("isExplicit", 0);
      }
      if (this.editedSong.status) {
        formData.append("status", 1);
      } else {
        formData.append("status", 0);
      }

      if (this.editedSong.artists.length) {
        formData.append("artists", JSON.stringify(this.editedSong.artists));
      }

      if (this.editedSong.genres.length) {
        formData.append("genres", JSON.stringify(this.editedSong.genres));
      }
      // if (this.editedSong.artist_id) {
      //     formData.append("artist_id", this.editedSong.artist_id);
      // } else {
      //     if (this.editedSong.artist !== undefined) {
      //         formData.append(
      //             "artist_id",
      //             this.editedSong.artist == null
      //                 ? ""
      //                 : this.editedSong.artist.id
      //         );
      //     }
      // }
      // getting the audio source
      if (this.editedSong.source_format === "yt_video") {
        // source format is youtube video
        var videoID = this.getYoutubeVideoID(this.editedSong.source);
        if (this.editedSong.source && videoID) {
          formData.append("source", videoID);
        } else {
          return this.$notify({
            group: "foo",
            type: "error",
            title: this.$t("Error"),
            text: this.$t("Please add a valid YouTube video ID or URL."),
          });
        }
      } else if (
        this.editedSong.source_format === "file" ||
        this.editedSong.source_format === "audio_url"
      ) {
        var duration;
        if (
          this.editedSong.source_format === "audio_url" &&
          this.saveFileFromURL
        ) {
          formData.append("saveFileFromURL", 1);
        }
        var promise = new Promise((res, rej) => {
          if (this.songFile || this.editedSong.source) {
            if (!this.autoDuration && this.editedSong.duration > 1) {
              duration = this.editedSong.duration;
              res();
            } else {
              this.loadSongMetadata(
                this.songFile ? this.songFile : this.editedSong.source
              );
              duration = this.editedSong.duration;
              res();
            }
          } else {
            return this.$notify({
              group: "foo",
              type: "error",
              title: this.$t("Error"),
              text: this.$t("Please add a valid source file."),
            });
          }
        });
      }
      if (promise) {
        try {
          await promise;
        } catch (e) {
          return this.$notify({
            group: "foo",
            type: "error",
            title: this.$t("Error"),
            text: e,
          });
        }
      }
      const request = axios.CancelToken.source();
      this.editedSong.requestSource = request;
      this.isLoading = true;
      formData.append("source_format", this.editedSong.source_format);
      if (this.editedSong.isProduct) {
        formData.append("isProduct", 1);
        if (!this.licenses.length) {
          this.isLoading = false;
          return this.$notify({
            group: "foo",
            type: "error",
            title: this.$t("Error"),
            text: this.$t("Please add atleast one license/price"),
          });
        } else {
          formData.append("licenses", JSON.stringify(this.licenses));
        }
      } else {
        formData.append("isProduct", 0);
      }
      this.$emit("sleep");
      if (this.songFile || this.editedSong.source_format === "audio_url") {
        formData.append(
          "source",
          this.songFile ? this.songFile : this.editedSong.source
        );
        formData.append("file_size", this.fileSize);
      }
      formData.append("duration", duration || "");
      if (this.editedSong.new) {
        formData.append("new", true);
        if (this.album_id) {
          formData.append("album_id", this.album_id);
        }
        formData.append("uploaded_by", this.uploader);
        axios
          .post(
            "/api/" + (this.uploader === "user" ? "user/" : "") + "songs",
            formData,
            {
              headers: {
                "Content-Type": "multipart/form-data",
              },
              cancelToken: this.editedSong.requestSource.token,
              onUploadProgress: (progressEvent) => {
                let percentCompleted = Math.floor(
                  (progressEvent.loaded * 100) / progressEvent.total
                );
                this.progress =
                  percentCompleted === 100 ? 99 : percentCompleted;
                this.$emit("progress", this.progress);
              },
            }
          )
          .then((res) => {
            this.editedSong.new = false;
            this.editedSong.id = res.data.id;
            this.editedSong.source = res.data.source;
            this.editedSong.source_format = res.data.source_format;
            this.editedSong.file_name = res.data.file_name;
            this.editedSong.cover = res.data.cover;
            this.editedSong.title = res.data.title;
            this.editedSong.duration = res.data.duration;
            this.$emit("progress", 100);
            this.$emit("created");
            this.$emit("close");
            this.isLoading = false;
          })
          .catch((e) => {
            this.editedSong.progress = null;
            this.$emit("wake");
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
      } else {
        formData.append("id", this.editedSong.id);
        formData.append("_method", "PUT");
        axios
          .post("/api/songs/" + this.editedSong.id, formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
            cancelToken: this.editedSong.requestSource.token,
            onUploadProgress: (progressEvent) => {
              let percentCompleted = Math.floor(
                (progressEvent.loaded * 100) / progressEvent.total
              );
              this.progress = percentCompleted === 100 ? 99 : percentCompleted;
              this.$emit("progress", this.progress);
            },
          })
          .then((res) => {
            this.$emit("updated");
            this.$emit("progress", 100);
            this.editedSong.new = false;
            this.editedSong.id = res.data.id;
            this.editedSong.source_format = res.data.source_format;
            this.editedSong.file_name = res.data.file_name;
            this.editedSong.source = res.data.source;
            this.editedSong.cover = res.data.cover;
            this.editedSong.title = res.data.title;
            this.editedSong.duration = res.data.duration;
            this.isLoading = false;
            this.$emit("close");
          })
          .catch((e) => {
            this.editedSong.progress = null;
            this.isLoading = false;
            this.$emit("wake");
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

<style>
.row-row {
  margin-top: 0 !important;
}
</style>
