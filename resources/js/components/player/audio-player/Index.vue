<template>
    <div id="audio-player-container">
        <TV
            class="video-tv"
            :class="{
                'small-screen': smallScreen || fullScreenPlayer,
                'phone-layout': fullScreenPlayer
            }"
            v-if="$store.getters.getSettings.allowVideos"
            v-show="currentAudio.source_format === 'yt_video' && !$store.getters.getSettings.disableVideo && (fullScreenPlayer? (fullScreenPlayer && !playlistShownOnMobile) : true)"
            @small-screen="switchScreenToSmall"
            @large-screen="switchScreenToLarge"
        >
            <div
                id="youtube_video_container"
                :class="{ smallScreenVideo: smallScreen }"
            ></div>
        </TV>
        <phone-player-layout
            :currentAudio="currentAudio"
            :playlist="playlist"
            :getUp="getUp"
            :isLoading="isLoading"
            :playbackRate="playbackRate"
            :isLiked="isLiked"
            :isArtistFollowed="isArtistFollowed"
            :isPodcastEpisode="isPodcastEpisode"
            :isCurrentAudioAStream="isCurrentAudioAStream"
            :isCurrentAudioAFileAudio="isCurrentAudioAFileAudio"
            :downloadLoading="downloadLoading"
            :volumeButton="volumeButton"
            :duration="getDurationInHHMMSS(currentAudio.duration)"
            :buttons="buttons"
            :volume="volume"
            :fullScreenPlayerProp="fullScreenPlayer"
            :isPurchasable="isPurchasable"
            :isOwned="isOwned"
            :style="{
                transform:
                    'translateY(' + (fullScreenPlayer ? '0%' : '100%') + ')'
            }"
            @updateCurrentAudio="updateCurrentAudio($event[0], $event[1])"
            @playPause="playPause"
            @adjustPlayspeed="adjustPlayspeed"
            @rewindAudio="rewindAudio($event)"
            @fullScreenPlayer="fullScreenPlayer = false"
            @goNext="goNext"
            @goPrevious="goPrevious"
            @like="like"
            @showPlaylist="playlistShownOnMobile = $event"
            @mute="muteAudio"
            @downloadAudio="downloadAudio"
            @showPurchaseDialog="purchase"
            @addSongToPlaylist="addSongToPlaylist"
            @updateProgressOnPhone="updateProgressOnPhone"
            @loopAudio="loopAudio"
            @shuffleAudio="shuffleAudio"
        />
        <footer-player-layout
            :getUp="getUp"
            :playlist="playlist"
            :isLoading="isLoading"
            :playbackRate="playbackRate"
            :isLiked="isLiked"
            :isArtistFollowed="isArtistFollowed"
            :isPodcastEpisode="isPodcastEpisode"
            :isCurrentAudioAStream="isCurrentAudioAStream"
            :isCurrentAudioAFileAudio="isCurrentAudioAFileAudio"
            :downloadLoading="downloadLoading"
            :volumeButton="volumeButton"
            :duration="getDurationInHHMMSS(currentAudio.duration)"
            :buttons="buttons"
            :volume="volume"
            :fullScreenPlayerProp="fullScreenPlayer"
            :currentAudio="currentAudio"
            @volume="volume = $event"
            :isPurchasable="isPurchasable"
            :isOwned="isOwned"
            @mute="muteAudio"
            @fullScreenPlayer="fullScreenPlayer = true"
            @updateCurrentAudio="updateCurrentAudio($event[0], $event[1])"
            @downloadAudio="downloadAudio"
            @showPurchaseDialog="purchase"
            @playPause="playPause"
            @adjustPlayspeed="adjustPlayspeed"
            @rewindAudio="rewindAudio($event)"
            @goNext="goNext"
            @goPrevious="goPrevious"
            @like="like"
            @addSongToPlaylist="addSongToPlaylist"
            @updateProgress="updateProgress"
            @loopAudio="loopAudio"
            @shuffleAudio="shuffleAudio"
        />
    </div>
</template>

<script>
import TV from "../../elements/single-items/TV";
import FooterPlayerLayout from "./FooterPlayerLayout";
import PhonePlayerLayout from "./PhonePlayerLayout";
// mixins
import eventHandlers from '../../../mixins/player/eventHandlers';
import playerActionsMixin from '../../../mixins/player/playerActions';
import progress from '../../../mixins/player/progress';
import stream from '../../../mixins/player/stream';
import update from '../../../mixins/player/update';
import volume from '../../../mixins/player/volume';
import youTube from '../../../mixins/player/youTube';
import Billing from '../../../mixins/billing/billing';
import helpers from '../../../helpers'
export default {
    props: ["playlist"],
    mixins: [playerActionsMixin, eventHandlers, stream, update, progress, volume, youTube, Billing],
    components: {
        TV,
        FooterPlayerLayout,
        PhonePlayerLayout
    },
    async created() {
        this.initPlayer();
        this.startPlayer();
    },
    data() {
        return {
            audioPlayer: null,
            streamPlayer: null,
            streamAudioElement: null,
            streamStats: null,
            downloadLoading: false,
            queue: [],
            audioStatus: null,
            playlistShownOnMobile: false,
            retryCount: 0,
            getUp: false,
            fullScreenPlayer: false,
            isLoading: true,
            canPlay: false,
            smallScreen: true,
            playbackRateDirection: "up",
            videoPlayer: null,
            videoStatus: null,
            volumeButton: "volume-medium",
            timeUpdater: null,
            metadataFetcher: null,
            playbackRate: 1,
            currentAudio: {
                source: null,
                source_format: null,
                index: 0,
                title: "",
                album: "",
                artist: "",
                cover: "",
                progress: 0,
                duration: "-:--",
                currentTime: "0:00",
                videoCurrentTime: 0,
                isPlaying: false
            },
            buttons: {
                shuffle: false,
                loop: false
            }

            
        };
    },
    computed: {
        isPodcastEpisode() {
            return Boolean(this.currentAudio.type === "episode");
        },
        isCurrentAudioAStream() {
            return this.currentAudio.streamEndpoint;
        },
        isCurrentAudioAFileAudio() {
            return this.currentAudio.source_format === 'file' || this.currentAudio.source_format === 'audio_url' 
        },
        isCurrentAudioAYoutubeVideo() {
            return this.currentAudio.source_format === 'yt_video'
        },
        isLiked() {
            return (this.$store.getters.getLikedSongs || []).some(
                x => x.id == this.currentAudio.id
            );
        },
        isArtistFollowed() {
            return (this.$store.getters.getFollowedArtists || []).some(
                artist => artist.id === this.currentAudio.artist_id
            );
        },
        isPurchasable() {
            return  this.currentAudio.product &&  !this.isPurchased(this.currentAudio);
        },
        isOwned() {
            return  (this.currentAudio.artist && (this.$store.getters.getUser && this.$store.getters.getUser.artist)
            && this.currentAudio.artist.id === this.$store.getters.getUser.artist.id) ||  (this.currentAudio.product && this.isPurchased(this.currentAudio));
        }
    },
    methods: {
        initPlayer() {
            this.audioPlayer = new Audio();
            this.volume = this.$store.getters.getSettings.playerVolume || 50;
            this.audioPlayer.preload = "metadata";
            // attaching the events to their handlers
            this.initEventHandlers()
            setTimeout(() => {
                this.getUp = true;
            }, 100);
        },
        getRandomAudio(length, index) {
            let randomAudio = Math.floor(Math.random() * length);
            if (randomAudio == index) return this.getRandomAudio(length, index);
            else return randomAudio;
        },
        addSongToPlaylist(song_id) {
            if (this.$store.getters.getUser) {
                this.$store.commit("setAddSongToPlaylist", song_id);
            } else {
                this.loginOrCancel();
            }
        },
        startPlayer() {
            if (this.playlist[0] !== this.queue[0]) {
                this.updateCurrentAudio(0, true);
            }
            this.queue = this.playlist;
        },
        async purchase() {
            if (!this.$store.getters.isLogged) {
                // if the user is not logged
                await helpers.loginOrCancel();
            }
            this.$store.commit('purchase/setSellingAsset', this.currentAudio)
        }
    },
    watch: {
        playbackRate(val) {
            this.audioPlayer.playbackRate = val;
        },
        playlist() {
            setTimeout(() => {
                this.startPlayer();
            }, 100);
        },
        audioStatus(val) {
            if( this.$store.getters.getSettings.crossfade ) {
                if( val === "starting" ) {
                this.easeVolumeRaise()
                } else if ( val === "ending" ) {
                    this.easeVolumeDrop()
                }
            }
        }
    },
    beforeDestroy() {
        this.resetAudioElement();
        this.killStreamIfExists();
    }
};
</script>
