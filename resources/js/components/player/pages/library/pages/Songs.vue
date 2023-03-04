<template>
    <div class="local_songs-wrapper">
        <div class="header pr-5" v-if="songs">
            <div class="content-text">
                <div class="title">
                    <h3>{{$t('My Songs')}}</h3>
                </div>
                <div class="nb_songs">
                    <span class="highlight"
                        >{{
                            bytesToMB($store.getters.getUser.used_disk_space)
                        }}
                        / {{ ($store.getters.getUser.available_disk_space) }} {{$t('MB')}}
                        {{$t('of space used')}}.
                    </span>
                </div>
            </div>
            <div class="buttons">
                <v-btn
                    class="primary"
                    rounded
                    small
                    @click="$store.dispatch('updateQueue', { content: songs, reset: true })"
                    :disabled="!songs.length"
                >
                    {{$t('Play')}}
                </v-btn>
                <v-btn
                    class="info"
                    rounded
                    small
                    :disabled="!hasPermission('Upload songs')"
                    @click="createSongDialog"
                >
                    <v-icon left>$vuetify.icons.plus</v-icon> {{$t('Upload')}}
                </v-btn>
            </div>
        </div>
        <song-list :songs="songs" class="song-list" :options="[
            'privacy',
            'download',
            'delete'
        ]
        " />
        <empty-page
            v-if="songs && !songs.length"
            img="peep-34.png"
            :headline="$t('No songs yet!')"
            :sub="$t('This page is empty.')"
        />
          <v-dialog
            persistent
            v-model="uplaodSongDialog"
            max-width="950"
        >
        <edit-song-dialog
            v-if="uplaodSongDialog"
            uploader="user"
            @created="songCreated"
            @close="uplaodSongDialog = false"
            :song="song"
        />
        </v-dialog>
    </div>
</template>
<script>
import editSongDialog from "../../../../dialogs/edit/Song";
export default {
    components: {
        editSongDialog
    },
    created() {
        this.fetchOwnSongs();
    },
    data() {
        return {
            uplaodSongDialog: false,
            song: {}
        }
    },
    computed: {
        songs() {
            return this.$store.getters.getOwnSongs;
        }
    },
    methods: {
        fetchOwnSongs() {
            this.$store.dispatch("fetchOwnSongs");
        },
        createSongDialog() {
            this.song = {
                // id: Math.floor(Math.random() * (100000 - 5000) + 100000),
                new: true,
                cover: "/storage/defaults/images/song_cover.png",
                genres: [],
                artists: [],
                isProduct: false,
                isExplicit: false,
                isExclusive: false,
                public: true,
                source: "",
                source_format: "file"
            }
            this.uplaodSongDialog = true;
        },
        songCreated() {
            this.song = {};
            if( this.$store.getters.getSettings.ga4 && this.$store.getters.getSettings.analytics_fileUpload_event ) {
                emitAnalyticsEvent({
                    action: 'file_upload',
                    category: 'song',
                    label: 'Song file uploaded'
                })
            }
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Created"),
                text: this.$t("Song") + " " + this.$t("Created") +  "."
            });
            this.fetchOwnSongs()
        }
    }
};
</script>
