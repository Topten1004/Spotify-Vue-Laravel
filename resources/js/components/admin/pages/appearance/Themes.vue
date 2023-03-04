<template>
  <v-card class="page" v-if="themeColors.primary">
    <v-card-title>
      <div class="page__title">
        <v-icon color="primary" x-large>$vuetify.icons.palette</v-icon>
        {{$t('Colors')}}
      </div>
      <div class="page__options">
        <v-btn
          small
          class="info white--text"
          @click="resetThemes"
          :disabled="isLoading"
          >{{ $t('Reset') }}</v-btn
        >
        <v-btn small class="success" @click="saveColors" :disabled="isLoading"
          >{{ $t('Save') }}</v-btn
        >
      </div>
    </v-card-title>
    <v-container>
      <v-row>
        <v-col cols="12">
          <v-row>
            <v-col>
              <v-card outlined>
                <div class="color flex align-center p-2">
                  <div class="color__sample mr-2">
                    <v-avatar size="35" :color="themeColors.primary"></v-avatar>
                  </div>
                  <div class="color__details">
                    <div class="color__name">{{$t('Primary Color')}}</div>
                    <div class="color__details opacity-high">
                      <small>{{ $t('For buttons,icons, nav items, active items...')}}</small>
                    </div>
                  </div>
                  <div class="color__picker ml-auto">
                    <v-menu top :close-on-content-click="false">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn text v-bind="attrs" v-on="on" small>
                          {{$t('Change')}}
                        </v-btn>
                      </template>

                      <v-color-picker
                        dot-size="25"
                        swatches-max-height="200"
                        v-model="themeColors.primary"
                      ></v-color-picker>
                    </v-menu>
                  </div>
                </div>
              </v-card>
            </v-col>
            <v-col>
              <v-card outlined>
                <div class="color flex align-center p-2">
                  <div class="color__sample mr-2">
                    <v-avatar
                      size="35"
                      :color="themeColors.secondary"
                    ></v-avatar>
                  </div>
                  <div class="color__details">
                    <div class="color__name">{{ $t('Secondary Color')}}</div>
                    <div class="color__details opacity-high">
                      <small
                        >{{$t('For gradients with Prim. Color( Ex: progress bar of the player)')}}</small
                      >
                    </div>
                  </div>
                  <div class="color__picker ml-auto">
                    <v-menu top :close-on-content-click="false">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn text v-bind="attrs" v-on="on" small>
                          {{$t('Change')}}
                        </v-btn>
                      </template>
                      <v-color-picker
                        dot-size="25"
                        swatches-max-height="200"
                        v-model="themeColors.secondary"
                      ></v-color-picker>
                    </v-menu>
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
        <v-col lg="6" md="6" sm="12">
          <div class="card-title-medium">{{$t('Dark Theme')}}</div>
          <v-card outlined class="mb-2">
            <div class="color flex align-center p-2">
              <div class="color__sample mr-2">
                <v-avatar
                  size="35"
                  :color="themeColors.dark.background"
                ></v-avatar>
              </div>
              <div class="color__details">
                <div class="color__name">{{$t('Background Color')}}</div>
              </div>
              <div class="color__picker ml-auto">
                <v-menu top :close-on-content-click="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn text v-bind="attrs" v-on="on" small> {{$t('Change')}} </v-btn>
                  </template>
                  <v-color-picker
                    dot-size="25"
                    swatches-max-height="200"
                    v-model="themeColors.dark.background"
                  ></v-color-picker>
                </v-menu>
              </div>
            </div>
          </v-card>
          <v-card outlined class="mb-2">
            <div class="color flex align-center p-2">
              <div class="color__sample mr-2">
                <v-avatar size="35" :color="themeColors.dark.panel"></v-avatar>
              </div>
              <div class="color__details">
                <div class="color__name">{{$t('Panel Color')}}</div>
                <div class="color__details opacity-high">
                  <small>{{$t('Chat, Friendlist...etc')}}</small>
                </div>
              </div>
              <div class="color__picker ml-auto">
                <v-menu top :close-on-content-click="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn text v-bind="attrs" v-on="on" small> {{$t('Change')}} </v-btn>
                  </template>

                  <v-color-picker
                    dot-size="25"
                    swatches-max-height="200"
                    v-model="themeColors.dark.panel"
                  ></v-color-picker>
                </v-menu>
              </div>
            </div>
          </v-card>
          <v-card outlined class="mb-2">
            <div class="color flex align-center p-2">
              <div class="color__sample mr-2">
                <v-avatar size="35" :color="themeColors.dark.text"></v-avatar>
              </div>
              <div class="color__details">
                <div class="color__name">{{$t('Text Color')}}</div>
              </div>
              <div class="color__picker ml-auto">
                <v-menu top :close-on-content-click="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn text v-bind="attrs" v-on="on" small> {{$t('Change')}} </v-btn>
                  </template>

                  <v-color-picker
                    dot-size="25"
                    swatches-max-height="200"
                    v-model="themeColors.dark.text"
                  ></v-color-picker>
                </v-menu>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col lg="6" md="6" sm="12">
          <div class="card-title-medium">{{$t('Light Theme')}}</div>
          <v-card outlined class="mb-2">
            <div class="color flex align-center p-2">
              <div class="color__sample mr-2">
                <v-avatar
                  size="35"
                  :color="themeColors.light.background"
                ></v-avatar>
              </div>
              <div class="color__details">
                <div class="color__name">{{$t('Background Color')}}</div>
              </div>
              <div class="color__picker ml-auto">
                <v-menu top :close-on-content-click="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn text v-bind="attrs" v-on="on" small> {{$t('Change')}} </v-btn>
                  </template>
                  <v-color-picker
                    dot-size="25"
                    swatches-max-height="200"
                    v-model="themeColors.light.background"
                  ></v-color-picker>
                </v-menu>
              </div>
            </div>
          </v-card>
          <v-card outlined class="mb-2">
            <div class="color flex align-center p-2">
              <div class="color__sample mr-2">
                <v-avatar size="35" :color="themeColors.light.panel"></v-avatar>
              </div>
              <div class="color__details">
                <div class="color__name">{{$t('Panel Color')}}</div>
                <div class="color__details opacity-high">
                  <small>{{$t('Chat, Friendlist...etc')}}</small>
                </div>
              </div>
              <div class="color__picker ml-auto">
                <v-menu top :close-on-content-click="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn text v-bind="attrs" v-on="on" small> {{$t('Change')}} </v-btn>
                  </template>

                  <v-color-picker
                    dot-size="25"
                    swatches-max-height="200"
                    v-model="themeColors.light.panel"
                  ></v-color-picker>
                </v-menu>
              </div>
            </div>
          </v-card>
          <v-card outlined class="mb-2">
            <div class="color flex align-center p-2">
              <div class="color__sample mr-2">
                <v-avatar size="35" :color="themeColors.light.text"></v-avatar>
              </div>
              <div class="color__details">
                <div class="color__name">{{$t('Text Color')}}</div>
              </div>
              <div class="color__picker ml-auto">
                <v-menu top :close-on-content-click="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn text v-bind="attrs" v-on="on" small> {{$t('Change')}} </v-btn>
                  </template>
                  <v-color-picker
                    dot-size="25"
                    swatches-max-height="200"
                    v-model="themeColors.light.text"
                  ></v-color-picker>
                </v-menu>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
  <page-loading v-else></page-loading>
</template>

<script>
export default {
  created() {
    axios.get("/api/admin/themes").then((res) => (this.themeColors = res.data));
  },
  data() {
    return {
      isLoading: false,
      themeColorsDefault: {
        primary: "#4245a8",
        secondary: "#8C366C",
        dark: {
          background: "#010d1f",
          text: '#ebebeb',
          panel: "#182131",
        },
        light: {
          background: "#ffffff",
          text: '#09021d',
          panel: "#f7f7f7",
        },
      },
      themeColors: {},
    };
  },
  methods: {
    saveColors() {
      this.isLoading = true;
      return axios
        .post("/api/admin/save-themes", { themes: this.themeColors })
        .then(() => {
          this.isLoading = false;
          this.updateColors(this.themeColors);
          this.$notify({
            group: "foo",
            type: "success",
            title: this.$t("Updated"),
            text: this.$t('Themes') + ' ' + this.$t('updated successfully.'),
          });
        });
    },
    resetThemes() {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna reset to the default settings?")}`,
        button: {
          no: this.$t("Cancel"),
          yes: this.$t("Reset"),
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            this.themeColors = JSON.parse(
              JSON.stringify(this.themeColorsDefault)
            );
            this.saveColors()
              .then(() => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Reset"),
                  text: this.$t('Themes') +  ' ' + this.$t('reset successfully.'),
                });
              })
              .catch();
          }
        },
      });
    },
  },
};
</script>
