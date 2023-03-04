<template>
    <fixed-left v-if="album">
        <template v-slot:img>
            <div class="img-cover">
                <v-img :src="album.cover" class="img" aspect-ratio="1">
                    <template v-slot:placeholder>
                        <div class="fixed-area__image-skeleton">
                            <content-placeholders :rounded="true">
                                <content-placeholders-img />
                            </content-placeholders>
                        </div>
                    </template>
                </v-img>
            </div>
        </template>
        <template v-slot:infos>
            <div class="info-wrapper">
                <div class="title">
                    <div class="info-badge">{{ $t("Album") }}</div>
                    <div class="title__title">
                        <div class="align-center justify-between">
                            <div class="title">
                                {{ album.title }}
                            </div>
                            <div class="product-btn">
                                <product-btn :item="album"></product-btn>
                            </div>
                        </div>
                    </div>
                    <div class="sub">
                        <artists
                            class="py-3"
                            :artists="album.artists"
                            :withAvatar="true"
                        ></artists>
                        <span class="bold album-release-date">{{
                            moment(album.release_date).format("ll")
                        }}</span>
                        <div class="separator"></div>
                        <span>{{ album.songs? (album.songs.length == 1 ? '' : album.songs.length) : 0 }} {{ album.songs && album.songs.length == 1? $t("Single"): $t("Tracks") }}</span>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:buttons>
            <div class="buttons">
                <div class="btn-container">
                    <v-btn
                        class="play primary"
                        rounded
                        small
                        @click="$store.dispatch('playAlbum', { album })"
                    >
                        <v-icon left>$vuetify.icons.play</v-icon>
                        {{ $t("Play") }}</v-btn
                    >
                </div>
                <div class="btn-container">
                    <v-btn
                        rounded
                        small
                        @click="likeAlbum"
                        v-if="!isLiked"
                    >
                        <v-icon small>$vuetify.icons.heart-outline</v-icon>
                    </v-btn>
                    <v-btn
                        rounded
                        small
                        @click="likeAlbum"
                        v-else
                    >
                        <v-icon color="primary" small>$vuetify.icons.heart</v-icon>
                    </v-btn>
                </div>
                <div class="btn-container">
                    <v-btn
                        small
                        class="secondary"
                        rounded
                        @click="
                            $store.commit('shareItem', {
                                cover: album.cover,
                                url: getItemURL(album),
                                title: album.title,
                                type: album.type,
                                artist: getMainArtist(album)
                            })
                        "
                    >
                        <v-icon small>$vuetify.icons.share</v-icon>
                    </v-btn>
                </div>
            </div>
            <div class="pt-4">
                    <external-links :content="album"></external-links>
            </div>
        </template>
        <template v-slot:main>
            <div
                class="songs-container"
                v-if="album.songs && album.songs.length"
            >
                <List :isAlbum="true" :content="album.songs" class="song-list"  headers="true" ranked="true" />
            </div>
            <empty-page
                img="peep-68.png"
                :headline="$t('No Songs!')"
                :sub="$t('This album is empty.')"
                v-else-if="album.id"
            >
            </empty-page>
        </template>
    </fixed-left>
    <empty-page
        v-else-if="!album && errorStatus == 404"
        :headline="$t('Not Available!')"
        :sub="$t('Album') + ' ' + $t('does not exist.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" small rounded @click="$router.go(-1)">
                {{ $t("Go back") }}
            </v-btn>
        </template>
    </empty-page>
    <empty-page
        v-else-if="!album && errorStatus"
        :headline="$t('Something wrong happend!')"
        :sub="$t('Some server error has occurred. Try again later.')"
        img="peep-68.png"
    >
        <template v-slot:button>
            <v-btn class="primary" small rounded @click="$router.go(-1)">
                {{ $t("Go back") }}
            </v-btn>
        </template>
    </empty-page>
    <page-loading v-else />
</template>
<script>
import ProductBtn from '../../elements/other/ProductBtn.vue';
export default {
    metaInfo() {
        return {
            // title: this.generateMetaInfos(
            //     this.album,
            //     this.$store.getters.getSettings.albumPageTitle,
            //     "album"
            // ),
            meta: [
                // {
                //     name: "description",
                //     content: this.generateMetaInfos(
                //         this.album,
                //         this.$store.getters.getSettings.albumPageDescription,
                //         "album"
                //     )
                // },
                {
                    property: 'og:title',
                    content: this.album.title
                },
                {
                    property: 'og:description',
                    content: this.album.description
                },
                {
                    property: 'og:image',
                    content: this.album.cover
                },
                {
                    property: 'og:image:width',
                    content: '300'
                },
                {
                    property: 'og:image:height',
                    content: '300'
                }
            ]
        };
    },
    created() {
        this.fetchAlbum();
    },
    data() {
        return {
            album: {},
            errorStatus: null
        };
    },
    components: {
        ProductBtn
    },
    computed: {
        isLiked() {
            return (this.$store.getters.getLikedAlbums || []).some(
                album => album.id === this.album.id
            );
        }
    },
    methods: {
        fetchAlbum() {
            axios
                .get("/api/album/" + this.$route.params.id)
                .then(res => (this.album = res.data))
                .catch(e => (this.errorStatus = e.response.status));
        },
        likeAlbum() {
            if (!this.isLiked) {
                this.$store
                    .dispatch("like", {
                        type: "album",
                        id: this.album.id,
                        origin: this.album.origin
                    })
                    .catch(() => {});
            } else {
                this.$store
                    .dispatch("dislike", {
                        type: "album",
                        id: this.album.id,
                        origin: this.album.origin
                    })
                    .catch(() => {});
            }
        }
    }
};
</script>
