<template>
    <v-card>
        <v-container>
            <v-row v-if="!type" justify="space-around" class="py-2">
                <v-btn
                    :outlined="item_type !== 'song'"
                    color="primary"
                    small
                    @click="item_type = 'song'"
                >
                    {{ $t('Song') }}
                </v-btn>
                <v-btn
                    :outlined="item_type !== 'album'"
                    color="primary"
                    small
                    @click="item_type = 'album'"
                >
                    {{ $t('Album') }}
                </v-btn>
                <v-btn
                    :outlined="item_type !== 'playlist'"
                    color="primary"
                    small
                    @click="item_type = 'playlist'"
                >
                    {{ $t('Playlist') }}
                </v-btn>
                <v-btn
                    :outlined="item_type !== 'podcast'"
                    color="primary"
                    small
                    @click="item_type = 'podcast'"
                >
                    {{ $t('Podcast') }}
                </v-btn>
                <v-btn
                    :outlined="item_type !== 'radio-station'"
                    color="primary"
                    small
                    @click="item_type = 'radio-station'"
                >
                    {{ $t('Radio') }}
                </v-btn>
                <v-btn
                    :outlined="item_type !== 'genre'"
                    color="primary"
                    small
                    v-if="section_type !== 'cards' || section_type !== 'list'"
                    @click="item_type = 'genre'"
                >
                    {{ $t('Genre') }}
                </v-btn>
            </v-row>
            <v-row>
                <v-col>
                    <v-autocomplete
                        v-model="selectedAsset"
                        :items="searchedAssets"
                        :loading="isSearchAssetLoading"
                        :search-input.sync="searchAssets"
                        :item-text="name"
                        :no-data-text="$t('No data available')"
                        :label="
                            $t('Search') + ' ' + $t(item_type.replace(/s$/, ''))
                        "
                        :placeholder="$t('Start typing to Search')"
                        return-object
                    >
                        <template v-slot:item="{ item }">
                            <div class="item py-2">
                                <div class="list-item-cover">
                                    <v-img
                                        :src="item[imgSource]"
                                        width="40"
                                        class="img mr-2"
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
                            {{ item[name] }}
                        </template>
                    </v-autocomplete>
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <v-btn
                        class="success"
                        block
                        :disabled="!selectedAsset"
                        @click="emitAsset"
                        >{{ $t("Select") }}</v-btn
                    >
                </v-col>
            </v-row>
        </v-container>
    </v-card>
</template>

<script>
export default {
    props: ["type", "engines", "section_type"],
    data() {
        return {
            selectedAsset: null,
            searchedAssets: [],
            isSearchAssetLoading: false,
            searchAssets: null,
            item_type: this.type || 'song'
        };
    },
    computed: {
        imgSource(){
            let itemsWhoHasCover = ['song', 'album', 'podcast', 'playlist', 'radio-station', 'genre'];
            let itemsWhoHasAvatar = ['user', 'artist'];
            if ( itemsWhoHasCover.includes(this.item_type) ) {
                return 'cover'
            } else 
            if ( itemsWhoHasAvatar.includes(this.item_type) )
            {
                return 'avatar'
            }
        },
        name() {
            let itemsWhoHasTitle = ['song', 'album', 'podcast', 'playlist'];
            let itemsWhoHasName = ['radio-station', 'user', 'genre'];
            let itemsWhoHasDisplayname = ['artist'];
            if ( itemsWhoHasTitle.includes(this.item_type) ) {
                return 'title'
            } else 
            if ( itemsWhoHasName.includes(this.item_type) )
            {
                return 'name'
             }else 
            if ( itemsWhoHasDisplayname.includes(this.item_type) )
            {
                return 'displayname'
            }
        }
    },
    watch: {
        searchAssets(val) {
            if (val) {
                if (this.isSearchAssetLoading) return;
                this.isSearchAssetLoading = true;  
                axios
                    .get("/api/match-" + this.item_type + 's' + "?query=" +  val +  "&engines=" + (this.engines ? JSON.stringify([this.engines]) : JSON.stringify(['*'])))
                    .then(res => {
                        this.searchedAssets = res.data;
                    })
                    .catch()
                    .finally(() => (this.isSearchAssetLoading = false));
            }
        }
    },
    methods: {
        emitAsset() {
            this.selectedAsset.type = this.item_type
            this.$emit('asset', this.selectedAsset)
        }
    }
};
</script>
<style lang="scss" scoped>
.img {
    border-radius: 8px;
}
</style>
