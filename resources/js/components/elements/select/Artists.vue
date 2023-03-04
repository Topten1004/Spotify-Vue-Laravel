<template>
    <!-- <v-card v-if="attachArtistDialog" class="py-4">
        <v-container>
            <v-row>
                <v-col cols="12"> -->
    <v-autocomplete
        v-model="selectedArtists"
        :items="searchedArtists"
        :loading="isSearchArtistLoading"
        :search-input.sync="searchArtists"
        :label="multiple ? $t('Artists') : $t('Artist')"
        item-text="displayname"
        return-object
        hide-details
        :placeholder="$t('Start typing to Search')"
        :multiple="multiple"
        outlined
    >
        <template v-slot:selection="{ item }">
            <v-chip                
                close
                @click:close="remove(item.id)"
            >
                <v-avatar left>
                    <v-img :src="item.avatar" v-if="item.avatar"></v-img>
                    <v-icon  v-else>$vuetify.icons.account-music</v-icon>
                </v-avatar>
                {{ item.displayname }}
            </v-chip>
        </template>
        <template v-slot:item="{ item }">
            <template>
                <v-list-item-avatar>
                    <v-img :src="item.avatar" v-if="item.avatar"></v-img>
                    <v-icon  v-else>$vuetify.icons.account-music</v-icon>
                </v-list-item-avatar>
                <v-list-item-content>
                <v-list-item-title v-html="item.displayname"></v-list-item-title>
                <!-- <v-list-item-subtitle v-html="item.group"></v-list-item-subtitle> -->
                </v-list-item-content>
            </template>
        </template>
    </v-autocomplete>
</template>

<script>
export default {
    props: ['artists', 'multiple', 'engines'],
    data() {
        return {
            searchedArtists: JSON.parse(JSON.stringify(this.multiple ? this.artists : [this.artists])),
            selectedArtists: JSON.parse(JSON.stringify(this.artists)),
            isSearchArtistLoading: false,
            searchArtists: null
        };
    },
    watch: {
        searchArtists(val) {
            if (this.isSearchArtistLoading) return;
            this.isSearchArtistLoading = true;
            axios
                .get("/api/match-artists" + "?query=" + val + "&engines=" + (this.engines ? JSON.stringify([this.engines]) : JSON.stringify(['*'])) )
                .then(res => {
                    res.data.forEach(ar => {
                        this.searchedArtists.push(ar);
                    });
                })
                .catch()
                .finally(() => (this.isSearchArtistLoading = false));
        },
        selectedArtists(val) {
            this.$emit('artists', val)
        }
    },
    methods: {
        pushArtist(item) {
            this.selectedArtists.push(item)
        },
        remove(id) {
            if( this.multiple ) {
                const index = this.selectedArtists.findIndex(item => item.id === id);
                if (index >= 0) this.selectedArtists.splice(index, 1);
            } else {
                this.selectedArtists = null
            }
           
        }
    }
};
</script>

<style></style>
