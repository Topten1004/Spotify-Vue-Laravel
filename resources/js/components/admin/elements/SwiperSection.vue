<template>
    <v-card class="section-container section-skeleton  swiper-section" rounded>
        <v-card outlined class="section-option section-option__drag handle">
            <v-icon>$vuetify.icons.cursor-move</v-icon>
        </v-card>
        <v-card
            @click="$emit('edit')"
            outlined
            class="section-option section-option__edit"
        >
            <v-icon color="#039BE5">$vuetify.icons.pencil</v-icon>
        </v-card>
        <v-card
            @click="$emit('delete')"
            outlined
            class="section-option section-option__delete"
        >
            <v-icon color="error">$vuetify.icons.delete</v-icon>
        </v-card>
        <div class="section-header">
            <div class="section-header__title">
                {{ $t(section.title) }}
            </div>
            <div class="section-header__chevrons" v-if="section.swipable">
                <v-icon :id="'swiper-button-zuruck-' + section.id"
                    >$vuetify.icons.chevron-left</v-icon
                >
                <v-icon :id="'swiper-button-naschte-' + section.id"
                    >$vuetify.icons.chevron-right</v-icon
                >
            </div>
        </div>
        <div class="section-body" :style="{ sectionWidth }">
        <swiper ref="mySwiper" :options="swiperOptions" v-if="!contents">
            <swiper-slide v-for="n in 6" :key="n">
                <div class="song-expo-skeleton">
                    <content-placeholders :rounded="true">
                        <content-placeholders-img />
                        <content-placeholders-text :lines="2" />
                    </content-placeholders>
                </div>
            </swiper-slide>
        </swiper>
        <swiper ref="mySwiper" :options="swiperOptions" v-else>
            <template v-if="contents.length">
                <swiper-slide
                    v-for="(asset, i) in contents"
                    :key="i"
                >
                    <template v-if="asset.type === 'song'">
                        <song-expo :song="asset" />
                    </template>
                    <template v-else-if="asset.type === 'album'">
                        <album-expo :album="asset" />
                    </template>
                    <template v-else-if="asset.type === 'podcast'">
                        <podcast-box :podcast="asset" />
                    </template>
                    <template v-else-if="asset.type === 'playlist'">
                        <playlist-expo :playlist="asset" />
                    </template>
                    <template v-else-if="asset.type === 'radio-station'">
                        <radio-station :radioStation="asset" />
                    </template>
                    <template v-else-if="asset.type === 'genre'">
                        <genre :genre="asset" :admin="true" />
                    </template>
                </swiper-slide>
            </template>
            <template v-else>
                <div class="empty-section-text muted text-center">
                    <template v-if="section.endpoint">
                        {{ $t("No Data!") }}
                    </template>
                    <template v-else>
                        {{ $t("Empty Section") }}
                    </template>
                </div>
            </template>
        </swiper>
        </div>

    </v-card>
</template>
<script>
import { swiper, swiperSlide } from "vue-awesome-swiper";
import "swiper/css/swiper.css";
export default {
    props: ["section"],
    components: {
        swiper,
        swiperSlide
    },
    created() {
        this.getContent()
    },
    mounted() {
        setTimeout(() => {
            // trigering the section with to fix a bug related with API ( data renderes after the comp mount )
            this.sectionWidth = 1000;
        }, 200);
    },
    data() {
        return {
            loading: true,
            sectionWidth: 0,
            contents: null,
            swiperOptions: {
                slidesPerView: 5,
                spaceBetween: 10,
                navigation: {
                    nextEl: "#swiper-button-naschte-" + this.section.id,
                    prevEl: "#swiper-button-zuruck-" + this.section.id
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },
                breakpoints: {
                    1920: {
                        slidesPerView: 7,
                        spaceBetweenSlides: 10
                    },
                    1680: {
                        slidesPerView: 5,
                        spaceBetweenSlides: 10
                    },
                    1100: {
                        slidesPerView: 4,
                        spaceBetweenSlides: 10
                    },
                    900: {
                        slidesPerView: 3,
                        spaceBetweenSlides: 10
                    },
                    500: {
                        slidesPerView: 2,
                        spaceBetweenSlides: 10
                    }
                }
            }
        };
    },
    methods: {
     getContent() {
        axios.get('/api/section/content/' + this.section.id)
        .then((res) => {
            this.contents = res.data;
        })
    },
    }
};
</script>
<style lang="scss" scoped>
.section-header {
    width: 100% !important;
    &__title {
        margin-bottom: 0 !important;
    }
}
.empty-section-text {
    font-size: 1.2em;
    padding: 0.2em;
}
</style>
