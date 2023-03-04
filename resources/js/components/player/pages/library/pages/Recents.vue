<template>
    <div class="recents-wrapper">
        <div class="header">
            <div class="content-text">
                <div class="title">
                    <h3>{{$t('Recently Played')}}</h3>
                </div>
            </div>
            <div class="buttons">
                <v-btn
                    class="primary"
                    rounded
                    small
                    @click="$store.dispatch('updateQueue', { content: songs, reset: true })"
                    :disabled="!songs || (songs && !songs.length)"
                >
                    {{$t('Play')}}
                </v-btn>
            </div>
        </div>
        <song-list :songs="songs" :options="[
            'like'
        ]" />
        <empty-page
            v-if="songs && !songs.length"
            img="peep-34.png"
            :headline="$t('Empty!')"
            :sub="$t('Your history is empty.')"
        />
    </div>
</template>
<script>
export default {
    created() {
        this.$store.dispatch("fetchRecentPlays");
    },
    computed: {
        songs() {
            return this.$store.getters.getRecentlyPlayed;
        }
    }
};
</script>
