<template>
    <div class="content-list-wrapper">
        <ul v-if="albums">
            <li
                v-for="(album, i) in albums"
                :key="i"
                @click="$store.dispatch('playAlbum', { album })"
            >
                <div class="item-cover">
                    <v-img :src="album.cover" class="img" aspect-ratio="1">
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
                <div class="item-title" @click.stop>
                    <router-link
                        class="router-link"
                        :to="{ name: 'album', params: { id: album.id } }"
                    >
                        {{ album.title }}
                    </router-link>
                </div>
                <div class="item-artist" @click.stop>
                    <router-link
                        class="router-link sub"
                        :to="{
                            name: 'artist',
                            params: { id: album.artist.id }
                        }"
                    >
                        {{ album.artist.displayname }}
                    </router-link>
                </div>
                <div class="options">
                    <div class="like-button">
                        <div
                            class="button-svg-container"
                            @click.stop="like(album.id)"
                        >
                            <v-icon color="primary" small>$vuetify.icons.heart</v-icon>
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
    props: ["albums"],
    methods: {
        like(album_id) {
            this.$store.dispatch("dislikeAlbum", album_id);
        }
    }
};
</script>
