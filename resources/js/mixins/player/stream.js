import IcecastMetadataPlayer from "icecast-metadata-player";
import IcecastMetadataStats from "icecast-metadata-stats";

export default {
    methods: {
        afterPrepareForStream(index) {
            var audio = this.playlist[index];
            this.currentAudio.title = audio.name;
            this.resetAudioElement()
        },
        playStream() {
            this.afterPrepareForStream(this.currentAudio.index)
            this.initStreamPlayer(this.currentAudio);
        },
        killStreamIfExists() {
            this.streamAudioElement = null;
            if (this.streamPlayer) {
                this.streamPlayer.stop();
                this.streamPlayer.detachAudioElement();
                this.streamStats.stop();
                this.streamPlayer = null;
                this.streamStats = null;
            }
        },
        failedToPlay() {
            this.$notify({
                group: "foo",
                type: "warning",
                title: this.$t("Error"),
                text: this.$t("Failed to load the audio source")
            });
            if (this.queue.length > 1) {
                this.goNext();
            } else {
                this.$store.state.queue = [];
                // kill the stream
                this.killStreamIfExists();
            }
        },
        updateMetaData(source, stats) {
            if( source === 'server') {
                this.currentAudio.title = stats.title;
                this.currentAudio.artist = stats.artist;
            } else {
                if (stats) {
                    if (
                        stats.icestats &&
                        stats.icestats.source &&
                        stats.icestats.source.title
                    ) {
                        this.currentAudio.title =
                            stats.icestats.source.title || this.currentAudio.name;
                    } else if (stats.icy) {
                        this.currentAudio.title =
                            stats.icy.StreamTitle || this.currentAudio.name;
                    } else if (stats.ogg) {
                        if (stats.ogg.TITLE) {
                            this.currentAudio.title =
                                stats.ogg.TITLE || this.currentAudio.name;
                        }
                        if (stats.ogg.ARTIST) {
                            this.currentAudio.artist = stats.ogg.ARTIST;
                        }
                    }
                    document.title = "\u25b6 " + this.currentAudio.title;
                }
            }
        },
        getMetaDataUsingProxy(stationID) {
            axios
                .get(
                    "/api/get-stream-metadata-pr/" + stationID
                )
                .then(res => {
                    this.updateMetaData('proxy', res.data);
                })
                .catch(() => {
                    return {};
                });
        },
        getMetaDataFromServer(stationID) {
            axios
                .get(
                    "/api/get-stream-metadata-sr/" + stationID ,
                    {

                    }
                )
                .then(res => {
                    this.updateMetaData('server', res.data);
                })
                .catch(() => {
                    return {};
                });
        },
        startFetchMetadata(station) {
            if( station.statsSource === 'endpoint' ) {
                this.metadataFetcher = setInterval(function x() {
                    this.getMetaDataUsingProxy(station.id)
                }.bind(this), station.interval || 5000);
            } else {
                this.metadataFetcher = setInterval(function x() {
                    this.getMetaDataFromServer(station.id)
                }.bind(this), station.interval || 5000);
            }
        },
        initStreamPlayer(station) {
            if (station.statsSource === 'endpoint' &&  !station.proxy) {
                this.canPlay = true;
                this.isLoading = true;
                var timeInterval = 0;
                this.streamStats = new IcecastMetadataStats(station.streamEndpoint, {
                    sources: ["icestats", "ogg", "icy"],
                    interval: 10,
                    onStats: stats => {
                        this.updateMetaData('client', stats);
                    }
                });
                // creating new audio Element just for the volume since attaching the
                // audioPlayer causes bugs
                var volume = this.volume;
                this.streamAudioElement = new Audio();
                this.volume = volume;
                this.streamPlayer = new IcecastMetadataPlayer(
                    station.statsEndpoint,
                    {
                        metadataTypes: station.metadata_types,
                        audioElement: this.streamAudioElement,
                        onRetry: () => {
                            this.retryCount++;
                            if (this.retryCount > 10) {
                                this.failedToPlay();
                            }
                        },
                        onPlay: () => {
                            this.interval = setInterval(() => {
                                timeInterval++;
                                this.currentAudio.currentTime = this.getTimeFormat(
                                    timeInterval.toFixed(0)
                                );
                            }, 1000);
                            this.currentAudio.isStopped = false;
                            this.currentAudio.isPlaying = true;
                        },
                        onStreamStart: () => {
                            this.isLoading = false;
                        },
                        onLoading: () => {
                            this.isLoading = true;
                        },
                        onStop: () => {
                            clearInterval(this.interval);
                            this.currentAudio.isStopped = true;
                            if (this.audioPlayer.paused) {
                                this.currentAudio.isPlaying = false;
                            }
                        }
                    }
                );
                this.streamStats.start();
                this.streamPlayer.play();
            } else {
                this.startFetchMetadata(station);
                this.updateAudioElement(this.currentAudio.streamEndpoint)
                this.playPause();
            }
            this.$store.commit("setCurrentAudio", { id: this.currentAudio.id, type: this.currentAudio.type });
        }
    }
}