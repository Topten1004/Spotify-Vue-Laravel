<template>
  <div class="section-container featured-section" v-if="!serverError">
    <div class="section-header">
      <div class="section-header__title">
        {{ $t(section.title) }}
      </div>
    </div>
    <div class="section-body">
      <template v-if="!isContentLoading">
        <template v-if="mo7tawa.length">
          <template v-for="(item, i) in mo7tawa">
            <div
              class="featured-card-placeholder"
              v-if="!isEndpoint && !item && isOnSectionEdit"
              :key="i"
            >
              <div
                class="child absolute fill-5 align-justify-center"
                @click="$emit('attachAsset', i)"
              >
                <v-icon size="30" class="pointer">$vuetify.icons.plus</v-icon>
              </div>
            </div>
            <div
              class="featured-card"
              v-else-if="item && item.type !== 'genre'"
              :key="item.id"
            >
              <div class="body">
                <div class="cover-layer absolute fill"  @click="item.type !== 'radio-station'? $router.push({ name: item.type, params: { id: item.id } }) : ''"></div>
                <div class="cover">
                  <v-img class="img" :src="item.cover">
                    <div
                      class="dark-layer"
                      v-if="$store.getters.isCurrentlyPlaying(item)"
                    >
                      <div class="icon icon-inside-cover absolute fill">
                        <div class="epico_music-is-playing-container">
                          <span></span>
                          <span></span>
                          <span></span>
                        </div>
                      </div>
                    </div>
                    <div class="dark-layer play" v-else>
                      <div class="play-button">
                        <v-btn
                          icon
                          color="white"
                          class="btn"
                          @click="!isOnEditPage ? play(item) : ''"
                        >
                          <v-icon size="45">$vuetify.icons.play-circle</v-icon>
                        </v-btn>
                      </div>
                    </div>
                  </v-img>
                  <div class="live-dot" v-if="item.streamEndpoint">
                    <span class="absolute top-0">
                      <svg height="20" width="20" class="blinking">
                        <circle cx="10" cy="10" r="4" fill="red"></circle>
                      </svg>
                    </span>
                  </div>
                </div>
                <div class="right-side">
                  <div
                    class="card-title max-1-lines"
                   
                    v-text="
                      item.type == 'radio-station' ? item.name : item.title
                    "
                  ></div>
                  <td v-if="!isOnEditPage && !item.streamEndpoint">
                    <div
                      class="menu-button option-menu"
                      @click="$store.commit('setSongMenu', 'album-' + item.id)"
                    >
                      <song-menu
                        :item="item"
                        :closeOnContentClick="true"
                        @close="$store.commit('setSongMenu', null)"
                      ></song-menu>
                    </div>
                  </td>
                  <purchase-btn :item="item" size="small"></purchase-btn>
                  <!-- <div
                    class="epico_music-is-playing-container"
                    v-else-if="!isOnSectionEdit"
                  >
                    <span></span>
                    <span></span>
                    <span></span>
                  </div> -->
                </div>
              </div>
            </div>
          </template>
        </template>
        <empty-page
          v-else-if="!mo7tawa.length && !isContentLoading"
          :headline="$t('No content!')"
          v-show="showEmpty"
          :sub="$t('There is no content available yet for this section.')"
        ></empty-page>
      </template>
      <template v-else>
        <div
          class="featured-card featured-card__skeleton"
          v-for="n in 8"
          :key="n"
        >
          <content-placeholders :rounded="true">
            <content-placeholders-img />
          </content-placeholders>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import purchaseBtn from "../../../elements/other/ProductBtn.vue";
export default {
  components: {
    purchaseBtn,
  },
  props: {
    section: {
      type: Object,
    },
    contentLoading: {
      type: Boolean,
    },
    content: {
      type: Object,
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
    } else {
      this.contentLength = this.content.length;
      this.$emit("contentLength", this.content.length);
    }
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
  data() {
    return {
      contents: [],
      contentLength: 0,
      isContentLoading: false,
      serverError: false,
    };
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
          this.contentLength = res.data.length;
          this.$emit("content");
          this.$emit("contentLength", res.data.length ? 0 : -1);
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
    async play(item) {
      const params = {};
      params[item.type] = item;
      params.reset = true;
      if (item.type !== "radio-station") {
        this.$store.dispatch(
          "play" + item.type[0].toUpperCase() + item.type.substr(1),
          params
        );
      } else {
        this.$store.dispatch("playRadioStation", {
          radioStation: item,
        });
      }
    },
  },
};
</script>

<style lang="scss">
.featured-section > .section-body {
  display: flex;
  flex-wrap: wrap;
  .featured-card,
  .featured-card-placeholder {
    display: flex;
    flex-basis: 25%;
    padding: 0.5em 1.2em;
    border-radius: 5px;
    overflow: hidden;
    @media (max-width: 1800px) {
      flex-basis: 33%;
      width: 33%;
    }
    @media (max-width: 1200px) {
      flex-basis: 50%;
      width: 50%;
    }
    @media (max-width: 700px) {
      flex-basis: 100%;
      width: 100%;
    }
    .body {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      position: relative;
      box-shadow: 1px 2px 5px 1px rgb(0 0 0 / 15%);
      flex-grow: 1;
      max-width: 100%;
      border-top-right-radius: 5px;
      border-bottom-right-radius: 5px;
      .right-side {
        display: flex;
        padding: 0 1.3em;
        align-items: center;
        height: 100%;
        max-width: 92%;
        flex-grow: 2;
        justify-content: space-between;
      }
      .card-title {
        font-weight: bold;
        max-width: 90%;
        flex-grow: 1;
      }
      .cover {
        width: 80px;
        height: 80px;
      }
      .img {
        width: 100%;
        height: 100%;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
      }
      .img {
        // box-shadow: 0px 1px 3px 3px rgb(0 0 0 / 25%);
      }
      .icon-inside-cover {
        width: 100%;
        left: 0;
        display: flex;
        align-items: center;
        position: absolute;
        justify-content: center;
        .epico_music-is-playing-container {
          margin-left: unset;
        }
        .epico_music-is-playing-container > span {
          width: 6px;
          background-color: white;
          margin-top: auto;
          height: 80%;
        }
      }
      .dark-layer.play {
        background-color: transparent;
      }
      .play-button {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        .btn {
          opacity: 0;
          transition: all 0.3s;
          transform-origin: center;
          &:hover {
            transform: scale(1.1);
          }
        }
      }
    }
    .cover-layer {
      background-color: #cccccc;
      opacity: 0.1;
      border-radius: 5px;
      z-index: 0;
      border-top-right-radius: 5px;
      border-bottom-right-radius: 5px;
    }
    .option-menu {
      opacity: 0;
      margin-right: 3em;
    }
    &:hover {
      .play-button {
        .btn {
          opacity: 1;
        }
      }
      .cover-layer {
        opacity: 0.27;
      }
      .option-menu {
        opacity: 1;
      }
      .dark-layer.play {
        background-color: rgba(0, 0, 0, 0.45);
      }
    }
  }
  .featured-card {
    &__skeleton {
      width: 100%;
      height: 100px;
      .vue-content-placeholders,
      .vue-content-placeholders-img {
        width: 100%;
      }
    }
  }
}

.featured-card-placeholder {
  position: relative;
  height: 80px;
  padding: 0.3em 0.3em;
  .child {
    background-color: #cccccc;
    opacity: 0.4;
    border-radius: 4px;
    // top: 10%;
    // bottom: 10%;
  }
}

.fill-5 {
  top: 5%;
  left: 5%;
  bottom: 5%;
  right: 5%;
}

.top-0 {
  top: 0;
}
</style>
