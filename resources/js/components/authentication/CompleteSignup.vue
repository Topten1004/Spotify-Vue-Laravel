<template>
  <authentification-template>
    <div class="register-wrapper">
      <div class="auth-box__title">
        <h2>{{$t('Complete Sign Up')}}</h2>
      </div>
      <div class="error-message-container">
        <v-chip
          class="ma-2"
          color="red"
          label
          v-if="error && !success"
          text-color="white"
        >
          {{ error }}
        </v-chip>
        <v-chip
          class="ma-2"
          color="success"
          label
          v-if="success"
          text-color="white"
        >
          <v-icon left>$vuetify.icons.checkbox-marked-circle-outline</v-icon>
          {{ success }}
        </v-chip>
      </div>
      <v-container>
        <v-row>
          <v-col cols="12" class="auth-box__inputs">
            <v-text-field
              v-model="profile.name"
              :rules="[rules.required]"
              light
             :label="$t('Name')"
              outlined
            ></v-text-field>
            <v-text-field
              v-model="profile.email"
              :rules="[rules.required]"
              light
             :label="$t('Email')"
              outlined
            ></v-text-field>
            <v-text-field
              :append-icon="showPassword1 ? 'mdi-eye' : 'mdi-eye-off'"
              :rules="[rules.required, rules.min]"
              :type="showPassword1 ? 'text' : 'password'"
              name="input-10-2"
             :label="$t('Password')"
              light
              v-model="profile.password1"
              hint="At least 6 characters"
              class="input-group--focused"
              outlined
              @click:append="showPassword1 = !showPassword1"
            ></v-text-field>
            <v-text-field
              :rules="[rules.required]"
              name="input-10-2"
              v-model="profile.password2"
              :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPassword2 ? 'text' : 'password'"
              @click:append="showPassword2 = !showPassword2"
              light
              @paste.prevent
             :label="$t('Confirm Password')"
              class="input-group--focused"
              outlined
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row justify="space-between" align="center" class="px-3" v-if="!$store.getters.getSettings.disableRegistration">
          <button
            @click="register"
            class="auth-box__auth-btn indigo v-btn v-btn--contained v-btn--block theme--dark v-size--default"
            :disabled="success ? true : false"
          >
            <div class="isLoading" v-if="isloading">
              <isLoading-circle size="24" />
            </div>
            <div v-else>{{$t('Sign Up')}}</div>
          </button>
        </v-row>
      </v-container>
    </div>
  </authentification-template>
</template>
<script>
import authentificationTemplate from "../templates/Authentication";
export default {
  metaInfo: {
    title:
      window.Settings.find((set) => set.key === "appName").value +
      " - Complete sign up",
  },
  props: ["profile", "driver"],
  components: {
    authentificationTemplate,
  },
  data() {
    return {
      error: "",
      success: "",
      isLoading: false,
      showPassword1: false,
      showPassword2: false,
      rules: {
        required: (value) => !!value || this.$t("Required."),
        min: (v) => v.length >= 8 || this.$t("Min 6 characters."),
        emailMatch: () => this.$t("Please enter a valid email."),
      },
    };
  },
  methods: {
    register() {
      this.isLoading = true;
      if (this.profile.password1 !== this.profile.password2) {
        this.error = this.$t("Password does not match!");
        this.isLoading = false;
      } else {
        this.$store
          .dispatch("login", { profile: this.profile, driver: this.driver })
          .then((res) => {
            this.success = this.$t("Logging in...");
          })
          .catch((e) => {
            if (e.response.data.errors) {
              this.error = Object.values(e.response.data.errors)[0][0];
            } else {
              this.error = e.response.data;
            }
          })
          .finally(() => (this.isLoading = false));
      }
    },
  },
};
</script>
