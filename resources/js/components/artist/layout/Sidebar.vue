<template>
    <v-card class="artist-list">
        <v-list rounded class="list">
            <v-list-item-group color="primary">
                <v-list-item
                    v-for="(item, i) in items"
                    :key="i"
                    :to="item.path"
                    @click="$emit('clickOnItem')"
                    link
                >
                    <v-list-item-icon>
                            <v-icon v-text="'$vuetify.icons.' + item.icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title
                            v-text="item.text"
                        ></v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-item-group>
            <div class="space_used mt-5">
                <div class="highlight">
                    {{$t('Space Used')}}
                </div>
                <div class="space_used_value small">
                    {{ bytesToMB($store.getters.getArtist.used_disk_space) }} /
                    {{ $store.getters.getArtist.available_disk_space }} {{$t('MB')}} {{$t('used')}}.
                </div>
            </div>
            <div class="space_used mt-5">
               <router-link class="router-link" :to="{ name: 'artist.contact' }">
                   {{ $t('Contact Us') }}
               </router-link>
            </div>
        </v-list>
    </v-card>
</template>

<script>
export default {
    created() {
        this.items = this.items.filter(item => {
            if (item.name === "podcasts") {
                return this.hasPermission("CED podcasts(artist)") ? 1 : 0;
            } else if (item.name === "albums") {
                return this.hasPermission("CED albums(artist)") ? 1 : 0;
            } else if (item.name === "songs") {
                return this.hasPermission("CED songs(artist)") ? 1 : 0;
            } else if (item.name === "earnings") {
                return this.$store.getters.getSettings.saas;
            } else {
                return 1;
            } 
        });
    },
    data() {
        return {
            items: [
                {
                    path: "/artist/analytics",
                    icon: "google-analytics",
                    text: this.$t('Analytics')
                },
                {
                    path: "/artist/earnings",
                    icon: "currency-usd",
                    text: this.$t('Earnings'),
                    name: "earnings"
                },
                {
                    path: "/artist/albums",
                    icon: "album",
                    text: this.$t('Albums'),
                    name: "albums"
                },
                {
                    path: "/artist/songs",
                    icon: "music-note",
                    text: this.$t('Songs'),
                    name: "songs"
                },
                {
                    path: "/artist/podcasts",
                    icon: "microphone",
                    text: this.$t('Podcasts'),
                    name: "podcasts"
                },
                {
                    path: "/artist/account",
                    icon: "account-music",
                    text: this.$t('Account'),
                    name: "account"
                }
            ]
        };
    }
};
</script>

<style lang="scss" scoped>
#sidebar-wrapper {
    min-height: 100vh;
    margin-left: -15rem;
    -webkit-transition: margin 0.25s ease-out;
    -moz-transition: margin 0.25s ease-out;
    -o-transition: margin 0.25s ease-out;
    transition: margin 0.25s ease-out;
}

#sidebar-wrapper .sidebar-heading {
    padding: 0.875rem 1.25rem;
    font-size: 1.2rem;
}

#page-content-wrapper {
    min-width: 100vw;
}

#wrapper.toggled #sidebar-wrapper {
    margin-left: 0;
}

@media (min-width: 768px) {
    #sidebar-wrapper {
        margin-left: 0;
    }

    #page-content-wrapper {
        min-width: 0;
        width: 100%;
    }

    #wrapper.toggled #sidebar-wrapper {
        margin-left: -15rem;
    }
}
.artist-list {
    height: 100%;
    padding-top: 4em;
}
.list {
    height: 100%;
}
</style>
