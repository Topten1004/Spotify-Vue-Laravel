<template>
    <div class="podcasts-home-wrapper content-page pr-5">
        <div class="icon-header">
            <div class="cover">
                <v-icon dark>$vuetify.icons.microphone</v-icon>
            </div>
            <div class="title">
                <div class="headline">
                    {{$t('Podcasts')}}
                </div>
                <div class="sub">
                    {{$t('Explore & listen to podcasts')}}
                </div>
            </div>
        </div>
        <div class="main-contet">
            <div class="page-title-medium">
                {{$t('Genres')}}
            </div>
            <template v-if="genres && popularPodcasts">
                <podcast-genres :genres="genres" class="pt-5"></podcast-genres>
                <div class="section-popular mt-5" v-if="popularPodcasts && popularPodcasts.length">
                    <swiper-section
                        :section="{
                            rank: 10,
                            title: $t('Popular Podcasts'),
                        }"
                        :content="popularPodcasts"
                    />
                </div>
            </template>
            <page-loading v-else />
        </div>
    </div>
</template>
<script>
export default {
    metaInfo() {
        return {
            title: this.generateMetaInfos(
                {},
                this.$store.getters.getSettings.podcastsPageTitle
            ),
            meta: [
                {
                    name: "description",
                    content: this.generateMetaInfos(
                        {},
                        this.$store.getters.getSettings.podcastsPageDescription
                    )
                }
            ]
        };
    },
    data() {
        return {
            genres: null,
            popularPodcasts: null
        };
    },
    async created() {
        const res = await axios.get(`/api/podcast-genres`);
        const res2 = await axios.get(`/api/popular-podcasts?nb_items=10&src=*`);
        this.genres = res.data;
        this.popularPodcasts = res2.data;
    }
};
</script>
<style lang="scss" scoped>
.icon-header {
    .cover {
        background: linear-gradient(
            45.34deg,
            #fbb13c 5.66%,
            #ff7a72 48.62%,
            #ff7a72 94.35%
        );
    }
}
</style>
