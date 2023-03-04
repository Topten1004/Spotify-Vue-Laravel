
export default {
    methods: {
        playPause() {
            if (this.currentAudio.source_format === "yt_video") {
                if (!this.$store.getters.getSettings.allowVideos) {
                    this.$notify({
                        group: "foo",
                        type: "warning",
                        title: this.$t("Oops!"),
                        text: this.$t("Sorry, you can not play YouTube videos.")
                    });
                }
                if (
                    this.videoStatus === 2 ||
                    this.videoPlayer.getCurrentTime() === 0 ||
                    this.videoStatus === 0
                ) {
                    this.videoPlayer.playVideo();
                    this.$nextTick(() => {
                        document.title =
                            "\u25b6 " +
                            this.currentAudio.title +
                            (this.getMainArtist(this.currentAudio) ? ' - ' + this.getMainArtist(this.currentAudio) : "");
                    });
                    this.$store.commit("setCurrentAudio",{ id: this.currentAudio.id, type: this.currentAudio.type });
                    this.currentAudio.isPlaying = true;
                    if (this.videoPlayer.getCurrentTime() === 0) {
                        this.$store.dispatch("registerPlay", {
                            id: this.currentAudio.id,
                            type: this.isPodcastEpisode ? "episode" : "song",
                            label: this.currentAudio.title,
                            artist_id: this.currentAudio.artist ? this.currentAudio.artist.id : '',
                            duration: this.currentAudio.duration,
                            origin: 'youtube'
                        });
                    }
                } else if (this.videoPlayer.getCurrentTime() > 0) {
                    this.currentAudio.isPlaying = false;
                    this.$store.commit("setCurrentAudio", null);
                    this.$nextTick(() => {
                        document.title =
                            this.currentAudio.title +
                            (this.getMainArtist(this.currentAudio) ? ' - ' + this.getMainArtist(this.currentAudio) : "");
                    });
                    this.videoPlayer.pauseVideo();
                }
            } else if (this.currentAudio.source_format === "file") {
                if (
                    this.audioPlayer.paused ||
                    this.audioPlayer.currentTime === 0
                ) {
                    const int = setInterval(() => {
                        if (this.canPlay) {
                            this.audioPlayer.play();
                            this.$nextTick(() => {
                                document.title =
                                    "\u25b6 " +
                                    this.currentAudio.title +
                                    (this.getMainArtist(this.currentAudio) ? ' - ' + this.getMainArtist(this.currentAudio) : "");
                            });
                            this.$store.commit(
                                "setCurrentAudio",
                                { id: this.currentAudio.id, type: this.currentAudio.type }
                            );
                            this.currentAudio.isPlaying = true;
                            clearInterval(int);
                        }
                    }, 200);
                    if (this.audioPlayer.currentTime === 0) {
                        this.$store.dispatch("registerPlay", {
                            id: this.currentAudio.id,
                            type: this.isPodcastEpisode ? "episode" : "song",
                            label: this.currentAudio.title,
                            artist_id: this.currentAudio.artist ? this.currentAudio.artist.id : '',
                            duration: this.currentAudio.duration,
                            origin: this.currentAudio.origin
                        });
                    }
                } else if (this.audioPlayer.currentTime > 0) {
                    this.currentAudio.isPlaying = false;
                    this.$store.commit("setCurrentAudio", null);
                    this.$nextTick(() => {
                        document.title =
                            this.currentAudio.title +
                            (this.getMainArtist(this.currentAudio) ? ' - ' + this.getMainArtist(this.currentAudio) : "");
                    });
                    this.audioPlayer.pause();
                }
            } else {
                // live stream
                if (this.streamPlayer) {
                    if (this.streamPlayer.state == "stopped") {
                        this.streamPlayer.play();
                        this.$nextTick(() => {
                            document.title =
                                "\u25b6 " + this.currentAudio.title;
                        });
                        // current audio works of songs only
                        this.$store.commit("setCurrentAudio", { id: this.currentAudio.id, type: this.currentAudio.type });
                        this.currentAudio.isPlaying = true;
                        if (this.audioPlayer.currentTime === 0) {
                            this.$store.dispatch("registerPlay", {
                                id: this.currentAudio.id,
                                type: "radio-sation",
                                label: this.currentAudio.title
                            });
                        }
                    } else {
                        this.currentAudio.isPlaying = false;
                        this.$store.commit("setCurrentAudio", null);
                        this.$nextTick(() => {
                            document.title = this.currentAudio.title;
                        });
                        this.streamPlayer.stop();
                    }
                } else {
                    if (
                        this.audioPlayer.paused ||
                        this.audioPlayer.currentTime === 0
                    ) {
                        this.audioPlayer.play();
                        this.currentAudio.isPlaying = true;
                        this.$nextTick(() => {
                            document.title =
                                "\u25b6 " + this.currentAudio.title;
                        });
                        this.$store.commit("setCurrentAudio", { id: this.currentAudio.id, type: this.currentAudio.type });
                        if (this.audioPlayer.currentTime === 0) {
                            this.$store.dispatch("registerPlay", {
                                id: this.currentAudio.id,
                                type: "radio-sation",
                                label: this.currentAudio.title
                            });
                        }
                    } else if (this.audioPlayer.currentTime > 0) {
                        this.currentAudio.isPlaying = false;
                        // emitAnalyticsTime({
                        //     name: 'stream_time',
                        //     value: this.audioPlayer.currentTime,
                        //     event_category: 'Radio Station - ' + this.currentAudio.title
                        // })
                        this.$store.commit("setCurrentAudio", null);
                        this.$nextTick(() => {
                            document.title = this.currentAudio.title;
                        });
                        this.audioPlayer.pause();
                    }
                }
            }
        },
        rewindAudio(seconds) {
            this.audioPlayer.currentTime =
                this.audioPlayer.currentTime + seconds;
            if (this.currentAudio.source_format === "yt_video") {
                this.videoPlayer.seekTo(this.audioPlayer.currentTime);
            }
        },
        loopAudio() {
            this.buttons.shuffle = false;
            this.buttons.loop = !this.buttons.loop;
        },
        shuffleAudio() {
            this.buttons.loop = false;
            this.buttons.shuffle = !this.buttons.shuffle;
        },
        goNext() {
            var index;
            if (this.currentAudio.index == this.playlist.length - 1) {
                index = 0;
            } else {
                index = this.currentAudio.index + 1;
            }
            this.updateCurrentAudio(index, true);
        },
        goPrevious() {
            var index;
            if (this.currentAudio.index == 0) {
                index = this.playlist.length - 1;
            } else {
                index = this.currentAudio.index - 1;
            }
            this.updateCurrentAudio(index, true);
        },
        switchScreenToSmall() {
            this.smallScreen = true;
        },
        switchScreenToLarge() {
            this.smallScreen = false;
        },
        like(song) {
            if (this.isLiked) {
                this.$store.dispatch("dislike",  song );
            } else {
                this.$store.dispatch("like",  song );
            }
        },
        async downloadAudio() {
            this.downloadLoading = true;
            try {
                await this.$store.dispatch("downloadAudio", {
                    id: this.currentAudio.id,
                    type: this.isPodcastEpisode ? "episode" : "song",
                    file_name: this.currentAudio.file_name
                });
            } catch (e) {}
            this.downloadLoading = false;
            if( this.$store.getters.getSettings.ga4 && this.$store.getters.getSettings.analytics_download_event ) {
                emitAnalyticsEvent({
                    action: 'file_download',
                    category: this.isPodcastEpisode ? "episode" : "song",
                    label: this.currentAudio.file_name
                })
            }
        }
    }
 }