<template>
  <v-card class="page" v-if="!isLoading">
    <v-card-title>
      <div class="page__title">
        <v-icon color="primary" x-large>$vuetify.icons.account-key-outline</v-icon>
        {{$t('Authentication')}}
      </div>
      <div class="page__options">
        <v-btn
          small
          class="success"
          @click="save"
          :disabled="isLoadingForButton"
          >{{ $t('Save') }}</v-btn
        >
      </div>
    </v-card-title>
    <v-container>
      <v-row>
        <v-col cols="12">
          <v-switch
            v-model="settings.disableRegistration"
          :label="$t('Disable Registration')"
          :messages="$t('Remove user registration from the application')"
        ></v-switch>
        </v-col>
        <v-col cols="12">
          <v-switch
            v-model="settings.requireAuth"
          :label="$t('Require Authentication')"
          :messages="$t('Require authentication will force visitors to authenticate.')"
        ></v-switch>
        </v-col>
        <v-divider></v-divider>
        <v-col cols="12">
           <v-switch
          v-model="settings.enableGoogleLogin"
         :label="$t('Google OAuth')"
        ></v-switch>
      <v-text-field
       :label="$t('Google Client ID')"
        outlined
        :rules="googleClientIdRule"
        v-if="settings.enableGoogleLogin"
        v-model="settings.google_oauth_client_id"
        hint="xxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com"
      ></v-text-field>
      <!-- <v-divider></v-divider> -->
      <!-- <v-row class="pl-4">
        <v-switch
          v-model="settings.enableFacebookLogin"
         :label="$t('Facebook Login')"
        ></v-switch>
      </v-row>
      <v-text-field
       :label="$t('Facebook Client ID')"
        outlined=""
        v-if="settings.enableFacebookLogin"
        v-model="settings.facebook_oauth_client_id"
      ></v-text-field> -->
        </v-col>
      </v-row>
    </v-container>
  </v-card>
  <page-loading v-else />
</template>
<script>
export default {
  props: ["settings", "isLoading", "isLoadingForButton"],
  data() {
    return {
      googleClientIdRule: [
        (v) =>
          v.substr(v.length - 27) === ".apps.googleusercontent.com" ||
          "Please add a valid Google OAuth Client ID.",
      ],
    };
  },
  methods: {
    save() {
      this.$emit("save-settings");
    },
  },
};
</script>
