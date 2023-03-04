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
                           :label="$t('Email')"
                            outlined
                        />
                        <v-text-field
                            v-model="password"
                            type="password"
                            light
                           :label="$t('New Password')"
                            outlined
                        />
                        <v-text-field
                            v-model="password_confirmation"
                            type="password"
                            light
                           :label="$t('Confirm Password')"
                            outlined
                        />
                    </v-col>
                    <v-col cols="12">
                        <v-btn
                            block
                            :loading="loading"
                            color="primary"
                            :disabled="loading"
                            @click="resetPassword"
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
            " - Reset password"
    },
    components: {
        authentificationTemplate
    },
    data() {
        return {
            error: null,
            success: "",
            password: "",
            email: "",
            password_confirmation: "",
            loading: false
        };
    },
    methods: {
        resetPassword() {
            this.loading = true;
            axios
                .post("/api/password/reset", {
                    token: this.$route.params.token,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                })
                .then(() => {
                    this.success =
                        "Password reset successfully. Redirecting to login...";
                    setTimeout(() => {
                        this.$router.push({ name: "login" });
                    }, 1500);
                })
                .catch(error => {
                    this.error = Object.values(error.response.data.errors).join(
                        "<br />"
                    );
                })
                .finally(() => (this.loading = false));
        }
    }
};
</script>
