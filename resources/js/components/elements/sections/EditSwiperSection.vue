<template>
  <v-card class="section-skeleton" elevation="lg" v-if="section">
    <div class="section-header">
      <div class="section-header__title">
        {{ section.title }}
      </div>
      <div class="section-header__chevrons">
        <v-icon :id="'swiper-button-zuruck-' + section.id"
          >$vuetify.icons.chevron-left</v-icon
        >
        <v-icon :id="'swiper-button-naschte-' + section.id"
          >$vuetify.icons.chevron-right</v-icon
        >
      </div>
    </div>
    <div class="section-body" :style="{ sectionWidth }" v-if="mo7tawa">
      <swiper ref="mySwiper" :options="swiperOptions">
        <template v-for="(item, i) in mo7tawa">
          <swiper-slide
            :key="i"
            class="section-item"
            v-if="!item && !section.endpoint && isOnSectionEdit"
          >
            <div class="absolute fill align-justify-center">
              <v-icon
                size="150"
                class="pointer"
                @click="$emit('attachAsset', i)"
                >$vuetify.icons.plus</v-icon
              >
            </div>
          </swiper-slide>
          <swiper-slide :key="i" v-else-if="item">
            <template v-if="item.type === 'song'">
              <song-expo admin="true" :song="item">
                <template v-slot:control-layer v-if="!section.endpoint">
                  <edit-item-position
                    @moveRight="$emit('moveRight', i)"
                    @moveLeft="$emit('moveLeft', i)"
                    @delete="$emit('delete', i)"
                  ></edit-item-position>
                </template>
              </song-expo>
            </template>
            <template v-else-if="item.type === 'album'">
              <album-expo :album="item">
                <template v-slot:control-layer v-if="!section.endpoint">
                  <edit-item-position
                    @moveRight="$emit('moveRight', i)"
                    @moveLeft="$emit('moveLeft', i)"
                    @delete="$emit('delete', i)"
                  ></edit-item-position>
                </template>
              </album-expo>
            </template>
            <template v-else-if="item.type === 'podcast'">
              <podcast-box :podcast="item">
                <template v-slot:control-layer v-if="!section.endpoint">
                  <edit-item-position
                    @moveRight="$emit('moveRight', i)"
                    @moveLeft="$emit('moveLeft', i)"
                    @delete="$emit('delete', i)"
                  ></edit-item-position>
                </template>
              </podcast-box>
            </template>
            <template v-else-if="item.type === 'genre'">
              <genre :genre="item" :admin="true">
                <template v-slot:control-layer v-if="!section.endpoint">
                  <edit-item-position
                    @moveRight="$emit('moveRight', i)"
                    @moveLeft="$emit('moveLeft', i)"
                    @delete="$emit('delete', i)"
                  ></edit-item-position>
                </template>
              </genre>
            </template>
            <template v-else-if="item.type === 'radio-station'">
              <radio-station :radioStation="item">
                <template v-slot:control-layer v-if="!section.endpoint">
                  <edit-item-position
                    @moveRight="$emit('moveRight', i)"
                    @moveLeft="$emit('moveLeft', i)"
                    @delete="$emit('delete', i)"
                  ></edit-item-position>
                </template>
              </radio-station>
            </template>
            <template v-else-if="item.type === 'playlist'">
              <playlist-expo :playlist="item" :admin="true">
                <template v-slot:control-layer v-if="!section.endpoint">
                  <edit-item-position
                    @moveRight="$emit('moveRight', i)"
                    @moveLeft="$emit('moveLeft', i)"
                    @delete="$emit('delete', i)"
                  ></edit-item-position>
                </template>
              </playlist-expo>
            </template>
          </swiper-slide>
        </template>
      </swiper>
    </div>
  </v-card>
</template>
<script>
import { swiper, swiperSlide } from "vue-awesome-swiper";
import "swiper/css/swiper.css";
import EditItemPosition from "../../dialogs/admin/edit/ItemPosition.vue"
export default {
  props: {
    section: {
      type: Object,
    },
    contentLoading: {
      type: Boolean,
    },
    content: {
      type: Array,
    },
    isOnEditPage: {
      type: Boolean,
    },
    isOnSectionEdit: {
      type: Boolean,
    },
    isEndpoint: {
      type: Boolean,
    },
    showEmpty: {
      type: Boolean,
    },
  },
  created() {
    if (!this.content) {
      this.getContent();
    }
  },
  components: {
    swiper,
    swiperSlide,
    EditItemPosition
  },
  mounted() {
    setTimeout(() => {
      // trigering the section with to fix a bug related with API ( data renderes after the comp mount )
      this.sectionWidth = 1000;
    }, 200);
  },

  data() {
    return {
      sectionWidth: 0,
      contents: [],
      isContentLoading: false,
      serverError: false,
      swiperOptions: {
        slidesPerView: 4,
        spaceBetween: 10,
        navigation: {
          nextEl: "#swiper-button-naschte-" + this.section.id,
          prevEl: "#swiper-button-zuruck-" + this.section.id,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
      },
    };
  },
  computed: {
    mo7tawa() {
      if (this.content && this.content.length) {
        return this.content;
      } else if (this.contents && this.contents.length) {
        return this.contents;
      } else {
        return [];
      }
    },
  },
  watch: {
    content(val) {
      this.contents = val;
    },
  },
  methods: {
    getContent() {
      this.serverError = false;
      this.isContentLoading = true;
      axios
        .get("/api/section/content/" + this.section.id)
        .then((res) => {
          this.contents = res.data;
          this.$emit("content");
        })
        .catch(() => {
          this.contents = [];
          this.serverError = true;
        })
        .finally(() => {
          this.isContentLoading = false;
          this.$emit("content", this.contents);
        });
    },
  },
};
</script>

<style></style>
