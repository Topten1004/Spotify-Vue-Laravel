<template>
  <div id="player-sidebar" v-if="items">
    <v-card id="sidebar-wrapper" class="panel-color">
      <router-link :to="{ path: $store.getters.getSettings.playerLanding }">
        <div class="logo">
          <v-img
            width="3.5em"
            height="3.5em"
            :src="$store.getters.getSettings.appLogo"
            alt="Logo image"
          />
        </div>
      </router-link>
      <v-list class="list panel-color scrollbar-hidden my-2">
        <v-list-item-group color="primary" class="list-group">
          <template v-for="(item, i) in items">
            <v-list-item
              :key="i"
              :to="item.path"
              link
              v-if="item.path.substr(0, 1) === '/' && item.visibility"
              class="sidebar-item"
            >
              <div>
                <v-icon>$vuetify.icons.{{ item.icon }}</v-icon>
              </div>
              <v-list-item-content>
                <v-list-item-title
                  v-text="$t(`${item.name}`)"
                ></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              :key="i + 100"
              @click="navigate(item.path)"
              link
              v-else-if="item.visibility && item.path.substr(0, 1) !== '/'"
              class="sidebar-item"
            >
              <div>
                <v-icon>$vuetify.icons.{{ item.icon }}</v-icon>
              </div>
              <v-list-item-content>
                <v-list-item-title v-text="item.name"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </template>
        </v-list-item-group>
      </v-list>
      <div
        class="install-pwa-button-container mt-5"
        v-if="$store.getters.getInstallPrompt"
      >
        <v-btn
          text
          x-small
          class="primary"
          :title="$t('Install the app')"
          dark
          rounded
          @click="showInstallationPrompt"
        >
          <v-icon small left>$vuetify.icons.download</v-icon>
          {{ $t("Install") }}
        </v-btn>
      </div>
      <!-- <div class="container theme-switch">
                <v-switch
                    small
                    v-model="$vuetify.theme.dark"
                    @change="changeTheme"
                    label=""
                >
                    <template v-slot:label>
                        <v-icon small v-if="$vuetify.theme.dark"
                            >$vuetify.icons.weather-night</v-icon
                        >
                        <v-icon small v-else
                            >$vuetify.icons.white-balance-sunny</v-icon
                        >
                    </template>
                </v-switch>
            </div> -->
    </v-card>
    <v-bottom-navigation id="bottom-nav" grow color="primary">
      <v-btn
        v-for="(item, i) in items"
        :key="i"
        @click="navigate(item.path)"
        v-show="item.visibility"
      >
        <span>{{ $t(item.name) }}</span>
        <v-icon>$vuetify.icons.{{ item.icon }}</v-icon>
      </v-btn>
    </v-bottom-navigation>
    <player
      :playlist="$store.getters.getQueue"
      v-if="$store.getters.getQueue.length"
    ></player>
  </div>
</template>
<script>
import Loader from "../../../services/Loader";
export default {
  props: ["installButton"],
  components: {
    player: () => import("../audio-player/Index.vue"),
  },
  created() {
    axios.get("/api/navigation-items").then((res) => (this.items = res.data));
    if (this.$store.getters.getSettings.youtubePlugin) {
      const loader = new Loader();
      if (this.$store.getters.getSettings.allowVideos) {
        loader.loadAsset("https://www.youtube.com/iframe_api");
        return;
      }
    }
  },
  data() {
    return {
      items: [],
    };
  },
  methods: {
    navigate(path) {
      if (path.match(/\./)) {
        window.open(path, "_self");
      } else {
        this.$router.push({ path: path });
      }
    },
    showInstallationPrompt() {
      // localStorage.getItem('deferredPrompt').prompt();
      this.$store.getters.getInstallPrompt.prompt();
      // // Wait for the user to respond to the prompt
      // const { outcome } = await deferredPrompt.userChoice;
    },
  },
};
</script>
<style lang="scss" scoped>
.theme-switch {
  position: absolute;
  bottom: 65px;
  transform: scale(0.9);
}
.list {
  height: 80%;
  overflow-y: auto;
  background: linear-gradient(
      var(--light-theme-panel-bg-color) 33%,
      rgba(41, 38, 54, 0)
    ),
    linear-gradient(rgba(41, 38, 54, 0), var(--light-theme-panel-bg-color) 66%)
      0 100%,
    radial-gradient(
      farthest-side at 50% 0,
      rgba(148, 148, 148, 0.5),
      rgba(0, 0, 0, 0)
    ),
    radial-gradient(
        farthest-side at 50% 100%,
        rgba(148, 148, 148, 0.5),
        rgba(0, 0, 0, 0)
      )
      0 100%;
  background-color: var(--light-theme-panel-bg-color);
  background-repeat: no-repeat;
  background-attachment: local, local, scroll, scroll;
  background-size: 100% 45px, 100% 45px, 100% 15px, 100% 15px;
  @media (max-height: 950px) {
    height: 75%;
  }
  @media (max-height: 750px) {
    height: 70%;
  }
  @media (max-height: 650px) {
    height: 65%;
  }
  @media (max-height: 550px) {
    height: 60%;
  }
  @media (max-height: 500px) {
    height: 55%;
  }
}
.player--dark {
  .list {
    background: linear-gradient(
        var(--dark-theme-panel-bg-color) 33%,
        rgba(41, 38, 54, 0)
      ),
      linear-gradient(rgba(41, 38, 54, 0), var(--dark-theme-panel-bg-color) 66%)
        0 100%,
      radial-gradient(
        farthest-side at 50% 0,
        rgba(148, 148, 148, 0.5),
        rgba(0, 0, 0, 0)
      ),
      radial-gradient(
          farthest-side at 50% 100%,
          rgba(148, 148, 148, 0.5),
          rgba(0, 0, 0, 0)
        )
        0 100%;
    background-color: var(--dark-theme-panel-bg-color);
    background-repeat: no-repeat;
    background-attachment: local, local, scroll, scroll;
    background-size: 100% 45px, 100% 45px, 100% 15px, 100% 15px;
  }
}

.list-group {
  height: 95%;
}
.install-pwa-button-container {
  display: flex;
  justify-content: center;
}
</style>
