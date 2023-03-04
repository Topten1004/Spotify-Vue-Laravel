<template>
        <v-card>
            <v-container>
                <v-row>
                    <v-col cols="12">
                        <v-autocomplete
                            v-model="selectedSong"
                            :items="searchedSongs"
                            :loading="isSearchSongLoading"
                            :search-input.sync="searchSongs"
                            item-text="title"
                            :label="$t('Search Song')"
                            :placeholder="$t('Start typing to search')"
                            return-object
                            prepend-icon="mdi-music-note"
                        >
                            <template v-slot:item="{ item }">
                                <div class="item py-2">
                                    <div class="list-item-cover">
                                        <v-img
                                            :src="item.cover"
                                            class="img mr-2"
                                            width="50"
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
                                </div>
                                {{ item.title }}
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="12">
                        <v-btn
                            class="success"
                            block
                            :disabled="!selectedSong"
                            @click="
                                $emit('songSelected', selectedSong), (searchedSongs = [])
                            "
                            >{{ $t('Add') }}</v-btn
                        >
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
</template>

<script>
export default {
    props: ['creator'],
    data() {
        return {
            selectedSong: null,
            searchedSongs: [],
            isSearchSongLoading: false,
            searchSongs: null
        };
    },
    watch: {
        searchSongs(val) {
            // Items have already been requested
            if (this.isSearchSongLoading) return;
            this.isSearchSongLoading = true;
            // Lazily load input items
            axios
                .get("/api/" + (this.creator === 'artist' ? "artist/match-songs" :  "match-songs" 
                ) + "?query=" + val)
                .then(res => {
                    this.searchedSongs = res.data;
                })
                .catch()
                .finally(() => (this.isSearchSongLoading = false));
        }
    }
};
</script>
