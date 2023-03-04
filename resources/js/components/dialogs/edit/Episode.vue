<template>
    <edit-dialog
        @callToAction="saveEpisode"
        @cancel="closeWindow"
        :loading="isLoading"
        :hideCallToAction="true"
        editing="episode"
    >
        <template v-slot:body>
            <v-container>
                <v-row>
                    <v-col cols="5">
                        <veutifySelect
                            outlined
                            :items="sourceFormats"
                            @change="resetSource"
                           :label="$t('Source Format')"
                            item-value="value"
                            v-model="editedEpisode.source_format"
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
                        </veutifySelect>
                    </v-col>
                    <v-col cols="7">
                        <v-file-input
                            v-if="editedEpisode.source_format === 'file'"
                            :rules="[rules.fileSize]"
                            accept="audio/*"
                            show-size
                            outlined
                            v-model="episodeFile"
                            @update:error="error = $event"
                            :label="
                                editedEpisode.file_name
                                    ? editedEpisode.file_name
                                    : $t('Attach Audio File')
                            "
                            :loading="episodeSourceLoading"
                            @change="loadEpisodeMetadata($event)"
                        ></v-file-input>
                        <v-text-field
                            v-else-if="
                                editedEpisode.source_format === 'yt_video'
                            "
                           :label="$t('Video URL')"
                            :hint="$t('You can add the video ID, but It must be valid.')"
                            v-model="editedEpisode.source"
                            outlined
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-text-field
                           :label="$t('Title')"
                            dense
                            outlined
                            hide-details=""
                            v-model="editedEpisode.title"
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row justify="center">
                    <v-col cols="6" sm="8">
                        <v-text-field
                           :label="$t('Duration')"
                            :placeholder="$t('Duration in seconds')"
                            type="number"
                            outlined
                            dense
                            hide-details
                            v-model="editedEpisode.duration"
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
                                    ? loadEpisodeMetadata(
                                          episodeFile || editedEpisode.source
                                      )
                                    : ''
                            "
                           :label="$t('Auto Duration')"
                            :disabled="
                                editedEpisode.source_format === 'yt_video'
                            "
                        >
                        </v-switch>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12">
                        <v-textarea
                            outlined
                           :label="$t('Description')"
                            v-model="editedEpisode.description"
                            hide-details=""
                            :hint="$t('General description of the episode.')"
                            :disabled="sourceMissing"
                        ></v-textarea>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12">
                        <div>
                            <div
                                v-if="isLoading && progress"
                                class="button-upload"
                            >
                                <button
                                    class="uploading my-2  v-btn v-btn--block v-btn--contained theme--dark v-size--default"
                                    color="transparent"
                                >
                                    <div class="content-text">
                                        {{$t('Saving...')}}
                                        {{ progress }}%
                                    </div>
                                    <div
                                        class="percentage success"
                                        :style="{
                                            width: progress + '%'
                                        }"
                                    ></div>
                                </button>
                                <v-btn
                                    class="cancel my-2 "
                                    block
                                    color="error"
                                    @click="cancelRequest()"
                                >
                                    {{$t('Cancel')}}
                                </v-btn>
                            </div>
                            <v-btn
                                v-else
                                block
                                class="my-2 success"
                                @click="saveEpisode"
                                :disabled="error"
                                >{{$t('Save')}}
                            </v-btn>
                        </div></v-col
                    >
                </v-row>
            </v-container>
        </template>
    </edit-dialog>
</template>

<script>
import { VSelect } from "vuetify/lib";
export default {
    props: ["episode", "podcast_id", "uploader"],
    components: {
        veutifySelect: VSelect
    },
    data() {
        return {
            firstCopy: JSON.parse(JSON.stringify(this.episode)),
            rules: {
                fileSize: file =>
                    file?.size <
                        parseInt(this.$store.getters.getSettings.maxFileSize) *
                            1024 *
                            1024 ||
                    "Max file size is " +
                        this.$store.getters.getSettings.maxFileSize +
                        this.$t("MB")
            },
            editedEpisode: this.episode,
            episodeFile: null,
            artistsFocused: false,
            rolesDialog: false,
            progress: null,
            isLoading: false,
            fileSize: null,
            episodeSourceLoading: false,
            sourceFormats: [
                {
                    value: "file",
                    text: this.$t('Audio File'),
                    icon: "file-chart"
                },
                {
                    value: "yt_video",
                    text: this.$t('YouTube Video'),
                    icon: "youtube"
                }
            ],
            autoDuration: true,
            error: false
        };
    },
    computed: {
        sourceMissing() {
            return !this.episodeFile && !this.editedEpisode.source;
        }
    },
    methods: {
        imageReady(e) {
            this.editedEpisode.cover = e;
        },
        loadEpisodeMetadata(file) {
            if (!file)
                return (
                    (this.episodeFile = null),
                    (this.editedEpisode.source = null)
                );
            this.episodeSourceLoading = true;
            return new Promise((res, rej) => {
                this.getAudioMetadata(
                    file ? file : this.editedEpisode.source,
                    function(audioMetadata) {
                        if (!audioMetadata) {
                            this.$notify({
                                group: "foo",
                                type: "error",
                                title: this.$t("File corrupted!"),
                                text:
                                    this.$t("The source file you are trying to upload is corrupted.")
                            });
                            this.episodeSourceLoading = false;
                            rej();
                            return;
                        }
                        this.episodeFile = file;
                        this.fileSize = file.size;
                        this.editedEpisode.duration = Math.floor(
                            audioMetadata.duration
                        );
                        this.episodeSourceLoading = false;
                        res();
                    }.bind(this)
                );
            });
        },
        closeWindow() {
            let changed = false;
            if (this.editedEpisode.title != this.firstCopy.title) {
                changed = true;
            }
            if (this.episodeFile || changed) {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna quit without saving the changes ? maybe consider just hiding the window.")}`,
                    button: {
                        no: this.$t("Cancel"),
                        yes: this.$t("Discard")
                    },
                    callback: confirm => {
                        if (confirm) {
                            if (this.editedEpisode.requestSource) {
                                this.editedEpisode.requestSource.cancel();
                            }
                            this.editedEpisode.title = this.firstCopy.title;
                            this.$emit("close");
                        }
                    }
                });
            } else {
                this.$emit("close");
            }
        },
        cancelRequest() {
            this.editedEpisode.progress = null;
            this.editedEpisode.requestSource.cancel("upload canceled");
        },
                getYoutubeVideoID(string = '') {
          if( string ) {
          if( string.length === 11 ) {
            return string;
          }
          var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
          var match = string.match(regExp);
          return match && match[7].length == 11 ? match[7] : "";
          }
          return "";
          },
        async saveEpisode() {
            var formData = new FormData();
            formData.append("podcast_id", this.podcast_id);
            formData.append(
                "description",
                this.editedEpisode.description || ""
            );
            formData.append("title", this.editedEpisode.title || "");
            this.isLoading = true;
            var duration;
           // getting the audio source
            if (this.editedEpisode.source_format === "yt_video") {
                // source format is youtube video
                var videoID = this.getYoutubeVideoID(this.editedEpisode.source);
                if (this.editedEpisode.source && videoID) {
                    formData.append("source", videoID);
                } else {
                    return this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: this.$t('Please add a valid YouTube video ID or URL.')
                    });
                }
                duration = this.editedEpisode.duration;
            } else if (this.editedEpisode.source_format === "file") {
                var promise = new Promise((res, rej) => {
                    if (this.episodeFile || this.editedEpisode.source) {
                        if (
                            !this.autoDuration &&
                            this.editedEpisode.duration > 1
                        ) {
                            duration = this.editedEpisode.duration;
                            res();
                        } else {
                            this.loadEpisodeMetadata(
                                this.episodeFile
                                    ? this.episodeFile
                                    : this.editedEpisode.source
                            );
                            duration = this.editedEpisode.duration;
                            res();
                        }
                    } else {
                        return this.$notify({
                            group: "foo",
                            type: "error",
                            title: this.$t("Error"),
                            text: this.$t('Please add a valid source file.')
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
                        text: e
                    });
                }
            }
                const request = axios.CancelToken.source();
                this.editedEpisode.requestSource = request;
                this.isLoading = true;
                formData.append("source_format", this.editedEpisode.source_format);
                this.$emit("sleep");
                if (this.episodeFile) {
                    formData.append("source", this.episodeFile);
                    formData.append("file_size", this.fileSize);
                }
                formData.append("duration", duration || '');
                if (this.editedEpisode.new) {
                    formData.append("new", true);
                    formData.append("uploaded_by", this.uploader);
                    this.createNewEpisode(formData);
                } else {
                    formData.append("id", this.editedEpisode.id);
                    this.updateEpisode(formData);
                }

        },
        updateEpisode(formData) {
            formData.append("_method", "PUT");
            axios
                .post("/api/episodes/" + this.editedEpisode.id, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                    cancelToken: this.editedEpisode.requestSource.token,
                    onUploadProgress: progressEvent => {
                        let percentCompleted = Math.floor(
                            (progressEvent.loaded * 100) / progressEvent.total
                        );
                        this.progress =
                            percentCompleted === 100 ? 99 : percentCompleted;
                        this.$emit("progress", this.progress);
                    }
                })
                .then(res => {
                    this.$emit("updated");
                    this.$emit("progress", 100);
                    this.editedEpisode = res.data;
                    this.isLoading = false;
                    this.$emit("close");
                })
                .catch(e => {
                    this.editedEpisode.progress = null;
                    this.isLoading = false;
                    this.$emit("wake");
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: Object.values(e.response.data.errors).join(
                            "<br />"
                        )
                    });
                });
        },
                resetSource($event) {
          this.editedEpisode.source = null;
          if( $event === 'yt_video' ) {
            this.autoDuration = false
          } else if($event === 'file' ) {
            this.autoDuration = true
          }
        },
        createNewEpisode(formData) {
            axios
                .post("/api/episodes", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                    cancelToken: this.editedEpisode.requestSource.token,
                    onUploadProgress: progressEvent => {
                        let percentCompleted = Math.floor(
                            (progressEvent.loaded * 100) / progressEvent.total
                        );
                        this.progress =
                            percentCompleted === 100 ? 99 : percentCompleted;
                        this.$emit("progress", this.progress);
                    }
                })
                .then(res => {
                    this.editedEpisode.id = res.data.id;
                    this.editedEpisode.source = res.data.source;
                    this.editedEpisode.new = false;
                    this.$emit("progress", 100);
                    this.$emit("created");
                    this.$emit("close");
                    this.isLoading = false;
                })
                .catch(e => {
                    this.editedEpisode.progress = null;
                    this.$emit("wake");
                    this.isLoading = false;
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
