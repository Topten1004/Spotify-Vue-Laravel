export default {
    methods: {
        initEventHandlers() {
            this.audioPlayer.onloadstart = this.onloadstart;
            this.audioPlayer.onerror = this.onerror;
            this.audioPlayer.onloadedmetadata = this.onloadmetadata;
            this.audioPlayer.oncanplay = this.oncanplay;
            this.audioPlayer.ontimeupdate = this.ontimeupdate;
            this.audioPlayer.onended = this.onended;
            this.audioPlayer.onwaiting = this.onwaiting;
            this.audioPlayer.oncanplaythrough = this.oncanplay;
        },
        onloadstart() {
            this.isLoading = true;
        },
        onerror() {
            if (!this.isRadioStation) {
                this.failedToPlay();
            }
        },
        onwaiting() {
            this.isLoading = true;
        },
        onloadmetadata() {
            // this.currentAudio.currentTime = "0:00";
            this.currentAudio.duration = this.audioPlayer.duration
        },
        oncanplay() {
            this.canPlay = true;
            this.isLoading = false;
        },
        ontimeupdate() {
            // this.currentAudio.isPlaying = true
            this.updateTime(
                this.audioPlayer.currentTime,
                this.audioPlayer.duration
            );
        },
        onended() {
            var index;
            this.canPlay = false;
            this.$store.commit("setCurrentAudio", null);
            this.currentAudio.isPlaying = false;
            if (this.$store.getters.getUser && this.$store.getters.getUser.id) {
                this.$store.dispatch("endPlay");
            }
            if (!this.buttons.loop) {
                if (this.buttons.shuffle && this.playlist.length > 1) {
                    index = this.getRandomAudio(
                        this.playlist.length,
                        this.currentAudio.index
                    );
                    this.updateCurrentAudio(index);
                } else {
                    if (this.playlist.length !== this.currentAudio.index + 1) {
                        index = this.currentAudio.index + 1;
                    } else {
                        index = 0;
                    }
                    this.updateCurrentAudio(index);
                }
            } else {
                if (this.isCurrentAudioAFileAudio) {
                    index = this.currentAudio.index;
                    this.updateCurrentAudio(index, true);
                } else if (this.currentAudio.source_format === "yt_video") {
                    this.videoPlayer.seekTo(0);
                    this.videoPlayer.playVideo();
                }
            }
        }
    }
}