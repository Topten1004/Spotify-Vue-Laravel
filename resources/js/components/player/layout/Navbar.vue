<template>
  <nav class="navbar" id="navbar">
    <div class="navbar__left-side">
      <div class="chevrons">
        <div class="chevron-left mr-3 pointer" @click="$router.go(-1)">
          <v-icon>$vuetify.icons.chevron-left</v-icon>
        </div>
        <div class="chevron-right pointer" @click="$router.go(1)">
          <v-icon>$vuetify.icons.chevron-right</v-icon>
        </div>
      </div>
      <div class="logo">
        <router-link :to="{ path: $store.getters.getSettings.playerLanding }">
          <v-img
            :src="$store.getters.getSettings.appLogo"
            width="3em"
            height="3em"
            alt="Logo image"
          />
        </router-link>
      </div>
      <div
        class="searchbar-container"
        :style="{
          transform: showSearchBar ? 'translateY(50px)' : 'translateY(8px)',
          opacity: showSearchBar ? 1 : 0,
          pointerEvents: showSearchBar ? 'initial' : 'none',
        }"
      >
        <PlayerSearchbar @navigation="showSearchBar = !showSearchBar" />
      </div>
    </div>

    <div class="navbar__right-side">
      <div
        class="search-icon pointer mr-3 ml-auto"
        @click="showSearchBar = !showSearchBar"
      >
        <v-icon>$vuetify.icons.magnify</v-icon>
      </div>
      <div
        class="lang-switcher mr-1"
        v-if="
          $store.getters.getSettings.enableLangSwitcher &&
          $store.getters.getCurrentLang
        "
      >
        <div
          class="pointer"
          @click="$store.commit('setChooseLangDialog', true)"
        >
          <v-img
            width="18px"
            v-if="$store.getters.getCurrentLang.flag"
            height="100%"
            :src="
              '/storage/defaults/icons/flags/' +
              $store.getters.getCurrentLang.flag +
              '.svg'
            "
          ></v-img>
          <div class="small bold lang" v-else>
            {{ $store.getters.getCurrentLang.locale }}
          </div>
        </div>
      </div>
      <div
        class="theme-switch"
        v-if="$store.getters.getSettings.enableThemeSwitcher"
      >
        <v-btn icon @click="changeTheme">
          <v-icon small>$vuetify.icons.brightness-4</v-icon>
        </v-btn>
      </div>
      <div class="user" v-if="user">
        <Cart
          class="mx-1"
          v-if="$store.getters['purchase/getCart'].items.length"
        ></Cart>
        <!-- <div class="upgrade-account" v-if="$store.getters.getPlans && $store.getters.getPlans.length">
                    <SubButton></SubButton>
                </div> -->
        <div class="icons">
          <div
            class="chat"
            v-if="
              $store.getters.getSettings.enableRealtime &&
              $store.getters.getSettings.enableChat &&
              hasPermission('Chat with friends')
            "
          >
            <v-menu left bottom :position-y="125" offset-y :close-on-content-click="false" :dark="$vuetify.theme.dark" v-model="chatMenu">
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon v-bind="attrs" v-on="on">
                  <v-icon small
                    >$vuetify.icons.message-processing-outline</v-icon
                  >
                </v-btn>
                <div class="unread-indicator" v-if="unreadMessages">
                  <span>{{ unreadMessages }}</span>
                </div>
              </template>
              <chat-component
                :user="$store.getters.getUser"
                :whoToOpen="openChatEventFriend"
                @chatOpened="$store.commit('setOpenChatWith', null)"
                :amIAlive="chatMenu"
                @unread="unreadMessages += $event"
              />
            </v-menu>
          </div>
          <div class="notifications" v-if="notifications">
            <v-menu
              bottom
              left
              nudge-bottom="45"
              max-height="20em"
              :close-on-content-click="false"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn small v-bind="attrs" v-on="on" icon>
                  <v-badge
                    left
                    overlap
                    dot
                    :color="
                      isThereUnreadNotifications ? 'error' : 'transparent'
                    "
                  >
                    <v-icon small>$vuetify.icons.bell</v-icon>
                  </v-badge>
                </v-btn>
              </template>
              <v-card class="panel">
                <notifications-box
                  @handled="show('notifications')"
                  :notifications="notifications"
                >
                </notifications-box>
              </v-card>
            </v-menu>
          </div>
          <div class="friends" @click="$emit('toggle')">
            <v-icon class="mr-3" small>$vuetify.icons.account-group</v-icon>
          </div>
        </div>
        <User></User>
      </div>
      <div class="auth" v-else>
        <div class="buttons">
          <v-btn
            class="primary white--text"
            small
            @click="$router.push({ name: 'login' })"
            v-if="!$store.getters.getSettings.disableRegistration"
            >{{ $t("Login") }}</v-btn
          >
          <v-btn
            class="register__btn ml-2 white--text"
            small
            outlined
            color="primary"
            @click="$router.push({ name: 'register' })"
            v-if="!$store.getters.getSettings.disableRegistration"
            >{{ $t("Register") }}</v-btn
          >
        </div>
      </div>
    </div>
  </nav>
</template>
<script>
import PlayerSearchbar from "../../elements/PlayerSearchbar";
import NotificationsBox from "../../notifications/Index.vue";
import User from "../../elements/User.vue";
export default {
  components: {
    NotificationsBox,
    User,
    PlayerSearchbar,
    // SubButton: () => import('../../elements/single-items/SubButton'),
    ChatComponent: () => import("../chat/Chat"),
    Cart: () => import("../../dialogs/selling/Cart.vue"),
  },
  data() {
    return {
      userMenu: false,
      showing: null,
      openChatEventFriend: null,
      unreadMessages: 0,
      showSearchBar: false,
      chatMenu: false
    };
  },
  computed: {
    user() {
      return this.$store.getters.getUser;
    },
    notifications() {
      return this.$store.getters.getNotifications;
    },
    isThereUnreadNotifications() {
      return (
        this.notifications &&
        this.notifications.length &&
        this.notifications.filter((not) => not.read_at).length <
          this.notifications.length
      );
    },
    openChatEvent() {
      return this.$store.getters.getOpenChatWith;
    },
  },
  watch: {
    openChatEvent() {
      this.showing = "chat";
      this.chatMenu = true
      this.openChatEventFriend = this.openChatEvent;
    },
  },
  methods: {
    show(whatToShow) {
      if (this.showing == whatToShow) {
        this.showing = null;
      } else {
        this.showing = whatToShow;
        if (whatToShow == "chat") {
          this.unreadMessages = 0;
        }
      }
    },
    deleteNotification(not_id) {
      this.notifications.splice(not_id, 1);
    },
  },
};
</script>

<style lang="scss" scoped>
.lang {
  border: 1px solid black;
  padding: 0.1em 0.4em;
  border-radius: 3px;
}
</style>