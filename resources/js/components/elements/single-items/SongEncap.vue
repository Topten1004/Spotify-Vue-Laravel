<template>
    <div class="content-encap-wrapper">
        <div class="control-layer">
            <button class="play-button" @click="$store.dispatch('playSong', { song, reset: true });">
                <img src="/svg/play-round.svg" alt="next" class="svg-image" />
            </button>
        </div>
        <div class="infos-layer">
            <div class="overlay"></div>
            <div class="info-badge">
                <span>{{$t('Latest Song')}}</span>
            </div>
            <div class="song-title max-2-lines">
                {{ song.title }}
            </div>
        </div>
        <div class="cover-layer">
            <v-img :src="song.cover" class="img" aspect-ratio="1">
                <template v-slot:placeholder>
                    <div class="song-expo-skeleton fill-height">
                        <content-placeholders :rounded="true">
                            <content-placeholders-img />
                        </content-placeholders>
                    </div>
                </template>
            </v-img>
        </div>
    </div>
</template>
<script>
import addToPlaylist from "../../dialogs/Playlists";
export default {
    props: ["song"],
    components: {
        addToPlaylist
    },
    data() {
        return {
            showMenu: false,
            addToPlaylist: false
        };
    },
    computed: {
        isLiked() {
            return false;
        },
        isArtistFollowed() {
            if (this.$store.getters.getFollowedArtists) {
                var song = this.song;
                return this.$store.getters.getFollowedArtists.some(
                    artist => artist.id === song.artist_id
                );
            } else {
                return false;
            }
        }
    },
    methods: {
        like(song_id) {
            if (this.isLiked) {
                this.$store.dispatch("dislike", song_id);
            } else {
                this.$store.dispatch("like", song_id);
            }
        }
    }
};
</script>
<style lang="scss" scoped>
.content-encap-wrapper {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    .control-layer {
        background-color: rgba(0, 0, 0, 0.4);
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        z-index: 3;
        opacity: 0;
        transition: opacity 0.5s;
        border-radius: 0px;
        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: transparent;
            border: none;
            .svg-image {
                width: 3em;
            }
        }
    }
    .infos-layer {
        position: absolute;
        top: 0;
        left: 0;
        padding: 1rem;
        color: white;
        bottom: 0;
        right: 0;
        height: 50%;
        margin-top: auto;
        z-index: 2;
        .song-title {
            margin-top: 0.3em;
        }
        .overlay {
            background: -webkit-linear-gradient(
                top,
                rgba(0, 0, 0, 0) 0%,
                rgb(88, 79, 66) 100%
            );
            position: absolute;
            bottom: 0em;
            left: 0em;
            right: 0em;
            height: 100%;
            z-index: -1;
            border-radius: 0.3em;
        }
        .info-badge {
            background: #fff;
            color: #000;
            display: inline-block;
            text-transform: uppercase;
            border-radius: 0.2rem;
            font-weight: 600;
            font-size: 0.67em;
            padding: 0 0.3em;
        }
        .artist {
            font-size: 0.8em;
            font-weight: lighter;
            font-weight: lighter;
        }
    }
    .cover-layer {
        overflow: hidden;
        z-index: 1;
        border-radius: 0px;
        .img {
            background-size: cover;
            transition: transform 0.5s;
        }
    }
    &:hover {
        .control-layer {
            opacity: 1;
        }
        .cover-layer {
            .img {
                transform: scale(1.08);
            }
        }
    }
}
</style>
