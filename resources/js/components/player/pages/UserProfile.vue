
<template>
    <fixed-left v-if="user">
        <template v-slot:img>
            <div class="img-cover">
                <v-img :src="user.avatar" class="img" aspect-ratio="1">
                    <template v-slot:placeholder>
                        <div class="fixed-area__image-skeleton">
                            <content-placeholders :rounded="true">
                                <content-placeholders-img />
                            </content-placeholders>
                        </div>
                    </template>
                </v-img>
            </div>
        </template>
        <template v-slot:infos>
            <div class="infos-container">
                <div class="info-wrapper d-flex">
                    <div class="displayname max-2-lines">
                        {{ user.name }}
                    </div>
                    <div class="badge" v-if="user.plan" :title="$t('Subscribed to') + ' '  + user.plan.name + ' plan'">
                        <v-btn outlined small color="primary">
                            <v-icon left v-if="user.plan.badge">$vuetify.icons.{{ user.plan.badge }}</v-icon>
                            {{ user.plan.name }}
                        </v-btn>
                    </div>
                </div>
                <!-- upcoming feature -->
                <!-- <div class="stats">
                    <span
                        class="followers"
                        v-if="$store.getters.getSettings.enableFriendshipSystem"
                        >{{ user.nb_friends }} Friends
                    </span>
                </div> -->
            </div>
        </template>
        <template v-slot:buttons>
            <div class="buttons">
                <div
                    class="follow"
                    v-if="
                        $store.getters.getUser &&
                            $route.params.id != $store.getters.getUser.id &&
                            friendStatus !== null &&
                            $store.getters.getSettings.enableFriendshipSystem
                    "
                >
                    <v-btn
                        class="primary"
                        rounded
                        small
                        @click="addFriend($route.params.id)"
                        v-if="friendStatus == 'notFriends'"
                    >
                        {{$t('Add Friend')}}
                    </v-btn>
                    <v-btn
                        class="secondary"
                        rounded
                        small
                        v-else-if="friendStatus == 'requested'"
                        disabled
                    >
                        {{$t('Friend request sent')}}
                    </v-btn>
                    <div id="unfriend_button" v-else>
                        <v-btn
                            class="secondary"
                            id="friends_are_friends"
                            rounded
                            small
                        >
                            {{$t('Friends')}}
                        </v-btn>
                        <v-btn
                            class="error"
                            id="friends_no_friends"
                            rounded
                            small
                            @click="addFriend($route.params.id)"
                        >
                            {{$t('Unfriend')}}
                        </v-btn>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:main>
            <div
                class="profile-main-content"
                v-if="
                    user.mostPlayed.length ||
                        user.playlists.length ||
                        user.likes.length
                "
            >
                <div class="most-played" v-if="user.mostPlayed.length">
                    <div class="card-title-medium mb-3">{{$t('Most played songs')}}</div>
                    <div class="content">
                        <song-list :songs="mostPlayedSpliced" :options="['like']"></song-list>
                    </div>
                    <div class="more-or-less">
                        <div
                            class="more"
                            @click="nMostPlayed += 3"
                            v-if="nMostPlayed < user.mostPlayed.length"
                        >
                            {{$t('More')}}
                        </div>
                    </div>
                </div>
                <!-- <div
                    class="followed-artists"
                    v-if="user.followed_artists.length"
                >
                    <div class="card-title-medium mb-3">{{$t('Followed artists')}}</div>
                    <div class="artists-container">
                        <artist
                            v-for="artist in user.followed_artists"
                            :artist="artist"
                            :key="artist.id"
                        />
                    </div>
                </div> -->
                <div class="singles" v-if="user.likes.length">
                    <div class="card-title-medium mb-3">{{$t('Likes')}}</div>
                    <div class="content">
                        <song-list :songs="LikesSpliced"></song-list>
                    </div>
                    <div class="more-or-less">
                        <div
                            class="more"
                            @click="nLikes += 3"
                            v-if="nLikes < user.likes.length"
                        >
                            {{$t('More')}}
                        </div>
                    </div>
                </div>
                <!-- <div
                    class="followed-playlists"
                    v-if="user.followed_playlists.length"
                >
                    <div class="card-title-medium mb-3">{{$t('Followed playlists')}}</div>
                    <div class="playlists-container">
                        <playlist-expo
                            v-for="playlist in user.followed_playlists"
                            :playlist="playlist"
                            :key="playlist.id"
                        />
                    </div>
                </div> -->
                <div class="users_playlists" v-if="user.playlists.length">
                    <div class="card-title-medium mb-3">
                        {{ user.name }} {{$t('Playlists')}}
                    </div>
                    <div class="content content-list-wrapper">
                        <ul>
                            <li
                                v-for="(playlist, i) in user.playlists"
                                :key="i"
                                @click="
                                    $router.push({
                                        name: 'playlist',
                                        params: { id: playlist.id }
                                    })
                                "
                            >
                                <div class="item-cover">
                                    <v-img
                                        :src="playlist.cover"
                                        class="img"
                                        aspect-ratio="1"
                                    >
                                        <template v-slot:placeholder>
                                            <v-row
                                                class="fill-height ma-0"
                                                align="center"
                                                justify="center"
                                            >
                                                <content-placeholders
                                                    :rounded="true"
                                                    style="width: 100%"
                                                >
                                                    <content-placeholders-img />
                                                </content-placeholders>
                                            </v-row>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="item-title">
                                    {{ playlist.title }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <template v-else>
                <empty-page
                    img="peep-68.png"
                    :headline="$t('This profile is empty!')"
                    :sub="$t('Looks like this account is still new!')"
                >
                </empty-page>
            </template>
        </template>
    </fixed-left>
    <empty-page
        v-else-if="!user && errorStatus == 404"
        :headline="$t('Not Available!')"
        :sub="$t('User does not exist.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)"
                >{{$t('Go Back')}}</v-btn
            >
        </template>
    </empty-page>
    <empty-page
        v-else-if="!user && errorStatus"
        ::headline="$t('Something wrong happend')"
        :sub="$t('Some server error has occurred. Try again later.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)"
                >{{$t('Go Back')}}</v-btn
            >
        </template>
    </empty-page>
    <page-loading v-else />
</template>

<script>
export default {
    metaInfo() {
        return {
            title: this.generateMetaInfos(
                this.user,
                this.$store.getters.getSettings.userProfilePageTitle,
                "user"
            ),
            meta: [
                {
                    name: "description",
                    content: this.generateMetaInfos(
                        this.user,
                        this.$store.getters.getSettings
                            .userProfilePageDescription,
                        "user"
                    )
                }
            ]
        };
    },
    created() {
        this.fetchUserProfile();
    },
    data() {
        return {
            user: null,
            friendStatus: null,
            nLikes: 3,
            nMostPlayed: 3,
            errorStatus: null
        };
    },
    computed: {
        LikesSpliced() {
            if (this.user.likes !== 3) {
                return this.user.likes.slice(0, this.nLikes);
            } else {
                return this.user.likes;
            }
        },
        mostPlayedSpliced() {
            if (this.user.mostPlayed !== 3) {
                return this.user.mostPlayed.slice(0, this.nMostPlayed);
            } else {
                return this.user.mostPlayed;
            }
        }
    },
    methods: {
        addFriend(user_id) {
            if (this.friendStatus == "friends") {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna unfriend")}  ${this.user.name}?`,
                    button: {
                        no: this.$t("Cancel"),
                        yes: this.$t("Yes")
                    },
                    /**
                     * Callback Function
                     * @param {Boolean} confirm
                     */
                    callback: confirm => {
                        if (confirm) {
                            this.friendStatus = "notFriends";
                            this.$store
                                .dispatch("removeFriend", user_id)
                                .then(() => {
                                    this.$notify({
                                        group: "foo",
                                        type: "success",
                                        title: this.$t("Removed"),
                                        text: this.$t('Friend removed successfully.')
                                    });
                                });
                        }
                    }
                });
            } else if (this.friendStatus == "notFriends") {
                this.friendStatus = "requested";
                this.$store.dispatch("addFriend", user_id).then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Requested"),
                        text: this.$t('Friend Request sent successfully.')
                    });
                });
            }
        },
        fetchUserProfile() {
            axios
                .get("/api/profile/" + this.$route.params.id)
                .then(res => (this.user = res.data))
                .then(() => {
                    this.$store
                        .dispatch("checkFriendStatus", this.$route.params.id)
                        .then(res => (this.friendStatus = res));
                })
                .catch(e => (this.errorStatus = e.response.status));
        }
    }
};
</script>
<style lang="scss" scoped>
.artists-container {
    display: flex;
    flex-wrap: wrap;
    & > * {
        flex-basis: 20%;
        max-width: 12em;
    }
}
.followed-playlists {
    .playlists-container {
        display: flex;
        flex-wrap: wrap;
        & > * {
            flex-basis: 25%;
            max-width: 12em;
        }
    }
}
.profile-main-content {
    & > * {
        margin-bottom: 2.5em;
    }
}
#unfriend_button {
    #friends_no_friends {
        display: none;
    }
    &:hover {
        #friends_are_friends {
            display: none;
        }
        #friends_no_friends {
            display: initial;
        }
    }
}
</style>
