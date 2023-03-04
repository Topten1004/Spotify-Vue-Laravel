<template>
    <authentification-template v-if="!loadingPage">
        <div class="login-wrapper">
            <div class="auth-box__title">
                <h2>{{ $t("Login to your account") }}</h2>
            </div>
            <div class="error-message-container">
                <v-chip
                    class="ma-2"
                    color="red"
                    label
                    v-if="error"
                    text-color="white"
                >
                    <v-icon left>$vuetify.icons.alert</v-icon>
                    {{ error }}
                </v-chip>
            </div>
            <v-container>
                <v-row>
                    <v-col cols="12" class="auth-box__inputs">
                        <v-text-field
                            v-model="email"
                            :label="$t('Email')"
                            outlined
                        ></v-text-field>
                        <v-text-field
                            :append-icon="
                                showPassword ? 'mdi-eye' : 'mdi-eye-off'
                            "
                            class="mt-3"
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            :label="$t('Password')"
                            outlined
                            hide-details
                            @click:append="showPassword = !showPassword"
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row justify="end" align="center" class="py-3">
                    <v-col cols="12">
                        <v-btn
                            block
                            :loading="loading"
                            color="primary"
                            :disabled="loading"
                            @click="login"
                        >
                            {{ $t("Login") }}
                            <template v-slot:loader>
                                <span class="custom-loader">
                                    <v-icon>$vuetify.icons.cached</v-icon>
                                </span>
                            </template>
                        </v-btn>
                    </v-col>
                </v-row>
                <v-row class="text-center">
                    <div class="link-text flex-grow-1">
                        <span
                            class="text"
                            @click="$router.push({ name: 'forgot_password' })"
                        >
                            {{ $t("Forgot your password?") }}
                        </span>
                    </div>
                </v-row>
                <div
                    class="justify-center mt-2"
                    v-if="
                        $store.getters.getSettings.facebook_oauth_client_id ||
                        ($store.getters.getSettings.enableGoogleLogin &&
                            $store.getters.getSettings.google_oauth_client_id)
                    "
                >
                    <div class="divider divider__small">
                        <strong class="divider__text"> {{ $t("Or") }} </strong>
                    </div>
                </div>
                <v-row
                    class="text-center"
                    v-if="$store.getters.getSettings.enableGoogleLogin"
                >
                    <v-col>
                        <div class="justify-center">
                            <div
                                class="g_id_signin px-2"
                                data-type="standard"
                                v-if="
                                    $store.getters.getSettings
                                        .google_oauth_client_id
                                "
                            ></div>
                        </div>
                    </v-col>
                </v-row>
                <div class="divider wide-divider"></div>
                <div v-if="!$store.getters.getSettings.disableRegistration">
                    <div>
                        <div class="bold text-center">
                            <h3>{{ $t("You do not have an account yet?") }}</h3>
                        </div>
                        <div class="signup-button-con text-center mt-4">
                            <v-btn
                                color="primary"
                                outlined
                                @click="$router.push({ name: 'register' })"
                                >{{ $t("Open New Account") }}</v-btn
                            >
                        </div>
                    </div>
                </div>
            </v-container>
        </div>
    </authentification-template>
    <LoadingBackground v-else />
</template>
<script>
import LoadingBackground from "../LoadingBackground";
import authentificationTemplate from "../templates/Authentication";
// import { VFBLoginScope as VFacebookLoginScope } from "vue-facebook-login-component";
export default {
    metaInfo: {
        title:
            window.Settings.find((set) => set.key === "appName").value +
            " - Login to your account",
    },
    components: {
        LoadingBackground,
        authentificationTemplate,
        // VFacebookLoginScope
    },
    data() {
        return {
            email: "",
            password: "",
            remember: false,
            error: "",
            loading: false,
            loadingPage: false,
            showPassword: false,
        };
    },
    created() {},
    methods: {
        async login() {
            this.loading = true;
            try {
                await this.$store.dispatch("login", {
                    email: this.email,
                    password: this.password,
                });
            } catch (e) {
                this.loading = false;
            }
        },
    },
};
</script>
