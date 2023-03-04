<template>
    <div class="local_songs-wrapper">
        <div class="header pr-5" v-if="songs">
            <div class="content-text">
                <div class="title">
                    <h3>{{$t('Purchases')}}</h3>
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
        <song-list :songs="songs" class="song-list" :options="[ 'download' ]" />
        <empty-page
            v-if="songs && !songs.length"
            img="peep-34.png"
            :headline="$t('No purchases yet!')"
            :sub="$t('This page is empty.')"
        />
    </div>
</template>
<script>
import Billing from '../../../../../mixins/billing/billing'
export default {
    created() {
        this.fetchPurchases();
    },
    mixins: [Billing],
    computed: {
        songs() {
            return this.purchasedSongs
        }
    },
    methods: {
        fetchPurchases() {
            this.$store.dispatch("fetchPurchases");
        }
    }
};
</script>
