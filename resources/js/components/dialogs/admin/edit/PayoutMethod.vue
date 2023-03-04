<template>
    <edit-dialog
        @callToAction="save"
        @cancel="$emit('close')"
        :callToActionText="$t('Save')"
        :editing="'Payout Method'"
    >
        <template v-slot:body>
            <div class="edit-dialog__body">
                <v-container>
                    <v-text-field
                        :label="$t('Name')"
                        outlined
                        v-model="formData.name"
                    ></v-text-field>
                    <v-textarea
                        :label="$t('Description')"
                        outlined
                        v-model="formData.description"
                    ></v-textarea>
                    <div class="d-flex align-center">
                        <v-text-field
                            :label="$t('Amount')"
                            hint="Important: the amount should be in cents ( 1$ = 100 )"
                            :value="0"
                            class="pr-2"
                            v-model="formData.minimum"
                        ></v-text-field>
                        <div>
                            <div class="plan__price">
                                <div class="plan__price__currency">
                                    {{ defaultCurrency.symbol }}
                                </div>
                                <div class="plan__price__amount">
                                    {{ price(formData.minimum) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </v-container>
            </div>
        </template>
    </edit-dialog>
</template>

<script>
import Billing from "@mixins/billing/billing";
export default {
    props: ["method"],
    mixins: [Billing],
    data() {
        return {
            formData: {
                ...this.method
            }
        };
    },
    methods: {
        save() {
            this.isLoading = true;
            var formData = new FormData();

            Object.keys(this.formData).forEach(key => {
                formData.append(key, this.formData[key]);
            });

            if (this.method.new) {
                axios
                    .post("/api/admin/payout-methods", formData)
                    .then(() => {
                        this.$emit("created");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.$notify({
                            group: "foo",
                            type: "error",
                            title: this.$t("Error"),
                            text: e.response.data.message
                        });
                    });
            } else {
                formData.append("_method", "PUT");
                axios.post("/api/admin/payout-methods/" + this.method.id, formData)
                    .then(() => {
                        this.$emit("updated");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.$notify({
                            group: "foo",
                            type: "error",
                            title: this.$t("Error"),
                            text: e.response.data.message
                        });
                    });
            }
        }
    }
};
</script>
