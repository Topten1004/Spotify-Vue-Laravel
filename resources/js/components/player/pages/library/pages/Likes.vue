<template>
    <div class="likes-wrapper">
        <div class="header" v-if="songs">
            <div class="content-text">
                <div class="title">
                    <h3>{{$t('My Likes')}}</h3>
                </div>
                <div class="nb_songs">
                    <strong>{{ songs.length }}</strong> {{$t('Songs')}}
                </div>
            </div>

            <div class="buttons">
                <v-btn
                    class="primary"
                    rounded
                    small
                    @click="$store.dispatch('updateQueue', { content: songs, reset: true })"
                    :disabled="!songs.length"
                >
                    {{$t('Play')}}
                </v-btn>
            </div>
        </div>
        <song-list :songs="songs" :options="['like']"></song-list>
        <empty-page
            v-if="songs && !songs.length"
            img="peep-34.png"
            :headline="$t('No likes yet!')"
            :sub="$t('This page is empty.')"
        />
    </div>
</template>
<script>
export default {
    created() {
        this.$store.dispatch("likes", "song");
    },
    computed: {
        songs() {
            return this.$store.getters.getLikedSongs;
        }
    }
};
</script>
<style lang="scss">
.nb_songs {
    font-size: 0.8rem;
}
</style>
