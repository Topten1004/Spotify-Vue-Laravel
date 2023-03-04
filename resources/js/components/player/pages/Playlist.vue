<template>
  <fixed-left v-if="playlist">
    <template v-slot:img>
      <div class="img-cover">
        <v-img :src="playlist.cover" class="img" aspect-ratio="1">
          <template v-slot:placeholder>
            <div class="fixed-area__image-skeleton">
              <content-placeholders :rounded="true">
                <content-placeholders-img />
              </content-placeholders>
            </div>
          </template>
        </v-img>
      </div>
    </template>
    <template v-slot:infos>
      <div class="info-wrapper">
        <div class="info-badge">{{ $t("PLAYLIST") }}</div>
        <div class="title justify-between">
          {{ playlist.title }}
          <v-icon v-if="!playlist.public" :title="$t('Private playlist')"
            >$vuetify.icons.key</v-icon
          >
          <div class="menu-toggle" v-if="playlist.songs.length">
            <v-icon @click="showMenu = !showMenu">$vuetify.icons.menu</v-icon>
            <abs-menu v-if="showMenu" :style="{ top: '24px', right: 0 }">
              <ul class="abs-menu-container__list">
                <li
                  @click="
                    $store.commit('pushIntoQueue', playlist.songs);
                    showMenu = false;
                  "
                >
                  <v-icon>$vuetify.icons.account-music</v-icon>
                  {{ $t("Add To Queue") }}
                </li>
              </ul>
            </abs-menu>
          </div>
        </div>
      </div>
    </template>
    <template v-slot:buttons>
      <div class="buttons">
        <div class="btn-container">
          <v-btn
            class="primary"
            rounded
            small
            :disabled="!playlist.songs.length"
            @click="$store.dispatch('playPlaylist', { playlist })"
            >{{ $t("Play") }}</v-btn
          >
        </div>
        <div
          class="btn-container"
          v-if="
            playlist.user &&
            $store.getters.getUser &&
            playlist.user.id == $store.getters.getUser.id
          "
        >
          <v-btn
            class="info white--text"
            rounded
            small
            @click="editPlaylist = true"
            >{{ $t("Edit") }}</v-btn
          >
        </div>
        <div class="btn-container" v-else>
          <v-btn
            class="secondary"
            rounded
            small
            @click="followPlaylist"
            v-if="!isFollowed"
            >{{ $t("Follow") }}</v-btn
          >
          <v-btn class="primary" rounded small @click="followPlaylist" v-else>{{
            $t("Unfollow")
          }}</v-btn>
        </div>
      </div>
    </template>
    <template v-slot:main>
      <edit-playlist
        v-if="editPlaylist"
        @updated="playlistUpdated"
        @cancel="editPlaylist = false"
        :playlist="playlist"
      />
      <div
        class="trcks-container"
        v-if="playlist.songs && playlist.songs.filter((valid) => valid).length"
      >
        <!-- <playlist-song-list
                    :mine="
                        playlist.user &&
                            $store.getters.getUser &&
                            playlist.user.id == $store.getters.getUser.id
                    "
                    :songs="playlist.songs"
                    @deleted="spliceSong($event)"
                    :playlist_id="playlist.id"
                    headers="true"
                    ranked="true"
                /> -->
        <List
          :isAlbum="true"
          :isPlaylistOwner="
            playlist.user &&
            $store.getters.getUser &&
            playlist.user.id == $store.getters.getUser.id
          "
          :playlist_id="playlist.id"
          @deleted="spliceSong($event)"
          :content="playlist.songs"
          class="song-list"
          headers="true"
          ranked="true"
        />
      </div>
      <empty-page
        img="peep-68.png"
        :headline="$t('No Songs!')"
        :sub="$t('This playlist is empty.')"
        v-else
      >
      </empty-page>
    </template>
  </fixed-left>
  <empty-page
    v-else-if="!playlist && errorStatus == 404"
    :headline="$t('Not Available!')"
    :sub="
      $t(
        'Playlist does not exist or maybe it is not available for public access. who knows!'
      )
    "
    img="peep-68.png"
  >
    <template v-slot:button>
      <v-btn class="primary" rounded small @click="$router.go(-1)">{{
        $t("Go Back")
      }}</v-btn>
    </template>
  </empty-page>
  <empty-page
    v-else-if="!playlist && errorStatus"
    :headline="$t('Something wrong happend!')"
    :sub="$t('Some server error has occurred. Try again later.')"
    img="peep-68.png"
  >
    <template v-slot:button>
      <v-btn class="primary" rounded small @click="$router.go(-1)">{{
        $t("Go Back")
      }}</v-btn>
    </template>
  </empty-page>
  <page-loading v-else />
</template>

<script>
import playlistSongList from "../../elements/lists/playlistSongs";
import EditPlaylistDialog from "../../dialogs/EditPlaylist";
export default {
  metaInfo() {
    return {
      title: this.generateMetaInfos(
        this.playlist,
        this.$store.getters.getSettings.playlistPageTitle,
        "playlist"
      ),
      meta: [
        {
          name: "description",
          content: this.generateMetaInfos(
            this.playlist,
            this.$store.getters.getSettings.playlistPageDescription,
            "playlist"
          ),
        },
      ],
    };
  },
  components: {
    playlistSongList,
    EditPlaylist: EditPlaylistDialog,
  },
  created() {
    this.fetchPlaylist();
  },
  data() {
    return {
      playlist: null,
      showMenu: false,
      editPlaylist: false,
      errorStatus: null,
    };
  },
  computed: {
    isFollowed() {
      return (this.$store.getters.getFollowedPlaylists || []).some(
        (playlist) => playlist.id === this.playlist.id
      );
    },
  },
  methods: {
    spliceSong(song_id) {
      this.playlist.songs.splice(
        this.playlist.songs.findIndex((song) => song.id === song_id),
        1
      );
    },
    fetchPlaylist() {
      axios
        .get("/api/playlist/" + this.$route.params.id)
        .then((res) => (this.playlist = res.data))
        .catch((e) => {
          this.errorStatus = e.response.status;
        });
    },
    followPlaylist() {
      if (this.isFollowed) {
        this.$store
          .dispatch("unfollow", this.playlist)
          .then(() => {
            // this.isFollowed = false;
          })
          .catch(() => {});
      } else {
        this.$store
          .dispatch("follow", this.playlist)
          .then(() => {
            // this.isFollowed = true;
          })
          .catch(() => {});
      }
    },
    playlistUpdated() {
      this.editPlaylist = false;
      this.fetchPlaylist();
    },
    deletePlaylist() {
      this.$store
        .dispatch("delete_user_playlist", this.playlist.id)
        .then((res) => {
          this.$notify({
            group: "foo",
            type: "success",
            title: this.$t("Deleted"),
            text: this.$t("Playlist") + " " + this.$t("deleted successfully."),
          });
          this.showMenu = false;
          this.$router.push({ name: "library.playlists" });
        })
        .catch(() => {
          this.$notify({
            group: "foo",
            type: "danger",
            title: this.$t("Oops!"),
            text: this.$t("Something went wrong. Please try again."),
          });
        });
    },
  },
};
</script>
