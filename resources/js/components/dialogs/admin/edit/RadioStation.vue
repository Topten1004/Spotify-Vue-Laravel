<template>
    <edit-dialog
        @callToAction="saveRadioStation"
        @cancel="closeWindow"
        :loading="isLoading"
        editing="Radio Station"
    >
        <template v-slot:body>
            <v-container class="px-3 pb-3">
                <v-row>
                    <v-col cols="auto">
                        <upload-image
                            @imageReady="imageReady($event)"
                            :source="
                                editedRadioStation.cover || defaultCoverPath
                            "
                        />
                    </v-col>
                    <v-col>
                        <div class="align-space-between-and-column"></div>
                        <v-text-field
                            :label="$t('Name')"
                            v-model="editedRadioStation.name"
                            outlined
                            dense
                        ></v-text-field>
                        <v-text-field
                            :label="$t('Stream Endpoint')"
                            v-model="editedRadioStation.streamEndpoint"
                            placeholder="http://example.com:8000/le85.mp3"
                            outlined
                            dense
                        ></v-text-field>
                        <vuetifySelect
                            dense
                            :items="serverTypes"
                            item-value="value"
                            item-text="text"
                            outlined
                            v-model="editedRadioStation.serverType"
                            label="Server Type"
                            hide-details
                        ></vuetifySelect>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <v-container>
                            <v-row>
                                <v-col>
                                    <div class="card-bb-header">
                                        <div class="header">
                                            <div class="title">
                                                {{ $t("Stats") }}
                                            </div>
                                        </div>
                                        <v-divider class="pt-2"></v-divider>
                                        <div class="sub">
                                            {{
                                                $t(
                                                    "The data of your stream such as the title of the current song."
                                                )
                                            }}
                                        </div>
                                        <div class="body">
                                            <v-row>
                                                <v-col>
                                                    <vuetifySelect
                                                        dense
                                                        :items="statsSources"
                                                        item-value="value"
                                                        item-text="text"
                                                        outlined
                                                        v-model="
                                                            editedRadioStation.statsSource
                                                        "
                                                        label="Stats source"
                                                        hide-details
                                                    ></vuetifySelect>
                                                </v-col>
                                            </v-row>
                                            <v-row
                                                v-if="
                                                    editedRadioStation.statsSource ===
                                                        'server'
                                                "
                                            >
                                                <v-col cols="6">
                                                    <v-text-field
                                                        outlined
                                                        dense
                                                        label="Server URL"
                                                        placeholder="http://your-server:port"
                                                        v-model="
                                                            editedRadioStation.serverURL
                                                        "
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col cols="6">
                                                    <v-text-field
                                                        v-if="
                                                            editedRadioStation.serverType ===
                                                                'shoutcast'
                                                        "
                                                        outlined
                                                        dense
                                                        label="Server ID(SID)"
                                                        v-model="
                                                            editedRadioStation.serverID
                                                        "
                                                    ></v-text-field>
                                                    <v-text-field
                                                        v-else
                                                        outlined
                                                        dense
                                                        label="Server Mount"
                                                        placeholder="Add server mount without '/'"
                                                        v-model="
                                                            editedRadioStation.serverMount
                                                        "
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col cols="6">
                                                    <v-text-field
                                                        outlined
                                                        dense
                                                        label="Server Username"
                                                        v-model="
                                                            editedRadioStation.serverUsername
                                                        "
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col cols="6">
                                                    <v-text-field
                                                        outlined
                                                        dense
                                                        label="Server Password"
                                                        v-model="
                                                            editedRadioStation.serverPassword
                                                        "
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>
                                            <v-row v-else>
                                                <v-col cols="12">
                                                    <v-text-field
                                                        outlined
                                                        dense
                                                        label="Stats Endpoint"
                                                        hide-details=""
                                                        placeholder="https://example.com/stream/status-json.xsl"
                                                        v-model="
                                                            editedRadioStation.statsEndpoint
                                                        "
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col cols="12">
                                                    <v-switch
                                                        label="Use Proxy"
                                                        messages="to read web radio feed data from servers that does not have the proper CORS settings"
                                                        v-model="
                                                            editedRadioStation.proxy
                                                        "
                                                    ></v-switch>
                                                </v-col>
                                            </v-row>
                                            <v-row>
                                                <v-col cols="12">
                                                    <v-text-field
                                                        label="Interval"
                                                        v-model="
                                                            editedRadioStation.interval
                                                        "
                                                        :messages="
                                                            $t(
                                                                'Duration in milliseconds to check for stats.'
                                                            )
                                                        "
                                                        type="number"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col cols="12">
                                                    <v-switch
                                                        label="Highlight"
                                                        messages="Show on the live radio section on the right-sidebar"
                                                        v-model="
                                                            editedRadioStation.highlight
                                                        "
                                                    ></v-switch>
                                                </v-col>
                                            </v-row>
                                        </div>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-col>
                </v-row>
            </v-container>
        </template>
    </edit-dialog>
</template>
<script>
import { VSelect } from "vuetify/lib";
export default {
    props: ["radioStation"],
    components: {
        vuetifySelect: VSelect
    },
    data() {
        return {
            editedRadioStation: this.radioStation,
            isThereAnError: false,
            file: null,
            isLoading: false,
            serverTypes: [
                {
                    value: "icecast",
                    text: "Icecast"
                },
                {
                    value: "shoutcast",
                    text: "SHOUTcast"
                }
            ],
            statsSources: [
                {
                    value: "endpoint",
                    text: "Endpoint"
                },
                {
                    value: "server",
                    text: "Server"
                }
            ],
            errors: {},
            defaultCoverPath: "/storage/defaults/images/podcast_cover.png",
            firstCopy: JSON.parse(JSON.stringify(this.radioStation))
        };
    },
    methods: {
        imageReady(e) {
            this.editedRadioStation.cover = e;
        },
        updateErrorState(event) {
            this.isThereAnError = event;
        },
        closeWindow() {
            let changed = false;
            if (this.editedRadioStation.name != this.firstCopy.name) {
                changed = true;
            }
            if (this.editedRadioStation.cover != this.firstCopy.cover) {
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
                            this.editedRadioStation = this.firstCopy;
                            this.$emit("close");
                        }
                    }
                });
            } else {
                this.$emit("close");
            }
        },
        saveRadioStation() {
            this.isLoading = true;
            var formData = new FormData();
            formData.append("name", this.editedRadioStation.name || "");
            formData.append(
                "streamEndpoint",
                this.editedRadioStation.streamEndpoint || ""
            );
            formData.append(
                "serverType",
                this.editedRadioStation.serverType || ""
            );
            formData.append(
                "serverURL",
                this.editedRadioStation.serverURL || ""
            );
            formData.append("serverID", this.editedRadioStation.serverID || "");
            formData.append(
                "serverMount",
                this.editedRadioStation.serverMount || ""
            );
            formData.append(
                "serverUsername",
                this.editedRadioStation.serverUsername || ""
            );
            formData.append(
                "serverPassword",
                this.editedRadioStation.serverPassword || ""
            );
            formData.append(
                "statsEndpoint",
                this.editedRadioStation.statsEndpoint || ""
            );
            formData.append(
                "statsSource",
                this.editedRadioStation.statsSource || ""
            );
            formData.append(
                "highlight",
                this.editedRadioStation.highlight ? 1 : 0
            );
            formData.append("proxy", this.editedRadioStation.proxy ? 1 : 0);
            formData.append("interval", this.editedRadioStation.interval);

            if (
                this.editedRadioStation.cover &&
                this.editedRadioStation.cover.data
            ) {
                // if cover was picked, the value is stored as an object from the CropImage component
                formData.append(
                    "cover",
                    this.editedRadioStation.cover.data,
                    this.editedRadioStation.cover.title
                );
            } else if (
                this.editedRadioStation.cover &&
                !this.editedRadioStation.cover.data
            ) {
                // no cover was picked, the value is stored as a string
                formData.append("cover", this.editedRadioStation.cover);
            } else {
                formData.append("cover", this.defaultCoverPath);
            }
            if (this.editedRadioStation.new) {
                axios
                    .post("/api/admin/radio-stations", formData, {
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
                formData.append("id", this.editedRadioStation.id);
                formData.append("_method", "PUT");
                axios
                    .post(
                        "/api/admin/radio-stations/" +
                            this.editedRadioStation.id,
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
    }
};
</script>

<style lang="scss" scoped>
.align-space-between-and-column {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
</style>
