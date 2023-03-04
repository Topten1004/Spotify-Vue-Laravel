<template>
    <section class="section-container swiper-section" v-if="section && !serverError" v-show="contentLength">
        <div class="section-header" v-if="_title">
            <div class="section-header__title">{{ $t(_title) }}</div>
            <div class="chevrons d-flex">
            </div>
        </div>
        <div class="section-header-small-screen">
            <div class="section-header__title">{{ $t(_title) }}</div>
        </div>
        <swiper :options="swiperOptions" v-if="isContentLoading">
            <swiper-slide v-for="n in 6" :key="n">
                <div class="song-expo-skeleton">
                    <content-placeholders :rounded="true">
                        <content-placeholders-img />
                        <content-placeholders-text :lines="2" />
                    </content-placeholders>
                </div>
            </swiper-slide>
        </swiper>
        <swiper
            ref="mySwiper"
            :options="swiperOptions"
            @slideChange="onSlideChange"
            v-else
        >
            <swiper-slide v-for="(asset, i) in _content" :key="i">
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
                <template v-else-if="asset.type === 'genre'">
                    <genre :genre="asset" />
                </template>
                <template v-else-if="asset.type === 'radio-station'">
                    <radio-station :radioStation="asset" />
                </template>
            </swiper-slide>
        </swiper>
        <div
            class="swiper-prev-el"
            :id="'swiper-button-prev-' + section.id"
            slot="button-prev"
            v-show="prevBtn"
        >
            <v-btn fab small height="35" width="35">
                <v-icon>
                    $vuetify.icon.chevron-left
                </v-icon>
            </v-btn>
        </div>
        <div
            class="swiper-next-el"
            :id="'swiper-button-next-' + section.id"
            slot="button-next"
            v-show="nextBtn"
        >
            <v-btn fab height="35" width="35">
                <v-icon>
                    $vuetify.icon.chevron-right
                </v-icon>
            </v-btn>
        </div>
    </section>
</template>
<script>
import { swiper, swiperSlide } from "vue-awesome-swiper";
import "swiper/css/swiper.css";
export default {
    created() {
        if (!this.content) {
            this.getContent();
        } else {
            this.contentLength = this.content.length;
            this.$emit("contentLength", this.content.length);
        }
    },
    props: {
        section: {
            type: Object,
            default: function () {
                return {
                    id: Math.floor(Math.random(25) * 25)
                }
            }
        },
        content: { type: Array, default: null },
        rank: String | Number,
        title: {
            type: String,
            default: ""
        },
        _breakpoints: {
            type: Object,
            default: function() {
                return {
                    1500: {
                        slidesPerView: 5,
                        slidesPerGroup: 5,
                        spaceBetweenSlides: 20
                    },
                    1200: {
                        slidesPerView: 4,
                        slidesPerGroup: 4,
                        spaceBetweenSlides: 20
                    },
                    750: {
                        slidesPerView: 3,
                        slidesPerGroup: 3,
                        spaceBetweenSlides: 10
                    },
                    500: {
                        slidesPerView: 2,
                        slidesPerGroup: 2,
                        spaceBetweenSlides: 10
                    }
                }
            }
        }
    },
    components: {
        swiper,
        swiperSlide
    },
    computed: {
        _content() {
            return this.contents || this.content || this.section.content;
        },
        _title() {
            return this.title || this.section.title;
        },
        _rank() {
            return this.rank || this.section.rank;
        }
    },
    data() {
        return {
            _swiper: null,
            prevBtn: false,
            nextBtn: true,
            contents: null,
            contentLength: 0,
            serverError: false,
            isContentLoading: false,
            swiperOptions: {
                slidesPerView: 6,
                spaceBetween: 0,
                slidesPerGroup: 5,
                navigation: {
                    nextEl: "#swiper-button-next-" + this.section.id,
                    prevEl: "#swiper-button-prev-" + this.section.id
                },
                breakpoints: this._breakpoints
            }
        };
    },
    methods: {
        getContent() {
            this.serverError = false
            this.isContentLoading = true
            axios.get("/api/section/content/" + this.section.id).then(res => {
                this.contents = res.data;
                this.contentLength = res.data.length;
                this.$emit("contentLength", res.data.length ? 0 : -1);
                this.$emit("content");
            })  
            .catch(() => {
                this.contents = [];
                this.serverError = true
            })
            .finally(() => this.isContentLoading = false)
        },
        onSlideChange() {
            this.prevBtn = !this.$refs.mySwiper.swiper.isBeginning;
            this.nextBtn = !this.$refs.mySwiper.swiper.isEnd;
        }
    }
};
</script>

<style lang="scss">
.swiper-next-el {
    position: absolute;
    right: -8px;
    top: 40%;
    z-index: 3;
}
.swiper-prev-el {
    position: absolute;
    left: -14px;
    top: 40%;
    z-index: 3;
}
.song-expo-skeleton {
    padding: .3em;
}
.swiper-section 
.vue-content-placeholders-img
{
    padding-top: 100%;
}

</style>
