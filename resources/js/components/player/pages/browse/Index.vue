<template>
    <div class="browse-container content-page">
        <div class="icon-header">
            <div class="cover">
                <v-icon dark>$vuetify.icons.compass</v-icon>
            </div>
            <div class="title">
                <div class="headline">{{ $t('Browse') }}</div>
                <div class="sub">
                    {{ $t('Explore and listen to various genres & moods.') }}
                </div>
            </div>
        </div>
        <div class="options-menu">
            <ul id="navigation-list">
                <span class="underline" id="navigation-underline"></span>
                <li id="navigation-genres" @click="moveUnderline(0)">
                    <router-link to="/browse" class="router-link">
                        {{ $t('Genres & Moods') }}
                    </router-link>
                </li>
                <li
                    id="charts"
                    @click="moveUnderline(1)"
                    v-if="$store.getters.getSettings.enableCharts"
                >
                    <router-link
                        :to="{ name: 'browse.charts' }"
                        class="router-link"
                    >
                        {{ $t('Charts') }}
                    </router-link>
                </li>
            </ul>
        </div>
        <div id="main-content">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
export default {
    metaInfo() {
        return {
            title: this.generateMetaInfos(
                {},
                this.$store.getters.getSettings.browsePageTitle
            ),
            meta: [
                {
                    name: "description",
                    content: this.generateMetaInfos(
                        {},
                        this.$store.getters.getSettings.browsePageDescription
                    )
                }
            ]
        };
    },
    mounted() {
        this.moveUnderline(this.$route.path.match("chart") ? 1 : 0);
    },
    methods: {
        moveUnderline(index) {
            const coordinates = [];
            const $0 = document.getElementById("navigation-genres");
            const underline = document.getElementById("navigation-underline");
            underline.style.width = "4px";
            const listOffset = document
                .getElementById("navigation-list")
                .getBoundingClientRect().left;
            coordinates.push({
                left: $0.getBoundingClientRect().left,
                length: $0.offsetWidth
            });
            if (this.$store.getters.getSettings.enableCharts) {
                const $1 = document.getElementById("charts");
                coordinates.push({
                    left: $1.getBoundingClientRect().left,
                    length: $1.offsetWidth
                });
            }
            setTimeout(() => {
                underline.style.width = "2rem";
            }, 500);
            underline.style.left =
                coordinates[index].length / 2 +
                coordinates[index].left -
                listOffset +
                "px";
        }
    }
};
</script>
<style lang="scss">
@import "../../../../../sass/player/pages/browse";
</style>
