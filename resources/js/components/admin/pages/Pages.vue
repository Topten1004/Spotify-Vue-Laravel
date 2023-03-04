<template>
  <div class="pages-wrapper">
    <v-card outlined>
      <v-card-title>
        <v-icon color="primary" x-large
          >$vuetify.icons.text-box-multiple-outline</v-icon
        >
        <v-btn class="mx-2" dark small color="primary" @click="editPage('new')">
          <v-icon>$vuetify.icons.plus</v-icon> {{ $t("New") }} {{ $t("Page") }}
        </v-btn>
      </v-card-title>
      <v-data-table
        :no-data-text="$t('No data available')"
        :loading-text="$t('Fetching data') + '...'"
        :headers="headers"
        :items="pages || []"
        :items-per-page="25"
        :loading="!pages"
        class="elevation-1"
      >
        <template v-slot:item.name="{ item }">
          <router-link
            :to="{
              path: item.path
            }"
            class="router-link"
            target="_blank"
          >
            {{ item.name }}
            <v-icon x-small class="mb-1 ml-1"
              >$vuetify.icons.open-in-new</v-icon
            >
          </router-link>
        </template>
        <template v-slot:item.operations="{ item }">
          <v-btn
            class="mx-2"
            @click="editPage(item)"
            x-small
            fab
            dark
            color="info"
          >
            <v-icon>$vuetify.icons.pencil</v-icon>
          </v-btn>
          <v-btn
            class="mx-2"
            @click="deletePage(item.id)"
            v-if="item.id !== 1"
            x-small
            fab
            dark
            color="error"
          >
            <v-icon>$vuetify.icons.delete</v-icon>
          </v-btn>
        </template>
        <template v-slot:item.created_at="{ item }">
          <div>
            {{ moment(item.created_at).format("ll") }}
          </div>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="editDialog" no-click-animation persistent fullscreen>
      <edit-page-dialog
        v-if="editDialog"
        @updated="pageCreated"
        @created="pageCreated"
        @close="editPage(null)"
        :page="editingPage"
      />
    </v-dialog>
  </div>
</template>

<script>
import editPageDialog from "../../dialogs/admin/edit/Page";
export default {
  components: {
    editPageDialog,
  },
  created() {
    this.fetchPages();
  },
  data() {
    return {
      pages: null,
      editingPage: {},
      headers: [
        { text: this.$t("Name"), value: "name" },
        { text: this.$t("Operations"), value: "operations", align: "end" },
      ],
      editDialog: false,
    };
  },
  methods: {
    fetchPages() {
      axios.get("/api/admin/pages").then((res) => {
        this.pages = res.data;
      });
    },
    deletePage(page_id) {
      this.$confirm({
        message: `${
          this.$t("Are you sure you wanna delete this") +
          " " +
          this.$t("Page") +
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
              .delete("/api/admin/pages/" + page_id)
              .then((res) => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Deleted"),
                  text: this.$t("Page") + " " + this.$t("Deleted") + ".",
                });
                this.fetchPages();
              })
              .catch();
          }
        },
      });
    },
    editPage(page) {
      if (!page) {
        this.editDialog = false;
        this.editingPage = {};
      } else if (page === "new") {
        this.editingPage = {
          new: true,
          name: "Custom name",
          title: this.$t("Custom title"),
          icon: "music-circle",
          isEditable: true,
          description: "Custom description",
          path: "/page/custom",
          sections: [],
        };
        this.editDialog = true;
      } else {
        this.editingPage = page;
        this.editDialog = true;
      }
    },
    pageEdited() {
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Saved"),
        text: this.$t("Page") + " " + this.$t("Updated") + ".",
      });
      this.fetchPages();
    },
    pageCreated() {
      this.pages = null;
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Created"),
        text: this.$t("Page") + " " + this.$t("Updated") + ".",
      });
      this.fetchPages();
    },
  },
};
</script>

<style>
</style>