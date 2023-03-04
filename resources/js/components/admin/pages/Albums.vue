<template>
  <div class="albums-wrapper">
    <v-card outlined>
      <v-card-title>
        <v-icon color="primary" x-large>$vuetify.icons.album</v-icon>
        <v-btn
          class="mx-2"
          dark
          small
          color="primary"
          @click="editAlbum('new')"
        >
          <v-icon>$vuetify.icons.plus</v-icon> {{ $t('New') }}
        </v-btn>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <div class="admin-search-bar">
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
           :label="$t('Search')"
            single-line
            hide-details
          ></v-text-field>
        </div>
      </v-card-title>
      <v-data-table
        :no-data-text="$t('No data available')"
        :loading-text="$t('Fetching data') + '...'"
        :headers="headers"
        :items="albums || []"
        :items-per-page="25"
        :loading="!albums"
        :search="search"
        class="elevation-1"
      >
        <template v-slot:item.cover="{ item }">
          <div class="img-container py-2">
            <v-img
              :src="item.cover"
              :alt="item.title"
              class="album-cover"
              width="50"
              height="50"
            ></v-img>
          </div>
        </template>
        <template v-slot:item.artists="{ item }">
          <artists :artists="item.artists"></artists>
        </template>
        <template v-slot:item.title="{ item }">
          <router-link
            class="router-link"
            :to="{ name: 'album', params: { id: item.id } }"
            target="_blank"
          >
            {{ item.title }}
            <v-icon x-small class="mb-1 ml-1">$vuetify.icons.open-in-new</v-icon>
          </router-link>
        </template>
        <template v-slot:item.operations="{ item }">
          <v-btn
            @click="editAlbum(item)"
            x-small
            fab
            dark
            color="info"
          >
            <v-icon>$vuetify.icons.pencil</v-icon>
          </v-btn>
          <v-btn
            @click="deleteAlbum(item.id)"
            x-small
            fab
            dark
            color="error"
          >
            <v-icon>$vuetify.icons.delete</v-icon>
          </v-btn>
        </template>
        <template v-slot:item.artist="{ item }">
          <template v-if="item.artist">
            <router-link
              class="router-link"
              :to="{ name: 'artist', params: { id: item.artist.id } }"
              target="_blank"
            >
              <div class="avatar">
                <v-avatar size="35">
                  <v-img :src="item.artist.avatar"></v-img>
                </v-avatar>
              </div>
              <div class="artist-name">
                {{ item.artist.displayname }}
                <v-icon x-small class="mb-1 ml-1">$vuetify.icons.open-in-new</v-icon>
              </div>
            </router-link>
          </template>
          <template v-else> - </template>
        </template>
        <template v-slot:item.release_date="{ item }">
          <div>
            {{ moment(item.release_date).format("ll") }}
          </div>
        </template>
        <template v-slot:item.created_at="{ item }">
          <div>
            {{ moment(item.created_at).format("ll") }}
          </div>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog
      v-model="editDialog"
      :fullscreen="!editingAlbum.new"
      max-width="950"
      no-click-animation
      persistent
    >
      <edit-album-dialog
        v-if="editDialog"
        @beforeDestroy="fetchAlbums"
        @updated="albumEdited"
        @created="albumCreated"
        @close="editAlbum(null)"
        :album="editingAlbum"
        creator="admin"
      />
    </v-dialog>
  </div>
</template>
<script>
// import Artist from '../../dialogs/admin/edit/Artist.vue';
import editAlbumDialog from "../../dialogs/edit/Album";
export default {
  components: {
    editAlbumDialog,
  },
    data() {
    return {
      albums: null,
      search: "",
            editingAlbum: {},
      headers: [
        {
          text: this.$t("Cover"),
          align: "start",
          sortable: false,
          value: "cover",
        },
        { text: this.$t('Title'), value: "title" },
        { text: this.$t('Artists'), value: "artists", align: "center" },
        { text: this.$t('Plays'), value: "plays" },
        { text: this.$t('Likes'), value: "likes" },
        { text: this.$t('Released At'), value: "release_date" },
        { text: this.$t('Created At'), value: "created_at" },
        { text: this.$t('Operations'), value: "operations", align: "center" },
      ],
      editDialog: false,
    };
  },
  created() {
    this.fetchAlbums();
  },
  methods: {
    fetchAlbums() {
      return axios.get("/api/admin/albums").then((res) => {
        this.albums = res.data;
      });
    },
    deleteAlbum(album_id) {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna delete this") + " " + this.$t('Album') + "?"}`,
        button: {
          no: this.$t('No'),
          yes: this.$t('Yes'),
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            axios
              .delete("/api/admin/albums/" + album_id)
              .then(() => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Deleted"),
                  text: this.$t('Album')  + " " + this.$t('Deleted') + ".",
                });
                this.fetchAlbums();
              })
              .catch();
          }
        },
      });
    },
    editAlbum(album) {
      if (!album) {
        this.editDialog = false;
        this.editingAlbum = {};
      } else if (album === "new") {
        this.editingAlbum = {
          new: true,
          artists: []
        };
        this.editDialog = true;
      } else {
        this.editingAlbum = album;
        this.editDialog = true;
      }
    },
    albumEdited() {
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Saved"),
        text: this.$t('Album') + " " +  this.$t('Updated') + ".",
      });
      this.fetchAlbums();
    },
    albumCreated() {
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Created"),
        text: this.$t('Album') + " " + this.$t('Created') + ".",
      });
      this.fetchAlbums().then(() => {
        this.editingAlbum = this.albums[0];
        this.editDialog = true;
      });
    },
  },
};
</script>
<style>
.album-cover {
  border-radius: 5px;
}
</style>