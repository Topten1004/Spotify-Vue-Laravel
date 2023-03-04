import Vue from "vue";
import store from "./store/index.js";
import moment from "moment";
import router from "./router.js";
import ColorManager from "./colorManager.js";
import helpers from "./helpers.js";


export default Vue.mixin({
    computed: {
        /**
         * Get the permissions of the user.
         */
        permissions() {
            return store.getters.getUser.permissions;
        },
        userPlan() {
            return store.getters.getUser.plan
        },
        isUpgradable() {
            var plans_length = store.getters.getPlans && store.getters.getPlans ? store.getters.getPlans.length : 0;
            var is_current_plan_upgradable = store.getters.getUser.plan && store.getters.getUser.plan.upgradable;
            return plans_length > 1 && (is_current_plan_upgradable || !this.userPlan);
        },
        defaultLocale() {
            return document.documentElement.lang
        }
    },
    methods: {
        // emitAnalyticsEvent({ action, category, label }) {
        //     this.$gtag.event(action, {
        //         'event_category': category,
        //         'event_label': label
        //         // 'value': <value>
        //     })
        // },
        getItemURL(item) {
            const appUrl = store.getters.getSettings.appUrl;
            if (!item.podcast) {
                return (
                    appUrl +
                    (appUrl.substring(appUrl.length - 1) === "/"
                        ? item.type + "/"
                        : "/" + item.type + "/") +
                    item.id
                );
            }
        },
        price(amount){
            if( isNaN(amount) ) return 0;
            return parseFloat((amount/100).toFixed(2));
        },
        /**
         * This function replaces the variables of the SEO settings
         * stored on the database with their actual values
         * @param {*} item
         * @param {*} titleTemplate
         * @param {*} page
         */
        generateMetaInfos(item, titleTemplate, page) {
            if (!item)
                return (
                    store.getters.getSettings.appName ||
                    "Play & enjoy to pure music"
                );
            let title = titleTemplate
                .replace("%site_name", store.getters.getSettings.appName)
                .replace(
                    "%artist_name",
                    this.getMainArtist(item)
                )
                .replace(
                    "%user_name",
                    item.name ? item.name : item.user ? item.user.name : ""
                )
                .replace("%creation_date", moment(item.created_at).format("ll"))
                .replace(/(—|-)\W+/, function(c) {
                    if (c.length >= 3) return c.replace(/(—|-)/, "");
                    else return c;
                });
            if (
                page === "song" ||
                page === "album" ||
                page === "podcast" ||
                page === "playlist"
            ) {
                title = title
                    .replace("%song_title", item.title)
                    .replace("%album_title", item.title)
                    .replace("%podcast_title", item.title)
                    .replace("%playlist_title", item.title);
            }
            if (page === "genre" || page === "podcast-genre") {
                title = title
                    .replace("%genre_name", item.name)
                    .replace("%podcast-genre_name", item.name);
            }
            return (
                title.slice(0, 1).toUpperCase() + title.slice(1, title.length)
            );
        },
        slug(Text) {
            return Text.toLowerCase()
                .replace(/ /g, "-")
                .replace(/[^\w-]+/g, "") || Text;
        },
        /**
         * Updates the colors of the application.
         * @param {*} themes
         */
        updateColors(themes) {
            this.$store.getters.getSettings.themes = themes;
            const colorManager = new ColorManager(themes);
            // the root colors of the DOM
            colorManager.updateRootColors();
            // Mutate Vuetify default themes & colors
            this.$vuetify.theme.primary = colorManager.primaryColor;
            this.$vuetify.theme.themes.dark.primary = this.$vuetify.theme.themes.light.primary =
                colorManager.primaryColor;
            this.$vuetify.theme.themes.dark.secondary = this.$vuetify.theme.themes.light.secondary =
                colorManager.secondaryColor;
            this.$vuetify.theme.themes.dark.background =
                colorManager.darkThemeBgColor;
            this.$vuetify.theme.themes.dark.panel =
                colorManager.darkThemePanelColor;
            this.$vuetify.theme.themes.dark.text =
                colorManager.darkThemeTextColor;
            this.$vuetify.theme.themes.dark.textContMedium =
                colorManager.darkThemeTextContMedium;
            this.$vuetify.theme.themes.dark.textContLow =
                colorManager.darkThemeTextColorContLow;
            this.$vuetify.theme.themes.light.background =
                colorManager.lightThemeBgColor;
            this.$vuetify.theme.themes.light.panel =
                colorManager.lightThemePanelColor;
            this.$vuetify.theme.themes.light.text =
                colorManager.lightThemeTextColor;
            this.$vuetify.theme.themes.light.textContMedium =
                colorManager.lightThemeTextContMedium;
            this.$vuetify.theme.themes.light.textContLow =
                colorManager.lightThemeTextColorContLow;
        },
        /**
         * Check if the current logged user has a permission.
         * @param {*} permission
         */
        hasPermission(permission) {
            return Boolean(
                (store.getters.getUser && store.getters.getUser.is_admin) ||
                    (store.getters.getUser && store.getters.getUser.permissions
                        ? store.getters.getUser.permissions.some(
                              per => per.name === permission
                          )
                        : false)
            );
        },
        changeTheme() {
            this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
            localStorage.setItem('dark-theme', this.$vuetify.theme.dark)
        },
        isSubTo(plan_id) {
            return store.getters.getUser && store.getters.getUser.plan && store.getters.getUser.plan.id === plan_id
        },
        planCurrencySymbol(plan) {
            return plan.currency_symbol ? plan.currency_symbol :  plan.currency.symbol ?  plan.currency.symbol : (plan.currency.value || plan.currency)
        },
        /**
         * Logout the user.
         */
        logout() {
            store.dispatch("logout");
        },
        /**
         * Checks if the current logged user is an aritst.
         */
        isArtist() {
            const user = store.getters.getUser;
            if (user) {
                return user.roles
                    ? user.roles.some(role => role.name === "artist")
                    : false;
            }
            return false;
        },
        /**
         * Checks if the current logged user is an admin.
         */
        isAdmin() {
            const user = store.getters.getUser;
            if (user) {
                return user.roles
                    ? user.roles.some(role => role.name === "admin") ||
                          user.is_admin
                    : false;
            }
            return false;
        },
        /**
         * Converts the songs artists from the array format
         * to a representative value (ex: [A,B,C] => A Ft. B, C)
         * @param {*} artists
         */
        getArtists(artists) {
            if (artists) {
                if (artists.length) {
                    return artists.map( artist => artist.displayname).join(", ")
                } else {
                    return "";
                }
            }
        },
        /**
         * Converts the songs artists from the array format
         * to a representative value (ex: [A,B,C] => A Ft. B, C)
         * @param {*} artists
         */
        getMainArtist(item) {
            return  item.artist ? (item.artist.name ? item.artist.name : item.artist.displayname) : ''
        },
        async copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                this.$notify({
                    group: "foo",
                    type: "success",
                    title: this.$t("Copied"),
                    text: this.$t('URL copied to clipboard.')
                });
            } catch (err) {
                this.$notify({
                    group: "foo",
                    type: "info",
                    title: this.$t("Oops!"),
                    text: this.$t('You need to copy the URL manually.')
                });
            }
        },
        /**
         * Get the file name from the asset URL.
         * @param {*} url
         */
        extractFileName(url) {
            return url
                .match(/\d+.+/)[0]
                .replace(/^\d+/, "")
                .replace(/\w{3,4}$/, "");
        },
        /**
         * Go to another page ( route ).
         * @param {*} param0
         */
        pushRoute({ name, id }) {
            store.commit("setCurrentPageId");
            router.push({ name, params: { id } });
        },
        /**
         * Convert a value from bytes to MB.
         * @param {*} bytes
         */
        bytesToMB(bytes) {
            return (bytes / 1024 / 1024).toFixed(0);
        },
        /**
         * Coverts time from seconds to mm:ss format.
         * @param {*} secs
         */
        mmss(secs) {
            var minutes = Math.floor(secs / 60);
            secs = secs % 60;
            return `${("0" + minutes).slice(-2)}:${("0" + secs).slice(-2)}`;
        },
        /**
         * Checks if a file extension exists on an array of extensions.
         * @param {*} fileName
         * @param {*} extensions
         */
        doesExtensionMatch(fileName, extensions) {
            var extension = fileName
                .split(".")
                .pop()
                .toLowerCase();
            return extensions.indexOf(extension) > -1;
        },
        /**
         * Fetch an audio File metadata.
         * @param {*} file
         * @param {*} callback
         */
        getAudioMetadata(file, callback) {
            var metadataLoaded = false;
            var audio = document.createElement("audio");
            audio.preload = "metadata";
            audio.src = file instanceof Blob ? URL.createObjectURL(file) : file;
            audio.onloadedmetadata = function() {
                metadataLoaded = true;
                window.URL.revokeObjectURL(audio.src);
                callback(audio);
            };
            setTimeout(() => {
                if (!metadataLoaded) {
                    callback();
                }
            }, 2000);
        },
        parseJSON(string) {
            if( typeof string !== 'number' && typeof string == 'string' ) {
                try {
                    JSON.parse(string);
                } catch (e) {
                    return string;
                }
                return this.parseJSON(JSON.parse(string));
            }
            return string;
        },
        parseAd(ad) {
            const parsedAd = this.parseJSON(ad);
            return {
                code: this.parseJSON(parsedAd.code),
                position: parsedAd.position
            }
        },
        moment,
        loginOrCancel: helpers.loginOrCancel
    }
});
