<template>
    <div class="genre-songs-grid content-page">
        <div class="songs-genre" v-if="genre">
            <div class="icon-header">
                <div class="cover">
                    <img :src="genre.cover" alt="" class="img" />
                    <div class="icon">
                        <img :src="genre.icon" alt="" />
                    </div>
                </div>
                <div class="title">
                    <div class="headline">
                        {{
                            $t(genre.name[0].toUpperCase() + genre.name.slice(1).toLowerCase())
                        }}
                        {{  $t('Music') }}
                    </div>
                    <div class="sub">
                        {{ $t('Here you can explore all') }} {{ $t(genre.name) }} {{ $t('Music') }}
                    </div>
                </div>
            </div>
        </div>
        <empty-page
            v-if="!genre && errorStatus == 404"
            :headline="$t('Not Available!')"
            :sub="$t('Genre does not exist!')"
            img="peep-68.png"
        >
            <template v-slot:button>
                <v-btn class="primary" rounded small @click="$router.go(-1)"
                    >{{ $t('Go Back') }}</v-btn
                >
            </template>
        </empty-page>
        <empty-page
            v-else-if="!genre && errorStatus"
            :headline="$t('Something wrong happened!')"
            :sub="$t('Some server error has occurred. Try again later.')"
            img="peep-68.png"
        >
            <template v-slot:button>
                <v-btn class="primary" rounded small @click="$router.go(-1)"
                    >{{ $t('Go Back') }}</v-btn
                >
            </template>
        </empty-page>
        <page-loading v-else-if="!genre && !songs && !errorStatus" />
        <div class="songs" v-else>
            <Grid :items="songs"></Grid>
            <div class="empty" v-if="songs && !songs.length">
                <empty-page
                    :headline="$t('No Content!')"
                    :sub="$t('Looks like there is no content yet for this genre.')"
                    img="peep-68.png"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    metaInfo() {
        return {
            title: this.generateMetaInfos(
                this.genre,
                this.$store.getters.getSettings.genrePageTitle,
                "genre"
            ),
            meta: [
                {
                    name: "description",
                    content: this.generateMetaInfos(
                        this.genre,
                        this.$store.getters.getSettings.genrePageDescription,
                        "genre"
                    )
                }
            ]
        };
    },
    data() {
        return {
            songs: null,
            genre: null,
            errorStatus: null
        };
    },
    created() {
        axios
            .get("/api/genre/" + this.$route.params.id)
            .then(res1 => {
                this.genre = res1.data;
                axios.get(`/api/genre-songs/${this.genre.id}`).then(res2 => {
                    this.songs = res2.data;
                });
            })
            .catch(e => {
                this.errorStatus = e.response.status;
            });
    }
};
</script>
<style lang="scss" scoped>
.genre-songs-grid {
    .songs-genre {
        display: flex;
        align-items: center;
        padding-left: 1em;
        padding-bottom: 1em;
        .cover {
            position: relative;
            .img {
                width: 100%;
                border-radius: 15px;
            }
            .icon {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                & > * {
                    width: 2em;
                }
            }
        }
        .name_and_sub {
            .name {
                font-size: 2.5em;
                line-height: 1.1;
            }
            .sub {
                font-size: 0.9em;
                opacity: 0.8;
            }
        }
    }
    .empty {
        padding-top: 2em;
    }
}
</style>
