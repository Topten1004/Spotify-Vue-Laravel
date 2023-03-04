<template>
  <div
    class="artist"
    v-if="artist"
    @click="$router.push({ name: 'artist', params: { id: artist.id } })"
  >
    <div class="image">
      <v-img :src="artist.avatar" class="img" aspect-ratio="1">
        <template v-slot:placeholder>
          <div class="fill-height">
            <content-placeholders :rounded="true">
              <content-placeholders-img />
            </content-placeholders>
          </div>
        </template>
      </v-img>
    </div>

    <div class="name" v-if="artist">
      {{ artist.displayname }}
    </div>
  </div>
</template>

<script>
export default {
  props: ["artist"],
};
</script>

<style lang="scss" scoped>
.artist {
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  flex-direction: column;
  min-width: 8em;
  .image {
    width: 100%;
    max-width: 140px;
    overflow: hidden;
    border-radius: 50%;
    .img {
      width: 100%;
      transition: all 0.5s;
    }
  }
  .name {
    font-weight: 600;
    text-align: center;
    margin-top: 0.5rem;
  }
  &:hover {
    .img {
      transform: scale(1.05);
    }
  }
}
</style>
