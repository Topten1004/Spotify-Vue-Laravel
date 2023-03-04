<template>
    <div class="searchbar-wrapper">
        <div class="searchbar">
            <input
                type="text"
                :placeholder="$t('Search')"
                v-model="keyword"
                class="searchbar_input m_input"
                @click.stop="showResultPanel = true"
                @input="search()"
            />
            <v-icon class="style-search-icon" light
                >$vuetify.icons.magnify</v-icon
            >
        </div>
        <div class="search-panel" v-if="showResultPanel" @click.stop>
            <v-bottom-navigation
                v-model="menuItem"
                class="searchbar-bottom-nav"
                shift
            >
                <v-btn>
                    <span>{{ $t("Top") }}</span>
                    <v-icon>$vuetify.icons.feature-search-outline</v-icon>
                </v-btn>
                <!-- <v-btn>
                    <span>{{ $t('Radio Stations') }}</span>
                    <v-icon>$vuetify.icons.radio-tower</v-icon>
                </v-btn> -->
                <v-btn>
                    <span>{{ $t("Songs") }}</span>
                    <v-icon>$vuetify.icons.music-note</v-icon>
                </v-btn>
                <v-btn>
                    <span>{{ $t("Albums") }}</span>
                    <v-icon>$vuetify.icons.album</v-icon>
                </v-btn>
                <v-btn>
                    <span>{{ $t("Artists") }}</span>
                    <v-icon>$vuetify.icons.account-music</v-icon>
                </v-btn>
                <v-btn>
                    <span>{{ $t("Users") }}</span>
                    <v-icon>$vuetify.icons.account</v-icon>
                </v-btn>
                <v-btn>
                    <span>{{ $t("Playlists") }}</span>
                    <v-icon>$vuetify.icons.playlist-music-outline</v-icon>
                </v-btn>
                <v-btn v-if="$store.getters.getSettings.enablePodcasts">
                    <span>{{ $t("Podcasts") }}</span>
                    <v-icon>$vuetify.icons.microphone</v-icon>
                </v-btn>
            </v-bottom-navigation>
            <div class="search-results-container">
                <template v-if="loading">
                    <page-loading height="18vh"></page-loading>
                </template>
                <template v-else-if="currentPage === 'top'">
                    <div
                        class="search-category"
                        v-if="
                            searchResults && searchResults.radioStations.length
                        "
                    >
                        <div class="category-title">
                            {{ $t("Radio Stations") }}
                        </div>
                        <div class="category-results">
                            <div
                                class="search-result"
                                v-for="radioStation in searchResults.radioStations.slice(
                                    0,
                                    3
                                )"
                                :key="radioStation.id"
                                @click.stop="
                                    $store.dispatch('playRadioStation', {
                                        radioStation
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="radioStation.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ radioStation.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="search-category"
                        v-if="searchResults && searchResults.songs.length"
                    >
                        <div class="category-title">
                            {{ $t("Songs") }}
                        </div>
                        <div class="category-results">
                            <div
                                class="search-result"
                                v-for="song in searchResults.songs.slice(0, 3)"
                                :key="song.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: song.id,
                                        name: 'song'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="song.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ song.title }}
                                    </div>
                                    <div class="asset-artists">
                                        {{ getArtists(song.artists) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="search-category"
                        v-if="searchResults && searchResults.artists.length"
                    >
                        <div class="category-title">
                            {{ $t("Artists") }}
                        </div>
                        <div class="category-results">
                            <div
                                class="search-result"
                                v-for="artist in searchResults.artists.slice(
                                    0,
                                    3
                                )"
                                :key="artist.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: artist.id,
                                        name: 'artist'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="artist.avatar"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title" v-if="artist">
                                        {{ artist.displayname }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="search-category"
                        v-if="searchResults && searchResults.albums.length"
                    >
                        <div class="category-title">
                            {{ $t("Albums") }}
                        </div>
                        <div class="category-results">
                            <div
                                class="search-result"
                                v-for="album in searchResults.albums.slice(
                                    0,
                                    3
                                )"
                                :key="album.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: album.id,
                                        name: 'album'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="album.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ album.title }}
                                    </div>
                                    <div
                                        class="asset-artists"
                                        v-if="album.artist"
                                    >
                                        {{ album.artist.displayname }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="search-category"
                        v-if="
                            searchResults &&
                                $store.getters.getSettings.enablePodcasts &&
                                searchResults.podcasts.length
                        "
                    >
                        <div class="category-title">
                            {{ $t("Podcasts") }}
                        </div>
                        <div class="category-results">
                            <div
                                class="search-result"
                                v-for="podcast in searchResults.podcasts.slice(
                                    0,
                                    3
                                )"
                                :key="podcast.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: podcast.id,
                                        name: 'podcast'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="podcast.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ podcast.title }}
                                    </div>
                                    <div
                                        class="asset-artists"
                                        v-if="podcast.artist"
                                    >
                                        {{ podcast.artist.displayname }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="search-category"
                        v-if="searchResults && searchResults.playlists.length"
                    >
                        <div class="category-title">
                            {{ $t("Playlists") }}
                        </div>
                        <div class="category-results">
                            <div
                                class="search-result"
                                v-for="playlist in searchResults.playlists.slice(
                                    0,
                                    3
                                )"
                                :key="playlist.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: playlist.id,
                                        name: 'playlist'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="playlist.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ playlist.title }}
                                    </div>
                                    <div class="asset-artists">
                                        {{
                                            playlist.user
                                                ? playlist.user.name
                                                : ""
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="search-category"
                        v-if="searchResults && searchResults.users.length"
                    >
                        <div class="category-title">
                            {{ $t("Users") }}
                        </div>
                        <div class="category-results">
                            <div
                                class="search-result"
                                v-for="user in searchResults.users.slice(0, 3)"
                                :key="user.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: user.id,
                                        name: 'profile'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="user.avatar"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ user.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="no-results"
                        v-else-if="
                        searchResults && 
                            ((searchResults.podcasts &&
                                !searchResults.podcasts.length) ||
                                !searchResults.podcasts) &&
                                !searchResults.radioStations.length &&
                                !searchResults.albums.length &&
                                !searchResults.playlists.length &&
                                !searchResults.songs.length &&
                                !searchResults.artists.length &&
                                !searchResults.users.length
                        "
                    >
                        <empty-page
                            :headline="$t('No Results!')"
                            :sub="
                                $t(
                                    'There are no results found for this search keyword.'
                                )
                            "
                        ></empty-page>
                    </div>
                </template>
                <template v-else-if="searchResults">
                    <!-- <template v-if="currentPage === 'radioStations'">
                        <div class="search-results" v-if="searchResults.radioStations.length">
                                                                               <div
                                class="search-result"
                                v-for="radioStation in searchResults.radioStations.slice(0, 3)"
                                :key="radioStation.id"
                                
                                @click.stop="$store.dispatch('playRadioStation', { radioStation })"
                            >
                                <div class="cover">
                                    <v-img
                                        :src="radioStation.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ radioStation.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="no-results" v-else>
                            <empty-page
                                :headline="$t('No Results!')"
                                :sub="$t('There are no results found for this search keyword.')"
                            ></empty-page>
                        </div>
                    </template> -->
                    <template v-if="currentPage === 'songs'">
                        <div
                            class="search-results"
                            v-if="searchResults.songs.length"
                        >
                            <div
                                class="search-result"
                                v-for="song in searchResults.songs"
                                :key="song.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: song.id,
                                        name: 'song'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="song.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ song.title }}
                                    </div>
                                    <div class="asset-artists">
                                        {{ getArtists(song.artists) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="no-results" v-else>
                            <empty-page
                                :headline="$t('No Results!')"
                                :sub="
                                    $t(
                                        'There are no results found for this search keyword.'
                                    )
                                "
                            ></empty-page>
                        </div>
                    </template>
                    <template v-if="currentPage === 'albums'">
                        <div
                            class="search-results"
                            v-if="searchResults.albums.length"
                        >
                            <div
                                class="search-result"
                                v-for="album in searchResults.albums"
                                :key="album.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: album.id,
                                        name: 'album'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="album.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ album.title }}
                                    </div>
                                    <div
                                        class="asset-artists"
                                        v-if="album.artist"
                                    >
                                        {{ album.artist.displayname }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="no-results" v-else>
                            <empty-page
                                :headline="$t('No Results!')"
                                :sub="
                                    $t(
                                        'There are no results found for this search keyword.'
                                    )
                                "
                            ></empty-page>
                        </div>
                    </template>
                    <template v-if="currentPage === 'playlists'">
                        <div
                            class="search-results"
                            v-if="searchResults.playlists.length"
                        >
                            <div
                                class="search-result"
                                v-for="playlist in searchResults.playlists"
                                :key="playlist.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: playlist.id,
                                        name: 'playlist'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="playlist.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ playlist.title }}
                                    </div>
                                    <div class="asset-artists">
                                        {{ playlist.user.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="no-results" v-else>
                            <empty-page
                                :headline="$t('No Results!')"
                                :sub="
                                    $t(
                                        'There are no results found for this search keyword.'
                                    )
                                "
                            ></empty-page>
                        </div>
                    </template>
                    <template v-if="currentPage === 'podcasts'">
                        <div
                            class="search-results"
                            v-if="searchResults.podcasts.length"
                        >
                            <div
                                class="search-result"
                                v-for="podcast in searchResults.podcasts"
                                :key="podcast.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: podcast.id,
                                        name: 'podcast'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="podcast.cover"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ podcast.title }}
                                    </div>
                                    <div
                                        class="asset-artists"
                                        v-if="podcast.artist"
                                    >
                                        {{ podcast.artist.displayname }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="no-results" v-else>
                            <empty-page
                                :headline="$t('No Results!')"
                                :sub="
                                    $t(
                                        'There are no results found for this search keyword.'
                                    )
                                "
                            ></empty-page>
                        </div>
                    </template>
                    <template v-if="currentPage === 'artists'">
                        <div
                            class="search-results"
                            v-if="searchResults.artists.length"
                        >
                            <div
                                class="search-result"
                                v-for="artist in searchResults.artists"
                                :key="artist.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: artist.id,
                                        name: 'artist'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="artist.avatar"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title" v-if="artist">
                                        {{ artist.displayname }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="no-results" v-else>
                            <empty-page
                                :headline="$t('No Results!')"
                                :sub="
                                    $t(
                                        'There are no results found for this search keyword.'
                                    )
                                "
                            ></empty-page>
                        </div>
                    </template>
                    <template v-if="currentPage === 'users'">
                        <div
                            class="search-results"
                            v-if="searchResults.users.length"
                        >
                            <div
                                class="search-result"
                                v-for="user in searchResults.users"
                                :key="user.id"
                                @click.stop="
                                    goToSearchRoute({
                                        id: user.id,
                                        name: 'profile'
                                    })
                                "
                            >
                                <div class="cover">
                                    <v-img
                                        :src="user.avatar"
                                        width="50"
                                        height="50"
                                    >
                                        <template v-slot:placeholder>
                                            <content-placeholders
                                                :rounded="true"
                                            >
                                                <content-placeholders-img
                                                    class="small-image-skeleton"
                                                />
                                            </content-placeholders>
                                        </template>
                                    </v-img>
                                </div>
                                <div class="body">
                                    <div class="asset-title">
                                        {{ user.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="no-results" v-else>
                            <empty-page
                                :headline="$t('No Results!')"
                                :sub="
                                    $t(
                                        'There are no results found for this search keyword.'
                                    )
                                "
                            ></empty-page>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            menuItem: 0,
            keyword: "",
            searchResults: null,
            loading: false
        };
    },
    methods: {
        search() {
            if (!this.keyword) {
                this.searchResults = null;
                this.loading = false;
            } else {
                this.loading = true;
                axios
                    .get("/api/search/" + this.keyword)
                    .then(res => {
                        this.searchResults = res.data;
                    })
                    .finally(() => (this.loading = false));
            }
        },
        goToSearchRoute({ id, name }) {
            this.$emit("navigation");
            this.pushRoute({ name, id });
            this.showResultPanel = false;
        }
    },
    computed: {
        currentPage() {
            switch (this.menuItem) {
                case 0:
                    return "top";
                case 1:
                    return "songs";
                case 2:
                    return "albums";
                case 3:
                    return "artists";
                case 4:
                    return "users";
                case 5:
                    return "playlists";
                case 6:
                    return "podcasts";
            }
        },
        showResultPanel: {
            set(val) {
                this.$store.commit("setSearchResultsPanel", val);
            },
            get() {
                return this.$store.getters.getSearchResultsPanel;
            }
        }
    }
};
</script>
<style lang="scss" scoped>
@import "../../../sass/_variables";
.searchbar-wrapper {
    position: relative;
    width: 24em;
    margin-right: 1em;
    @media screen and (max-width: 700px) {
        width: 23em;
    }
    @media screen and (max-width: 700px) {
        // width: 95%;
        margin-right: 0em;
    }
    .search-panel {
        position: absolute;
        top: 110%;
        background-color: $light-theme-bg-color;
        border-radius: 6px;
        width: 100%;
        z-index: 999;
        box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.2),
            0px 4px 5px 0px rgba(0, 0, 0, 0.14),
            0px 1px 10px 0px rgba(0, 0, 0, 0.12);
    }
}
.searchbar {
    position: relative;
    transition: all 0.5s;
    font-size: 1.05rem;
    border-radius: 8px;
    background-color: rgb(226, 226, 226);
    input {
        border: none;
        font-weight: bold;
        padding: 0.2rem 2.3rem;
        width: 100%;
        color: black;
        border-radius: 8px;
    }
    .style-search-icon {
        position: absolute;
        left: 0.5rem;
        width: 1.15rem;
        top: 50%;
        transform: translateY(-50%);
    }
    .searchbar-bottom-nav {
        overflow-x: auto;
        overflow-y: hidden;
        justify-content: flex-start;
    }
}
.searchbar-wrapper .v-item-group.v-bottom-navigation {
    bottom: 0;
    display: flex;
    left: 0;
    width: 100%;
    box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.2),
        0px 4px 5px 0px rgba(0, 0, 0, 0.14),
        0px 1px 10px 0px rgba(0, 0, 0, 0.12);
    &::-webkit-scrollbar {
        width: 7px;
        height: 7px;
        transform: translateX(-50px);
    }
    .v-btn {
        border-radius: 0 !important;
        box-shadow: none !important;
        font-size: 0.6rem !important;
        height: inherit !important;
        max-width: 168px !important;
        min-width: 50px !important;
        position: relative !important;
        text-transform: none !important;
    }
}
.search-panel {
    .search-results-container {
        max-height: 400px;
        overflow-y: auto;
        padding: 0.6em 0.5em;
        .search-result {
            border-radius: 10px;
            transition: background-color 0.5s;
            &:hover {
                background-color: rgba(201, 201, 201, 0.308);
            }
            display: flex;
            align-items: center;
            cursor: pointer;
            .cover {
                padding: 0.5em;
                & > * {
                    border-radius: 7px;
                }
            }
            .body {
                color: $light-theme-text-color;
                .asset-title {
                    font-weight: 600;

                    font-size: 0.95em !important;
                }
                .asset-artists {
                    font-size: 0.8em !important;
                    opacity: 0.85;
                }
            }
        }
        .search-category {
            .category-title {
                color: #4c60a3;
                font-size: 0.9rem;
                font-weight: bold;
                padding-left: 0.5rem;
                padding-top: 0.6rem;
            }
            .category-results {
                padding: 0.5em 0 0.5em 0.9em;
            }
        }
    }
}
.player--dark {
    .searchbar-wrapper {
        .search-panel {
            box-shadow: none;
            background-color: $dark-theme-panel-bg-color;
            .search-results-container {
                .search-result {
                    .body {
                        color: $dark-theme-text-color;
                    }
                }
                .search-category {
                    .category-title {
                        color: #4c60a3;
                    }
                }
            }
        }
    }
    .searchbar {
        background-color: rgb(238, 238, 238);
    }
}
</style>
