<template>
  <div class="podcasts-wrapper relative">
    <disabled-page
      :text="$t('Podcasts are disabled.')"
      v-if="!$store.getters.getSettings.enablePodcasts"
    />
    <v-card outlined>
      <v-card-title>
        <v-icon color="primary" x-large>$vuetify.icons.podcast</v-icon>
        <v-btn
          class="mx-2"
          dark
          small
          color="primary"
          @click="editPodcast('new')"
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
        :items="podcasts || []"
        :items-per-page="25"
        :loading="!podcasts"
        :search="search"
        class="elevation-1"
      >
        <template v-slot:item.cover="{ item }">
          <div class="img-container py-2">
            <v-img
              :src="item.cover"
              :alt="item.title"
              class="user-avatar podcast-cover"
              width="50"
              height="50"
            ></v-img>
          </div>
        </template>
        <template v-slot:item.title="{ item }">
          <router-link
            class="router-link"
            :to="{ name: 'podcast', params: { id: item.id } }"
            target="_blank"
          >
            {{ item.title }}
            <v-icon x-small class="mb-1 ml-1"
              >$vuetify.icons.open-in-new</v-icon
            >
          </router-link>
        </template>
        <template v-slot:item.operations="{ item }">
          <v-btn
            @click="editPodcast(item)"
            x-small
            fab
            dark
            color="info"
          >
            <v-icon>$vuetify.icons.pencil</v-icon>
          </v-btn>
          <v-btn
            @click="deletePodcast(item.id)"
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
      :fullscreen="!editingPodcast.new"
      max-width="950"
      no-click-animation
      persistent
    >
      <edit-podcast-dialog
        v-if="editDialog"
        creator="admin"
        @updated="podcastEdited"
        @created="podcastCreated"
        @close="editPodcast(null)"
        :podcast="editingPodcast"
      />
    </v-dialog>
  </div>
</template>
<script>
import editPodcastDialog from "../../dialogs/edit/Podcast.vue";
export default {
  components: {
    editPodcastDialog,
  },
  data() {
    return {
      podcasts: null,
      search: "",
      headers: [
        {
          text: this.$t("Cover"),
          align: "start",
          sortable: false,
          value: "cover",
        },
        { text: this.$t("Title"), value: "title" },
        { text: this.$t("Podcaster"), value: "artist", align: "center" },
        { text: this.$t("Followers"), value: "followers_count" },
        { text: this.$t("Created At"), value: "created_at" },
        { text: this.$t("Operations"), value: "operations", align: "center" },
      ],
      editDialog: false,
      editingPodcast: {},
    };
  },
  created() {
    this.fetchPodcasts();
  },
  methods: {
    fetchPodcasts() {
      return axios.get("/api/admin/podcasts").then((res) => {
        this.podcasts = res.data;
      });
    },
    deletePodcast(podcast_id) {
      this.$confirm({
        message: `${
          this.$t("Are you sure you wanna delete this") +
          " " +
          this.$t("podcast") +
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
              .delete("/api/podcasts/" + podcast_id)
              .then(() => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Deleted"),
                  text: this.$t("Podcast") + " " + this.$t("Deleted") + ".",
                });
                this.fetchPodcasts();
              })
              .catch();
          }
        },
      });
    },
    editPodcast(podcast) {
      if (!podcast) {
        this.editDialog = false;
        this.editingPodcast = {};
      } else if (podcast === "new") {
        this.editingPodcast = {
          new: true,
          description: "",
          genres: [],
          artist: null,
        };
        this.editDialog = true;
      } else {
        this.editingPodcast = podcast;
        this.editDialog = true;
      }
    },
    podcastEdited() {
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Saved"),
        text: this.$t("Podcast") + " " + this.$t("Updated") + ".",
      });
      this.fetchPodcasts();
    },
    podcastCreated() {
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Created"),
        text: this.$t("Podcast") + " " + this.$t("Created") + ".",
      });
      this.fetchPodcasts().then(() => {
        this.editingPodcast = this.podcasts[0];
        this.editDialog = true;
      });
    },
  },
};
</script>
