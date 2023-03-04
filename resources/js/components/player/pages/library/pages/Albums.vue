<template>
  <div class="likes-wrapper">
      <div class="header">
          <div class="content-text">
            <div class="title">
                <h3>{{$t('Liked Albums')}}</h3>
            </div>
          </div>
      </div>
      <album-list :albums="albums" ></album-list>
      <empty-page v-if="albums && !albums.length" img="peep-34.png" :headline="$t('No likes yet!')" :sub="$t('This page is empty.')" />
  </div>
</template>
<script>
export default {
    created() {
        this.$store.dispatch('likes', 'album')
    },
    computed: {
        albums() {
            return this.$store.getters.getLikedAlbums
        }
    }
}
</script>
