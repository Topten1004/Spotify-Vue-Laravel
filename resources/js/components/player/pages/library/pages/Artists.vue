<template>
    <div class="artist-wrapper">
        <div class="title">
            <h3>{{$t('Followed Artists')}}</h3>
        </div>
        <div class="artists-container">
            <artist
                v-for="(artist, i) in artists"
                :key="artist ? artist.id : i"
                :artist="artist"
                class="artist"
            />
            <empty-page
                v-if="artists && !artists.length"
                img="peep-41.png"
                :headline="$t('No artists!')"
                :sub="$t('You did not follow any artist yet!')"
            />
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            artists: []
        };
    },
    created() {
        this.fetchFollowedArtists();
    },
    methods: {
        fetchFollowedArtists() {
            axios.get("/api/user/follows/artist").then((res) => {
                this.artists = res.data;
            });
        }
    }
};
</script>
<style lang="scss" scoped>
.artists-container {
    display: flex;
    flex-wrap: wrap;
    .artist {
        flex-basis: 20%;
        min-width: 120px;
        .name {
            text-align: center;
        }
    }
}
</style>
