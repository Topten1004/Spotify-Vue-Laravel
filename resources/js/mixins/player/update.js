import axios from "axios";

export default {
    methods: {
        async updateCurrentAudio(index, force) {
            this.isLoading = true
            this.reset();
            await this.prepare(index)

            if( this.$store.getters.getSettings.autoPlay && this.$store.getters.getQueue.length < 2) {
                this.fetchSimilarities(index)
            }

            if (this.isCurrentAudioAYoutubeVideo) {
                this.playYoutubeVideo(index, force);
            } else if (this.isCurrentAudioAFileAudio) {
                this.playAudioFile(index, force);
            } else if(this.isCurrentAudioAStream) {
                this.playStream(index, force);
            }
        },
        reset() {
            clearInterval(this.timeUpdater);
            clearInterval(this.metadataFetcher);
            this.initCurrentAudioObj();
            this.resetBasicValues()
            this.resetAudioElement();
            this.resetTime();

            this.killStreamIfExists();
            // this.initCurrentAudioObj();

            // stop the video if it is mounted
            if (this.videoPlayer && this.videoPlayer.stopVideo) {
                this.videoPlayer.stopVideo();
            }
        },
        playAudioFile(index, force) {
            this.updateAudioElement(this.currentAudio.source)
            if (
                this.currentAudio.index !== 0 ||
                force ||
                this.buttons.shuffle
            ) {
                this.playPause();
            }
        },
        // async afterPrepareForNonStream(index) {
        //     var audio = this.playlist[index];
        //     if( !audio.source ) {
        //         await this.getYoutubeVideoIfExists(audio.title);
        //         return;
        //     }
        //     return
        // },
        async prepare(index) {
            var audio = this.playlist[index];
            this.currentAudio.index = index
        
            // updating the currentAudio with the new audio values
            Object.keys(audio).forEach(key => {
                this.$set(this.currentAudio, key, audio[key])
            });
            
            if (audio.type === "podcast" && audio.origin === "listenNotes") {
                let res = await axios.get("/api/podcast/" + audio.id);
                audio = res.data;
            } else
            if( !this.isCurrentAudioAStream && !this.currentAudio.source ) {
                const title = this.currentAudio.title;
                const artist = this.currentAudio.artists.length  ? this.currentAudio.artists[0].name ? this.currentAudio.artists[0].name : this.currentAudio.artists[0].displayname : '';
                this.currentAudio.source = await this.getYoutubeVideoIfExists(title, artist);
            } else if( this.isCurrentAudioAStream ) {
                this.currentAudio.source = this.currentAudio.streamEndpoint
            }
        },
        initCurrentAudioObj() {
            this.currentAudio = {
                src: null,
                title: "",
                album: "",
                artist: "",
                progress: 0,
                duration: "-:--",
                currentTime: "0:00",
                streamEndpoint: null,
                videoCurrentTime: 0,
                isPlaying: false
            };
        },
        updateAudioElement(source) {
            this.audioPlayer.src = source;
            this.audioPlayer.load();
        },
        resetAudioElement() {
            this.audioPlayer.pause();
        },
        resetBasicValues() {
            // resetting basic keys
            this.currentAudio.isPlaying = false
            this.currentAudio.duration = "-:--"
            this.currentAudio.source_format = null;
            this.currentAudio.file_name = null;
            this.currentAudio.source = null;
            this.currentAudio.progress = 0;
            this.currentAudio.currentTime = "0:00";
        },
        updateTime(currentTime, duration) {
            // updating the bar progress
            if( !this.isCurrentAudioAStream ) {
                if (this.audioPlayer.duration === Infinity && !this.isCurrentAudioAYoutubeVideo) {
                    this.currentAudio.duration = "live";
                    this.currentAudio.progress = 100;
                } else  if (currentTime < duration || currentTime == 0) {
                    this.currentAudio.progress = (currentTime / duration) * 100;
                } else {
                    this.currentAudio.progress = 0;
                }
                
                if( !isNaN(currentTime) && !isNaN(duration) && duration - currentTime <= 5) {
                    this.audioStatus = "ending"
                } else 
                if( !isNaN(currentTime) && !isNaN(duration) && currentTime >= 0 && currentTime <=5) {
                    this.audioStatus = "starting"
                } else {
                    this.audioStatus = null
                }
            }
            // updating the current time "xx:yy"
            this.$set(this.currentAudio, 'currentTime', this.getTimeFormat(
                currentTime.toFixed(0)
            ))
        },
        easeVolumeRaise() {
            const initVolume = this.volume;
            const dropDownVolume = setInterval(() => {
                this.volume = this.volume + (this.volume / 20)
                if(this.volume >= initVolume ) {
                    clearInterval(dropDownVolume)
                }
            }, 250);
        },
        easeVolumeDrop() {
            const initVolume = this.volume;
            this.volume = 0;
            const dropDownVolume = setInterval(() => {
                this.volume = this.volume - (this.volume / 20)
                if(this.volume <= initVolume ) {
                    clearInterval(dropDownVolume)
                }
            }, 250);
        },
        async getYoutubeVideoIfExists(title, artist)
        {
            try {
                let res = await axios.get('/api/track-source?title=' + title + '&artist=' + artist)
                return res.data
            } catch (error) {
                return null
            }
        },

        async fetchSimilarities(index) {
            var audio = this.playlist[index];

            axios.get('/api/next-songs?id='  + audio.id + "&origin=" + audio.origin )
            .then((res) => {
                if( res.data.length ) {
                    this.$store.dispatch('updateQueue', { content: res.data, reset: false });
                }
            })
        }
    }
};
