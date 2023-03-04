export default {
    computed: {
        volume: {
            set(val) {
                if (this.streamAudioElement) {
                    this.streamAudioElement.volume = val / 100;
                } else if (
                    this.videoPlayer &&
                    this.isCurrentAudioAYoutubeVideo
                ) {
                    this.videoPlayer.setVolume(val);
                } else {
                    this.audioPlayer.volume = val / 100;
                }
            },
            get() {
                if (this.streamAudioElement) {
                    return Math.round(this.streamAudioElement.volume * 100);
                } else if (this.videoPlayer && this.videoPlayer.getVolume) {
                    return this.videoPlayer.getVolume() || 70;
                } else {
                    return Math.round(this.audioPlayer.volume * 100);
                }
            }
        }
    },
    methods: {
        muteAudio() {
            if (this.audioPlayer.muted) {
                this.audioPlayer.muted = false;
                this.volumeButton = "volume-high";
            } else {
                this.audioPlayer.muted = true;
                this.volumeButton = "volume-off";
            }
            if (this.streamAudioElement) {
                if (this.streamAudioElement.muted) {
                    this.streamAudioElement.muted = false;
                    this.volumeButton = "volume-high";
                } else {
                    this.volumeButton = "volume-off";
                    this.streamAudioElement.muted = true;
                }
            }
            if (this.isCurrentAudioAYoutubeVideo) {
                if (!this.videoPlayer.isMuted()) {
                    this.videoPlayer.mute();
                    this.volumeButton = "volume-off";
                } else {
                    this.volumeButton = "volume-high";
                    this.videoPlayer.unMute();
                }
            }
        }
    }
};
