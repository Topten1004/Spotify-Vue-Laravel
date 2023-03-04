<template>
    <div class="friends-container">
        <div class="friends-container__main">
            <div class="friends-container__title">
                {{ $t("Friends") }}
            </div>
            <div class="friends-list" v-if="friends.length">
                <ul>
                    <li v-for="friend in friends" :key="friend.id">
                        <div class="top-side">
                            <div class="avatar user-avatar">
                                <div class="badge" v-if="friend.badge">
                                    <v-icon color="primary"
                                        >$vuetify.icons.{{ friend.badge }}</v-icon
                                    >
                                </div>
                                <img :src="friend.avatar" alt="" />
                                <div
                                    class="status status-online"
                                    v-if="friend.online"
                                ></div>
                                <div class="status status-offline" v-else></div>
                            </div>
                            <div class="infos">
                                <div class="name-container">
                                    <div class="name max-1-lines">
                                        {{ friend.name }}
                                    </div>
                                </div>
                                <div class="activity-container">
                                    <div class="hiding-box-left"></div>
                                    <div
                                        class="activity"
                                        v-if="
                                            friend.is_playing && friend.online
                                        "
                                    >
                                        {{ whatIsHeListeningTo(friend) }}
                                    </div>
                                    <div class="hiding-box-right"></div>
                                </div>
                            </div>
                        </div>
                        <div class="bottom-side">
                            <div class="options">
                                <div
                                    class="profile"
                                    @click="
                                        $router.push({
                                            name: 'profile',
                                            params: { id: friend.id }
                                        })
                                    "
                                >
                                    <div class="icon">
                                        <img
                                            src="/svg/user-circle.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="option-title">
                                        {{ $t("Profile") }}
                                    </div>
                                </div>
                                <div
                                    class="chat"
                                    @click="
                                        $store.commit(
                                            'setOpenChatWith',
                                            friend.id
                                        )
                                    "
                                    v-if="
                                        $store.getters.getSettings
                                            .enableRealtime &&
                                            $store.getters.getSettings
                                                .enableChat &&
                                            hasPermission('Chat with friends')
                                    "
                                >
                                    <div class="icon">
                                        <img
                                            src="/svg/speech-bubble-line.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="option-title">
                                        {{ $t("Chat") }}
                                    </div>
                                </div>
                                <button
                                    class="hear"
                                    @click="
                                        listenToFriendPlayer(friend.is_playing)
                                    "
                                    v-if="
                                        friend.is_playing &&
                                            friend.online &&
                                            hasPermission('Listen with friends')
                                    "
                                >
                                    <div class="icon">
                                        <img
                                            src="/svg/headphone.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="option-title">
                                        {{ $t("Listen") }}
                                    </div>
                                    <div class="overlay"></div>
                                </button>
                                <div class="unfollow" @click="unfriend(friend)">
                                    <div class="icon">
                                        <img
                                            src="/svg/male-remove.svg"
                                            alt=""
                                            class="svg-image"
                                        />
                                    </div>
                                    <div class="option-title">
                                        {{ $t("Unfriend") }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <empty-page
                v-else
                id="no-friends"
                :headline="$t('No Friends!')"
                :sub="$t('You seem to be having no friends. Add some!')"
            />
        </div>
    </div>
</template>
<script>
import Echo from "laravel-echo"
window.Pusher = require('pusher-js');
export default {
    created() {
        // init pusher/echo
        if (this.$store.getters.getSettings.enableRealtime) {
            window.Echo = new Echo({
                auth: {
                headers: {
                    Authorization: "Bearer " + this.$store.getters.getToken,
                },
                },
                authEndpoint: "/api/broadcasting/auth",
                broadcaster: "pusher",
                key: this.$store.getters.getSettings.pusherKey,
                cluster: this.$store.getters.getSettings.pusherCluster,
            });
                window.Echo.channel("Chat").listen("HearingEvent", e => {
                let friend = this.friends.find(
                    friend => friend.id == e.user_id
                );
                if (friend) {
                    friend.is_playing = e.is_playing;
                }
            });
            window.Echo.join(`Chat`)
                .here(users => {
                    // this script is added so if the user closed the window and did not complete
                    // a song, then his 'is_playing' status woudn't update. When a user rejoins
                    // his status will be reseted ( is_playing = null )
                    users.forEach(user => {
                        if (user.id == this.$store.getters.getUser.id) {
                            if (!this.$store.getters.getCurrentAudio) {
                                this.$store.dispatch("endPlay");
                            }
                        }
                    });
                    this.friends.forEach(friend => {
                        users.forEach(user => {
                            if (user.id == friend.id) {
                                friend.online = true;
                            }
                        });
                    });
                })
                .joining(user => {
                    this.friends.forEach(friend =>
                        user.id == friend.id ? (friend.online = true) : ""
                    );
                })
                .leaving(user => {
                    this.friends.forEach(friend =>
                        user.id == friend.id
                            ? ((friend.online = false),
                              (friend.is_playing = null))
                            : ""
                    );
                });
        }
    },
    data() {
        return {
            focused: null
        };
    },
    computed: {
        friends() {
            return this.$store.getters.getFriends;
        }
    },
    methods: {
        unfriend(friend) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna unfriend")}  ${
                    friend.name
                }?")}`,
                button: {
                    no: this.$t("No"),
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        this.$store
                            .dispatch("removeFriend", friend.id)
                            .then(() => this.$store.dispatch("fetchFriends"));
                    }
                }
            });
        },
        listenToFriendPlayer(content) {
            if (content.content_item.podcast) {
                this.$store.dispatch("playEpisode", {
                    episode: content.content_item,
                    reset: true
                });
            } else {
                this.$store.dispatch("playSong", {
                    song: content.content_item,
                    reset: true
                });
            }
        },
        whatIsHeListeningTo(friend) {
            var is_playing = friend.is_playing;
            return (
                this.$t("Listening to") +
                " " +
                is_playing.title +
                (is_playing.artists
                    ? " " +
                      this.$t("by") +
                      " " +
                      this.getArtists(is_playing.artists)
                    : "") +
                (is_playing.parent_title
                    ? " " +
                      this.$t("from") +
                      " " +
                      is_playing.parent_title +
                      (is_playing.content_type === "song"
                          ? " " + this.$t("album")
                          : is_playing.content_type === "episode"
                          ? " " + this.$t("podacst")
                          : "")
                    : "")
            );
        }
    }
};
</script>
