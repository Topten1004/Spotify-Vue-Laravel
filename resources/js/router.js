import Vue from "vue";
import VueRouter from "vue-router";
import VueMeta from "vue-meta";

import store from "./store/index.js";


Vue.use(VueMeta, {
    refreshOnceOnNavigation: true
});

Vue.use(VueRouter);


const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            component: () =>
                import(
                    /* webpackChunkName: "landing" */ "./components/Landing.vue"
                ),
            name: "landing"
        },
        {
            path: "/login",
            component: () =>
                import(
                    /* webpackChunkName: "auth" */ "./components/authentication/Login.vue"
                ),
            name: "login"
        },
        {
            path: "/register/complete",
            component: () =>
                import(
                    /* webpackChunkName: "auth" */ "./components/authentication/CompleteSignup.vue"
                ),
            name: "complete_signup",
            props: true
        },
        {
            path: "/register",
            component: () =>
                import(
                    /* webpackChunkName: "auth" */ "./components/authentication/Register.vue"
                ),
            name: "register"
        },
        {
            path: "/password/forgot",
            component: () =>
                import(
                    /* webpackChunkName: "auth" */ "./components/authentication/ForgotPassword.vue"
                ),
            name: "forgot_password"
        },
        {
            path: "/password/reset/:token",
            component: () =>
                import(
                    /* webpackChunkName: "auth" */ "./components/authentication/ResetPassword.vue"
                ),
            name: "reset_password"
        },
        {
            path: "/admin",
            component: () =>
                import(
                    /* webpackChunkName: "admin" */ "./components/admin/layout/Master.vue"
                ),
            name: "admin",
            meta: {
                requiresAdmin: true
            },
            children: [
                {
                    path: "settings",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Index.vue"
                        ),
                    meta: {
                        requireShowSettingsPermission: true
                    },
                    children: [
                        {
                            path: "config",
                            redirect: "config/general"
                        },
                        {
                            path: "config/general",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/General.vue"
                                ),
                            name: "settings.general",
                            props: true
                        },
                        {
                            path: "config/billing",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Billing.vue"
                                ),
                            name: "settings.billing",
                            props: true
                        },
                        {
                            path: "config/player",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Player.vue"
                                ),
                            name: "settings.player",
                            props: true
                        },
                        {
                            path: "config/providers",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Providers.vue"
                                ),
                            name: "settings.providers",
                            props: true
                        },
                        {
                            path: "config/analytics",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Analytics.vue"
                                ),
                            name: "settings.analytics",
                            props: true
                        },
                        {
                            path: "config/storage",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Storage.vue"
                                ),
                            name: "settings.storage",
                            props: true
                        },
                        {
                            path: "config/seo",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Seo.vue"
                                ),
                            name: "settings.seo",
                            props: true
                        },
                        {
                            path: "config/mail",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Mail.vue"
                                ),
                            name: "settings.mail",
                            props: true
                        },
                        {
                            path: "config/auth",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/settings/Authentification.vue"
                                ),
                            name: "settings.auth",
                            props: true
                        }
                    ]
                },
                {
                    path: "pages",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Pages.vue"
                        ),
                    name: "admin.pages"
                },
                {
                    path: "roles",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Roles.vue"
                        ),
                    name: "admin.roles"
                },
                {
                    path: "users",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Users.vue"
                        ),
                    name: "admin.users"
                },
                {
                    path: "analytics",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Analytics.vue"
                        ),
                    name: "admin.analytics"
                },
                {
                    path: "songs",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Songs.vue"
                        ),
                    name: "admin.songs"
                },
                {
                    path: "radio-stations",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/RadioStations.vue"
                        ),
                    name: "admin.radio-stations"
                },
                {
                    path: "albums",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Albums.vue"
                        ),
                    name: "admin.albums"
                },
                {
                    path: "podcasts",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Podcasts.vue"
                        ),
                    name: "admin.podcasts"
                },
                {
                    path: "playlists",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Playlists.vue"
                        ),
                    name: "admin.playlists"
                },
                {
                    path: "genres",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Genres.vue"
                        ),
                    name: "admin.genres"
                },
                {
                    path: "podcast-genres",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/PodcastGenres.vue"
                        ),
                    name: "admin.podcast-genres"
                },
                {
                    path: "appearance",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/appearance/Index.vue"
                        ),
                    children: [
                        {
                            path: "",
                            name: "admin.appearance",
                            redirect: "general"
                        },
                        {
                            path: "general",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/appearance/general.vue"
                                ),
                            name: "appearance.general"
                        },
                        {
                            path: "landing",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/appearance/Landing.vue"
                                ),
                            name: "appearance.landing"
                        },
                        {
                            path: "navigation",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/appearance/Navigation.vue"
                                ),
                            name: "appearance.navigation"
                        },
                        {
                            path: "preview",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/appearance/Preview.vue"
                                ),
                            name: "appearance.preview"
                        },
                        {
                            path: "themes",
                            component: () =>
                                import(
                                    /* webpackChunkName: "admin" */ "./components/admin/pages/appearance/Themes.vue"
                                ),
                            name: "appearance.themes"
                        }
                    ]
                },
                {
                    path: "contact",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Contact.vue"
                        ),
                    name: "contact"
                },
                {
                    path: "translations",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Translations.vue"
                        ),
                    name: "translations"
                },
                {
                    path: "artists",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Artists.vue"
                        ),
                    name: "admin.artists"
                },
                {
                    path: "advertising",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Advertising.vue"
                        ),
                    name: "admin.advertising"
                },
                {
                    path: "plans",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Plans.vue"
                        ),
                    name: "admin.plans"
                },
                {
                    path: "subscriptions",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Subscriptions.vue"
                        ),
                    name: "admin.subscriptions"
                },
                {
                    path: "sales",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Sales.vue"
                        ),
                    name: "admin.sales"
                },
                {
                    path: "payouts",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/Payouts.vue"
                        ),
                    name: "admin.payouts"
                },
                {
                    path: "payout-methods",
                    component: () =>
                        import(
                            /* webpackChunkName: "admin" */ "./components/admin/pages/PayoutMethods.vue"
                        ),
                    name: "admin.payout-methods"
                }
            ]
        },
        {
            path: "/artist",
            component: () =>
                import(
                    /* webpackChunkName: "artist" */ "./components/artist/layout/Master.vue"
                ),
            name: "artist-area",
            meta: {
                requiresArtist: true
            },
            children: [
                {
                    path: "analytics",
                    component: () =>
                        import(
                            /* webpackChunkName: "artist" */ "./components/artist/pages/Analytics.vue"
                        ),
                    name: "artist.analytics"
                },
                {
                    path: "songs",
                    component: () =>
                        import(
                            /* webpackChunkName: "artist" */ "./components/artist/pages/songs.vue"
                        ),
                    name: "artist.songs"
                },
                {
                    path: "albums",
                    component: () =>
                        import(
                            /* webpackChunkName: "artist" */ "./components/artist/pages/Albums.vue"
                        ),
                    name: "artist.albums"
                },
                {
                    path: "podcasts",
                    component: () =>
                        import(
                            /* webpackChunkName: "artist" */ "./components/artist/pages/Podcasts.vue"
                        ),
                    name: "artist.podcasts"
                },
                {
                    path: "account",
                    component: () =>
                        import(
                            /* webpackChunkName: "artist" */ "./components/artist/pages/Account.vue"
                        ),
                    name: "artist.account"
                },
                {
                    path: "earnings",
                    component: () =>
                        import(
                            /* webpackChunkName: "artist" */ "./components/artist/pages/Earnings.vue"
                        ),
                    name: "artist.earnings"
                },
                {
                    path: "contact",
                    component: () =>
                        import(
                            /* webpackChunkName: "artist" */ "./components/artist/pages/Contact.vue"
                        ),
                    name: "artist.contact"
                }
            ]
        },
        {
            path: "/",
            component: () =>
                import(
                    /* webpackChunkName: "player" */ "./components/player/layout/Master.vue"
                ),
            name: "player",
            meta: {
                player: true
            },
            children: [
                {
                    path: "/subscription",
                    component: () =>
                        import(
                            /* webpackChunkName: "subscription" */ "./components/subscription/Index.vue"
                        ),
                    name: "subscription"
                },
                {
                    path: "/account/settings",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/AccountSettings.vue"
                        ),
                    name: "account-settings",
                    meta: {
                        requiresAuth: true
                    }
                },
                {
                    path: "/browse",
                    component: () =>
                        import(
                            /* webpackChunkName: "browse" */ "./components/player/pages/browse/Index.vue"
                        ),
                    children: [
                        {
                            path: "",
                            name: "browse",
                            component: () =>
                                import(
                                    /* webpackChunkName: "browse" */ "./components/player/pages/browse/Genres.vue"
                                )
                        },
                        {
                            path: "charts",
                            component: () =>
                                import(
                                    /* webpackChunkName: "browse" */ "./components/player/pages/browse/Charts.vue"
                                ),
                            name: "browse.charts"
                        }
                    ]
                },
                {
                    path: "genre/:id/:genre_name",
                    name: "genre-page",
                    component: () =>
                        import(
                            /* webpackChunkName: "browse" */ "./components/player/pages/browse/GenreSongs.vue"
                        ),
                    props: true
                },
                {
                    path: "podcast/:id",
                    name: "podcast",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/Podcast.vue"
                        ),
                    meta: {
                        podcastRelated: true
                    }
                },
                {
                    path: "/podcasts",
                    name: "podcasts",
                    component: () =>
                        import(
                            /* webpackChunkName: "podcasts" */ "./components/player/pages/podcast/Index.vue"
                        ),
                    meta: {
                        podcastRelated: true
                    }
                },
                {
                    path: "podcasts/:genre_id/:genre_slug",
                    name: "podcasts.genre",
                    props: true,
                    component: () =>
                        import(
                            /* webpackChunkName: "podcasts" */ "./components/player/pages/podcast/GenrePodcasts.vue"
                        ),
                    meta: {
                        podcastRelated: true
                    }
                },
                {
                    path: "artist/:id",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/Artist.vue"
                        ),
                    name: "artist"
                },
                {
                    path: "song/:id",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/song.vue"
                        ),
                    name: "song"
                },
                {
                    path: "album/:id",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/Album.vue"
                        ),
                    name: "album"
                },
                {
                    path: "playlist/:id",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/Playlist.vue"
                        ),
                    name: "playlist"
                },
                {
                    path: "profile/:id",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/UserProfile.vue"
                        ),
                    name: "profile"
                },
                {
                    path: "library",
                    component: () =>
                        import(
                            /* webpackChunkName: "library" */ "./components/player/pages/library/Index.vue"
                        ),
                    meta: {
                        requiresAuth: true
                    },
                    children: [
                        {
                            path: "playlists",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/Playlists.vue"
                                ),
                            name: "library.playlists",
                            props: true
                        },
                        {
                            path: "artists",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/Artists.vue"
                                ),
                            name: "library.artists"
                        },
                        {
                            path: "my-songs",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/songs.vue"
                                ),
                            name: "library.my-songs"
                        },
                        {
                            path: "purchases",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/Purchases.vue"
                                ),
                            name: "library.purchases"
                        },
                        {
                            path: "recent",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/Recents.vue"
                                ),
                            name: "library.recent"
                        },
                        {
                            path: "likes",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/Likes.vue"
                                ),
                            name: "library.likes"
                        },
                        {
                            path: "podcasts",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/Podcasts.vue"
                                ),
                            name: "library.podcasts",
                            meta: {
                                podcastRelated: true
                            }
                        },
                        {
                            path: "albums",
                            component: () =>
                                import(
                                    /* webpackChunkName: "library" */ "./components/player/pages/library/pages/Albums.vue"
                                ),
                            name: "library.albums"
                        }
                    ]
                },
                {
                    path: "*",
                    component: () =>
                        import(
                            /* webpackChunkName: "player" */ "./components/player/pages/CustomPage.vue"
                        ),
                    name: "custom-page"
                }
            ]
        }
    ]
});

// open in a new window function
VueRouter.prototype.open = function(routeObject) {
    const { href } = this.resolve(routeObject);
    window.open(href, "_blank");
};

// check if the current user has a permission
function hasPermission(permission) {
    return Boolean(
        store.getters.getUser.is_admin ||
            (store.getters.getUser
                ? store.getters.getUser.permissions.some(
                      per => per.name === permission
                  )
                : false)
    );
}

// Check whether the admin has privileges to access a page
function isPageAllowedToAccessByAdmin(to) {
    if (to.matched.some(record => record.meta.requireShowSettingsPermission)) {
        return hasPermission("Edit settings") ? 1 : 0;
    } else if (to.name === "admin.users") {
        return hasPermission("Read users") ? 1 : 0;
    } else if (to.name === "admin.pages") {
        return hasPermission("CED pages") ? 1 : 0;
    } else if (to.name === "admin.artists") {
        return hasPermission("Read artists") ? 1 : 0;
    } else if (to.name === "admin.podcasts") {
        return hasPermission("CED podcasts") ? 1 : 0;
    } else if (to.name === "admin.radio-stations") {
        return hasPermission("CED radio_stations") ? 1 : 0;
    } else if (to.name === "admin.appearance") {
        return hasPermission("Edit appearance") ? 1 : 0;
    } else if (to.name === "admin.albums") {
        return hasPermission("CED albums") ? 1 : 0;
    } else if (to.name === "admin.songs") {
        return hasPermission("CED songs") ? 1 : 0;
    } else if (to.name === "admin.roles") {
        return hasPermission("Read roles") ? 1 : 0;
    } else if (to.name === "admin.playlists") {
        return hasPermission("CED playlists") ? 1 : 0;
    } else if (to.name === "admin.genres") {
        return hasPermission("CED song_genres") ? 1 : 0;
    } else if (to.name === "admin.podcast_genres") {
        return hasPermission("CED podcast_genres") ? 1 : 0;
    } else if (to.name === "admin.translations") {
        return hasPermission("CED translations") ? 1 : 0;
    } else if (to.name === "admin.contact") {
        return hasPermission("contact") ? 1 : 0;
    } else if (to.name === "admin.sales") {
        return hasPermission("View sales") ? 1 : 0;
    } else if (to.name === "admin.payouts") {
        return hasPermission("View payouts") && store.getters.getSettings.saas
            ? 1
            : 0;
    } else if (to.name === "admin.plans") {
        return hasPermission("CED plans") ? 1 : 0;
    } else if (to.name === "admin.subscriptions") {
        return hasPermission("CED subscriptions") ? 1 : 0;
    } else if (to.name === "admin.advertising") {
        return hasPermission("Edit advertisements") ? 1 : 0;
    } else if (
        to.name === "artist.earnings" ||
        to.name === "admin.payout-methods" ||
        to.name === "admin.payouts" ||
        to.name === "admin.sales" ||
        to.name === "admin.advertisements" ||
        to.name === "admin.subscriptions" ||
        to.name === "admin.plans"
    ) {
        return store.getters.getSettings.saas;
    } else {
        return 1;
    }
}
router.beforeEach((to, from, next) => {
    // udpate route key: allow component to re-render while visiting the same route

    store.commit("setCurrentPageId");
    // stop playing audios when going outside the player
    if (!to.matched.some(record => record.meta.player)) {
        store.state.queue = [];
    }
    // redirect the user to a 404 page if he visited a page related to podcasts
    // meanwhile it is not allowed to access by the admins
    if (
        to.matched.some(record => record.meta.podcastRelated) &&
        !store.getters.getSettings.enablePodcasts
    ) {
        return next({ path: "/not-found" });
    }
    // if no plans there is no subscription
    if (
        to.name === "subscription" &&
        !store.getters.getSettings.enable_subscription &&
        !(
            store.getters.getPlans &&
            store.getters.getPlans.filter(plan => plan.active).length > 0
        )
    ) {
        return next({ path: "/not-found" });
    }

    // redirect to the player if the landing is disabled
    if (to.name === "landing" && !store.getters.getSettings.enableLandingPage) {
        return next({ path: store.getters.getSettings.playerLanding });
    }

    // hide the registration route if the registration is disabled
    if (
        to.name === "register" &&
        store.getters.getSettings.disableRegistration
    ) {
        return next({ path: "/not-found" });
    }

    // redirect the user to the login page if auth is required but he not logged in
    if (
        store.getters.getSettings.requireAuth &&
        !store.getters.getUser &&
        to.name !== "login" &&
        to.name !== "register" &&
        to.name !== "landing" &&
        to.name !== "complete_signup" &&
        to.name !== "forgot_password" &&
        to.name !== "reset_password"
    ) {
        return next({ name: "login" });
    }
    // redirect the user to the player landing page if he is already logged in
    // and visited login or register pages
    if (
        (to.name == "login" || to.name == "register") &&
        store.getters.getUser
    ) {
        return next({ path: store.getters.getSettings.playerLanding });
    }
    // redirect the user to a 404 page if he visited a built-in page `charts`
    //  not allowed to access by the admin
    if (!store.getters.getSettings.enableBrowse && to.name.match(/browse/)) {
        return next({ path: "/not-found" });
    }
    // managing user access to routes that require admin role, artist role ...etc
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!store.getters.getUser) {
            if (from.path === "/") {
                next({ name: "login" });
            } else {
                Vue.$confirm({
                    message: `You need to login to your account`,
                    button: {
                        no: "Cancel",
                        yes: "Login"
                    },
                    callback: confirm => {
                        if (confirm) {
                            next({ name: "login" });
                        } else {
                            next(false);
                        }
                    }
                });
            }
        } else {
            if (to.name === "library.my-songs" || to.path === "/library") {
                return store.getters.getSettings.enableUserUpload
                    ? next()
                    : next({ name: "library.playlists" });
            } else if (to.name === "library.purchases") {
                return store.getters.getSettings.enable_selling
                    ? next()
                    : next({ name: "library.playlists" });
            } else {
                next()
            }
        }
    } else if (to.matched.some(record => record.meta.requiresAdmin)) {
        // if the visiting route requires admin priviliges
        var user = store.getters.getUser;
        if (
            user &&
            (user.roles.some(role => role.name === "admin") || user.is_admin)
        ) {
            if (!isPageAllowedToAccessByAdmin(to) || to.path === "/admin") {
                return next({ name: "admin.analytics" });
            } else {
                return next();
            }
        } else {
            return next({ path: "/not-found" });
        }
    } else if (to.matched.some(record => record.meta.requiresArtist)) {
        // if the visiting route requires artist priviliges
        var user = store.getters.getUser;
        if (user && user.roles.some(role => role.name === "artist")) {
            if (to.path === "/artist") {
                next({ name: "artist.analytics" });
                return;
            }
            next();
            return;
        } else {
            next({ path: "/not-found" });
            return;
        }
    } else {
        next();
    }
});

export default router;
