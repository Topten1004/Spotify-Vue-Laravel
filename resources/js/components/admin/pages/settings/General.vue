<template>
  <v-card class="page pb-4" v-if="!isLoading">
    <v-card-title>
      <div class="page__title">
        <v-icon color="primary" x-large>$vuetify.icons.application-cog</v-icon>
        {{ $t("General") }}
      </div>
      <div class="page__options">
        <v-btn
          small
          class="info"
          @click="reset"
          :disabled="isLoadingForButton"
          :loading="isLoadingForButton"
        >
          <template v-slot:loader>
            <span class="custom-loader">
              <v-icon light>$vuetify.icons.cached</v-icon>
            </span>
          </template>
          {{ $t("Reset") }}</v-btn
        >
        <v-btn
          small
          class="success"
          @click="save"
          :disabled="isLoadingForButton"
          :loading="isLoadingForButton"
        >
          <template v-slot:loader>
            <span class="custom-loader">
              <v-icon light>$vuetify.icons.cached</v-icon>
            </span>
          </template>
          {{ $t("Save") }}</v-btn
        >
      </div>
    </v-card-title>
    <v-container class="pl-5">
      <v-row>
        <v-col cols="12">
          <div class="title">{{ $t("Localization") }}</div>
          <v-divider></v-divider>
        </v-col>
        <v-col>
          <v-select
            :items="locales"
            v-model="settings.locale"
            :label="$t('Default Language')"
            item-text="name"
            item-value="locale"
            hide-details
            outlined
          >
            <template v-slot:item="{ item }">
              <div class="align-center">
                <div class="img px-2 py-1">
                  <v-img
                    max-width="25"
                    :src="flagsPath + item.flag + '.svg'"
                  ></v-img>
                </div>
                {{ item.name }}
              </div>
            </template>
            <template v-slot:selection="{ item }">
              <div class="align-center">
                <div class="img px-2 py-1">
                  <v-img
                    max-width="25"
                    :src="flagsPath + item.flag + '.svg'"
                  ></v-img>
                </div>
                {{ item.name }}
              </div>
            </template>
          </v-select>
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.enableLangSwitcher"
            :label="$t('Enable language switcher')"
            :messages="
              $t(
                'Offer your users the possibility to change the language from the ones you make available.'
              ) +
              ' ' +
              $t('The language switcher will show on the navbar.')
            "
          ></v-switch>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <div class="title">{{ $t("Platform") }}</div>
          <v-divider></v-divider>
        </v-col>
        <v-col cols="12">
          <v-text-field
            :label="$t('Platform Name')"
            v-model="settings.appName"
            outlined
            hide-details=""
          />
        </v-col>
        <v-col cols="12">
          <v-text-field
            :label="$t('Platform Base URL')"
            v-model="settings.appUrl"
            outlined
            hide-details=""
          />
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.enablePodcasts"
            :label="$t('Podcasts')"
            :messages="
              $t(
                'Disabling podcasts will remove anything related to podcasts on the application.'
              )
            "
          ></v-switch>
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.enableBrowse"
            :label="$t('Browse Page')"
            :messages="
              $t('Remove the browse page which includes music genres & charts.')
            "
          ></v-switch>
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.enableUserUpload"
            :label="$t('User Upload')"
            :messages="$t('Allow users to upload audio files locally.')"
          ></v-switch>
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.youtubePlugin"
            :label="$t('YouTube Plugin')"
            :messages="$t('Allow YouTube videos on your platform.')"
          ></v-switch>
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.allowGuestsToContact"
            :label="$t('Allow guests to contact you')"
          ></v-switch>
        </v-col>
      </v-row>
      <v-row class="row-row">
        <v-col cols="12">
          <div class="title">{{ $t("Community") }}</div>
          <v-divider></v-divider>
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.enableFriendshipSystem"
            :label="$t('Friendship System')"
            :messages="
              $t(
                'Disable this functionality if you do not want users to be friends with each other.'
              )
            "
          ></v-switch>
        </v-col>
        <v-col cols="12">
          <v-row v-if="settings.enableFriendshipSystem">
            <v-col cols="12">
              <v-switch
                v-model="settings.enableRealtime"
                :label="$t('Enable Realtime')"
                :messages="
                  $t(
                    'This will allow live chat and shows what the users are currently playing.'
                  )
                "
              ></v-switch>
              <v-row v-if="settings.enableRealtime">
                <v-col cols="12"> </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    dense
                    hide-details=""
                    :label="$t('Pusher App ID')"
                    outlined
                    v-model="settings.pusherAppId"
                /></v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    dense
                    hide-details=""
                    :label="$t('Pusher Key')"
                    outlined
                    v-model="settings.pusherKey"
                /></v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    dense
                    hide-details=""
                    :label="$t('Pusher Secret')"
                    outlined
                    type="password"
                    v-model="settings.pusherSecret"
                /></v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    dense
                    hide-details=""
                    :label="$t('Pusher Cluster')"
                    outlined
                    v-model="settings.pusherCluster"
                /></v-col>
              </v-row>
            </v-col>

            <v-col cols="12" sm="6">
              <v-switch
                v-model="settings.enableChat"
                :label="$t('Enable Chat')"
                v-if="settings.enableRealtime"
                :messages="$t('Enable chat between friends')"
              ></v-switch>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <div class="title">{{ $t("Artists") }}</div>
          <v-divider></v-divider>
        </v-col>
        <v-col>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-switch
                  v-model="settings.enable_artist_account"
                  hide-details=""
                  :label="$t('Enable Artist Accounts')"
                ></v-switch>
              </v-col>
              <template v-if="settings.enable_artist_account">
                <v-col>
                  <v-switch
                    v-model="settings.allowArtistAccountRequests"
                    :label="$t('Allow artist account requests')"
                    :messages="
                      $t(
                        'Allow users to request an artist account by sending their information to the admins'
                      )
                    "
                  ></v-switch>
                </v-col>
                <v-col>
                  <v-switch
                    v-if="settings.saas"
                    v-model="settings.royalties"
                    label="Enable Artist Royalty"
                    messages="Check if you want artists to earn money from streams of their songs"
                    type="checkbox"
                    required
                  ></v-switch>
                </v-col>
                <v-col cols="12" v-if="settings.saas && settings.royalties">
                  <div class="justify-center align-start">
                    <v-text-field
                      v-model="settings.artist_royalty"
                      :label="$t('Artist Royalties')"
                      outlined
                      class="pr-2"
                      type="number"
                      :messages="
                        $t(
                          'How much should an artist earn per 100 play. Enter the amount in cents'
                        )
                      "
                    ></v-text-field>

                    <div class="price d-flex mt-3">
                      <div class="price__currency bold">
                        {{ defaultCurrency.symbol }}
                      </div>
                      <div class="price__amount bold">
                        {{ price(settings.artist_royalty)
                        }}{{ " " + $t("for each") + " " }}
                        <strong>100</strong>
                        {{ " " + $t("Play") }}.
                      </div>
                    </div>
                  </div>
                </v-col>
                <v-col v-if="settings.saas">
                  <v-text-field
                    v-model="saleCut"
                    :label="$t('Artist Sale Cut')"
                    outlined
                    class="pr-2"
                    type="number"
                    :messages="
                      $t(
                        'Artist cut after the sale of his product. Enter the amount in percentage (max. 100)'
                      )
                    "
                  ></v-text-field>
                </v-col>
              </template>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <div class="title">{{ $t("External Links") }}</div>
          <v-divider></v-divider>
        </v-col>
        <v-col>
          <v-container>
            <v-row align="center">
              <v-col cols="auto">
                <v-sheet width="150" class="pt-2">
                  <v-icon>$vuetify.icons.facebook</v-icon>
                  Facebook
                </v-sheet>
              </v-col>
              <v-col>
                <v-text-field
                  label="URL"
                  v-model="settings.facebook_link"
                  :value="settings.facebook_link"
                  hide-details=""
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row align="center">
              <v-col cols="auto">
                <v-sheet width="150" class="pt-2"></v-sheet>
                <v-icon>$vuetify.icons.youtube</v-icon>
                Youtube
              </v-col>
              <v-col>
                <v-text-field
                  label="URL"
                  v-model="settings.youtube_link"
                  :value="settings.youtube_link"
                  hide-details=""
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row align="center">
              <v-col cols="auto">
                <v-sheet width="150" class="pt-2"></v-sheet>
                <v-icon>$vuetify.icons.twitter</v-icon>
                Twitter
              </v-col>
              <v-col>
                <v-text-field
                  label="URL"
                  v-model="settings.twitter_link"
                  :value="settings.twitter_link"
                  hide-details=""
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row align="center">
              <v-col cols="auto">
                <v-sheet width="150" class="pt-2"></v-sheet>
                <v-icon>$vuetify.icons.instagram</v-icon>
                Instagram
              </v-col>
              <v-col>
                <v-text-field
                  label="URL"
                  v-model="settings.instagram_link"
                  :value="settings.instagram_link"
                  hide-details=""
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row align="center">
              <v-col cols="auto">
                <v-sheet width="150" class="pt-2"></v-sheet>
                <v-icon>$vuetify.icons.soundcloud</v-icon>
                Soundcloud
              </v-col>
              <v-col>
                <v-text-field
                  label="URL"
                  v-model="settings.soundcloud_link"
                  :value="settings.soundcloud_link"
                  hide-details=""
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row align="center">
              <v-col cols="auto">
                <v-sheet width="150" class="pt-2"></v-sheet>
                <v-icon>$vuetify.icons.spotify</v-icon>
                Spotify
              </v-col>
              <v-col>
                <v-text-field
                  label="URL"
                  v-model="settings.spotify_link"
                  :value="settings.spotify_link"
                  hide-details=""
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <div class="title">{{ $t("Share Options") }}</div>
          <div class="sub"></div>
          <v-divider></v-divider>
        </v-col>
        <v-container>
          <v-row>
            <v-col cols="6" sm="4">
              <v-switch
                label="Facebook"
                v-model="settings.enable_share_facebook"
              ></v-switch>
            </v-col>
            <v-col cols="6" sm="4">
              <v-switch
                label="Twitter"
                v-model="settings.enable_share_twitter"
              ></v-switch>
            </v-col>
            <v-col cols="6" sm="4">
              <v-switch
                label="Whatsapp"
                v-model="settings.enable_share_whatsapp"
              ></v-switch>
            </v-col>
            <v-col cols="6" sm="4">
              <v-switch
                label="Telegram"
                v-model="settings.enable_share_telegram"
              ></v-switch>
            </v-col>
          </v-row>
        </v-container>
      </v-row>
      <v-row>
        <v-col cols="12">
          <div class="title">{{ $t("Agreement") }}</div>
          <div class="sub"></div>
          <v-divider></v-divider>
        </v-col>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-textarea
                :label="$t('Account creation Agreement')"
                outlined
                :messages="
                  $t('The message that shows below the registration form')
                "
                v-model="settings.account_agreement"
              ></v-textarea>
            </v-col>
          </v-row>
        </v-container>
      </v-row>
    </v-container>
  </v-card>
  <page-loading v-else />
</template>
<script>
import axios from "axios";
import Billing from "@mixins/billing/billing";
export default {
  props: ["settings", "isLoading", "isLoadingForButton"],
  mixins: [Billing],
  data() {
    return {
      sitemapLoading: false,
      url: null,
      flagsPath: "/storage/defaults/icons/flags/",
      locales: [],
    };
  },
  created() {
    axios.get("/api/languages").then((res) => (this.locales = res.data));
    axios.get("/api/admin/url").then((res) => {
      this.url = res.data;
    });
  },
  methods: {
    save() {
      this.$emit("save-settings");
    },
    reset() {
      this.$emit("reset-settings");
    },
  },
  computed: {
    saleCut: {
      get() {
        return this.settings.artist_sale_cut;
      },
      set(val) {
        if (val > 100) {
          this.settings.artist_sale_cut = 100;
        } else {
          this.settings.artist_sale_cut = val;
        }
      },
    },
  },
};
</script>

<style lang="scss" scoped>
.price {
  font-size: 1.15em;
}
</style>
