<template>
    <v-card class="admin-list">
        <v-list rounded class="list">
            <v-list-item-group color="primary">
                <template v-for="(item, i) in items">
                    <v-list-item
                        :key="i"
                        :to="item.path"
                        @click="$emit('clickOnItem')"
                        link
                        v-if="item.path"
                    >
                        <v-list-item-icon>
                            <v-icon v-text="'$vuetify.icons.' + item.icon"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title
                                v-text="$t(item.text)"
                            ></v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-group
                        v-else
                        :key="i"
                        :prepend-icon="'$vuetify.icons.' + item.icon"
                        :value="item.value"
                    >
                        <template v-slot:activator>
                            <v-list-item-title>{{
                                $t(item.text)
                            }}</v-list-item-title>
                        </template>
                        <v-list-item
                            v-for="(sub_item, j) in item.children"
                            :key="j"
                            :to="sub_item.path"
                            @click="$emit('clickOnItem')"
                            link
                        >
                            <v-list-item-icon>
                                <v-icon v-text="'$vuetify.icons.' + sub_item.icon"></v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title
                                    v-text="$t(sub_item.text)"
                                ></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list-group>
                </template>
            </v-list-item-group>
        </v-list>
    </v-card>
</template>
<script>
export default {
    created() {
        // hide navigation items for the user if the permissions is not granted
            this.items = this.items.filter(item => {
                if (
                    item.name === "settings" ||
                    (item.children &&
                        item.children.find(item => item.name === "settings"))
                ) {
                    return this.hasPermission("Edit settings") ? 1 : 0;
                } else if (
                    item.name === "users" ||
                    (item.children &&
                        item.children.find(item => item.name === "users"))
                ) {
                    return this.hasPermission("Read users") ? 1 : 0;
                } else if (
                    item.name === "artists" ||
                    (item.children &&
                        item.children.find(item => item.name === "artists"))
                ) {
                    return this.hasPermission("Read artists") &&  this.$store.getters.getSettings.enable_artist_account ? 1 : 0;
                } else if (
                    item.name === "podcasts" ||
                    (item.children &&
                        item.children.find(item => item.name === "podcasts"))
                ) {
                    return this.hasPermission("CED podcasts") ? 1 : 0;
                } else if (
                    item.name === "radio-stations" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "radio-stations"
                        ))
                ) {
                    return this.hasPermission("CED radio_stations") ? 1 : 0;
                } else if (
                    item.name === "appearance" ||
                    (item.children &&
                        item.children.find(item => item.name === "appearance"))
                ) {
                    return this.hasPermission("Edit appearance") ? 1 : 0;
                } else if (
                    item.name === "albums" ||
                    (item.children &&
                        item.children.find(item => item.name === "albums"))
                ) {
                    return this.hasPermission("CED albums") ? 1 : 0;
                } else if (
                    item.name === "songs" ||
                    (item.children &&
                        item.children.find(item => item.name === "songs"))
                ) {
                    return this.hasPermission("CED songs") ? 1 : 0;
                } else if (
                    item.name === "roles" ||
                    (item.children &&
                        item.children.find(item => item.name === "roles"))
                ) {
                    return this.hasPermission("Read roles") ? 1 : 0;
                } else if (
                    item.name === "playlists" ||
                    (item.children &&
                        item.children.find(item => item.name === "playlists"))
                ) {
                    return this.hasPermission("CED playlists") ? 1 : 0;
                } else if (
                    item.name === "genres" ||
                    (item.children &&
                        item.children.find(item => item.name === "genres"))
                ) {
                    return this.hasPermission("CED song_genres") ? 1 : 0;
                } else if (
                    item.name === "podcast_genres" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "podcast_genres"
                        ))
                ) {
                    return this.hasPermission("CED podcast_genres") ? 1 : 0;
                } else if (
                    item.name === "subscriptions" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "subscriptions"
                        ))
                ) {
                    return this.hasPermission("CED subscriptions") && this.$store.getters.getSettings.saas ? 1 : 0;
                } else if (
                    item.name === "plans" ||
                    (item.children &&
                        item.children.find(item => item.name === "plans"))
                ) {
                    return this.hasPermission("CED plans") ? 1 : 0;
                } else if (
                    item.name === "advertisements" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "advertisements"
                        ))
                ) {
                    return this.hasPermission("Edit advertisements") && this.$store.getters.getSettings.saas ? 1 : 0;
                } else if (
                    item.name === "translations" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "translations"
                        ))
                ) {
                    return this.hasPermission("CED translations") ? 1 : 0;
                } else if (
                    item.name === "sales" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "sales"
                        ))
                ) {
                    return this.hasPermission("View sales") && this.$store.getters.getSettings.saas ? 1 : 0;
                }
                else if (
                    item.name === "monetizing"
                ) {
                    return this.$store.getters.getSettings.saas ? 1 : 0;
                }
                else if (
                    item.name === "payouts" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "payouts"
                        ))
                ) {
                    return this.hasPermission("View payouts") && this.$store.getters.getSettings.saas  ? 1 : 0;
                } else if (
                    item.name === "contact" ||
                    (item.children &&
                        item.children.find(
                            item => item.name === "contact"
                        ))
                ) {
                    return this.hasPermission("contact") ? 1 : 0;
                }
                else {
                    return 1;
                } 
            });
    },
    data() {
        return {
            items: [
                {
                    path: "/admin/analytics",
                    icon: "google-analytics",
                    text: this.$t("Analytics")
                },
                {
                    icon: "cog",
                    name: "settings",
                    text: this.$t("Settings"),
                    children: [
                        {
                            icon: "wrench",
                            name: "settings",
                            text: this.$t("Configuration"),
                            path:"/admin/settings/config"
                        },
                        {
                            path: "/admin/appearance",
                            icon: "desktop-mac-dashboard",
                            name: "appearance",
                            text: this.$t("Appearance")
                        }
                    ]
                },
                {
                    icon: "currency-usd",
                    text: this.$t("Monetizing"),
                    name: 'monetizing',
                    children: [
                        {
                            path: "/admin/advertising",
                            icon: "google-ads",
                            name: "advertisements",
                            text: this.$t("Advertisements")
                        },

                        {
                            path: "/admin/plans",
                            icon: "notebook-multiple",
                            name: "plans",
                            text: this.$t("Plans")
                        },
                        {
                            path: "/admin/subscriptions",
                            icon: "handshake-outline",
                            name: "subscriptions",
                            text: this.$t("Subscriptions")
                        },
                        {
                            path: "/admin/sales",
                            icon: "shopping",
                            name: "sales",
                            text: this.$t("Sales")
                        },
                        {
                            path: "/admin/payouts",
                            icon: "cash-check",
                            name: "payouts",
                            text: this.$t("Payouts")
                        },
                                               {
                            path: "/admin/payout-methods",
                            icon: "account-cash-outline",
                            name: "payout-methods",
                            text: this.$t("Payout Methods")
                        }
                    ]
                },
                {
                    path: "/admin/pages",
                    icon: "text-box-multiple-outline",
                    name: "pages",
                    text: this.$t("Pages")
                },
                {
                    path: "/admin/artists",
                    icon: "account-music",
                    name: "artists",
                    text: this.$t("Artists")
                },
                {
                    icon: "folder-music",
                    name: "content",
                    text: this.$t("Content"),
                    value: true,
                    children: [
                        {
                            path: "/admin/albums",
                            icon: "album",
                            name: "albums",
                            text: this.$t("Albums")
                        },
                        {
                            path: "/admin/radio-stations",
                            icon: "radio-tower",
                            name: "radio-stations",
                            text: this.$t("Radio Stations")
                        },
                        {
                            path: "/admin/songs",
                            icon: "music-note",
                            name: "songs",
                            text: this.$t("Songs")
                        },
                        {
                            path: "/admin/playlists",
                            icon: "playlist-music",
                            name: "playlists",
                            text: this.$t("Playlists")
                        },
                        {
                            path: "/admin/podcasts",
                            icon: "microphone",
                            name: "podcasts",
                            text: this.$t("Podcasts")
                        },
                        {
                            path: "/admin/genres",
                            icon: "folder-music",
                            name: "genres",
                            text: this.$t("Song Genres")
                        },
                        {
                            path: "/admin/podcast-genres",
                            icon: "podcast",
                            name: "podcast_genres",
                            text: this.$t("Podcast Genres")
                        }
                    ]
                },
                
                {
                    icon: "account-supervisor-circle",
                    text: this.$t("Members"),
                    children: [
                        {
                            path: "/admin/roles",
                            icon: "account-network",
                            name: "roles",
                            text: this.$t("Roles")
                        },
                        {
                            path: "/admin/users",
                            icon: "account-circle",
                            name: "users",
                            text: this.$t("Users")
                        }
                    ]
                },
                {
                    path: "/admin/contact",
                    icon: "email-multiple",
                    name: "contact",
                    text: "Contact"
                },
                {
                    path: "/admin/translations",
                    icon: "translate",
                    name: "translations",
                    text: "Translations"
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
.admin-list {
    height: 100%;
    padding-top: 4em;
}
.list {
    height: 100%;
}
</style>
