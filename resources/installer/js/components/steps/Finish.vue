<template>
  <v-container>
      <div class="installation w-100 h-100">
        <div class="installation__icon py-3">
            <v-icon x-large color="success">$vuetify.icons.check-circle-outline</v-icon>
        </div>
        <div class="installation__text bold success--text">
            Congratulation! Installation was successful.
        </div>
        <div class="installation__action my-5  text-center">
           <v-btn small color="primary" @click="goToLogin">
              Login to admin account
           </v-btn>
        </div>
      </div>
  </v-container>
</template>

<script>
export default {
  methods: {
    goToLogin() {
      return window.location.href = '/login'
    }
  }
}
</script>

<style lang="scss">
.installation {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-image: url('/images/background.svg');
  background-size: cover;
  &__text {
    font-size: 1.25em;
  }
}
.w-100 {
  width: 100%;
}
.h-100 {
  height: 100ù;
}
</style>