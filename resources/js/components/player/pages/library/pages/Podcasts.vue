<template>
  <div class="likes-wrapper">
      <div class="header">
          <div class="content-text">
            <div class="title">
                <h3>{{$t('Followed Podcasts')}}</h3>
            </div>
          </div>
      </div>
      <template>
          <podcast-list :podcasts="podcasts"></podcast-list>
      </template>
      <template v-if="podcasts && !podcasts.length">
          <empty-page img="peep-41.png" :headline="$t('No Podcasts!')" :sub="$t('You did not follow any podcasts yet!')" />
      </template>
  </div>
</template>
<script>
export default {
    data() {
        return {
            podcasts: []
        };
    },
    created() {
        this.fetchFollowedPodcasts();
    },
    methods: {
        fetchFollowedPodcasts() {
            axios.get("/api/user/follows/podcast").then((res) => {
                this.podcasts = res.data;
            });
        }
    }
}
</script>
