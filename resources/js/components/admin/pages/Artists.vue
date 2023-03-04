<template>
  <div class="artists-wrapper">
    <v-card outlined>
      <v-card-title>
        <v-icon color="primary" x-large>$vuetify.icons.account-music</v-icon>
        <v-btn
          class="mx-2"
          dark
          small
          color="primary"
          @click="editArtist('new')"
          v-if="hasPermission('create artists')"
        >
          <v-icon>$vuetify.icons.plus</v-icon> {{ $t("New") }}
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
        :items="artists || []"
        :search="search"
        :items-per-page="25"
        class="elevation-1"
        :loading="!artists"
      >
        <template v-slot:item.displayname="{ item }">
          <router-link
            :to="{
              name: 'artist',
              params: { id: item.id },
            }"
            class="router-link"
            target="_blank"
          >
            {{ item.displayname }}
            <v-icon x-small class="mb-1 ml-1"
              >$vuetify.icons.open-in-new</v-icon
            >
          </router-link>
        </template>
        <template v-slot:item.picture="{ item }">
          <div class="img-container py-2">
            <v-img
              :src="item.avatar"
              :alt="item.name"
              class="user-avatar artist-avatar"
              width="50"
              height="50"
            ></v-img>
          </div>
        </template>
        <template v-slot:item.user="{ item }">
          <template v-if="item.user">
            <router-link
              :to="{ name: 'artist', params: { id: item.id } }"
              class="router-link"
              target="_blank"
            >
              <div class="avatar">
                <v-avatar size="35">
                  <v-img :src="item.user.avatar"></v-img>
                </v-avatar>
              </div>
              <div class="artist-name">
                {{ item.user.email }}
                <v-icon x-small class="mb-1 ml-1"
                  >$vuetify.icons.open-in-new</v-icon
                >
              </div>
            </router-link>
          </template>
          <template v-else> - </template>
        </template>
        <template v-slot:item.firstname="{ item }">
          {{ item.firstname || "-" }}
        </template>
        <template v-slot:item.lastname="{ item }">
          {{ item.lastname || "-" }}
        </template>
        <template v-slot:item.operations="{ item }">
          <v-btn
            class="mx-2"
            @click="editArtist(item)"
            v-if="hasPermission('Edit artists')"
            x-small
            fab
            dark
            color="info"
          >
            <v-icon>$vuetify.icons.pencil</v-icon>
          </v-btn>
          <v-btn
            class="mx-2"
            @click="deleteArtist(item.id)"
            v-if="hasPermission('Delete artists')"
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
    <v-dialog v-model="editDialog" max-width="1200">
      <edit-artist-dialog
        v-if="editDialog"
        @updated="artistUpdated"
        @close="editArtist(null)"
        :artist="editingArtist"
      />
    </v-dialog>
  </div>
</template>
<script>
import editArtistDialog from "../../dialogs/admin/edit/Artist";
export default {
  components: {
    editArtistDialog,
  },
  data() {
    return {
      artists: null,
      search: "",
      headers: [
        { text: this.$t("Avatar"), sortable: false, value: "picture" },
        { text: this.$t("Displayname"), value: "displayname" },
        { text: this.$t("Firstname"), value: "firstname" },
        { text: this.$t("Lastname"), value: "lastname" },
        { text: this.$t("User Account"), value: "user", align: "center" },
        { text: this.$t("Followers"), value: "nb_followers", align: "center" },
        { text: this.$t("Albums"), value: "nb_albums", align: "center" },
        { text: this.$t("Operations"), value: "operations" },
      ],
      editDialog: false,
      editingArtist: null,
    };
  },
  created() {
    this.fetchArtists();
  },
  methods: {
    fetchArtists() {
      axios.get("/api/admin/artists").then((res) => {
        this.artists = res.data;
      });
    },
    deleteArtist(id) {
      this.$confirm({
        message: `${
          this.$t("Are you sure you wanna delete this") +
          " " +
          this.$t("artist") +
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
            axios
              .delete("/api/admin/artists/" + id)
              .then((res) => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Deleted"),
                  text: this.$t("Artist") + " " + this.$t("Deleted") + ".",
                });
                this.fetchArtists();
              })
              .catch();
          }
        },
      });
    },
    editArtist(artist) {
      if (!artist) {
        this.editDialog = false;
        this.editingArtist = null;
      } else if (artist === "new") {
        this.editingArtist = {
          new: true,
        };
        this.editDialog = true;
      } else {
        this.editingArtist = artist;
        this.editDialog = true;
      }
    },
    artistUpdated() {
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Saved"),
        text: this.$t("User") + " " + this.$t("Updated") + ".",
      });
      this.fetchArtists();
    },
  },
};
</script>