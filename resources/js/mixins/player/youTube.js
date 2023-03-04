
export default {
    methods: {
        playYoutubeVideo(index, force) {
            if (this.$store.getters.getSettings.allowVideos) {
                if (
                    this.currentAudio.index !== 0 ||
                    force ||
                    this.buttons.shuffle
                ) {
                    this.setVideo(
                        this.currentAudio.source,
                        this.videoReadyCallback,
                        this.videoStatusChangeCallback
                    );
                }
            } else {
                // Sorry, can't play this song
                this.$notify({
                    group: "foo",
                    type: "warning",
                    title: this.$t("Oops!"),
                    text: this.$t("Sorry, you can not play YouTube videos.")
                });
            }
        },
        setVideo(youtube_id, readyCallback, stateChangedCallback) {
            if (!this.videoPlayer) {
                this.$nextTick(() => {
                    this.videoPlayer = new YT.Player(
                        "youtube_video_container",
                        {
                            width: "500",
                            height: "305",
                            videoId: youtube_id,
                            events: {
                                onReady: readyCallback,
                                onStateChange: stateChangedCallback
                            }
                        }
                    );
                });
            } else {
                this.videoPlayer.loadVideoById(youtube_id, 0);
            }
            this.$store.commit("setCurrentAudio", this.currentAudio);
        },
        videoStatusChangeCallback(event) {
            this.videoStatus = event.data;
        },
        videoReadyCallback(event) {
            this.currentAudio.duration = event.target.getDuration();
            // update volume
            this.videoPlayer.setVolume(this.volume);
            this.isLoading = false;
            this.playPause();
        }
    },
    watch: {
        videoStatus(val) {
            switch (val) {
                case -1:
                    if (this.currentAudio.source_format === "yt_video") {
                        this.isLoading = false;
                    }
                    break;
                case 0:
                    this.onended();
                    break;
                case 1:
                    this.currentAudio.duration = this.videoPlayer.getDuration()
                    this.isLoading = false;
                    this.currentAudio.isPlaying = true;
                    var self = this;
                    function updateTime() {
                        var oldTime = self.currentAudio.videoCurrentTime;
                        if (
                            self.videoPlayer &&
                            self.videoPlayer.getCurrentTime
                        ) {
                            self.currentAudio.videoCurrentTime = self.videoPlayer.getCurrentTime();
                        }
                        if (self.currentAudio.videoCurrentTime !== oldTime) {
                            self.updateTime(
                                self.currentAudio.videoCurrentTime,
                                self.videoPlayer.getDuration()
                            );
                        }
                    }
                    this.timeUpdater = setInterval(updateTime, 100);
                    break;
                case 2:
                    clearInterval(this.timeUpdater);
                    this.currentAudio.isPlaying = false;
                    break;
                case 3:
                    this.isLoading = true;
                    break;
            }
        }
    }
}