export default {
    methods: {
        updateProgress(event) {
            let progressBar = document.getElementById("progress-bar");
            if (this.isCurrentAudioAFileAudio) {
                var currentAudioDurationInSeconds = this.audioPlayer.duration;
                var currentTimeInSeconds =
                    (event.offsetX * currentAudioDurationInSeconds) /
                    progressBar.offsetWidth;
                this.audioPlayer.currentTime = currentTimeInSeconds;
            } else {
                var currentAudioDurationInSeconds = this.videoPlayer.getDuration();
                var currentTimeInSeconds =
                    (event.offsetX * currentAudioDurationInSeconds) /
                    progressBar.offsetWidth;
                this.videoPlayer.seekTo(currentTimeInSeconds);
            }
        },
        getDurationInHHMMSS(duration) {
            if( isNaN(duration)  ) {
                return "-:--"
            }
            function addZeroBeforeSingleChar(i) {
                if (i < 10) {
                  i = "0" + i;
                }
                return i;
            }
            var h = Math.floor(duration / 3600);
            duration %= 3600;
            var m = Math.floor(duration / 60);
            var s = Math.floor(duration % 60);
            // add a zero in front of numbers<10
            h = addZeroBeforeSingleChar(h);
            m = addZeroBeforeSingleChar(m);
            s = addZeroBeforeSingleChar(s);
            return (h !== "00" ? h  + ":": "") + m + ":" + s;
            // if( duration !== '-:--' ) {
            //     return this.moment(duration * 1000).format('mm:ss')
            // }
        },
        getTimeFormat(secs) {
            var minutes = Math.floor(secs / 60);
            var seconds = secs % 60;
            return (
                minutes +
                ":" +
                (Math.floor(seconds / 10) > 0 ? seconds : "0" + seconds)
            );
        },
        adjustPlayspeed() {
            this.playbackRateDirection == "up"
                ? (this.playbackRate += 0.25)
                : (this.playbackRate -= 0.25);
            if (this.playbackRate == 2) {
                this.playbackRateDirection = "down";
            } else if (this.playbackRate == 0.25) {
                this.playbackRateDirection = "up";
            }
            if (this.currentAudio.source_format === "yt_video") {
                this.videoPlayer.setPlaybackRate(this.playbackRate);
            }
        },
        updateProgressOnPhone(event) {
            let progressBar = document.getElementById(
                "progress-bar_phone_layout"
            );
            if (this.isCurrentAudioAFileAudio) {
                const seconds =
                    (event.offsetX * this.audioPlayer.duration) /
                    progressBar.offsetWidth;
                this.audioPlayer.currentTime = seconds;
            } else {
                const seconds =
                    (event.offsetX * this.videoPlayer.getDuration()) /
                    progressBar.offsetWidth;
                this.videoPlayer.seekTo(seconds);
            }
        },
        resetTime() {
            this.currentAudio.progress = 0;
            this.currentAudio.currentTime = this.currentAudio.currentTime;
        }
    }
}