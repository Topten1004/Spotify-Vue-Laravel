<template>
  <v-card class="page" v-if="!isLoading">
    <v-card-title>
      <div class="page__title">
        <v-icon color="primary" x-large>$vuetify.icons.email-multiple</v-icon>
        {{ $t("Email") }}
      </div>
      <div class="page__options">
        <v-btn
          small
          class="success"
          @click="save"
          :disabled="isLoadingForButton"
          :loading="isLoadingForButton"
          >{{ $t("Save") }}
          <template v-slot:loader>
            <span class="custom-loader">
              <v-icon light>$vuetify.icons.cached</v-icon>
            </span>
          </template>
        </v-btn>
      </div>
    </v-card-title>
    <v-container fluid>
      <v-row class="pl-4">
        <v-col cols="12" sm="6">
          <v-switch
            v-model="settings.enableMail"
            :label="$t('Enable Emails')"
            :messages="
              $t(
                'Enable to receive Emails from users. Enable to verify users Email addresses.'
              )
            "
          ></v-switch>
        </v-col>
      </v-row>
      <template v-if="settings.enableMail">
        <v-row>
          <v-col md="6" cols="12">
            <v-text-field
              :label="$t('From Address')"
              :messages="
                $t('The address that the users receive an Email from you.')
              "
              outlined
              v-model="settings.mailFromAddress"
            ></v-text-field
          ></v-col>
          <v-col md="6" cols="12">
            <v-text-field
              :label="$t('From Name')"
              :messages="
                $t(
                  'The name that the users will see when they receive an Email from you.'
                )
              "
              outlined
              v-model="settings.mailFromName"
            ></v-text-field
          ></v-col>
          <v-col cols="12">
            <v-select
              :label="$t('Mailer')"
              outlined
              :items="mailers"
              item-value="value"
              item-text="text"
              v-model="settings.mailMailer"
            ></v-select
          ></v-col>

          <template v-if="settings.mailMailer === 'smtp'">
            <v-col md="6" cols="12">
              <v-text-field
                :label="$t('SMTP Host')"
                outlined
                v-model="settings.mailHost"
              ></v-text-field
            ></v-col>
            <v-col md="6" cols="12">
              <v-text-field
                :label="$t('SMTP Username')"
                outlined
                v-model="settings.mailUsername"
              ></v-text-field
            ></v-col>
            <v-col md="6" cols="12">
              <v-text-field
                :label="$t('SMTP Password')"
                type="password"
                outlined
                v-model="settings.mailPassword"
              ></v-text-field
            ></v-col>
            <v-col md="6" cols="12">
              <v-text-field
                :label="$t('SMTP Port')"
                outlined
                v-model="settings.mailPort"
              ></v-text-field
            ></v-col>
            <v-col cols="12" md="6">
              <v-select
                outlined
                :label="$t('Encryption')"
                :items="['tls', 'ssl']"
                v-model="settings.mailEncryption"
              ></v-select>
            </v-col>
          </template>
          <template v-if="settings.mailMailer === 'mailgun'">
            <v-col md="6" cols="12">
              <v-text-field
                :label="$t('Domain')"
                outlined
                :messages="$t('Usually your domain (Not API base URL)')"
                v-model="settings.mailgunURL"
              ></v-text-field
            ></v-col>
            <v-col md="6" cols="12">
              <v-text-field
                :label="$t('API Key')"
                outlined
                v-model="settings.mailgunAPIKey"
              ></v-text-field
            ></v-col>
          </template>
          <!-- <v-col cols="12" md="6">
                        <v-text-field
                            :label="$t('Contact Email Address')"
                            outlined
                            v-model="settings.contactEmailAddress"
                            @change="settings.mailFromAddress = $event"
                        ></v-text-field>
                    </v-col> -->

          <v-col>
            <v-alert type="error" v-if="detailedError">
              <h2>{{ $t("Detailed error message") }}</h2>
              <div>
                {{ detailedError }}
              </div>
            </v-alert>
          </v-col>
        </v-row>
      </template>
    </v-container>
  </v-card>
  <page-loading v-else />
</template>
<script>
export default {
  props: ["settings", "isLoading"],
  data() {
    return {
      isLoadingForButton: false,
      detailedError: "",
      mailers: [
        {
          text: "SMTP",
          value: "smtp",
        },
        {
          text: "Mailgun API",
          value: "mailgun",
        },
      ],
    };
  },
  methods: {
    save() {
      this.detailedError = "";
      if (this.settings.enableMail) {
        if (
          this.settings.mailMailer === "smtp" &&
          this.settings.mailHost &&
          this.settings.mailPort &&
          this.settings.mailUsername &&
          this.settings.mailPassword &&
          this.settings.mailFromAddress &&
          this.settings.mailEncryption &&
          this.settings.mailFromName
        ) {
        } else if (
          this.settings.mailMailer === "mailgun" &&
          this.settings.mailgunURL &&
          this.settings.mailgunAPIKey &&
          this.settings.mailFromAddress &&
          this.settings.mailFromName
        ) {
        } else {
          this.$notify({
            group: "foo",
            type: "error",
            title: this.$t("Oops!"),
            text: this.$t("Please fill all the information."),
          });
          return;
        }
      }
      this.isLoadingForButton = true;
      const formData = new FormData();
      formData.append("enable_mail", this.settings.enableMail ? 1 : "");
      formData.append(
        "contact_email_address",
        this.settings.contactEmailAddress
      );
      formData.append("mailMailer", this.settings.mailMailer);
      if (this.settings.mailMailer === "smtp") {
        formData.append("username", this.settings.mailUsername.toString().trim());
        formData.append("password", this.settings.mailPassword.toString().trim());
        formData.append("encryption", this.settings.mailEncryption);
        formData.append("host", this.settings.mailHost.toString().trim());
        formData.append("port", this.settings.mailPort.toString().trim());
      } else if (this.settings.mailMailer === "mailgun") {
        formData.append("mailgunAPIKey", this.settings.mailgunAPIKey.toString().trim());
        formData.append("mailgunURL", this.settings.mailgunURL.toString().trim());
      }
      formData.append("from_address", this.settings.mailFromAddress.toString().trim());
      formData.append("from_name", this.settings.mailFromName.toString().trim());
      axios
        .post("/api/admin/save-mail-settings", formData)
        .then(() => {
          this.$notify({
            group: "foo",
            type: "success",
            title: this.$t("Updated"),
            text: this.$t("Settings") + " " + this.$t("updated successfully."),
          });
        })
        .catch((e) => {
          this.detailedError = e.response.data.detailed_message;
          // this.$notify({
          //     group: "foo",
          //     type: "error",
          //     title: this.$t("Error"),
          //     text: Object.values(e.response.data.errors).join(
          //         "<br />"
          //     )
          // });
        })
        .finally(() => {
          this.isLoadingForButton = false;
        });
    },
  },
};
</script>
