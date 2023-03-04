<template>
    <v-card class="page" v-if="!isLoading">
        <v-card-title>
            <div class="page__title">
                <v-icon color="primary" x-large>$vuetify.icons.application-cog</v-icon>
                {{$t('General')}}
            </div>
            <div class="page__options">
                <v-btn
                    small
                    class="success"
                    @click="saveSettings"
                    :disabled="isLoadingForButton"
                    >{{$t('Save')}}
                                         <template v-slot:loader>
                        <span class="custom-loader">
                            <v-icon light>$vuetify.icons.cached</v-icon>
                        </span>
                    </template>
                    </v-btn
                >
            </div>
        </v-card-title>
        <v-container fluid>
            <v-row>
                <v-col cols="12" sm="6">
                    <veutify-select
                        :items="themes"
                        item-value="value"
                        item-text="text"
                        v-model="$store.getters.getSettings.defaultTheme"
                        @change="changeTheme"
                        hide-details
                       :label="$t('Default Theme')"
                        outlined
                    ></veutify-select>
                </v-col>
                <v-col cols="12" sm="6">
                    <veutify-select
                        :items="playerLandingPageOptions"
                        v-model="$store.getters.getSettings.playerLanding"
                        item-value="path"
                        item-text="name"
                        :label="$t('Home Page')"
                        :messages="$t('Select the landing page of your platform. You can create custom pages.')"
                        outlined
                    ></veutify-select>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-switch
                        v-model="$store.getters.getSettings.enableThemeSwitcher"
                        :label="$t('Enable theme switcher')"
                        :messages="
                            $t(
                                'Offer your users the possibility to switch the theme.' 
                            )
                            + ' '
                            + $t('The theme switcher will show on the navbar.')
                  "></v-switch>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-switch :label="$t('Hide Right-sidebar')" v-model="$store.getters.getSettings.hideRightSidebar"></v-switch>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                <div class="title">{{ $t('Images & Icons') }}</div>
                <v-divider></v-divider>
                </v-col>

                <v-col cols="6">
                    <image-picker
                        :image_name="$t('Logo')"
                        :src="
                            appLogo.base64 || appLogo
                        "
                        @image="appLogo = $event"
                        @default="resetDefault('logo')"
                    />
                </v-col>
                <v-col cols="6">
                    <image-picker
                        :image_name="$t('Favicon')"
                        :src="
                            appFavicon.base64 ||
                                appFavicon
                        "
                        @image="appFavicon = $event"
                        @default="resetDefault('favicon')"
                    />
                </v-col>
            </v-row>
                        <!-- <v-row>
                <v-col cols="12">
                <div class="title">{{ $t('Subscription Button') }}</div>
                <v-divider></v-divider>
                </v-col>
                <v-col cols="6" sm="4">
                    <v-text-field :label="$t('Text')" outlined dense  v-model="$store.getters.getSettings.subscriptionButtonText"></v-text-field>
                </v-col>
                <v-col cols="6" sm="4">
                    <v-text-field :label="$t('Icon')" :hint="$t('Must be a material design icon') +  ' @materialdesignicons.com'" outlined dense  v-model="$store.getters.getSettings.subscriptionButtonIcon"></v-text-field>
                </v-col>
                <v-col cols="6" sm="4">
                     <v-text-field :label="$t('Color')" outlined dense v-model="$store.getters.getSettings.subscriptionButtonColor">
                        <template v-slot:append>
                    <v-menu top :close-on-content-click="false">
                      <template v-slot:activator="{ on, attrs }">
                        <v-icon primary v-bind="attrs" v-on="on">
                          mdi-palette
                        </v-icon >
                      </template>
                      <v-color-picker
                        dot-size="25"
                        swatches-max-height="200"
                        mode="hexa"
                        v-model="$store.getters.getSettings.subscriptionButtonColor"
                      ></v-color-picker>
                    </v-menu>
                        </template>
                    </v-text-field>
                </v-col> -->
                <!-- <v-col cols="6" sm="4">
                      <veutifySelect
                            v-model="$store.getters.getSettings.subscriptionButtonsPositions"
                            :items="subscriptionButtonPositions"
                            chips
                           :label="$t('Positions')"
                            multiple
                            outlined
                        />
                </v-col> -->
                <!-- <v-col cols="6" sm="3">
                      <v-switch color="primary" v-model="$store.getters.getSettings.subscriptionButtonOutlined" :label="$t('Outlined')">
                      </v-switch>
                </v-col>
                <v-col cols="12" sm="5">
                    <div class="align-justify-center">
                    <div>
                        <div class="title mr-2">
                            {{ $t('Preview') }}
                        </div>
                    </div>
                    <v-sheet class="p-2">
                    <v-btn dark :color="$store.getters.getSettings.subscriptionButtonColor" :outlined="$store.getters.getSettings.subscriptionButtonOutlined" depressed>
                        <v-icon left>
                            mdi-{{ $store.getters.getSettings.subscriptionButtonIcon  }}
                        </v-icon> {{ $store.getters.getSettings.subscriptionButtonText }}
                    </v-btn>
                    </v-sheet>
                    </div>
                </v-col> -->
            <!-- </v-row> -->
        </v-container>
    </v-card>
    <page-loading v-else />
</template>

<script>
import axios from 'axios';
import { VSelect } from "vuetify/lib";
import imagePicker from "../../elements/ImagePickerSetting";
export default {
  components: {
    veutifySelect: VSelect,
    imagePicker,
  },
  created() {
    this.isLoading = true;
    axios
      .get("/api/admin/pages-infos")
      .then((res) =>
        res.data.map((page) => this.playerLandingPageOptions.push(page))
      )
      .finally(() => (this.isLoading = false));
  },
  data() {
    return {
      isLoading: false,
      isLoadingForButton: false,
      appLogo: JSON.parse(
        JSON.stringify(this.$store.getters.getSettings.appLogo)
      ),
      appFavicon: JSON.parse(
        JSON.stringify(this.$store.getters.getSettings.appFavicon)
      ),
      appLogoDefault: "/storage/defaults/favicon/logo.png",
      appFaviconDefault: "/storage/defaults/favicon/icon-192x192.png",
      playerLandingPageOptions: [
        {
          path: "/browse",
          name: this.$t("Browse"),
        },
        {
          path: "/podcasts",
          name: this.$t("Podcasts"),
        },
      ],
      themes: [
        {
          text: this.$t("Dark"),
          value: "Dark",
        },
        {
          text: this.$t("Light"),
          value: "Light",
        },
      ],
    };
  },
  methods: {
    changeTheme(e) {
      this.$vuetify.theme.dark = e === "Dark";
    },
    resetDefault(img) {
      if (img === "logo") {
        this.appLogo = this.appLogoDefault;
      } else if (img === "favicon") {
        this.appFavicon = this.appFaviconDefault;
      }
    },
    saveSettings() {
      this.isLoadingForButton = true;
      var formData = new FormData();
      formData.append("logo", this.appLogo.file || this.appLogo);
      formData.append(
        "defaultTheme",
        this.$store.getters.getSettings.defaultTheme
      );
      formData.append(
        "playerLanding",
        this.$store.getters.getSettings.playerLanding
      );
      formData.append(
        "hideRightSidebar",
        this.$store.getters.getSettings.hideRightSidebar ? 1 : 0
      );
      formData.append(
        "enableThemeSwitcher",
        this.$store.getters.getSettings.enableThemeSwitcher ? 1 : 0
      );
      formData.append("favicon", this.appFavicon.file || this.appFavicon);
      return axios
        .post("/api/admin/save-general-settings", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(() => {
          this.isLoadingForButton = false;
          this.$notify({
            group: "foo",
            type: "success",
            title: this.$t("Updated"),
            text: this.$t("Settings") + " " + this.$t("updated successfully."),
          });
          setTimeout(() => {
            location.reload();
          }, 300);
        });
    },
  },
};
</script>
