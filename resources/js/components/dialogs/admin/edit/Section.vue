<template>
  <edit-dialog
    @callToAction="saveSection"
    @cancel="closeWindow"
    :loading="isLoading"
    editing="section"
  >
    <template v-slot:body>
      <v-container fluid>
        <v-row>
          <v-col cols="12" sm="6">
            <v-text-field
              :label="$t('title')"
              outlined
              v-model="editedSection.title"
              hide-details=""
              :rules="nameRules"
              @update:error="updateErrorState"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6">
            <v-text-field
              outlined
              :label="$t('Number of labels')"
              v-model.number="editedSection.nb_labels"
              hide-details=""
              type="number"
              @input="fillContent(false)"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              :label="$t('Content')"
              outlined
              v-model="editedSection.endpoint"
              hide-details=""
              :items="contents"
              item-text="name"
              item-value="endpoint"
              @change="fillContent(true)"
            ></v-select>
          </v-col>
          <template v-if="$store.getters.getSettings.provider_spotify">
            <v-col cols="12" sm="6">
              <v-select
                outlined
                :label="$t('Content Source')"
                v-model="editedSection.source"
                hide-details=""
                :items="sources"
                item-text="text"
                item-value="value"
                @change="fillContent(true)"
              ></v-select>
            </v-col>
          </template>
          <v-col cols="12" sm="6">
            <v-select
              outlined
              :label="$t('Layout')"
              v-model="editedSection.layout"
              hide-details=""
              :items="layouts"
              item-text="text"
              item-value="value"
              @change="fillContent(false)"
            ></v-select>
          </v-col>
        </v-row>
        <v-row>
          <v-col class="pt-4" cols="12">
            <EditedSection
              v-if="editedSection.layout === 'section'"
              :section="editedSection"
              :contentLoading="contentLoading"
              :isOnSectionEdit="true"
              :content="editedSection.content"
              :isEndpoint="editedSection.endpoint"
              @delete="removeContentItem($event)"
              @moveRight="moveRight($event)"
              @moveLeft="moveLeft($event)"
              @content="contentFetched($event)"
              @attachAsset="attachAsset($event)"
            />
            <List
              v-else-if="editedSection.layout === 'list'"
              :section="editedSection"
              :contentLoading="contentLoading"
              :content="editedSection.content"
              :isOnSectionEdit="true"
              :isEndpoint="editedSection.endpoint"
              @delete="removeContentItem($event)"
              @moveRight="moveRight($event)"
              @moveLeft="moveLeft($event)"
              @content="contentFetched($event)"
              @attachAsset="attachAsset($event)"
            />
            <Featured
              v-else-if="editedSection.layout === 'cards'"
              :section="editedSection"
              :contentLoading="contentLoading"
              :content="editedSection.content"
              :isOnSectionEdit="true"
              :isEndpoint="editedSection.endpoint ? true : false"
              @delete="removeContentItem($event)"
              @moveRight="moveRight($event)"
              @moveLeft="moveLeft($event)"
              @content="contentFetched($event)"
              @attachAsset="attachAsset($event)"
            />
          </v-col>
        </v-row>
      </v-container>
    </template>
    <template v-slot:dialogs>
      <v-dialog v-model="attachAssetDialogBoolean" max-width="500">
        <attach-asset-dialog
          v-if="assetIndex !== null"
          class="p-3"
          :section_type="editedSection.layout"
          :engines="editedSection.source"
          @asset="updateContent($event)"
        />
      </v-dialog>
    </template>
  </edit-dialog>
</template>

<script>
import { swiper, swiperSlide } from "vue-awesome-swiper";
import "swiper/css/swiper.css";
import AttachAssetDialog from "../../../dialogs/admin/AttachAssetDialog";
import EditedSection from "../../../elements/sections/EditSwiperSection.vue";
import List from "../../../elements/lists/List.vue";
import Featured from "../../../elements/sections/layouts/Featured.vue";
export default {
  props: ["section", "page"],
  components: {
    swiper,
    swiperSlide,
    Featured,
    List,
    EditedSection,
    AttachAssetDialog,
  },
  data() {
    return {
      editedSection: JSON.parse(JSON.stringify(this.section)),
      attachAssetDialogBoolean: false,
      firstCopy: JSON.parse(JSON.stringify(this.section)),
      isThereAnError: false,
      assetIndex: null,
      isLoading: false,
      contentLoading: false,
      sources: [
        {
          text: this.$t("Local"),
          value: "local",
        },
        {
          text: "Spotify",
          value: "spotify",
        },
        {
          text: "Listen Notes(podcast only)",
          value: "listen_notes",
        },
        {
          text: this.$t("Mixed"),
          value: "*",
        },
      ],
      layouts: [
        {
          text: "List",
          value: "list",
        },
        {
          text: "Section",
          value: "section",
        },
        {
          text: "Cards",
          value: "cards",
        },
      ],
      contents: [
        {
          name: this.$t("Custom"),
          endpoint: null,
        },
        {
          name: this.$t("Latest Albums"),
          endpoint: "new-releases",
        },
        {
          name: this.$t("Most Played Songs"),
          endpoint: "popular-songs",
        },
        {
          name: this.$t("Most Liked Songs"),
          endpoint: "most-liked-songs",
        },
        // {
        //     name: this.$t("Most played Albums"),
        //     endpoint: "popular-albums"
        // },
        {
          name:
            this.$t("Best Podcasts") +
            (!this.$store.getters.getSettings.enablePodcasts
              ? "(" + this.$t("podcasts disabled") + ")"
              : ""),
          endpoint: "best-podcasts",
          sources: "podcasts",
          disabled:
            !this.$store.getters.getSettings.enablePodcasts ||
            !this.$store.getters.getSettings.provider_listenNotes,
        },
        {
          name:
            this.$t("Most Played Podcasts") +
            (!this.$store.getters.getSettings.enablePodcasts
              ? "(" + this.$t("podcasts disabled") + ")"
              : ""),
          endpoint: "popular-podcasts",
          sources: "podcasts",
          disabled: !this.$store.getters.getSettings.enablePodcasts,
        },
        {
          name:
            this.$t("Latest Podcasts(local)") +
            (!this.$store.getters.getSettings.enablePodcasts
              ? "(" + this.$t("podcasts disabled") + ")"
              : ""),
          title: this.$t("Latest Podcasts"),
          endpoint: "latest-podcasts",
          sources: "podcasts",
          disabled: !this.$store.getters.getSettings.enablePodcasts,
        },
        // upcoming feature
        // {
        //     name: 'The reccomended songs for the user (based on the genres he played)',
        //     title: 'You may also like',
        //     endpoint: 'you-may-also-like',
        //     sources: 'songs'
        // },
      ],
      nameRules: [
        (value) => !!value || this.$t("Required"),
        (value) => value.length <= 40 || this.$t("Max 40 characters"),
        (value) => value.length >= 2 || this.$t("Min 2 characters"),
      ],
    };
  },
  computed: {
    contentComputed() {
      if (this.editedSection.content) {
        return this.editedSection.content.slice(0, this.section.nb_labels);
      } else {
        return [];
      }
    },
  },
  methods: {
    moveLeft(index) {
      if (this.editedSection.content[index - 1] !== undefined) {
        var previous = this.editedSection.content[index - 1];
        var current = this.editedSection.content[index];
        this.$set(this.editedSection.content, index, previous);
        this.$set(this.editedSection.content, index - 1, current);
      }
    },
    moveRight(index) {
      if (this.editedSection.content[index + 1] !== undefined) {
        var next = this.editedSection.content[index + 1];
        var current = this.editedSection.content[index];
        this.$set(this.editedSection.content, index, next);
        this.$set(this.editedSection.content, index + 1, current);
      }
    },
    removeContentItem(index) {
      this.$set(this.editedSection.content, index, null);
    },
    closeWindow() {
      this.$emit("close");
    },
    updateErrorState(event) {
      this.isThereAnError = event;
    },
    async createPageFirst() {
      const formData = new FormData();
      formData.append("title", this.page.title || "");
      formData.append("description", this.page.description || "");
      formData.append("name", this.page.name || "");
      formData.append("icon", this.page.icon || "");
      formData.append("path", this.page.path || "");
      let res = await axios.post("/api/admin/pages", formData);
      return res.data;
    },
    async saveSection() {
      this.isLoading = true;
      if (this.editedSection.nb_labels > this.editedSection.content.length) {
        this.editedSection.nb_labels = this.editedSection.content.length;
      }
      if (this.editedSection.content.find((a) => a && a.id)) {
        this.editedSection.content = this.editedSection.content
          .splice(0, this.editedSection.nb_labels)
          .filter((asset) => asset);
      } else {
        this.isLoading = false;
        return this.$notify({
          group: "foo",
          type: "error",
          title: this.$t("Error"),
          text: this.$t("Please add some content to your section!"),
        });
      }
      var formData = new FormData();
      formData.append("title", this.editedSection.title || "");
      formData.append("source", this.editedSection.source || "");
      formData.append("layout", this.editedSection.layout || "");
      formData.append(
        "content",
        JSON.stringify(
          this.editedSection.content.map((c) => {
            return {
              id: c.id,
              type: c.type,
            };
          })
        ) || ""
      );
      formData.append("nb_labels", this.editedSection.nb_labels || "");
      formData.append("endpoint", this.editedSection.endpoint || "");
      if (!this.page.id) {
        var page = await this.createPageFirst();
        this.$emit("id", page.id);
      }
      formData.append("page_id", this.page.id || page.id || "");
      if (this.editedSection.new) {
        axios
          .post("/api/admin/sections", formData)
          .then(() => {
            this.$emit("created");
            this.$notify({
              group: "foo",
              type: "success",
              title: this.$t("Created"),
              text: this.$t("The section was created successfully."),
            });
          })
          .finally(() => (this.isLoading = false));
      } else {
        formData.append("_method", "PUT");
        axios
          .post("/api/admin/sections/" + this.editedSection.id, formData)
          .then(() => {
            this.$emit("updated");
            this.$notify({
              group: "foo",
              type: "success",
              title: this.$t("Updated"),
              text:
                this.$t("Section was") + " " + this.$t("updated successfully."),
            });
          })
          .finally(() => (this.isLoading = false));
      }
    },
    fillContent(reset) {
      if (reset) {
        this.resetContent();
      }
      if (this.editedSection.endpoint) {
        this.contentLoading = true;
        if (this.editedSection.endpoint === "best-podcasts") {
          this.editedSection.source = "listen_notes";
        }
        axios
          .get(
            "/api/" +
              this.editedSection.endpoint +
              "?nb_items=" +
              this.editedSection.nb_labels +
              "&src=" +
              this.editedSection.source
          )
          .then((res) => {
            this.$set(this.editedSection, "content", res.data);
          })
          .finally(() => (this.contentLoading = false));
      } else {
        if (this.editedSection.content) {
          if (
            this.editedSection.nb_labels > this.editedSection.content.length
          ) {
            const diff =
              this.editedSection.nb_labels - this.editedSection.content.length;
            for (let i = 0; i < diff; i++) {
              this.editedSection.content.push(null);
              this.$set(
                this.editedSection,
                "content",
                this.editedSection.content
              );
            }
          } else if (
            this.editedSection.nb_labels < this.editedSection.content.length
          ) {
            this.editedSection.content.splice(
              0,
              this.editedSection.content.length - this.editedSection.nb_labels
            );
            this.$set(
              this.editedSection,
              "content",
              this.editedSection.content
            );
          }
        }
      }
    },
    contentFetched(content) {
      this.editedSection.content = content;
      this.fillContent();
    },
    resetContent() {
      var arr = [];
      for (let i = 0; i < this.editedSection.nb_labels; i++) {
        arr.push(null);
      }
      this.$set(this.editedSection, "content", arr);
    },
    attachAsset(index) {
      this.attachAssetDialogBoolean = true;
      this.assetIndex = index;
    },
    updateContent(event) {
      this.$set(this.editedSection.content, this.assetIndex, event);
      this.attachAssetDialogBoolean = false;
      this.assetIndex = null;
    },
  },
};
</script>
<style lang="scss" scoped>
.section-header__title {
  margin-bottom: 0 !important;
}
.control-layer {
  .actions {
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
}
</style>
