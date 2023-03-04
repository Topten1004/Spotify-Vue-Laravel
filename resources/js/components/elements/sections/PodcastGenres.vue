<template>
    <div class="podcast-genres-wrapper">
        <div
            @click="
                $router.push({
                    name: 'podcasts.genre',
                    params: { genre_slug: genre.slug, genre_id: genre.id }
                })
            "
            class="podcast-genre"
            v-for="genre in genres"
            :key="genre.id"
        >
            <div class="cover">
                <v-img :src="genre.cover" class="img podcast-genre-img" aspect-ratio="2.5">
                    <template v-slot:placeholder>
                        <div class="fill-height">
                            <content-placeholders :rounded="true">
                                <content-placeholders-img />
                            </content-placeholders>
                        </div>
                    </template>
                </v-img>
            </div>
            <div class="name">
                {{ $t(genre.name) }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["genres"]
};
</script>
<style lang="scss" scoped>
.podcast-genres-wrapper {
    display: flex;
    overflow-x: auto;
    overflow-y: hidden;
    flex-wrap: wrap;
    max-width: 1500px;
    padding-bottom: 0.5em;
    &::-webkit-scrollbar {
        height: 5px;
        width: 5px;
    }
    .podcast-genre {
        cursor: pointer;
        flex-basis: auto;
        min-width: 244px;
        max-width: 250px;
        padding: 0.2em 0.3em;
        position: relative;
        .name {
            position: absolute;
            color: white;
            font-size: 1.1em;
            font-weight: 600;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .cover {
            .podcast-genre-img {
                border-radius: 12px;
            }
            img {
                width: 100%;
            }
        }
        @media screen and (max-width: 900px) {
            flex-basis: 33.33%;
            min-width: auto;
            .podcast-genre-img {
                border-radius: 8px;
            }
        }
        @media screen and (max-width: 650px) {
            flex-basis: 50%;
            min-width: auto;
        }
    }
}
</style>
