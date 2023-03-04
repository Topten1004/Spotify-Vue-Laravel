<template>
    <div class="small-screen-player">
        <div class="epico_phone-layout-full">
            <div class="epico_player-container">
                <div class="epico_playlist-container">
                    <ul
                        class="epico_playlist-ul"
                        :style="{ maxHeight: showPlaylist ? '45vh' : '0px' }"
                    >
                        <li
                            class="epico_playlist-audio"
                            v-for="(audio, i) in playlist"
                            :key="i"
                            @click="$emit('updateCurrentAudio', [i, true])"
                            :class="{ 'active-Song': currentAudio.index == i }"
                        >
                            <div class="audio-cover">
                                <v-img :src="audio.cover" class="img" width="50" height="50">
                                    <template v-slot:placeholder>
                                        <div class="fill-height">
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img />
                                            </content-placeholders>
                                        </div>
                                    </template>
                                    <div
                                        class="dark-layer absolute fill justify-align-center"
                                        v-if="
                                            $store.getters.isCurrentlyPlaying(
                                                audio
                                            )
                                        "
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
                            <div class="audio-title text-center max-1-lines">
                                {{ audio.title }}
                            </div>
                            <div
                                class="audio-artist max-1-lines"
                                @click.prevent
                            >
                                <artists :artists="audio.artists"></artists>
                            </div>
                        </li>
                    </ul>
                    <div class="epico_playlist-text-container px-3">
                        <button
                            class="epico_chevron-down"
                            @click="fullScreenPlayer = false"
                        >
                            <v-icon large>$vuetify.icons.chevron-down</v-icon>
                        </button>
                        <div
                            class="download-button"
                            v-if="
                                hasPermission('Download songs') &&
                                    currentAudio.source_format === 'file' &&
                                    !$store.getters.getSettings.hideDownloadButton
                            "
                        >
                            <v-btn
                                x-small
                                :title="$t('Download')"
                                color="primary"
                                class="mr-2"
                                :disabled="downloadLoading"
                                fab
                                :loading="downloadLoading"
                                dense
                                @click="$emit('downloadAudio')"
                            >
                                <v-icon>$vuetify.icons.download-circle</v-icon>
                            </v-btn>
                        </div>
                        <div class="owned-icon" v-if="isOwned && currentAudio.isProduct">
                            <div class="premium" :title="$t('Premium')">
                                <v-icon color="#FFA500"
                                    >$vuetify.icons.crown</v-icon
                                >
                            </div>
                        </div>
                        <div class="purchase-button" v-else-if="isPurchasable">
                            <v-btn
                                x-small
                                :title="$t('Purchase')"
                                color="#FF8F00"
                                dark
                                class="mr-2"
                                fab
                                dense
                                @click="$emit('showPurchaseDialog')"
                            >
                                <v-icon>$vuetify.icons.cart</v-icon>
                            </v-btn>
                        </div>
                        <v-chip
                            v-if="playlist.length > 1"
                            @click="showPlaylist = !showPlaylist; $emit('showPlaylist', showPlaylist)"
                            class="ma-2"
                            outlined
                        >
                            <v-icon left color="textContMedium"
                                >$vuetify.icons.playlist-music</v-icon
                            >
                            {{ $t("Queue") }}
                            <v-icon
                                color="textContMedium"
                                :style="{
                                    transform: showPlaylist
                                        ? 'rotate(0deg)'
                                        : 'rotate(180deg)'
                                }"
                                >$vuetify.icons.chevron-up</v-icon
                            >
                        </v-chip>
                    </div>
                </div>
                <div class="epico_player-main-container">
                    <div
                        class="epico_image-section"
                        :style="{
                            opacity:
                                currentAudio.source_format === 'yt_video' && !$store.getters.getSettings.disableVideo
                                    ? 0
                                    : 1
                        }"
                    >
                        <v-img
                            :src="currentAudio.cover"
                            class="img"
                            aspect-ratio="1"
                        >
                            <template v-slot:placeholder>
                                <v-row
                                    class="fill-height ma-0"
                                    align="center"
                                    justify="center"
                                >
                                    <content-placeholders
                                        :rounded="true"
                                        style="width: 100%"
                                    >
                                        <content-placeholders-img />
                                    </content-placeholders>
                                </v-row>
                            </template>
                        </v-img>
                    </div>
                    <div class="epico_details-section">
                        <div
                            class="epico_audio-info"
                            v-if="!isCurrentAudioAStream"
                        >
                            <div
                                class="audio-album max-1-lines"
                                v-if="
                                    currentAudio.album &&
                                        currentAudio.album.title !==
                                            currentAudio.title
                                "
                            >
                                <router-link
                                    class="router-link"
                                    :to="{
                                        name: 'album',
                                        params: { id: currentAudio.album.id }
                                    }"
                                >
                                    {{ currentAudio.album.title }}
                                </router-link>
                            </div>
                            <div
                                class="audio-album max-1-lines"
                                v-else-if="currentAudio.podcast"
                            >
                                <router-link
                                    class="router-link"
                                    :title="currentAudio.podcast.title"
                                    :to="{
                                        name: 'podcast',
                                        params: { id: currentAudio.podcast.id }
                                    }"
                                >
                                    {{ currentAudio.podcast.title }}
                                </router-link>
                            </div>
                            <div
                                @click="$emit('fullScreenPlayer')"
                                class="epico_audio-title"
                            >
                                <router-link
                                    v-if="!isPodcastEpisode"
                                    :to="{
                                        name: 'song',
                                        params: { id: currentAudio.id }
                                    }"
                                    class="router-link"
                                    >{{ currentAudio.title }}</router-link
                                >
                                <template v-else>{{
                                    currentAudio.title
                                }}</template>
                            </div>
                            <div
                                class="audio-artists max-1-lines"
                                v-if="!isCurrentAudioAStream"
                            >
                                <artists
                                    :artists="currentAudio.artists"
                                ></artists>
                            </div>
                        </div>
                        <div class="epico_audio-info" v-else>
                            <div class="now-playing">
                                <template v-if="isLoading">
                                    {{ $t("Loading") }}...
                                </template>
                                <template
                                    v-if="!isLoading && currentAudio.title"
                                >
                                    {{ $t("Now Playing") }}
                                </template>
                            </div>
                            <div class="live-stream-title-container">
                                <div class="hiding-box-left"></div>
                                <div
                                    class="live-stream-title no-wrap"
                                    id="live-stream-title"
                                    :class="{
                                        slideAnimation:
                                            currentAudio.title &&
                                            currentAudio.title.length > 25
                                    }"
                                >
                                    {{
                                        currentAudio.title || currentAudio.name
                                    }}
                                </div>
                                <div class="hiding-box-right"></div>
                            </div>
                            <p
                                class="epico_audio-artist"
                                v-if="currentAudio.artist"
                            >
                                {{ currentAudio.artist }}
                            </p>
                        </div>
                        <div class="epico_control-section">
                            <div class="epico_progressbar-container">
                                <div
                                    class="epico_progressbar"
                                    @click="
                                        $emit('updateProgressOnPhone', $event)
                                    "
                                    id="progress-bar_phone_layout"
                                    v-if="!isCurrentAudioAStream"
                                >
                                    <div
                                        class="epico_progressbar-inner"
                                        :style="{
                                            width: currentAudio.progress + '%'
                                        }"
                                    >
                                        <span
                                            class="epico_progress-circle"
                                        ></span>
                                    </div>
                                </div>
                                <div
                                    class="epico_progressbar"
                                    @click="
                                        $emit('updateProgressOnPhone', $event)
                                    "
                                    id="progress-bar_phone_layout"
                                    v-else
                                >
                                    <div
                                        class="epico_progressbar-inner"
                                        :style="{
                                            width: '100%'
                                        }"
                                    >
                                        <span
                                            class="epico_progress-circle"
                                        ></span>
                                    </div>
                                </div>
                                <span class="epico_loading-circle"></span>
                                <span class="epico_current-audio-time">{{
                                    currentAudio.currentTime
                                }}</span>
                                <span
                                    class="epico_current-audio-duration"
                                    v-if="!isCurrentAudioAStream"
                                    v-text="duration"
                                ></span>
                                <span
                                    class="live-animation epico_current-audio-duration"
                                    v-else
                                >
                                    <div class="align-center">
                                        <svg
                                            height="20"
                                            width="13"
                                            class="blinking"
                                        >
                                            <circle
                                                cx="5"
                                                cy="10"
                                                r="3"
                                                fill="red"
                                            ></circle>
                                        </svg>
                                        {{ $t("Live") }}
                                    </div>
                                </span>
                            </div>
                            <div class="epico_main-control-section">
                                <div class="epico_play-next-previous-container">
                                    <button
                                        v-if="
                                            !isPodcastEpisode &&
                                                !isCurrentAudioAStream
                                        "
                                        class="epico_previous-button"
                                        @click="$emit('goPrevious')"
                                        :disabled="
                                            !playlist[currentAudio.index - 1]
                                        "
                                        :title="$t('Previous')"
                                    >
                                        <v-icon color="textContMedium"
                                            >$vuetify.icons.skip-previous</v-icon
                                        >
                                    </button>
                                    <button
                                        v-else-if="isPodcastEpisode"
                                        class="epico_previous-button epico_rewind-button"
                                        :title="$t('Rewind')"
                                        @click="$emit('rewindAudio', -10)"
                                    >
                                        <v-icon color="textContMedium"
                                            >$vuetify.icons.rewind-10</v-icon
                                        >
                                    </button>
                                    <button
                                        class="epico_btn epico_play-button epico_no-border"
                                    >
                                        <template v-if="isLoading">
                                            <v-progress-circular
                                                :size="60"
                                                :width="5"
                                                color="primary"
                                                indeterminate
                                                v-if="isLoading"
                                            ></v-progress-circular>
                                        </template>
                                        <template v-else>
                                            <button
                                                class="play-button"
                                                @click="$emit('playPause')"
                                                :title="$t('Play')"
                                                v-if="!currentAudio.isPlaying"
                                            >
                                                <v-icon
                                                    size="60"
                                                    :color="
                                                        $vuetify.theme.dark
                                                            ? 'textContMedium'
                                                            : 'primary'
                                                    "
                                                    >$vuetify.icons.play-circle</v-icon
                                                >
                                            </button>
                                            <button
                                                class="play-button"
                                                @click="$emit('playPause')"
                                                v-else
                                                :title="$t('Pause')"
                                            >
                                                <v-icon
                                                    size="60"
                                                    :color="
                                                        $vuetify.theme.dark
                                                            ? 'textContMedium'
                                                            : 'primary'
                                                    "
                                                    >$vuetify.icons.pause-circle</v-icon
                                                >
                                            </button>
                                        </template>
                                    </button>
                                    <button
                                        v-if="
                                            !isPodcastEpisode &&
                                                !isCurrentAudioAStream
                                        "
                                        class="epico_next-button"
                                        :disabled="
                                            !playlist[currentAudio.index + 1]
                                        "
                                        @click="$emit('goNext')"
                                        :title="$t('Next')"
                                    >
                                        <v-icon color="textContMedium"
                                            >$vuetify.icons.skip-next</v-icon
                                        >
                                    </button>
                                    <button
                                        v-else-if="isPodcastEpisode"
                                        class="epico_next-button epico_forwrad-button"
                                        @click="$emit('rewindAudio', +30)"
                                        :title="$t('Rewind')"
                                    >
                                        <v-icon color="textContMedium"
                                            >$vuetify.icons.fast-forward-30</v-icon
                                        >
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="epico_option-section">
                            <div
                                class="plus-container"
                                v-if="
                                    !isPodcastEpisode && !isCurrentAudioAStream
                                "
                            >
                                <song-menu
                                    :item="currentAudio"
                                    icon="plus"
                                    :isOnPlayer="true"
                                    :closeOnContentClick="true"
                                    @close="$store.commit('setSongMenu', null)"
                                ></song-menu>
                            </div>
                            <button
                                class="epico_shuffle-button"
                                v-if="
                                    !isPodcastEpisode && !isCurrentAudioAStream
                                "
                            >
                                <v-icon
                                    :title="$t('Shuffle')"
                                    @click="$emit('shuffleAudio')"
                                    :color="
                                        buttons.shuffle
                                            ? 'primary'
                                            : 'textContMedium'
                                    "
                                    >$vuetify.icons.shuffle-variant</v-icon
                                >
                            </button>
                            <template
                                v-if="
                                    !isCurrentAudioAStream &&
                                        !$store.getters.getSettings
                                            .disableRegistration && currentAudio.type !== 'episode'
                                "
                            >
                                <v-btn
                                    v-if="isLiked"
                                    icon
                                    @click="$emit('like', currentAudio)"
                                    class="align-center"
                                    small
                                >
                                    <v-icon
                      
                                        color="primary"
                                        :title="$t('Dislike')"
                                        >$vuetify.icons.heart</v-icon
                                    >
                                </v-btn>
                                <v-btn
                                    class="align-center"
                                    icon
                                    small
                                    @click="$emit('like', currentAudio)"
                                    v-else
                                >
                                    <v-icon :title="$t('Like')" 
                                        >$vuetify.icons.heart-outline</v-icon
                                    >
                                </v-btn>
                            </template>
                            <button
                                class="epico_repeat-button"
                                v-if="
                                    !isPodcastEpisode && !isCurrentAudioAStream
                                "
                            >
                                <v-icon
                                    :color="
                                        buttons.loop
                                            ? 'primary'
                                            : 'textContMedium'
                                    "
                                    :title="$t('Loop')"
                                    @click="$emit('loopAudio')"
                                    >$vuetify.icons.refresh</v-icon
                                >
                            </button>
                            <div class="epico_volume-button-container">
                                <v-btn
                                    @click="$emit('mute', $event)"
                                    icon
                                    small
                                >
                                    <v-icon color="textContMedium"
                                        >$vuetify.icons.{{
                                            volumeButton
                                        }}</v-icon
                                    >
                                </v-btn>
                                <!-- Mute or unmute only-->
                                <!-- <div
                        class="epico_volumebar-container epico_volumebar-container-u500h"
                    >
                        <v-slider
                            v-model="vol"
                            @change="$emit('volume', vol)"
                            thumb-color="primary"
                            tick-size="50"
                            thumb-size="22"
                            hide-details=""
                            :thumb-label="true"
                        ></v-slider>
                    </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            showAddMenu: false,
            showPlaylist: false
        };
    },
    computed: {
        fullScreenPlayer: {
            get() {
                return this.fullScreenPlayerProp;
            },
            set() {
                this.$emit("fullScreenPlayer");
            }
        }
    },
    props: [
        "playlist",
        "currentAudio",
        "downloadLoading",
        "fullScreenPlayerProp",
        "getUp",
        "isLoading",
        "buttons",
        "volumebarInnerWidth",
        "playbackRate",
        "volumeButton",
        "isLiked",
        "isArtistFollowed",
        "isPodcastEpisode",
        "isCurrentAudioAStream",
        "volumeButton",
        "volume",
        "duration",
        "isPurchasable",
        "isOwned"
    ]
};
</script>


<style>
.epico_audio-title {
    text-align: center;
    padding: 0 1em 1em 1em;
}
</style>