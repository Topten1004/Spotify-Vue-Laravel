<template>
    <div class="browse-genres-container" v-if="genres">
        <div class="genres">
            <genre v-for="genre in genres" :key="genre.id" :genre="genre"></genre>
        </div>
    </div>
    <page-loading v-else />
</template>
<script>
export default {
    async created() {
        const res = await axios.get(`/api/genres`);
        this.genres = res.data;
    },
    data() {
        return {
            genres: null
        };
    }
};
</script>
