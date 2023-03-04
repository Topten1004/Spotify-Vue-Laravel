<template>
    <div class="podcast-genre-wrapper content-page" v-if="podcastGenre">
        <div class="icon-header">
            <div class="cover">
                <v-icon dark>$vuetify.icons.microphone</v-icon>
            </div>
            <div class="title">
                <div class="headline">
                    {{ $t(
                        title.slice(0, 1).toUpperCase() +
                            title.slice(1, title.length))
                    }}
                    {{$t('Podcasts')}}
                </div>
                <div class="sub">
                    {{$t('Here you can explore all')}} {{$t(title)}} {{$t('podcasts')}}
                </div>
            </div>
        </div>
        <div class="podcasts-wrapper">
            <template v-if="podcasts && podcasts.length">
                <!-- <podcast-box
                    :podcast="podcast"
                    class="podcast"
                    v-for="podcast in podcasts"
                    :key="podcast.id"
                /> -->
                <Grid :items="podcasts"></Grid>
            </template>
            <div class="empty-page" v-else-if="!podcasts">
                 <page-loading  />
            </div>
            <template v-else>
                <empty-page
                    :headline="$t('No Podcasts!')"
                    :sub="$t('This genre has no podcasts yet.')"
                    img="peep-34.png"
                />
            </template>
        </div>
    </div>
    <empty-page
        v-else-if="!podcastGenre && errorStatus == 404"
        :headline="$t('Not Available!')"
        :sub="$t('Podcast genre does not exist.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)"
                >{{$t('Go Back')}}</v-btn
            >
        </template>
    </empty-page>
    <empty-page
        v-else-if="!podcastGenre && errorStatus"
        :headline="$t('Something wrong happend!')"
        :sub="$t('Some server error has occurred. Try again later.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" rounded small @click="$router.go(-1)"
                >{{$t('Go Back')}}</v-btn
            >
        </template>
    </empty-page>
    <page-loading v-else />
</template>
<script>
export default {
    props: ['genre_slug', 'genre_id'],
    metaInfo() {
        return {
            title: this.generateMetaInfos(
                { name: this.title },
                this.$store.getters.getSettings.podcastGenrePageTitle,
                "podcast-genre"
            ),
            meta: [
                {
                    name: "description",
                    content: this.generateMetaInfos(
                        { name: this.title },
                        this.$store.getters.getSettings
                            .podcastGenrePageDescription,
                        "podcast-genre"
                    )
                }
            ]
        };
    },
    data() {
        return {
            podcasts: null,
            podcastGenre: null,
            title: this.genre_slug,
            errorStatus: null
        };
    },
    created() {
        axios
            .get("/api/podcast-genre/" + this.genre_id)
            .then(res1 => {
                this.podcastGenre = res1.data;
                axios
                    .get(
                        `/api/podcast-genre-podcasts/${this.podcastGenre.id}`
                    )
                    .then(res2 => {
                        this.podcasts = res2.data;
                    });
            })
            .catch(e => {
                this.errorStatus = e.response.status;
            });
    }
};
</script>
<style lang="scss" scoped>
.icon-header > .cover {
    background: linear-gradient(45.34deg, #ea52f8 5.66%, #0066ff 94.35%);
}
.podcasts-wrapper {
    display: flex;
    .podcast {
        flex-basis: 20%;
    }
    .empty-page {
        width: 100%;
    }
}
</style>
