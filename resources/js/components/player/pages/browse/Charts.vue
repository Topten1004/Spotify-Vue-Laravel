<template>
    <div id="main-content">
        <page-loading v-if="!sections" />
        <template v-else>
            <template
                v-if="
                    sections.length && contentLength
                "
            >
                <swiper-section
                    v-for="section in sections"
                    :key="section.rank"
                    @contentLength="contentLength+= $event"
                    :section="section"
                ></swiper-section>
            </template>
            <empty-page
                v-else
                :headline="$t('No Content!')"
                :sub="$t('There is content yet.')"
                img="peep-68.png"
            ></empty-page>
        </template>
    </div>
</template>
<script>
import EmptyPage from "../../../templates/EmptyPage.vue";
export default {
    components: { EmptyPage },
    data() {
        return {
            sections: null,
            contentLength: 0
        };
    },
    created() {
        axios.get("/api/charts").then(res => {
            var sections = res.data;
            if (!this.$store.getters.getSettings.enablePodcasts) {
                sections = sections.filter(
                    section => (section.endpoint && !section.endpoint.match(/podcasts/))
                );
            }
            this.sections = sections;
            this.contentLength = this.sections.length;
        });
    }
};
</script>
<style lang="scss" scoped>
#main-content {
    padding-top : 5rem;
}
</style>
