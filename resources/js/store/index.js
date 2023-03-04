import Vue from "vue";
import Vuex from "vuex";

import { i18n } from "../i18n-setup.js";

Vue.use(Vuex);

// modules 
import auth from './modules/auth'
import userModule from './modules/user'
import player from './modules/player'
import purchase from './modules/purchase'
import axios from "axios";

/**
 * Checks if a song is playable.
 * @param {*} song
 * @returns {Boolean}
 */

export default new Vuex.Store({
    modules: {
        auth,
        player,
        userModule,
        purchase
    },
    state: {
        user: null,
        artist: null,
        messages: {},
        token: localStorage.getItem("token") || null,
        queue: [],
        playlists: null,
        dialog: null,
        screenWidth: window.innerWidth,
        settings: null,
        searchResultsPanel: false,
        notifications: null,
        plans: null,
        likedAlbums: null,
        currentPageId: 0,
        songMenu: null,
        // songContextMenu is a value to determine which song dialog should show
        songContextMenu: null,
        ads: null,
        followedPodcasts: null,
        ownSongs: null,
        followedArtists: null,
        followedPlaylists: null,
        openChatWith: null,
        friends: null,
        likedSongs: null,
        recentlyPlayed: null,
        registredPlayID: null,
        songs: null,
        fbLogoutFunction: null,
        // addSongToPlaylist is a null or an Integer value
        // after the user clicks on 'Add to playlist' on a song dialog
        // this value will receive the ID of the song
        // which will be sent after to the backend to attach
        addSongToPlaylist: null,
        sharableItem: null,
        showSharingDialog: false,
        currentLang: null,
        chooseLangDialog: false,
        availableLanguages: false,
        installPrompt: null
    },
    mutations: {
        shareItem(state, item) {
            state.sharableItem = item;
            state.showSharingDialog = true;
        },
        setRegistredPlayID(state, id) {
            state.registredPlayID = id;
        },
        hideSharingDialog(state) {
            state.showSharingDialog = false;
        },
        updateQueue(state, songs) {
            state.queue = songs;
        },
        setSongMenu(state, id) {
            state.songMenu == id
                ? (state.songMenu = null)
                : (state.songMenu = id);
        },
        /**
         * this setter makes sure that only one song dialog-box is oppened at a time
         * with the help of the song ID
         * @param {*} state
         * @param {*} id
         */
        setSongContextMenu(state, id) {
            state.songContextMenu == id
                ? // if the ID of the previous dialog is the same as the new one
                  // the value of the variable will be null, hence the dialog will close
                  (state.songContextMenu = null)
                : (state.songContextMenu = id);
        },
        pushIntoQueue(state, songs) {
            songs.map(song => state.queue.push(song));
        },
        setToken(state, token) {
            state.token = token;
        },
        setMessages(state, messages) {
            state.messages = messages;
        },
        setSettings(state, settings) {
            state.settings = settings;
        },
        setFbLogoutFunction(state, fbLogoutFunction) {
            state.fbLogoutFunction = fbLogoutFunction;
        },
        setCurrentPageId(state) {
            state.currentPageId++;
        },
        setSearchResultsPanel(state, value) {
            state.searchResultsPanel = value;
        },
        setPlaylists(state, playlists) {
            state.playlists = playlists;
        },
        setUser(state, user) {
            state.user = user;
        },
        setArtist(state, artist) {
            state.artist = artist;
        },
        setCurrentLang(state, currentLang) {
            state.currentLang = currentLang;
        },
        setLikedSongs(state, songs) {
            state.likedSongs = songs.filter(song => song);
        },
        setLikedAlbums(state, Albums) {
            state.likedAlbums = Albums;
        },
        setFollowedPodcasts(state, Podcasts) {
            state.followedPodcasts = Podcasts;
        },
        setRecentlyPlayed(state, Songs) {
            state.recentlyPlayed = Songs.filter(Song => Song);
        },
        setOwnSongs(state, Songs) {
            state.ownSongs = Songs;
        },
        /**
         * Remove a song from the array of liked songs ( by the current logged user)
         * @param {*} state
         * @param {*} param1
         */
        spliceFromLikes(state, { id }) {
            const index = state.likedSongs.findIndex(x => x.id == id);
            state.likedSongs.splice(index, 1);
        },
        pushIntoLikes(state, Song) {
            state.likedSongs.push(Song);
        },
        setDialog(state, value) {
            state.dialog = value;
        },
        setScreenWidth(state, value) {
            state.screenWidth = value;
        },
        setOpenChatWith(state, friend_id) {
            state.openChatWith = friend_id;
        },
        setAddSongToPlaylist(state, song) {
            state.addSongToPlaylist = song;
        },
        setUploadImage(state, img) {
            state.uploadImage = img;
        },
        setFollowedArtists(state, artists) {
            state.followedArtists = artists;
        },
        setFollowedPlaylists(state, playlists) {
            state.followedPlaylists = playlists;
        },
        setFriends(state, users) {
            state.friends = users;
        },
        setAds(state, ads) {
            state.ads = ads;
        },
        setNotifications(state, notifications) {
            state.notifications = notifications;
        },
        setPlans(state, plans) {
            state.plans = plans;
        },
        setChooseLangDialog(state, val) {
            state.chooseLangDialog = val;
        },
        setAvailableLanguages(state, val) {
            state.availableLanguages = val;
        },
        setInstallPrompt(state, e) {
            state.installPrompt = e;
        }
    },
    getters: {
        // getters for the state props.
        getQueue(state) {
            return state.queue;
        },
        getSharableItem(state) {
            return state.sharableItem;
        },
        getDialog(state) {
            return state.dialog;
        },
        getRegistredPlayID(state) {
            return state.registredPlayID;
        },
        getSettings(state) {
            return state.settings;
        },
        getSongMenu(state) {
            return state.songMenu;
        },
        getSongContextMenu(state) {
            return state.songContextMenu;
        },
        getCurrentLang(state) {
            return state.currentLang;
        },
        getAds(state) {
            return state.ads;
        },
        getSearchResultsPanel(state) {
            return state.searchResultsPanel;
        },
        getScreenWidth(state) {
            return state.screenWidth;
        },
        isLogged(state) {
            return state.token !== null;
        },
        getToken(state) {
            return state.token;
        },
        getMessages(state) {
            return state.messages;
        },
        getCurrentPageId(state) {
            return state.currentPageId;
        },
        getPlaylists(state) {
            return state.playlists;
        },
        getOwnSongs(state) {
            return state.ownSongs;
        },
        getLikedSongs(state) {
            return state.likedSongs;
        },
        getRecentlyPlayed(state) {
            return state.recentlyPlayed;
        },
        getUser(state) {
            return state.user;
        },
        getArtist(state) {
            return state.artist;
        },
        getNotifications(state) {
            return state.notifications;
        },
        getPlans(state) {
            return state.plans;
        },
        getLikedAlbums(state) {
            return state.likedAlbums;
        },
        getFollowedPodcasts(state) {
            return state.followedPodcasts;
        },
        getUploadImage(state) {
            return state.uploadImage;
        },
        getAddSongToPlaylist(state) {
            return state.addSongToPlaylist;
        },
        getOpenChatWith(state) {
            return state.openChatWith;
        },
        getFollowedArtists(state) {
            return state.followedArtists;
        },
        getFollowedPlaylists(state) {
            return state.followedPlaylists;
        },
        getFriends(state) {
            return state.friends;
        },
        /**
         * Checks if a user has a certain role.
         * @param {*} state
         * @return {Boolean}
         */
        isUser(state) {
            return function(role) {
                if (role === "admin" && state.user.is_admin) {
                    return true;
                }
                return state.user.roles.some(r => r.name === role);
            };
        },
        /**
         * Checks if a song is already liked by the user.
         * @param {*} state
         * @return {Boolean}
         */
        isLiked(state) {
            return function(id, type) {
                if( type === 'song' ) {
                    return (state.likedSongs || []).some(x =>
                        x ? x.id == id : false
                    );
                } else if ( type === 'album' ) {
                    return (state.likedAlbums || []).some(x =>
                        x ? x.id == id : false
                    );
                }
            };
        },
                /**
         * Checks if a song is already liked by the user.
         * @param {*} state
         * @return {Boolean}
         */
        isFollowed(state) {
        return function(id, type) {
            if( type === 'podcast' ) {
                return (state.followedPodcasts || []).some(x =>
                    x ? x.id == id : false
                );
            } else if ( type === 'artist' ) {
                return (state.followedArtists || []).some(x =>
                    x ? x.id == id : false
                );
            }
        };
    },
    getChooseLangDialog(state) {
        return state.chooseLangDialog
    },
    getAvailableLanguages(state) {
        return state.availableLanguages
    },
    getInstallPrompt(state) {
        return state.installPrompt;
    }
},
    actions: {
            /**
     * Register a play on the database for stats.
     * @param {*} context
     * @param {*} id
     */
    fetchMessages(context, locale) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/messages/" + locale)
                .then(msgs => {
                    const messages = {};
                    messages.en = msgs.data.en;
                    i18n.setLocaleMessage("en", messages.en);
                    if (locale !== "en") {
                        messages[locale] = msgs.data[locale];
                        i18n.setLocaleMessage(locale, messages[locale]);
                    }
                    i18n.locale = locale;
                    if( context.getters.getAvailableLanguages && context.getters.getAvailableLanguages.length) {
                        context.commit('setCurrentLang', context.getters.getAvailableLanguages.find(lang => lang.locale == locale))
                    }
                    context.commit("setMessages", messages);
                })
                .then(res => {
                    resolve(res);
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
        /**
         * Get the user notifications.
         * @param {*} context
         */
        fetchNotifications(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/api/user/notifications")
                    .then(res => {
                        context.commit("setNotifications", res.data);
                    })
                    .catch(e => {
                        reject(e);
                    });
            });
        },
        /**
         * Get the user notifications.
         * @param {*} context
         */
        fetchPlans(context) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/api/plans")
                    .then(res => {
                        context.commit("setPlans", res.data);
                        resolve();
                    })
                    .catch(e => {
                        reject(e);
                    });
            });
        },

        /**
         * Get the current logged user data.
         * @param {*} context
         */
        fetchUser(context) {
            return new Promise((res, rej) => {
                axios
                    .get("/api/user")
                    .then(result => {
                        context.commit("setUser", result.data);
                        context.dispatch("likes", "song");
                        context.dispatch("likes", "album");
                        context.dispatch("follows", "artist");
                        if( context.getters.getSettings.enablePodcasts ) {
                            context.dispatch("follows", "podcast");
                        }
                        context.dispatch("fetchPlaylists");
                        context.dispatch("fetchNotifications");
                        context.dispatch("fetchPurchases");

                        context.dispatch("fetchFriends");
                        context.commit('purchase/setCart', result.data.cart)
                        
                        res(result.data);
                    })
                    .catch(() => {
                        context.commit("setUser", null);
                        rej();
                    });
            });
        },
        /**
         * Get the current logged artist data.
         * @param {*} context
         */
        async fetchArtist(context) {
            try {
                let res = await axios.get("/api/artist");
                context.commit("setArtist", res.data);
            } catch (e) {
                context.commit("setArtist", {});
            }
        },
 
        registerPlay(context, { id, type, label, duration, origin, artist_id = '' }) {
            if( context.getters.getSettings.ga4 && context.getters.getSettings.analytics_play_event ) {
                emitAnalyticsEvent({
                    action: 'play',
                    category: type,
                    label: label
                })
            }
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        "/api" +
                            (context.getters.getUser ? "/user" : "") +
                            "/register-play",
                        {
                            id,
                            type,
                            artist_id,
                            duration,
                            origin
                        }
                    )
                    .then(res => {
                        context.commit('setRegistredPlayID', res.data)
                        resolve(res);
                    })
                    .catch(e => {
                        reject(e);
                    });
            });
        },
        /**
         * Play an album.
         * @param {*} context
         * @param {*} songs
         */
        playAlbum(context, { album }) {
            context.dispatch("registerPlay", { ...album, label: album.title });
            context.dispatch("updateQueue", {
                content: album.songs,
                reset: true
            });
        },
        /**
         * Play a podcast.
         * @param {*} context
         * @param {*} songs
         */
        async playPodcast(context, { podcast }) {
            context.dispatch("registerPlay", {
                ...podcast,
                label: podcast.title
            });
            // fetch podcast episodes if they are not fetched
            if( !podcast.episodes ) {
                var episodes = [];
                await axios.get('/api/podcast/' + podcast.id)
                .then((res) => {
                    episodes = res.data.episodes
                })
            } else {
                var episodes = podcast.episodes
            }
            context.dispatch("updateQueue", {
                content: episodes,
                reset: true
            });
        },
        /**
         * Play a playlist.
         * @param {*} context
         * @param {*} songs
         */
        playPlaylist(context, { playlist }) {
            context.dispatch("registerPlay", {
                ...playlist,
                label: playlist.title
            });
            context.dispatch("updateQueue", {
                content: playlist.songs,
                reset: true
            });
        },
        /**
         * Play a radio station.
         * @param {*} context
         * @param {*} stream
         */
        playRadioStation(context, { radioStation }) {
            context.dispatch("updateQueue", {
                content: [radioStation],
                reset: true
            });
        },

        /**
         * Play a radio station.
         * @param {*} context
         * @param {*} stream
         */
        playEpisode(context, { episode, reset }) {
            // registering the plays will take place on the player
            context.dispatch("updateQueue", { content: [episode], reset });
        },
        playSong(context, { song, reset }) {
            // registering the plays will take place on the player
            context.dispatch("updateQueue", { content: [song], reset });
        },
        /**
         * Reset the user status when he compeleted listening to an audio.
         */
        endPlay(context) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/api/user/end-play/" + context.getters.getRegistredPlayID)
                    .then(res => {
                        resolve(res);
                    })
                    .catch(e => {
                        reject(e);
                    });
            });
        },
        /**
         * Download a song.
         * @param {*} context
         * @param {*} param1
         */
        downloadAudio(context, { id, file_name, type }) {
            return new Promise(res => {
                axios
                    .get(
                        "/api/download-" +
                            (type === "episode" ? "episode" : "song") +
                            "/" +
                            id,
                        {
                            responseType: "arraybuffer"
                        }
                    )
                    .then(response => {
                        const url = window.URL.createObjectURL(
                            new Blob([response.data])
                        );
                        // creating an anchor to trigger the URL
                        const link = document.createElement("a");
                        link.href = url;
                        link.setAttribute("download", file_name);
                        document.body.appendChild(link);
                        // Execute the link
                        link.click();
                    })
                    .finally(() => res());
            });
        },
        updateLang(context, lang) {
            context.dispatch('fetchMessages', lang.locale).then(() => {
                context.commit('setCurrentLang', lang)
                localStorage.setItem('pref-locale', lang.locale)
                context.commit('setChooseLangDialog', false)
            })
        }
    }
});
