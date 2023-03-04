<template>
    <v-autocomplete
        v-model="selectedGenres"
        :items="searchedGenres"
        :loading="isSearchGenreLoading"
        :search-input.sync="searchGenres"
        :label="$t('Genres')"
        item-text="name"
        return-object
        :placeholder="$t('Start typing to Search')"
        multiple
        outlined
    >
        <template v-slot:selection="{ item }">
            <v-chip                
                close
                @click:close="remove(item.id)"
            >
                {{ item.name }}
            </v-chip>
        </template>
        <template v-slot:item="{ item }">
            <template>
                <v-list-item-content>
                <v-list-item-title v-html="item.name"></v-list-item-title>
                <!-- <v-list-item-subtitle v-html="item.group"></v-list-item-subtitle> -->
                </v-list-item-content>
            </template>
        </template>
    </v-autocomplete>
</template>

<script>
export default {
    props: ['genres'],
    data() {
        return {
            searchedGenres: JSON.parse(JSON.stringify(this.genres)),
            selectedGenres: JSON.parse(JSON.stringify(this.genres)),
            isSearchGenreLoading: false,
            searchGenres: null
        };
    },
    watch: {
        searchGenres(val) {
            if (this.isSearchGenreLoading) return;
            this.isSearchGenreLoading = true;
            axios
                .get("/api/match-genres?query=" + val)
                .then(res => {
                    res.data.forEach(ar => {
                        this.searchedGenres.push(ar);
                    });
                })
                .catch()
                .finally(() => (this.isSearchGenreLoading = false));
        },
        selectedGenres(val) {
            this.$emit('genres', val)
        }
    },
    methods: {
        pushGenre(item) {
            this.selectedGenres.push(item)
        },
        remove(id) {
            const index = this.selectedGenres.findIndex(item => item.id === id);
            if (index >= 0) this.selectedGenres.splice(index, 1);
        }
    }
};
</script>

<style></style>
