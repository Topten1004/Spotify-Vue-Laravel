<template>
  <v-card class="seo-settings Page" v-if="!isLoading">
    <v-card-title>
      <div class="Page__title">
        <v-icon color="primary" x-large>$vuetify.icons.text-box-search-outline</v-icon>
        {{$t('Seo')}}
      </div>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <div class="Page__options">
        <v-btn
          small
          class="info"
          @click="resetSeoSettings"
          :disabled="isLoadingForButton"
          >{{ $t('Reset') }}</v-btn
        >
        <v-btn
          small
          class="success"
          @click="save"
          :disabled="isLoadingForButton"
          >{{ $t('Save') }}</v-btn
        >
      </div>
    </v-card-title>
    <v-container class="pl-5">
      <v-row>
        <v-col>
          <v-alert
            border="top"
            colored-border
            type="info"
            class="mt-5"
            elevation="2"
          >
            <div class="title">{{$t('Quick Guide')}}</div>
            <div class="content">
              <div>Variable Placeholders:</div>
              <div class="var">
                <code>%site_name</code> {{ $t('will be replaced with')}} the site name.
              </div>
              <div class="var">
                <code>%creation_date</code> {{$t('will be replaced with')}} the date of
                creation of the specific content.
              </div>
              <div class="var">
                <code>%artist_name</code> {{$t('will be replaced with')}} the {{$t('Artist')}} {{$t('Name')}}
                of the content(if it exists).
              </div>
              <div class="var">
                <code>%album_title</code> {{$t('will be replaced with')}} the {{$t('Artist')}} {{$t('Name')}}
                of the content.
              </div>
              <div class="var">
                <code>%song_title</code> {{$t('will be replaced with')}} the song title.
              </div>
              <div class="div">
                Others (name of the placeholder references it's value):
              </div>
              <div class="var">
                <code class="mr-2">%user_name</code
                ><code class="mr-2">%playlist_title</code
                ><code class="mr-2">%genre_name</code
                ><code class="mr-2">%podcast_title </code
                ><code class="mr-2">%podcast-genre_name</code>
              </div>
              <div class="mt-2">
                <strong>N.B.:</strong> make sure you use each of these
                placeholders on the content that they belong to with the right
                syntax, otherwise the replace won't happen.
              </div>
            </div>
          </v-alert>

          <v-row>
            <v-col md="6" cols="12">
              <v-textarea
                outlined
                dense
                rows="3"
               :label="$t('Site Title')"
                v-model="settings.siteTitle"
              />
            </v-col>
            <v-col md="6" cols="12">
              <v-textarea
                outlined
                dense
                rows="3"
               :label="$t('Site Description')"
                v-model="settings.siteDescription"
              />
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Main')}} {{$t('Pages')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Home Page Title')"
                    v-model="settings.homePageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Home Page Description')"
                    v-model="settings.homePageDescription"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Browse Page Title')"
                    v-model="settings.browsePageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Browse Page Description')"
                    v-model="settings.browsePageDescription"
                  />
                </v-col>
                <v-col
                  md="6"
                  cols="12"
                  v-if="$store.getters.getSettings.enablePodcasts"
                >
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Podcasts Page Title')"
                    v-model="settings.podcastsPageTitle"
                  />
                </v-col>
                <v-col
                  md="6"
                  cols="12"
                  v-if="$store.getters.getSettings.enablePodcasts"
                >
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Podcasts Page Description')"
                    v-model="settings.podcastsPageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Artist')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.artistPageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.artistPageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Song')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.songPageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.songPageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Album')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.albumPageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.albumPageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Podcast')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.podcastPageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.podcastPageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Genre')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.genrePageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.genrePageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Playlist')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.playlistPageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.playlistPageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('Podcast Genre')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.podcastGenrePageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.podcastGenrePageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <div class="sub-title">{{$t('User Profile')}} {{$t('Page')}}</div>
              <v-divider class="pb-2"></v-divider>
              <v-row>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Title')"
                    v-model="settings.userProfilePageTitle"
                  />
                </v-col>
                <v-col md="6" cols="12">
                  <v-textarea
                    outlined
                    dense
                    rows="3"
                   :label="$t('Descriptiion')"
                    v-model="settings.userProfilePageDescription"
                  />
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
      <v-row>
        <v-col>
          <div class="title mb-3">Sitemap</div>
          <v-divider class="pb-2"></v-divider>
          <v-btn
            @click="generateSitemap"
            class="primary"
            :disabled="sitemapLoading"
            >{{$t('Generate')}} Sitemap</v-btn
          >
          <v-alert
            border="top"
            colored-border
            type="info"
            class="mt-5"
            elevation="2"
          >
            When you generate the sitemap, it will be available on:
            <code> {{ url }}/sitemap.xml </code> and the file will be on the
            <code>public/</code> directory.
          </v-alert>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
  <Page-loading v-else />
</template>
<script>
export default {
  props: ["settings", "isLoading", "isLoadingForButton"],
  data() {
    return {
      sitemapLoading: false,
      defaultSettings: {
        siteTitle: "%site_name — Play Music Anywhere & Anytime",
        siteDescription: "%site_name — Play Music Anywhere & Anytime",
        homePageTitle: "Explore & listen | %site_name",
        homePageDescription:
          "Explore & start listening to pure music on %site_name",
        browsePageTitle: "Browse & discover music | %site_name",
        browsePageDescription:
          "Discover & enjoy listening to pure music on %site_name",
        podcastsPageTitle: "Podcasts | %site_name",
        podcastsPageDescription:
          "Discover & enjoy listening to podcasts on %site_name",
        artistPageDescription:
          "Enjoy hearing %artist_name  on %site_name for free",
        artistPageTitle: "%artist_name | Play on %site_name",
        songPageTitle: "%song_title — %artist_name | Play On %site_name",
        songPageDescription:
          "%creation_date — Play %song_name on %site_name for free",
        albumPageTitle: "%artist_name — %album_title | Play on %site_name",
        albumPageDescription:
          "Play & enjoy %album_title album by %artist_name on %site_name",
        podcastPageTitle: "%artist_name — %podcast_title | Play on %site_name",
        podcastPageDescription:
          "Play & enjoy %podcast_title podcast by %artist_name on %site_name",
        playlistPageTitle: "%playlist_title | Play on %site_name",
        playlistPageDescription:
          "Play & enjoy %playlist_title playlist by %user_name on %site_name",
        genrePageTitle: "Top %genre_name music | Play on %site_name",
        genrePageDescription: "Play & enjoy %genre_name music on %site_name",
        podcastGenrePageTitle:
          "Top %podcast-genre_name podcasts | Play on %site_name",
        podcastGenrePageDescription:
          "Play & enjoy %podcast-genre_name podcasts on %site_name",
        userProfilePageTitle: "%user_name | %site_name",
        userProfilePageDescription: "Check %user_name profile on %site_name",
      },
      url: null,
    };
  },
  created() {
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
    resetSeoSettings() {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna reset to the default settings?")}`,
        button: {
          no: this.$t("Cancel"),
          yes: this.$t('Yes'),
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: () => {
          Object.keys(this.defaultSettings).forEach((key) => {
            this.settings[key] = this.defaultSettings[key];
          });
        },
      });
    },
    generateSitemap() {
      this.sitemapLoading = true;
      axios
        .get("/api/admin/generate-sitemap")
        .then(() => {
          this.$notify({
            group: "foo",
            type: "success",
            title: "Generated",
            text:'Sitemap' + ' ' +  this.$t('generated successfully.'),
          });
        })
        .finally(() => (this.sitemapLoading = false));
    },
  },
};
</script>
<style>
.seo-settings > .sub-title {
  font-size: 1.1em;
  padding-bottom: 0.45em;
}
</style>
