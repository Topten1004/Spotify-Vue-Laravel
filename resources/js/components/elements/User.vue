<template>
  <v-menu left bottom :position-y="125" offset-y>
    <template v-slot:activator="{ on, attrs }">
      <div class="account" v-bind="attrs" v-on="on">
        <div class="avatar user-avatar">
          <div class="badge" v-if="userPlan && userPlan.badge">
            <v-icon color="primary"
              >$vuetify.icons.{{ user.plan.badge }}</v-icon
            >
          </div>
          <img :src="user.avatar" alt="" class="avatar-image" />
        </div>
        <div class="name max-1-lines">
          {{ user.name }}
        </div>
        <div class="chevron">
          <v-icon small>$vuetify.icons.chevron-down</v-icon>
        </div>
      </div>
    </template>
    <v-list dense :class="{ 'dark-backround': $vuetify.theme.dark }">
      <v-list-item v-if="isAdmin()" :to="{ path: '/admin/analytics' }">
        <v-list-item-title>
          <v-icon size="20">$vuetify.icons.account-tie</v-icon>
          {{ $t("Admin Area") }}
        </v-list-item-title>
      </v-list-item>
      <v-list-item v-if="isArtist()" :to="{ path: '/artist/analytics' }">
        <v-list-item-title>
          <v-icon size="20">$vuetify.icons.account-music</v-icon>
          {{ $t("Artist Area") }}
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        v-if="!$route.matched.some((record) => record.meta.player)"
        @click="
          $router.push({ path: $store.getters.getSettings.playerLanding })
        "
        :to="{ path: $store.getters.getSettings.playerLanding }"
      >
        <v-list-item-title>
          <v-icon size="20">$vuetify.icons.music-note-eighth</v-icon>
          {{ $t("Player") }}</v-list-item-title
        >
      </v-list-item>

      <v-list-item v-if="isUpgradable" :to="{ name: 'subscription' }">
        <v-list-item-title>
          <v-icon size="20"
            >$vuetify.icons.{{
              $store.getters.getSettings.subscriptionButtonIcon
            }}</v-icon
          >
          {{ $t("Upgrade Account") }}</v-list-item-title
        >
      </v-list-item>
      <v-list-item
        :to="{ name: 'profile', params: {id: $store.getters.getUser.id} }"
        v-if="!$store.getters.getUser.is_admin"
      >
        <v-list-item-title>
          <v-icon size="20">$vuetify.icons.account-cog</v-icon>
          {{ $t("Profile") }}
        </v-list-item-title>
      </v-list-item>
      <v-list-item :to="{ name: 'account-settings' }">
        <v-list-item-title>
          <v-icon size="20">$vuetify.icons.account-cog</v-icon>
          {{ $t("Account Settings") }}
        </v-list-item-title>
      </v-list-item>
      <v-list-item @click="$store.dispatch('logout')">
        <v-list-item-title>
          <v-icon size="20">$vuetify.icons.logout</v-icon>
          {{ $t("Logout") }}</v-list-item-title
        >
      </v-list-item>
    </v-list>
  </v-menu>
</template>
<script>
export default {
  computed: {
    user() {
      return this.$store.getters.getUser;
    },
  },
};
</script>
<style lang="scss" scoped>
// .list-container {
//     .user-list {
//         list-style: none;
//         padding: .3em .2em;
//         &__item {
//             display: flex;
//             align-items: center;
//             border-radius: 5px;
//             padding: .3em .2em;
//             &:hover {
//                  background-color: rgba(201, 201, 201, 0.308);
//             }
//             &--text {
//                 margin-left: .6em;
//             }
//         }
//     }
// }
</style>