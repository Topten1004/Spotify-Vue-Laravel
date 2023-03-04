<template>
    <tr
        class="empty-skeleton-tr"
        v-if="!isEndpoint && !item && isOnSectionEdit"
    >
        <div
            class="child absolute fill align-justify-center"
            @click="$emit('attachAsset')"
        >
            <v-icon size="30" class="pointer">$vuetify.icons.plus</v-icon>
        </div>
    </tr>
    <tr
        v-else-if="item"
        :class="{ isAlbum }"
        @click="!isOnEditPage ? play(item) : ''"
        class="data-table-tr"
    >
        <td class="pl-2">
            <div class="cover-and-title">
                <div
                    class="rank pr-4"
                    :style="{ width: '30px' }"
                    v-if="isAlbum"
                >
                    {{ rank }}
                </div>
                <div class="img-container py-2 relative">
                    <v-img
                        :src="item.cover"
                        :alt="item.title"
                        class="table-cover"
                        width="50"
                        height="50"
                    >
                        <span
                            class="play-icon"
                            :class="{
                                hidden: $store.getters.isCurrentlyPlaying(item)
                            }"
                        >
                            <v-icon dark class="i"
                                >$veutify.icons.play-circle</v-icon
                            >
                        </span>
                        <div
                            class="dark-layer absolute fill justify-align-center"
                            v-if="$store.getters.isCurrentlyPlaying(item)"
                        >
                            <div
                                class="epico_music-is-playing-container white-bars"
                            >
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </v-img>
                </div>
                <div class="item-title ml-4  max-1-lines" @click.stop="">
                    <router-link
                        class="router-link"
                        :to="{
                            name: item.type,
                            params: { id: item.id }
                        }"
                    >
                        {{ item.title }}
                    </router-link>
                </div>
            </div>
        </td>
        <td v-if="!isOnEditPage">
            <div
                class="menu-button"
                @click="$store.commit('setSongMenu', 'album-' + item.id)"
            >
                <song-menu
                    :item="item"
                    :closeOnContentClick="true"
                    @close="$store.commit('setSongMenu', null)"
                ></song-menu>
            </div>
        </td>
        <td class="artists p-0">
            <artists :artists="item.artists"></artists>
        </td>
        <td>
            <div class="badges d-flex align-center">
                <purchase-btn :item="item" size="small"></purchase-btn>
                <div
                    class="explicit mx-2"
                    :title="$t('Explicit')"
                    v-if="item.isExplicit"
                >
                    <div class="explicit__sign">
                        E
                    </div>
                </div>
            </div>
        </td>
        <td class="p-0">
            <div class="like-button" v-if="!isOnEditPage">
                <div
                    class="button-svg-container"
                    v-if="$store.getters.isLiked(item.id, item.type)"
                    @click.stop="$store.dispatch('unlike', item)"
                >
                    <v-icon color="primary" class="pointer" small
                        >$vuetify.icons.heart</v-icon
                    >
                </div>
                <div
                    class="button-svg-container"
                    v-else
                    @click.stop="$store.dispatch('like', item)"
                >
                    <v-icon class="pointer" small
                        >$vuetify.icons.heart-outline</v-icon
                    >
                </div>
            </div>
        </td>
        <td class="right">
            <div class="align-center justify-end">
                <div class="delete-button">
                    <div
                        class="delete-button"
                        :title="$t('Remove song from playlist')"
                        @click.stop="
                            detachSong(item.id, item.origin, item.title)
                        "
                        v-if="isPlaylistOwner && playlist_id"
                    >
                        <v-icon class="pointer" small
                            >$vuetify.icons.delete</v-icon
                        >
                    </div>
                </div>
                <div class="duration  ml-4">
                    {{ item.duration ? mmss(item.duration) : "" }}
                </div>
            </div>
        </td>
    </tr>
</template>

<script>
import purchaseBtn from "../../elements/other/ProductBtn.vue";
export default {
    components: {
        purchaseBtn
    },
    props: {
        item: {
            type: Object
        },
        rank: {
            type: String
        },
        isOnEditPage: {
            type: Boolean
        },
        isOnSectionEdit: {
            type: Boolean
        },
        isAlbum: {
            type: Boolean
        },
        showEmpty: {
            type: Boolean
        },
        isEndpoint: {
            type: Boolean
        },
        playlist_id: {
            type: Number
        },
        isPlaylistOwner: {
            type: Boolean
        }
    },
    methods: {
        play(item) {
            const params = {};
            params[item.type] = item;
            params.reset = true;
            this.$store.dispatch(
                "play" + item.type[0].toUpperCase() + item.type.substr(1),
                params
            );
        },
        detachSong(song_id, origin, song_title) {
            this.$confirm({
                message: `${this.$t(
                    "Are you sure you wanna remove this song from this playlist?"
                )}`,
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
                            .dispatch("detachFromPlaylist", {
                                song_id,
                                song_origin: origin,
                                playlist_id: this.playlist_id
                            })
                            .then(res => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Song Removed"),
                                    text:
                                        song_title +
                                        " " +
                                        this.$t(
                                            "has been removed from playlist successfully."
                                        )
                                });
                                this.$emit("deleted", song_id);
                            });
                    }
                }
            });
        }
    }
};
</script>

<style lang="scss" scoped>
.data-table-tr {
    overflow-x: hidden;
    .cover-and-title {
        .item-title {
            max-width: 300px;
            @media (max-width: 1200px) {
                max-width: 200px;
            }
            @media (max-width: 510px) {
                max-width: 180px;
                font-size: 0.9em;
            }
        }
    }
    .artists {
        @media (max-width: 1100px) {
            max-width: 120px;
            font-size: 0.9em;
        }
    }
    @media (max-width: 1000px) {
        .artists {
            display: none;
        }
    }
    .duration {
        font-size: 0.9em;
        @media (max-width: 600px) {
            display: none;
        }
    }
    .menu-button {
        max-width: 30px;
    }
}

.isAlbum {
    .cover-and-title {
        .item-title {
            max-width: 200px;
            @media (max-width: 1200px) {
                max-width: 200px;
            }
            @media (max-width: 510px) {
                max-width: 180px;
                font-size: 0.9em;
            }
        }
    }
    .artists {
        max-width: 200px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        @media (max-width: 1350px) {
            max-width: 150px;
        }
        @media (max-width: 1200px) {
            display: none;
        }
    }
    .duration {
        @media (max-width: 1300px) {
            display: none;
        }
    }
}
.right {
    text-align: right;
}

.currently-playing {
    background-color: red;
}

.cover-and-title {
    display: flex;
    align-items: center;
}
</style>
