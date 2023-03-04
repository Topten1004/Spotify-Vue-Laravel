import helpers from "../../helpers";

const state = {};

const mutation = {};

const actions = {
    /**
     * Send a friend request to a user.
     * @param {*} context
     * @param {*} user_id
     */
    addFriend(context, user_id) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/add-friend", {
                    user_id
                })
                .then(res => {
                    context.dispatch("fetchFriends");
                    resolve(res);
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Remove a friend.
     * @param {*} context
     * @param {*} friend_id
     */
    removeFriend(context, friend_id) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/remove-friend", {
                    friend_id
                })
                .then(res => {
                    context.dispatch("fetchFriends");
                    resolve(res);
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Follow a playlist.
     * @param {*} context
     * @param {*} id
     * @param {*} type
     */
    async follow(context, { id, type, origin = "local" }) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        axios
            .post("/api/follow", {
                id,
                type,
                origin
            })
            .then(res => {
                context.dispatch(
                    "follows",
                    type
                );
                Promise.resolve(res);
            })
            .catch(e => {
                Promise.reject(e);
            });
    },
    /**
     * Like an album.
     * @param {*} context
     * @param {*} id
     * @param {*} type
     */
    async like(context, { id, type, origin = 'local' }) {
        if( context.getters.getSettings.ga4 && context.getters.getSettings.analytics_like_event ) {
            emitAnalyticsEvent({
                action: 'like',
                category: type
            })
        }
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        axios
            .post("/api/like", {
                id,
                type,
                origin
            })
            .then(res => {
                context.dispatch(
                    "likes",
                    type
                );
                Promise.resolve(res);
            })
            .catch(e => {
                Promise.reject(e);
            });
    },
    /**
     * Unlike an album.
     * @param {*} context
     * @param {*} album_id
     */
    async dislike(context, { id, type, origin = 'local'}) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
            axios
                .post("/api/dislike", {
                    id,
                    type,
                    origin
                })
                .then(res => {
                    context.dispatch(
                        "likes",
                        type
                    );
                    Promise.resolve(res);
                })
                .catch(e => {
                    Promise.reject(e);
                });
    },
    /*
     * Unfollow a podcast.
     * @param {*} context
     * @param {*} id
     * @param {*} type
     */
    async unfollow(context, { id, type, origin = "local" }) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        return new Promise((resolve, reject) => {
            axios
                .post("/api/unfollow", {
                    id,
                    type,
                    origin
                })
                .then(res => {
                    context.dispatch(
                        "follows",
                        type
                    );
                    resolve(res);
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Attach a song to a playlist.
     * @param {*} context
     * @param {*} param1
     */
    attachToPlaylist(context, { playlist_id, song_id, song_origin }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/attach-to-playlist", {
                    playlist_id,
                    song_id,
                    song_origin
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
     * Detach a song from a playlist.
     * @param {*} context
     * @param {*} param1
     */
    detachFromPlaylist(context, { playlist_id, song_id, song_origin }) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/detach-from-playlist", {
                    playlist_id,
                    song_id,
                    song_origin
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
     * Fetch the recent played songs by the current user.
     * @param {*} context
     */
    fetchRecentPlays(context) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user/recent-plays")
                .then(res => {
                    var json = res.data;
                    let arr = Object.keys(json).map(key => json[key]);
                    context.commit("setRecentlyPlayed", arr);
                })
                .catch(e => {
                    reject();
                });
        });
    },

    /**
     * Create a playlist.
     * @param {*} context
     * @param {*} data
     */
    createPlaylist(context, data) {
        emitAnalyticsEvent({
            action: "create",
            category: "playlist",
            label: data.title
        });
        return new Promise((resolve, reject) => {
            axios
                .post("/api/user/playlists", data)
                .then(res => {
                    resolve(res);
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Delete a playlist ( created by the current logged user ).
     * @param {*} context
     * @param {*} playlist_id
     */
    delete_user_playlist(context, playlist_id) {
        return new Promise((resolve, reject) => {
            axios
                .delete("/api/user/playlists/" + playlist_id)
                .then(res => {
                    context.dispatch("fetchPlaylists");
                    resolve(res);
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Make a song publicly visible.
     * @param {*} context
     * @param {*} song_id
     */
    make_song_public(context, song_id) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/make-song-public/" + song_id)
                .then(() => {
                    resolve();
                    context.dispatch("fetchOwnSongs");
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Make a song privatly visible.
     * @param {*} context
     * @param {*} song_id
     */
    make_song_private(context, song_id) {
        return new Promise((resolve, reject) => {
            axios
                .post("/api/make-song-private/" + song_id)
                .then(() => {
                    resolve();
                    context.dispatch("fetchOwnSongs");
                })
                .catch(e => reject(e));
        });
    },
    /**
     * Make a playlist publicly visible.
     * @param {*} context
     * @param {*} playlist_id
     */
    async make_playlist_public(context, playlist_id) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        axios
            .post("/api/make-playlist-public/" + playlist_id)
            .then(() => {
                Promise.resolve();
                context.dispatch("fetchPlaylists");
            })
            .catch(e => {
                Promise.reject(e);
            });
    },
    /**
     * Make a playlist privatly visible.
     * @param {*} context
     * @param {*} playlist_id
     */
    async make_playlist_private(context, playlist_id) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        axios
            .post("/api/make-playlist-private/" + playlist_id)
            .then(() => {
                Promise.resolve();
                context.dispatch("fetchPlaylists");
            })
            .catch(e => {
                Promise.reject(e);
            });
    },
    /**
     * Delete a song uploaded by the current logged user.
     * @param {*} context
     * @param {*} song_id
     */
    delete_user_song(context, song_id) {
        return new Promise((resolve, reject) => {
            axios
                .delete("/api/user/songs/" + song_id)
                .then(res => {
                    context.dispatch("fetchOwnSongs");
                    resolve(res);
                })
                .catch(e => reject(e));
        });
    },
    /**
     * Unfollow a playlist.
     * @param {*} context
     * @param {*} playlist_id
     */
    async unFollowPlaylist(context, playlist_id) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        axios
            .post("/api/unfollow-playlist", {
                playlist_id
            })
            .then(() => {
                context.dispatch("fetchFollowedPlaylists");
                Promise.resolve();
            })
            .catch(e => {
                Promise.reject(e);
            });
    },
    /**
     * Fetch the current logged user friends.
     * @param {*} context
     */
    fetchFriends(context) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user/friends")
                .then(res => {
                    context.commit("setFriends", res.data);
                    resolve();
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Fetch the liked songs by the current logged user.
     * @param {*} context
     */
    async likedSongs(context) {
        try {
            let res = await axios.get("/api/user/liked-songs");
            context.commit("setLikes", res.data);
        } catch (e) {}
    },
    /**
     * Fetch the liked albums by the current logged user.
     * @param {*} context
     */
    likes(context, type) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user/likes/" + type)
                .then(res => {
                    context.commit("setLiked" + (type[0].toUpperCase() + type.slice(1).toLowerCase()) + "s", res.data);
                    resolve();
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    /**
     * Fetch the uploaded songs by the current logged user.
     * @param {*} context
     */
    fetchOwnSongs(context) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user/songs")
                .then(res => {
                    context.commit("setOwnSongs", res.data);
                    resolve();
                })
                .catch(e => reject(e));
        });
    },
        /**
     * Fetch the purchased songs by the current logged user.
     * @param {*} context
     */
    fetchPurchases(context) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user/purchases")
                .then(res => {
                    context.commit("purchase/setPurchases", res.data);
                    resolve();
                })
                .catch(e => reject(e));
        });
    },
    /**
     * Fetch the liked albums by the current logged user.
     * @param {*} context
     */
    follows(context, type) {
        return new Promise((resolve, reject) => {
            axios
                .get("/api/user/follows/" + type)
                .then(res => {
                    context.commit("setFollowed" + (type[0].toUpperCase() + type.slice(1).toLowerCase()) + "s", res.data);
                    resolve();
                })
                .catch(e => {
                    reject(e);
                });
        });
    },
    // /**
    //  * Like a song.
    //  * @param {*} context
    //  * @param {*} song_id
    //  */
    // async like(context, song_id) {
    //     if (!context.getters.isLogged) {
    //         // if the user is not logged
    //         await helpers.loginOrCancel();
    //     }
    //     try {
    //         await axios.post("/api/like-song", {
    //             id: song_id
    //         });
    //         await context.dispatch("likedSongs");
    //         return Promise.resolve();
    //     } catch (e) {
    //         return Promise.reject();
    //     }
    // },
    // /**
    //  * Unlike a song.
    //  * @param {*} context
    //  * @param {*} song_id
    //  */
    // async unlike(context, song_id) {
    //     if (!context.getters.isLogged) {
    //         // if the user is not logged
    //         await helpers.loginOrCancel();
    //     }
    //     try {
    //         await axios.post("/api/unlike-song", {
    //             id: song_id
    //         });
    //         await context.dispatch("likedSongs");
    //         return Promise.resolve();
    //     } catch (e) {
    //         return Promise.reject();
    //     }
    // },

    /**
     * Follow an artist.
     * @param {*} context
     * @param {*} artist_id
     */
    async followArtist(context, artist_id) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        axios
            .post("/api/follow-artist", {
                user_id: context.getters.getUser.id,
                artist_id: artist_id
            })
            .then(() => {
                context.dispatch("fetchFollowedArtists");
                Promise.resolve();
            })
            .catch(e => Promise.reject(e));
    },
    /**
     * Unfollow an aritst.
     * @param {*} context
     * @param {*} artist_id
     */
    async unfollowArtist(context, artist_id) {
        if (!context.getters.isLogged) {
            // if the user is not logged
            await helpers.loginOrCancel();
        }
        axios
            .post("/api/unfollow-artist", {
                user_id: context.getters.getUser.id,
                artist_id: artist_id
            })
            .then(() => {
                context.dispatch("fetchFollowedArtists");
                Promise.resolve();
            })
            .catch(e => Promise.reject(e));
    },
    /**
     * Checks if a friend is a friend with the current user.
     * @param {*} context
     * @param {*} friend_id
     */
    async checkFriendStatus(context, friend_id) {
        var res = await axios.post("/api/check-friendship-status", {
            friend_id,
            user_id: context.getters.getUser.id
        });
        return res.data;
    },
    /**
     * Get the user playlists.
     * @param {*} context
     */
    async fetchPlaylists(context) {
        try {
            let res = await axios.get("/api/user/playlists");
            let playlists = res.data;
            context.commit("setPlaylists", playlists);
            Promise.resolve();
        } catch (e) {
            Promise.reject(e);
        }
    },
    /**
     * Checks if an artist is followed by the current user.
     * @param {*} context
     * @param {*} artist_id
     */
    async isArtistFollowed(context, artist_id) {
        if (context.getters.getFollowedArtists) {
            let artists = context.getters.getFollowedArtists;
            return artists.some(artist => artist.id === artist_id);
        } else if (context.state.user) {
            await context.dispatch("fetchFollowedArtists");
            context.commit("isArtistFollowed", artist_id);
        } else {
            return false;
        }
    }
};

export default {
    state,
    mutation,
    actions
};
