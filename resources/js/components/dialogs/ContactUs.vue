<template>
    <v-card max-width="600" width="100%">
        <v-form
            ref="form"
            class="p-3"
            v-model="valid"
            lazy-validation
        >
            <v-text-field
                v-model="fullname"
                :label="$t('Full Name')"
                required
                outlined
            ></v-text-field>
            <v-text-field
                v-model="email"
                :rules="emailRules"
                :label="$t('E-mail')"
                required
                outlined
            ></v-text-field>
            <v-text-field
                v-model="subject"
                :rules="subjectRules"
                :label="$t('Subject')"
                required
                outlined
            ></v-text-field>
            <v-textarea
                v-model="message"
                :label="$t('Message')"
                rows="6"
                outlined
                required
            ></v-textarea>
            <v-btn
                :disabled="!valid || isLoading"
                :loading="isLoading"
                color="success"
                class="mr-4"
                @click="validateAndSend"
            >
              <template v-slot:loader>
                        <span class="custom-loader">
                            <v-icon light>$vuetify.icons.cached</v-icon>
                        </span>
                    </template>
                {{ $t("Send") }}
            </v-btn>
        </v-form>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            isLoading: false,
            valid: true,
            userMenu: false,
            fullname: "",
            message: "",
            email: "",
            subject: "",
            subjectRules: [
                v => !!v || this.$t("Subject is required"),
                v =>
                    (v && v.length > 3) ||
                    this.$t("Subject must contain be at least 4 characters")
            ],
            email: "",
            emailRules: [
                v => !!v || this.$t("E-mail is required"),
                v => /.+@.+\..+/.test(v) || this.$t("E-mail must be valid")
            ]
        };
    },
    methods: {
        validateAndSend() {
            this.isLoading = true;
            const formData = new FormData();
            formData.append("email", this.email);
            formData.append("subject", this.subject);
            formData.append("message", this.message);
            formData.append("fullname", this.fullname);
            axios
                .post("/api/contact-email", formData)
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Sent"),
                        text: this.$t("Email sent successfully.")
                    });
                    this.$emit('emailSent')
                })
                .catch(() => {
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: Object.values(e.response.data.errors).join(
                            "<br />"
                        )
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
};
</script>

<style></style>
