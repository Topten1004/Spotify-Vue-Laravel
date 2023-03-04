<template>
  <v-menu
    :close-on-content-click="closeOnContentClick || false"
    top
    :style="{ zIndex: 50000 }"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        icon
        :dark="dark"
        v-bind="attrs"
        v-on="on"
        class="m-0"
        width="22px"
      >
        <v-icon v-if="!icon">$vuetify.icons.dots-horizontal</v-icon>
        <v-icon v-else>$vuetify.icons.{{ icon }}</v-icon>
      </v-btn>
    </template>
    <v-card
      min-width="160"
      v-if="item"
      :class="{ 'dark-background': $vuetify.theme.dark }"
    >
      <div class="header" v-if="!isOnPlayer">
        <div class="text-information">
          <div class="title">
            {{ item.title }}
          </div>
          <div class="artist" v-if="item.artist">
            {{
              item.artist.displayname
                ? item.artist.displayname
                : item.artist.name
            }}
          </div>
          <div class="stats">
            <div class="likes" v-if="item.likes">
              {{ item.likes }} {{ $t("likes") }}
            </div>
            <div class="separator-2" v-if="item.plays">|</div>
            <div class="plays" v-if="item.plays">
              {{ item.plays }} {{ $t("plays") }}
            </div>
          </div>
        </div>
        <div class="actions d-flex justify-space-around align-center">
          <v-btn x-small fab color="primary" @click="play(item)">
            <v-icon>$vuetify.icons.play</v-icon>
          </v-btn>
          <div v-if="isLikable(item.type)">
            <v-icon
              small
              v-if="!$store.getters.isLiked(item.id, item.type)"
              @click="like(item)"
              >$vuetify.icons.heart-outline</v-icon
            >
            <v-icon small v-else color="primary" @click="dislike(item)"
              >$vuetify.icons.heart</v-icon
            >
          </div>
          <div v-else-if="isFollowable(item.type)">
            <v-icon
              small
              v-if="$store.getters.isFollowed(item.id, item.type)"
              @click="follow(item)"
              >$vuetify.icons.heart-outline</v-icon
            >
            <v-icon small v-else color="primary" @click="unfollow(item)"
              >$vuetify.icons.heart</v-icon
            >
          </div>
          <v-icon
            small
            v-if="
              item.source_format == 'file' &&
              item.origin === 'local' &&
              !$store.getters.getSettings.hideDownloadButton
            "
            @click="$store.dispatch('downloadAudio', item)"
            >$vuetify.icons.cloud-download-outline</v-icon
          >
        </div>
      </div>
      <v-divider></v-divider>
      <div class="body">
        <v-list
          class="list"
          :class="{ 'dark-background': $vuetify.theme.dark }"
        >
          <v-list-item
            v-if="$store.getters.getUser && item.type === 'song'"
            @click="
              $store.commit('setAddSongToPlaylist', item);
              $emit('close');
            "
          >
            <v-icon small class="list-icon"
              >$vuetify.icons.playlist-music</v-icon
            >
            {{ $t("Add To Playlist") }}
          </v-list-item>
          <v-list-item
            v-if="$store.getters.getUser && item.type === 'song'"
            @click="
              $store.commit('pushIntoQueue', [item]);
              $emit('close');
            "
          >
            <v-icon small class="list-icon"
              >$vuetify.icons.playlist-play</v-icon
            >
            {{ $t("Add To Queue") }}
          </v-list-item>
          <v-list-item
            v-if="isPurchasable && !isOwned"
            @click="$store.commit('purchase/setSellingAsset', item)"
          >
            <v-icon small class="list-icon">$vuetify.icons.cart</v-icon>
            {{ $t("Purchase") }}
          </v-list-item>
          <v-list-item @click="showSharingDialog">
            <v-icon small class="list-icon">$vuetify.icons.share</v-icon>
            {{ $t("Share") }}
          </v-list-item>
          <v-list-item
            v-if="item.artists && item.artists.length"
            :to="{
              name: 'artist',
              params: { id: item.artists[0].id },
            }"
          >
            <v-list-item-title>
              <v-icon small class="list-icon"
                >$vuetify.icons.account-music</v-icon
              >
              {{ $t("Go To Artist") }}
            </v-list-item-title>
          </v-list-item>
          <v-list-item
            v-if="item.album"
            :to="{
              name: 'album',
              params: { id: item.album.id },
            }"
          >
            <v-list-item-title>
              <v-icon small class="list-icon">$vuetify.icons.album</v-icon>
              {{ $t("Go To Album") }}
            </v-list-item-title>
          </v-list-item>
          <!-- <v-list-item
            >
                <v-list-item-title>
                    <v-icon small class="list-icon">$vuetify.icons.thumb-down</v-icon>
                    {{$t('Dislike')}}
                </v-list-item-title
                >
            </v-list-item> -->
        </v-list>
      </div>
    </v-card>
  </v-menu>
  <!-- <div>
        <ul class="abs-menu-container__list">
            <li
                v-if="$store.getters.getUser"
                @click.stop="
                    $store.commit('setAddSongToPlaylist', item.id);
                    $emit('close');
                "
            >
                <v-icon>$vuetify.icons.playlist-music</v-icon> {{$t('Add To Playlist')}}
            </li>
            <li
                @click.stop="
                    $store.dispatch('playSong', { item, reset: false });
                    $emit('close');
                "
            >
                <v-icon>$vuetify.icons.playlist-music</v-icon> {{$t('Add To Queue')}}
            </li>
            <li v-if="item.artist">
                <div
                    @click.stop="
                        $router.push({
                            name: 'artist',
                            params: { id: item.artist.id }
                        });
                        $emit('close');
                        $emit('goToArtist')
                    "
                >
                    <v-icon>$vuetify.icons.account-music</v-icon>
                    {{$t('Go To Artist')}}
                </div>
            </li>
            <li @click="showSharingDialog">
                <div>
                    <v-icon>$vuetify.icons.share</v-icon>
                    {{$t('Share')}}
                </div>
            </li>
        </ul>
    </div> -->
</template>

<script>
import Billing from "../../../mixins/billing/billing";
export default {
  props: ["item", "icon", "isOnPlayer", "closeOnContentClick", "dark"],
  mixins: [Billing],
  computed: {
    isLikable() {
      return function (type) {
        const likableAssets = ["album", "song", "radio-station"];
        return likableAssets.includes(type);
      };
    },
    isFollowable() {
      return function (type) {
        const likableAssets = ["podcast", "artist", "playlist"];
        return likableAssets.includes(type);
      };
    },
    isPurchasable() {
      return this.item.product && !this.isPurchased(this.item);
    },
    isOwned() {
      return (
        (this.item.artist &&
          this.$store.getters.getUser &&
          this.$store.getters.getUser.artist &&
          this.item.artist.id === this.$store.getters.getUser.artist.id) ||
        (this.item.product && this.isPurchased(this.item))
      );
    },
  },
  methods: {
    showSharingDialog() {
      this.$store.commit("shareItem", {
        cover: this.item.cover,
        url: this.getItemURL(this.item),
        title: this.item.title,
        type: this.item.type,
        artist: this.getMainArtist(this.item),
      });
    },
    play(item) {
      if (item.type === "album") {
        this.$store.dispatch("playAlbum", { album: item, reset: true });
      } else if (item.type === "song") {
        this.$store.dispatch("playSong", { song: item, reset: true });
      } else if (item.type === "playlist") {
        this.$store.dispatch("playPlaylist", { playlist: item, reset: true });
      } else if (item.type === "podcast") {
        this.$store.dispatch("playPodcast", { podcast: item, reset: true });
      } else if (item.type === "radio-station") {
        this.$store.dispatch("playRadioStation", {
          radioStation: item,
        });
      }
    },
    follow(item) {
      this.$store.dispatch("follow", item).then(() => {
        this.$notify({
          group: "foo",
          type: "success",
          title: this.$t("Followed"),
          text: this.$t(
            item.type[0].toUpperCase() +
              item.type.slice(1).toLowerCase() +
              " added to your follows."
          ),
        });
      });
    },
    unfollow(item) {
      this.$store.dispatch("unfollow", item).then(() => {
        this.$notify({
          group: "foo",
          type: "success",
          title: this.$t("Unfollowed"),
          text: this.$t(
            item.type[0].toUpperCase() +
              item.type.slice(1).toLowerCase() +
              " removed to your follows."
          ),
        });
      });
    },
    like(item) {
      this.$store.dispatch("like", item).then(() => {
        this.$notify({
          group: "foo",
          type: "success",
          title: this.$t("Liked"),
          text: this.$t(
            item.type[0].toUpperCase() +
              item.type.slice(1).toLowerCase() +
              " added to your likes."
          ),
        });
      });
    },
    dislike(item) {
      this.$store.dispatch("dislike", item).then(() => {
        this.$notify({
          group: "foo",
          type: "success",
          title: this.$t("Disliked"),
          text: this.$t(
            item.type[0].toUpperCase() +
              item.type.slice(1).toLowerCase() +
              " removed to your likes."
          ),
        });
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.header {
  padding: 0.5em;
  .title {
    font-size: 0.8em !important;
    font-weight: bold;
    line-height: 1.6;
  }
  .artist {
    font-size: 0.8em;
    line-height: 1.8;
  }
  .separator-2 {
    margin: 0 0.5em;
  }
  .actions {
    margin-top: 1em;
  }
}
.body {
  .list-icon {
    margin-right: 0.5em;
  }
  .list {
    font-size: 0.9em !important;
    .v-list-item__title {
      font-size: 0.9em !important;
    }
  }
  .theme--dark.v-list {
    background-color: var(--dark-theme-panel-bg-color);
  }
}
</style>
