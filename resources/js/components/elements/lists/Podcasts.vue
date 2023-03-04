<template>
    <div class="content-list-wrapper">
        <ul v-if="podcasts" class="item-list">
            <li
                class="item-list__item"
                v-for="(podcast, i) in podcasts"
                :key="i"
                @click="
                    $router.push({
                        name: 'podcast',
                        params: { id: podcast.id }
                    })
                "
            >
                <div class="item-cover">
                    <v-img :src="podcast.cover" class="img" aspect-ratio="1">
                        <template v-slot:placeholder>
                            <v-row
                                class="fill-height ma-0"
                                align="center"
                                justify="center"
                            >
                                <content-placeholders
                                    :rounded="true"
                                    style="width: 100%"
                                >
                                    <content-placeholders-img />
                                </content-placeholders>
                            </v-row>
                        </template>
                    </v-img>
                </div>
                <div class="item-title">{{ podcast.title }}</div>
                <div class="item-artist" v-if="podcast.artist && podcast.artist.id" >
                    <router-link
                        class="router-link"
                        :to="{
                            name: 'artist',
                            params: { id: podcast.artist.id }
                        }"
                    >
                        {{ podcast.artist.displayname }}
                    </router-link>
                    <span>{{ podcast.artist.displayname }}</span>
                </div>
                <div class="options">
                    <div class="like-button">
                        <div @click.stop="unfollow(podcast.id)">
                            <v-icon color="primary">$vuetify.icons.heart</v-icon>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <ul class="skeleton-list" v-else>
            <div class="skeleton" v-for="n in 10" :key="n">
                <content-placeholders :rounded="true">
                    <content-placeholders-img />
                </content-placeholders>
                <content-placeholders :rounded="true">
                    <content-placeholders-img />
                </content-placeholders>
            </div>
        </ul>
    </div>
</template>

<script>
export default {
    props: ["podcasts"],
    methods: {
        unfollow(podcast_id) {
            this.$store.dispatch("unfollowPodcast", podcast_id);
        }
    }
};
</script>
