<template>
    <v-card class="page" v-if="!isLoading">
        <v-card-title>
            <div class="page__title">
                <v-icon color="primary" x-large>$vuetify.icons.credit-card-outline</v-icon>
                {{ $t("Billing") }}
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
        <v-container class="pl-5 pb-5">
            <v-row class="pl-4">
                <v-col cols="12">
                    <v-switch
                        v-model="settings.enableBilling"
                        :label="$t('Enable Billing')"
                    ></v-switch>
                </v-col>
                <!-- <v-col cols="6" v-if="settings.enableBilling">
                    <v-select outlined dense :label="$t('Default Currency')" :items="currencies" item-value="value" item-text="text" v-model="settings.default_currency" return-object></v-select>
                </v-col> -->
            </v-row>
            <template v-if="settings.enableBilling">
                <v-row>
                    <v-col cols="12" sm="6">
                        <v-card height="100%">
                            <v-card-title>
                                <v-img
                                    max-width="80"
                                    alt="Stripe"
                                    src="/storage/defaults/images/billing/stripe.png"
                                ></v-img>
                            </v-card-title>
                            <v-card-text>
                                <v-switch
                                    v-model="settings.stripeGateway"
                                    :label="'Stripe' + ' ' + $t('Gateway')"
                                ></v-switch>
                                <v-container v-if="settings.stripeGateway">
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field
                                                :label="$t('Stripe Client ID*')"
                                                outlined
                                                hide-details
                                                v-model="
                                                    settings.stripeClientID
                                                "
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-text-field
                                                type="password"
                                                :label="$t('Stripe Secret*')"
                                                outlined
                                                hide-details
                                                v-model="settings.stripeSecret"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-card height="100%">
                            <v-card-title>
                                <v-img
                                    max-width="90"
                                    alt="Paypal"
                                    src="/storage/defaults/images/billing/paypal-logo.png"
                                ></v-img>
                            </v-card-title>
                            <v-card-text>
                                <v-switch
                                    v-model="settings.paypalGateway"
                                    :label="'PayPal' + ' ' + $t('Gateway')"
                                ></v-switch>
                                <v-container v-if="settings.paypalGateway">
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field
                                                :label="$t('PayPal Client ID*')"
                                                hide-details
                                                outlined
                                                v-model="
                                                    settings.paypalClientID
                                                "
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-text-field
                                                type="password"
                                                :label="
                                                    $t('PayPal Client Secret*')
                                                "
                                                outlined
                                                hide-details
                                                v-model="settings.paypalSecret"
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-switch
                                                :label="
                                                    $t('PayPal Sandbox Mode')
                                                "
                                                v-model="
                                                    settings.paypalSandboxMode
                                                "
                                            ></v-switch>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </template>
             <!-- <v-row class="pl-4">
                <v-col cols="12">
                    <v-switch
                        v-model="settings.enableBilling"
                        :label="$t('Payout methods for artists')"
                        :messages="$t('From which the artists can select to receive their earnings.')"
                    ></v-switch>
                </v-col>
               <v-col cols="12">
                   <v-select multiple :item="payoutOptions" v-model="selectedPayoutMethods"></v-select>
               </v-col>
            </v-row> -->
        </v-container>
    </v-card>
    <page-loading v-else />
</template>
<script>
import currencies from '../../../../data/stripeSupportedCurrencies';
export default {
    props: ["settings", "isLoading"],
    computed: {
        selectedPayoutMethods: {
            get() {
                return this.payoutOptions.filter(pm => pm.active)
            },
            set(val) {
                console.log(val)
            }
        }
    },
    data() {
        return {
            currencies,
            isLoadingForButton: false,
            payoutOptions: []
        };
    },
    methods: {
        save() {
            if (
                (this.settings.enableBilling &&
                    !this.settings.stripeGateway &&
                    !this.settings.paypalGateway) ||
                (this.settings.enableBilling &&
                    this.settings.stripeGateway &&
                    (!this.settings.stripeClientID ||
                        !this.settings.stripeSecret)) ||
                (this.settings.enableBilling &&
                    this.settings.paypalGateway &&
                    (!this.settings.paypalClientID ||
                        !this.settings.paypalSecret))
            ) {
                return this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Oops!"),
                    text: this.$t("Please add your billing credentials.")
                });
            }
            this.isLoadingForButton = true;
            const formData = new FormData();
            formData.append(
                "enable_billing",
                this.settings.enableBilling ? 1 : 0
            );
            formData.append(
                "stripe_gateway",
                this.settings.stripeGateway ? 1 : 0
            );
            formData.append(
                "paypal_gateway",
                this.settings.paypalGateway ? 1 : 0
            );
            formData.append(
                "default_currency",
                JSON.stringify(this.settings.default_currency)
            );
            if (this.settings.stripeGateway) {
                formData.append(
                    "stripe_client_id",
                    this.settings.stripeClientID
                );
                formData.append("stripe_secret", this.settings.stripeSecret);
            }
            if (this.settings.paypalGateway) {
                formData.append(
                    "paypal_client_id",
                    this.settings.paypalClientID
                );
                formData.append("paypal_secret", this.settings.paypalSecret);
                formData.append(
                    "paypal_sandbox_mode",
                    this.settings.paypalSandboxMode ? 1 : 0
                );
            }
            axios
                .post("/api/admin/save-billing-settings", formData)
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Updated"),
                        text:
                            this.$t("Settings") +
                            " " +
                            this.$t("updated successfully.")
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                })
                .catch(e => {
                    if (e.response.data.message) {
                        this.$notify({
                            group: "foo",
                            type: "error",
                            title: this.$t("Error"),
                            text: e.response.data.message
                        });
                    }
                })
                .finally(() => {
                    this.isLoadingForButton = false;
                });
        }
    }
};
</script>
