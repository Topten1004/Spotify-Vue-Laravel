<template>
  <div class="genre content-item">
    <div class="content-item__header">
      <router-link
        v-if="!admin"
        :to="{
          name: 'genre-page',
          params: { genre_name: slug(genre.name), id: genre.id },
        }"
      >
        <div class="content-wrapper genre-content-wrapper">
          <div class="content">
            <img class="icon" :src="genre.icon" alt="" />
            <div class="content-text">
              {{ $t(genre.name) }}
            </div>
          </div>
          <slot name="control-layer" @click.stop=""></slot>
          <div class="genre-dark-layer dark-layer"></div>
          <div
            class="genre-img"
            :style="{ backgroundImage: 'url(' + genre.cover + ')' }"
          ></div>
        </div>
      </router-link>
      <div v-else class="content-wrapper genre-content-wrapper">
        <div class="content">
          <img class="icon" :src="genre.icon" alt="" />
          <div class="content-text">
            {{ $t(genre.name) }}
          </div>
        </div>
        <slot name="control-layer" @click.stop=""></slot>
        <div class="genre-dark-layer dark-layer"></div>
        <div class="genre-img">
          <v-img :src="genre.cover" class="img" aspect-ratio="1">
            <template v-slot:placeholder>
              <div class="fill-height">
                <content-placeholders :rounded="true">
                  <content-placeholders-img />
                </content-placeholders>
              </div>
            </template>
          </v-img>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["genre", "admin"],
};
</script>

<style lang="scss" scoped>
.genre {
  max-width: 240px;
  flex-basis: 20%;
  flex-wrap: wrap;
  min-width: 10em;
  @media screen and (max-width: 1300px) {
    flex-basis: 25%;
  }
  @media screen and (max-width: 1000px) {
    flex-basis: 33%;
  }
  @media screen and (max-width: 650px) {
    flex-basis: 50%;
    max-width: 600px;
  }
  @media screen and (max-width: 330px) {
    flex-basis: 100%;
    max-width: 300px;
  }
  .content-wrapper {
    overflow: hidden;
    border: 1px solid rgba(128, 128, 128, 0.253);
    border-radius: 7px;
    position: relative;
    .genre-dark-layer {
      display: flex;
      align-items: center;
      z-index: 1;
      border-radius: 7px;
      justify-content: center;
    }
    .content,
    .control-layer {
      display: flex;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      border-radius: 7px;
      bottom: 0;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 2;
      .icon {
        width: 2.4rem !important;
        margin-bottom: 1rem;
      }
      .content-text {
        font-size: 1.2rem !important;
        font-weight: bold;
        color: white;
      }
    }
    .control-layer {
      // display: none;
    }
  }
  .genre-img {
    transition: transform 0.5s;
    border-radius: 7px;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    height: 100%;
    width: 100%;
    background-size: cover;
  }
}

// .genre:hover,
// .hovered {
//   .genre-img {
//     //  transform: scale(1.08);
//     // .img {
//     //   transform: scale(1.08);
//     // }
//   }
//   .control-layer {
//     display: flex !important;
//     .svg-image {
//       width: 3em;
//     }
//   }
//   .content {
//     display: none !important;
//   }
// }
.genre-content-wrapper {
  padding-top: 100%;
}
</style>