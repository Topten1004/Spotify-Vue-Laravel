<template>
    <edit-dialog
        @callToAction="saveAlbum(true)"
        @cancel="closeWindow"
        :loading="isLoading"
        :fullscreen="!editedAlbum.new ? true : false"
        editing="Album"
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
        </template>
        <template v-slot:body>
            <v-container>
                <v-row>
                    <v-col cols="auto">
                        <upload-image
                            @imageReady="imageReady($event)"
                            :id="'album' + editedAlbum.id"
                            :source="editedAlbum.cover || defaultCoverPath"
                        />
                    </v-col>
                    <v-col>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        :label="$t('Title')"
                                        v-model="editedAlbum.title"
                                        outlined
                                        hide-details
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" v-if="creator === 'admin'">
                                    <select-artists
                                        v-if="artistsComp"
                                        :artists="editedAlbum.artists"
                                        @artists="editedAlbum.artists = $event"
                                        :multiple="true"
                                    />
                                </v-col>
                                <v-col cols="12">
                                    <v-menu
                                        ref="menu"
                                        v-model="dateMenu"
                                        :close-on-content-click="false"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="290px"
                                    >
                                        <template
                                            v-slot:activator="{ on, attrs }"
                                        >
                                            <v-text-field
                                                v-model="
                                                    editedAlbum.release_date
                                                "
                                                :label="$t('Release Date')"
                                                readonly
                                                v-bind="attrs"
                                                outlined
                                                hide-details
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            ref="picker"
                                            v-model="editedAlbum.release_date"
                                            :max="
                                                new Date()
                                                    .toISOString()
                                                    .substr(0, 10)
                                            "
                                            min="1950-01-01"
                                            @change="saveReleaseDate"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" sm="6">
                        <v-switch
                            v-model="editedAlbum.isExclusive"
                            :label="$t('Exclusive')"
                        ></v-switch>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-switch
                            v-model="editedAlbum.isExplicit"
                            :label="$t('Explicit')"
                        ></v-switch>
                    </v-col>
                </v-row>
                <v-row>
                    <edit-external-links
                        :item="editedAlbum"
                        @spotify_link="editedAlbum.spotify_link = $event"
                        @youtube_link="editedAlbum.youtube_link = $event"
                        @soundcloud_link="editedAlbum.soundcloud_link = $event"
                        @itunes_link="editedAlbum.itunes_link = $event"
                        @deezer_link="editedAlbum.deezer_link = $event"
                    />
                </v-row>
                <v-row
                    v-if="
                        $store.getters.getSettings.enable_selling &&
                            canBeProduct
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
                            :disabled="
                                !$store.getters.getSettings.enableBilling
                            "
                            v-model="editedAlbum.isProduct"
                        ></v-switch>
                    </v-col>
                    <v-col cols="12" v-if="editedAlbum.isProduct">
                        <div class="d-flex justify-space-between">
                            <div class="title">{{ $t("Licenses") }}</div>
                            <div></div>
                            <v-btn
                                small
                                class="primary"
                                @click="licenseDialog = true"
                                >{{ $t("Add License") }}</v-btn
                            >
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
                                        <tr
                                            v-for="(license, n) in licenses"
                                            :key="n"
                                        >
                                            <td>{{ license.name }}</td>
                                            <td>
                                                {{
                                                    price(license.amount) +
                                                        defaultCurrency.symbol
                                                }}
                                            </td>
                                            <td>{{ license.description }}</td>
                                            <td>
                                                <v-btn
                                                    @click="
                                                        licenses.splice(n, 1)
                                                    "
                                                    x-small
                                                    fab
                                                    dark
                                                    color="error"
                                                >
                                                    <v-icon
                                                        >$vuetify.icons.delete</v-icon
                                                    >
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
            <v-container class="songs pt-5" v-if="!editedAlbum.new">
                <div class="songs-wrapper">
                    <v-card outlined>
                        <v-card-title>
                            <v-icon color="primary" x-large
                                >$vuetify.icons.music-note</v-icon
                            >
                            <div class="large-icon-text tertiary--text">
                                {{ $t("Songs") }}
                            </div>
                            <v-btn
                                class="mx-2 ml-3"
                                dark
                                small
                                color="primary"
                                @click="editSong('new')"
                            >
                                <v-icon dark>$vuetify.icons.plus</v-icon>
                                {{ $t("New") }}
                            </v-btn>
                            <v-btn
                                class="mx-2"
                                dark
                                small
                                color="secondary"
                                @click="addSong = true"
                            >
                                <v-icon>$vuetify.icons.link</v-icon>
                                {{ $t("Attach") }}
                            </v-btn>
                        </v-card-title>
                        <v-data-table
                            :no-data-text="$t('No data available')"
                            :loading-text="$t('Fetching data') + '...'"
                            :headers="songTableHeaders"
                            :items="editedAlbum.songs || []"
                            :items-per-page="15"
                            hide-default-footer
                            :loading="!editedAlbum.songs"
                        >
                            <template v-slot:body="props">
                                <draggable
                                    v-model="editedAlbum.songs"
                                    v-if="editedAlbum.songs.length"
                                    tag="tbody"
                                >
                                    <tr
                                        v-for="(song,
                                        index) in editedAlbum.songs"
                                        :key="index"
                                    >
                                        <td>
                                            <v-icon
                                                small
                                                class="page__grab-icon"
                                            >
                                                mdi-arrow-all
                                            </v-icon>
                                        </td>
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <div class="img-container py-2">
                                                <v-img
                                                    :src="
                                                        song.cover.image ||
                                                            song.cover
                                                    "
                                                    :alt="song.title"
                                                    class="user-avatar song-cover"
                                                    width="50"
                                                    height="50"
                                                >
                                                    <div
                                                        class="upload-percentage"
                                                        v-if="
                                                            song.progress !=
                                                                null &&
                                                                song.progress <
                                                                    100
                                                        "
                                                    >
                                                        <div
                                                            class="content-text"
                                                        >
                                                            <template
                                                                v-if="
                                                                    song.progress <
                                                                        99
                                                                "
                                                            >
                                                                {{
                                                                    song.progress
                                                                }}%
                                                            </template>
                                                            <template v-else>
                                                                <v-progress-circular
                                                                    :size="15"
                                                                    :width="3"
                                                                    color="grey"
                                                                    indeterminate
                                                                ></v-progress-circular>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </v-img>
                                            </div>
                                        </td>
                                        <td>
                                            <router-link
                                                class="router-link"
                                                :to="{
                                                    name: 'song',
                                                    params: { id: song.id }
                                                }"
                                                target="_blank"
                                            >
                                                {{ song.title }}
                                            </router-link>
                                        </td>
                                        <td>
                                            {{ getArtists(song.artists) }}
                                        </td>
                                        <td class="text-center">
                                            {{ song.nb_plays }}
                                        </td>
                                        <td class="text-center">
                                            {{ song.nb_likes }}
                                        </td>
                                        <td>
                                            {{
                                                moment(song.created_at).format(
                                                    "ll"
                                                )
                                            }}
                                        </td>
                                        <td>
                                            <v-btn
                                                class="mx-2"
                                                @click="editSong(song.id)"
                                                x-small
                                                fab
                                                dark
                                                color="info"
                                            >
                                                <v-icon dark
                                                    >$vuetify.icons.pencil</v-icon
                                                >
                                            </v-btn>
                                            <v-btn
                                                class="mx-2"
                                                @click="detachSong(song.id)"
                                                x-small
                                                fab
                                                dark
                                                :title="
                                                    $t(
                                                        'Detach song from this album'
                                                    )
                                                "
                                                color="secondary"
                                            >
                                                <v-icon dark
                                                    >$vuetify.icons.link-off</v-icon
                                                >
                                            </v-btn>
                                            <v-btn
                                                class="mx-2"
                                                @click="deleteSong(song.id)"
                                                x-small
                                                fab
                                                :title="$t('Delete Song')"
                                                dark
                                                color="error"
                                            >
                                                <v-icon
                                                    >$vuetify.icons.delete</v-icon
                                                >
                                            </v-btn>
                                        </td>
                                    </tr>
                                </draggable>

                                <empty-page
                                    class="empty-songs"
                                    :headline="$t('No Songs!')"
                                    :sub="$t('This album is empty.')"
                                    v-else
                                />
                            </template>
                        </v-data-table>
                    </v-card>
                </div>
            </v-container>
        </template>
        <template v-slot:dialogs>
            <v-dialog
                v-model="editDialog"
                persistent
                scrollable=""
                max-width="950"
                @click:outside="hideAllsongs"
            >
                <template v-for="song in editedAlbum.songs">
                    <edit-song-dialog
                        :key="song.id"
                        v-if="song.isActive"
                        v-show="isShowing(song.id)"
                        @updated="songEdited(song.id)"
                        @progress="updateProgress($event, song.id)"
                        @created="songCreated"
                        @close="closeSong(song.id)"
                        @sleep="sleepSong(song.id)"
                        @wake="wakeSong(song.id)"
                        :song="song"
                        :uploader="creator"
                        :album_id="editedAlbum.id"
                    />
                </template>
            </v-dialog>
            <v-dialog v-model="deleteSongConfirmationDialog" max-width="400">
                <v-card>
                    <div class="p-2">
                        {{
                            $t("Are you sure you wanna delete this") +
                                $t("Song") +
                                "?"
                        }}
                    </div>
                    <v-card-actions>
                        <v-btn color="error" text @click="goodByeSong">{{
                            $t("Delete")
                        }}</v-btn>
                        <v-btn
                            @click="deleteSongConfirmationDialog = false"
                            text
                            >{{ $t("Cancel") }}</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="addSong" max-width="500px">
                <attach-song-dialog
                    v-if="addSong"
                    @songSelected="attachSong($event)"
                    :creator="creator"
                ></attach-song-dialog>
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
                                label="Choose License"
                            >
                                <template v-slot:item="{ item }">
                                    {{ item.name }}
                                    {{
                                        price(item.amount) +
                                            " " +
                                            item.currency_symbol
                                    }}
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.name }}
                                    {{
                                        price(item.amount) +
                                            " " +
                                            item.currency_symbol
                                    }}
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
                                                {{
                                                    price(editedLicense.amount)
                                                }}
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
                    type="album"
                    engine="spotify"
                    v-if="importFromSpotifyDialog"
                    @asset="addSpotifyData($event)"
                />
            </v-dialog>
        </template>
    </edit-dialog>
</template>
<script>
import draggable from "vuedraggable";
import editSongDialog from "./Song";
import attachSongDialog from "../../dialogs/AttachSong";
import AttachAssetDialog from "../../dialogs/admin/AttachAssetDialog.vue";
import Billing from "@mixins/billing/billing";
export default {
    props: ["album", "creator"],
    created() {
        axios.get("/api/prices").then(res => {
            this.previousPrices = this.previousPrices = res.data;
        });
    },
    mixins: [Billing],
    components: {
        draggable,
        editSongDialog,
        AttachAssetDialog,
        attachSongDialog
    },
    computed: {
        isShowing() {
            return song_id =>
                this.editedAlbum.songs[
                    this.editedAlbum.songs.findIndex(
                        song => song.id === song_id
                    )
                ].isShowing;
        },
        canBeProduct() {
            return this.creator === "admin" || this.creator === "artist";
        }
    },
    data() {
        return {
            editedAlbum: JSON.parse(JSON.stringify(this.album)),
            firstCopy: this.album,
            artistsFocused: false,
            editDialog: false,
            artistsComp: true,
            songToBeDeleted: null,
            defaultCoverPath: "/storage/defaults/images/album_cover.png",
            deleteSongConfirmationDialog: false,
            editingSong: null,
            artists: [],
            addSong: false,
            dateMenu: false,
            isLoading: false,
            errors: {},
            licenseDialog: false,
            selectedLicense: 0,
            editedLicense: {},
            licenses:
                (this.album.product ? this.album.product.prices : []) || [],
            importFromSpotifyDialog: false,
            progress: null,
            songTableHeaders: [
                {
                    text: ""
                },
                {
                    text: this.$t("#"),
                    sortable: true
                },
                {
                    text: this.$t("Cover"),
                    align: "start",
                    sortable: false,
                    value: "cover"
                },
                { text: this.$t("Title"), value: "title" },
                { text: this.$t("Artists"), value: "artists", align: "center" },
                { text: this.$t("Plays"), value: "plays", align: "center" },
                { text: this.$t("Likes"), value: "likes", align: "center" },
                { text: this.$t("Created At"), value: "created_at" },
                {
                    text: this.$t("Operations"),
                    value: "operations",
                    align: "center"
                }
            ]
        };
    },
    watch: {
        dateMenu(val) {
            val && setTimeout(() => (this.$refs.picker.activePicker = "YEAR"));
        },
        importFromSpotifyDialog() {
            this.artistsComp = false;
            setTimeout(() => {
                this.artistsComp = true;
            }, 10);
        }
    },
    beforeDestroy() {
        this.$emit("beforeDestroy");
    },
    methods: {
        addLicense() {
            if (this.selectedLicense) {
                var selectedLicense = this.previousPrices.find(
                    wx => wx.id === this.selectedLicense
                );
            } else {
                // validation
                if (!this.editedLicense.name) {
                    return this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Oops!"),
                        text: this.$t("Please provide a name for the license.")
                    });
                }
                if (
                    !this.editedLicense.amount ||
                    isNaN(this.editedLicense.amount)
                ) {
                    return this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Oops!"),
                        text: this.$t("Please enter a correct amount.")
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
                        )
                    });
                }

                var selectedLicense = this.editedLicense;
            }

            this.licenses.push({
                ...selectedLicense
            });

            this.licenseDialog = false;
            this.selectedLicense = 0;
            this.editedLicense = {};
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Added"),
                text: this.$t("License added!")
            });
        },
        closeWindow() {
            let changed = false;
            if (
                JSON.stringify(this.editedAlbum) !=
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
                    /**
                     * Callback Function
                     * @param {Boolean} confirm
                     */
                    callback: confirm => {
                        if (confirm) {
                            this.editedAlbum.title = this.firstCopy.title;
                            this.editedAlbum.artist = this.firstCopy.artist;
                            this.editedAlbum.cover = this.firstCopy.cover;
                            this.editedAlbum.release_date = this.firstCopy.release_date;
                            this.$emit("close");
                        }
                    }
                });
            } else {
                this.$emit("close");
            }
        },
        updateProgress(progress, song_id) {
            let index = this.editedAlbum.songs.findIndex(
                song => song.id === song_id
            );
            this.$set(this.editedAlbum.songs[index], "progress", progress);
        },
        addSpotifyData(data) {
            this.editedAlbum.cover = data.cover;
            this.editedAlbum.title = data.title;
            this.editedAlbum.artists = data.artists;

            this.importFromSpotifyDialog = false;
        },
        saveReleaseDate(date) {
            this.$refs.menu.save(date);
        },
        cancelUpload(song_id) {
            let index = this.editedAlbum.songs.findIndex(
                song => song.id === song_id
            );
            this.editedAlbum.songs[index].requestSource.cancel();
            this.editSong(song_id);
        },
        sleepSong(song_id) {
            this.editDialog = false;
            let index = this.editedAlbum.songs.findIndex(
                song => song.id === song_id
            );
            this.$set(this.editedAlbum.songs[index], "isShowing", false);
        },
        goodByeSong() {
            let index = this.editedAlbum.songs.findIndex(
                song => song.id === this.songToBeDeleted
            );
            if (this.editedAlbum.songs[index].requestSource) {
                this.editedAlbum.songs[index].requestSource.cancel();
            }
            this.deleteSongConfirmationDialog = false;
            this.editedAlbum.songs.splice(index, 1);
            axios
                .delete("/api/songs/" + this.songToBeDeleted)
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Deleted"),
                        text: this.$t("Song") + " " + this.$t("Deleted") + "."
                    });
                })
                .catch(e => {});
            this.songToBeDeleted = null;
        },
        wakeSong(song_id) {
            this.editDialog = true;
            let index = this.editedAlbum.songs.findIndex(
                song => song.id === song_id
            );
            this.$set(this.editedAlbum.songs[index], "isShowing", true);
        },
        hideAllsongs() {
            for (let i = 0; i < this.editedAlbum.songs.length; i++) {
                this.$set(this.editedAlbum.songs[i], "isShowing", false);
            }
            this.editDialog = false;
        },
        imageReady(e) {
            this.editedAlbum.cover = e;
        },
        deleteSong(song_id) {
            this.songToBeDeleted = song_id;
            this.deleteSongConfirmationDialog = true;
        },
        editSong(song_id) {
            if (song_id === "new") {
                this.editedAlbum.songs.unshift({
                    id: Math.floor(Math.random() * (100000 - 5000) + 100000),
                    new: true,
                    genres: [],
                    isActive: true,
                    public: true,
                    isShowing: true,
                    artists: this.editedAlbum.artists,
                    cover: this.editedAlbum.cover.data
                        ? this.editedAlbum.cover.data
                        : this.editedAlbum.cover,
                    nb_likes: 0,
                    nb_plays: 0,
                    source_format: "file"
                });
                this.editDialog = true;
            } else {
                this.editedAlbum.songs[
                    this.editedAlbum.songs.findIndex(
                        song => song.id === song_id
                    )
                ].isActive = true;
                this.editedAlbum.songs[
                    this.editedAlbum.songs.findIndex(
                        song => song.id === song_id
                    )
                ].isShowing = true;
                this.editDialog = true;
            }
        },
        closeSong(song_id) {
            let index = this.editedAlbum.songs.findIndex(
                song => song.id === song_id
            );
            if (this.editedAlbum.songs[index].isShowing) {
                this.editDialog = false;
            }
            this.$set(this.editedAlbum.songs[index], "isShowing", false);
            this.$set(this.editedAlbum.songs[index], "isActive", false);
            this.$forceUpdate();
        },
        songEdited(song_id) {
            let index = this.editedAlbum.songs.findIndex(
                song => song.id === song_id
            );
            this.$set(this.editedAlbum.songs[index], "progress", 0);
            this.$set(this.editedAlbum.songs[index], "isActive", false);
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Saved"),
                text: this.$t("Song") + " " + this.$t("Updated") + "."
            });
        },
        songCreated() {
            this.saveAlbum();
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Created"),
                text: this.$t("Song") + " " + this.$t("Created") + "."
            });
        },
        fetchArtists(search, loading) {
            if (search) {
                loading(true),
                    axios
                        .get("/api/match-artists/" + search)
                        .then(
                            res =>
                                (this.artists = res.data.map(artist => ({
                                    id: artist.id,
                                    displayname: artist.displayname,
                                    avatar: artist.avatar
                                })))
                        )
                        .finally(() => loading(false));
            }
        },
        attachSong(song) {
            axios
                .post("/api/attach-to-album", {
                    album_id: this.album.id,
                    song_id: song.id
                })
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Added"),
                        text: this.$t("Song attached to album.")
                    });
                    this.editedAlbum.songs.push(song);
                    this.addSong = false;
                });
        },
        detachSong(song_id) {
            axios
                .post("/api/detach-from-album", {
                    album_id: this.album.id,
                    song_id: song_id
                })
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Added"),
                        text: this.$t("Song detached from album!")
                    });
                    let index = this.editedAlbum.songs.findIndex(
                        song => song.id === song_id
                    );
                    this.editedAlbum.songs.splice(index, 1);
                });
        },
        saveAlbum(emit) {
            var formData = new FormData();
            this.isLoading = true;
            if (this.editedAlbum.cover && this.editedAlbum.cover.data) {
                // if cover was picked, the value is stored as an object from the CropImage component
                formData.append(
                    "cover",
                    this.editedAlbum.cover.data,
                    this.editedAlbum.cover.title
                );
            } else if (this.editedAlbum.cover && !this.editedAlbum.cover.data) {
                // no cover was picked, the value is stored as a string
                formData.append("cover", this.editedAlbum.cover);
            } else {
                formData.append("cover", this.defaultCoverPath);
            }
            if (this.editedAlbum.title) {
                formData.append("title", this.editedAlbum.title);
            }

            // links
            if (this.editedAlbum.spotify_link) {
                formData.append("spotify_link", this.editedAlbum.title);
            }
            if (this.editedAlbum.youtube_link) {
                formData.append("youtube_link", this.editedAlbum.youtube_link);
            }
            if (this.editedAlbum.soundcloud_link) {
                formData.append(
                    "soundcloud_link",
                    this.editedAlbum.soundcloud_link
                );
            }
            if (this.editedAlbum.itunes_link) {
                formData.append("itunes_link", this.editedAlbum.itunes_link);
            }
            if (this.editedAlbum.deezer_link) {
                formData.append("deezer_link", this.editedAlbum.deezer_link);
            }

            if (this.editedAlbum.release_date) {
                formData.append("release_date", this.editedAlbum.release_date);
            }
            if (this.editedAlbum.songs) {
                formData.append(
                    "songs",
                    JSON.stringify(this.editedAlbum.songs.map(s => s.id))
                );
            }

            if (this.editedAlbum.isExclusive) {
                formData.append("isExclusive", 1);
            } else {
                formData.append("isExclusive", 0);
            }
            if (this.editedAlbum.isExplicit) {
                formData.append("isExplicit", 1);
            } else {
                formData.append("isExplicit", 0);
            }
            if (this.editedAlbum.isProduct) {
                formData.append("isProduct", 1);
                if (!this.licenses.length) {
                     this.isLoading = false;
 return this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: this.$t("Please add atleast one license/price")
                    });
                }
                   
                else {formData.append("licenses", JSON.stringify(this.licenses));}
            } else {
                formData.append("isProduct", 0);
            }
            if (this.editedAlbum.artists.length) {
                formData.append(
                    "artists",
                    JSON.stringify(this.editedAlbum.artists)
                );
            }

            formData.append("created_by", this.creator);
            if (this.editedAlbum.new) {
                axios
                    .post("/api/albums", formData, {
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
                        this.$emit("created");
                        this.isLoading = false;
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
                formData.append("album_id", this.editedAlbum.id);
                formData.append("_method", "PUT");
                axios
                    .post("/api/albums/" + this.editedAlbum.id, formData, {
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
                        if (emit) {
                            this.$emit("updated");
                        }
                        this.isLoading = false;
                    })
                    .catch(e => {
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
        }
    }
};
</script>
<style scoped>
.selected-artist > .artist-name {
    color: var(--light-theme-text-color);
}
.v-application.theme--dark .selected-artist > .artist-name {
    color: var(--dark-theme-text-color);
}
</style>
