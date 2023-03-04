<template>
    <authentification-template>
        <div class="login-wrapper">
            <div class="auth-box__title">
                <h2>{{$t('Reset your password')}}</h2>
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
                <v-chip
                    class="ma-2"
                    color="success"
                    label
                    v-if="success"
                    text-color="white"
                >
                    <v-icon left>$vuetify.icons.checkmark</v-icon>
                    {{ success }}
                </v-chip>
            </div>
            <v-container>
                <v-row>
                    <v-col cols="12" class="auth-box__inputs">
                        <v-text-field
                            v-model="email"
                            light
                           :label="$t('Enter your login email')"
                            outlined
                        />
                    </v-col>
                   <v-col cols="12">
                        <v-btn
                            block
                            :loading="loading"
                            color="primary"
                            :disabled="loading"
                            @click="requestResetPassword"
                        >
                            {{ $t("Reset") }}
                            <template v-slot:loader>
                                <span class="custom-loader">
                                    <v-icon light>$vuetify.icons.cached</v-icon>
                                </span>
                            </template>
                        </v-btn>
                    </v-col>
                </v-row>
                <div class="divider wide-divider"></div>
                <div>
                    <div>
                        <div class="bold text-center">
                            <h3>{{$t('Remember you password?')}}</h3>
                        </div>
                        <div class="signup-button-con text-center mt-4">
                            <v-btn
                                color="primary"
                                outlined
                                @click="$router.push({ name: 'login' })"
                                >{{$t('Login to your account')}}</v-btn
                            >
                        </div>
                    </div>
                </div>
            </v-container>
        </div>
    </authentification-template>
</template>
<script>
import authentificationTemplate from "../templates/Authentication";
export default {
    metaInfo: {
        title:
            window.Settings.find(set => set.key === "appName").value +
            " - forget password"
    },
    components: {
        authentificationTemplate
    },
    data() {
        return {
            email: "",
            error: null,
            success: null,
            loading: false
        };
    },
    methods: {
        requestResetPassword() {
            this.loading = true;
            axios
                .post("/api/password/forgot", { email: this.email })
                .then(result => {
                    this.success = result.data.message;
                })
                .finally(() => (this.loading = false));
        }
    }
};
</script>
