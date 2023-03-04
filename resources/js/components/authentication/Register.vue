<template>
    <authentification-template>
        <div class="register-wrapper">
            <div class="auth-box__title">
                <h2>{{ $t("Create An Account") }}</h2>
            </div>
            <div class="error-message-container">
                <v-alert
                    class="ma-2"
                    type="error"
                    v-if="error && !success"
                    text-color="white"
                    dense
                >
                    {{ error }}
                </v-alert>
                <v-alert
                    class="ma-2"
                    icon="$vuetify.icons.checkbox-marked-circle-outline"
                    v-if="success"
                    dense
                    type="success"
                    text-color="white"
                >
                    {{ success }}
                </v-alert>
            </div>
            <v-container>
                <v-row>
                    <v-col cols="12" class="auth-box__inputs">
                        <v-text-field
                            v-model="name"
                            :rules="[rules.required]"
                            :label="$t('Name')"
                            outlined
                        ></v-text-field>
                        <v-text-field
                            v-model="email"
                            :rules="[rules.required]"
                            :label="$t('Email')"
                            outlined
                        ></v-text-field>
                        <v-text-field
                            :rules="[rules.required, rules.min]"
                            :type="showPassword1 ? 'text' : 'password'"
                            name="input-10-2"
                            :label="$t('Password')"
                            v-model="password1"
                            :hint="$t('At least 8 characters')"
                            class="input-group--focused"
                            outlined
                            @click:append="showPassword1 = !showPassword1"
                        >
                            <template v-slot:append>
                                <v-icon v-if="showPassword1">
                                    $vuetify.icons.eye-outline
                                </v-icon>
                                <v-icon v-else>
                                    $vuetify.icons.eye-outline-off
                                </v-icon>
                            </template>
                        </v-text-field>

                        <v-text-field
                            :append-icon="
                                showPassword2
                                    ? '$vuetify.icons.eye-outline'
                                    : '$vuetify.icons.eye-outline-off'
                            "
                            :rules="[rules.required]"
                            :type="showPassword2 ? 'text' : 'password'"
                            name="input-10-2"
                            v-model="password2"
                            @paste.prevent
                            :label="$t('Confirm Password')"
                            class="input-group--focused"
                            outlined
                            @click:append="showPassword2 = !showPassword2"
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row align="center" class="py-3">
                    <v-col cols="12">
                        <v-btn
                            block
                            :loading="loading"
                            color="primary"
                            :disabled="Boolean(loading || success)"
                            @click="register"
                        >
                            {{ $t("Register") }}
                            <template v-slot:loader>
                                <span class="custom-loader">
                                    <v-icon>$vuetify.icons.cached</v-icon>
                                </span>
                            </template>
                        </v-btn>
                    </v-col>
                </v-row>
                <div
                    class="center-content"
                    v-if="
                        $store.getters.getSettings.facebook_oauth_client_id ||
                        $store.getters.getSettings.facebook_oauth_client_id
                    "
                >
                    <div class="divider divider__small">
                        <strong class="divider__text"> {{ $t("Or") }} </strong>
                    </div>
                </div>
                <v-row
                    class="text-center"
                    v-if="
                        $store.getters.getSettings.facebook_oauth_client_id ||
                        $store.getters.getSettings.facebook_oauth_client_id
                    "
                >
                    <v-col>
                        <div class="justify-center">
                            <v-facebook-login-scope
                                v-if="
                                    $store.getters.getSettings
                                        .facebook_oauth_client_id
                                "
                                :app-id="
                                    $store.getters.getSettings
                                        .facebook_oauth_client_id
                                "
                                @login="logInWithFacebook($event)"
                                @sdk-init="handleSdkInit"
                            >
                                <v-btn
                                    class="px-2"
                                    slot-scope="scope"
                                    color="#3578E5"
                                    dark
                                    @click="scope.login"
                                >
                                    <v-icon left
                                        >$vuetify.icons.facebook</v-icon
                                    >
                                    {{ $t("Sign In") }}
                                </v-btn>
                            </v-facebook-login-scope>
                            <div
                                class="g_id_signin px-2"
                                data-type="standard"
                                v-if="
                                    $store.getters.getSettings
                                        .facebook_oauth_client_id
                                "
                            ></div>
                        </div>
                    </v-col>
                </v-row>
                <div class="divider wide-divider"></div>
                <div>
                    <div>
                        <div class="bold text-center">
                            <h3>{{ $t("Already have an account?") }}</h3>
                        </div>
                        <div class="signup-button-con text-center mt-4">
                            <v-btn
                                @click="$router.push({ name: 'login' })"
                                :color="success ? 'success' : 'primary'"
                                :outlined="!success"
                                >{{ $t("Login to your account") }}</v-btn
                            >
                        </div>
                    </div>
                </div>
                <template v-if="$store.getters.getSettings.account_agreement">
                    <div class="divider wide-divider"></div>
                    <div
                        class="agreement text-center"
                        v-html="$store.getters.getSettings.account_agreement"
                    ></div>
                </template>
            </v-container>
        </div>
    </authentification-template>
</template>

<script>
import authentificationTemplate from "../templates/Authentication";
// import { VFBLoginScope as VFacebookLoginScope } from "vue-facebook-login-component";
export default {
    metaInfo: {
        title:
            window.Settings.find((set) => set.key === "appName").value +
            " - Create new account",
    },
    components: {
        authentificationTemplate,
        // VFacebookLoginScope
    },
    data() {
        return {
            email: "",
            name: "",
            password1: "",
            password2: "",
            error: "",
            success: "",
            loading: false,
            showPassword1: false,
            showPassword2: false,
            rules: {
                required: (value) => !!value || this.$t("Required."),
                min: (v) => v.length >= 8 || this.$t("Min 8 characters"),
                emailMatch: () => this.$t("Please enter a valid email."),
            },
        };
    },
    methods: {
        register() {
            this.error = "";
            this.loading = true;
            if (this.password1 !== this.password2) {
                this.error = this.$t("Password does not match!");
                this.loading = false;
            } else {
                axios
                    .post("/api/register", {
                        email: this.email,
                        password: this.password1,
                        name: this.name,
                    })
                    .then((res) => {
                        this.success =
                            res.data.message ||
                            this.$t(
                                "Account created successfully. You can login now."
                            );
                    })
                    .catch((e) => {
                        // if (e.response.data.errors) {
                        //     this.error = Object.values(
                        //         e.response.data.errors
                        //     )[0];
                        // } else {
                        //     this.error = e.response.data;
                        // }
                    })
                    .finally(() => (this.loading = false));
            }
        },
        // upcoming feature
        // logInWithFacebook(res) {
        //   axios
        //     .get(
        //       `https://graph.facebook.com/me?fields=name,email,picture&access_token=${res.authResponse.accessToken}`
        //     )
        //     .then((result) => {
        //       let profile = {
        //         email: result.data.email,
        //         avatar: result.data.picture.data.url,
        //         name: result.data.name,
        //         id: result.data.id,
        //       };
        //       this.$store.dispatch("login", { driver: "facebook", profile });
        //     });
        // },
        handleSdkInit({ scope }) {
            this.$store.commit("setFbLogoutFunction", scope.logout);
        },
    },
};
</script>
