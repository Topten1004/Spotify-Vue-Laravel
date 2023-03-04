<template>
    <div class="methods-wrapper">
        <v-card outlined>
            <v-card-title>
                <v-icon color="primary" x-large
                    >$vuetify.icons.account-cash-outline</v-icon
                >
                <v-btn
                    class="mx-2"
                    dark
                    small
                    color="primary"
                    @click="editPodcast('new')"
                >
                    <v-icon>$vuetify.icons.plus</v-icon> {{ $t("New") }}
                </v-btn>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <div class="admin-search-bar">
                    <v-text-field
                        v-model="search"
                        append-icon="mdi-magnify"
                        :label="$t('Search')"
                        single-line
                        hide-details
                    ></v-text-field>
                </div>
            </v-card-title>
            <v-data-table
                :no-data-text="$t('No data available')"
                :loading-text="$t('Fetching data') + '...'"
                :headers="headers"
                :items="methods || []"
                :items-per-page="25"
                class="elevation-1"
                :loading="!methods"
            >
                <template v-slot:item.minimum="{ item }">
                    <div class="order-price bold">
                        {{ price(item.minimum) + defaultCurrency.symbol }}
                    </div>
                </template>
                <template v-slot:item.operations="{ item }">
                    <v-btn
                        class="mx-2"
                        @click="editPM(item)"
                        x-small
                        fab
                        dark
                        color="info"
                    >
                        <v-icon>$vuetify.icons.pencil</v-icon>
                    </v-btn>
                    <v-btn
                        class="mx-2"
                        @click="deletePM(item.id)"
                        x-small
                        fab
                        dark
                        color="error"
                    >
                        <v-icon>$vuetify.icons.delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
        <v-dialog v-model="editDialog" max-width="500">
            <editPayoutMethodDialog
                :method="editingMethod"
                @updated="pmEdited"
                @created="pmCreated"
                @close="editDialog = false"
            ></editPayoutMethodDialog>
        </v-dialog>
    </div>
</template>
<script>
import Billing from "@mixins/billing/billing.js";
import editPayoutMethodDialog from "../../dialogs/admin/edit/PayoutMethod.vue";
export default {
    mixins: [Billing],
    components: {
        editPayoutMethodDialog
    },
    data() {
        return {
            methods: null,
            search: "",
            headers: [
                { text: this.$t("Name"), value: "name" },
                {
                    text: this.$t("Description"),
                    value: "description"
                },
                { text: this.$t("Minimum"), value: "minimum", align: "center" },
                {
                    text: this.$t("Operations"),
                    value: "operations",
                    align: "end"
                }
            ],
            editDialog: false,
            editingMethod: null
        };
    },
    created() {
        this.fetchMethods();
    },
    methods: {
        fetchMethods() {
            axios.get("/api/admin/payout-methods").then(res => {
                this.methods = res.data;
            });
        },
        editPM(page) {
            if (!page) {
                this.editDialog = false;
                this.editingMethod = {};
            } else if (page === "new") {
                this.editingMethod = {
                    new: true,
                    name: "",
                    minimum: 100,
                    description: ""
                };
                this.editDialog = true;
            } else {
                this.editingMethod = page;
                this.editDialog = true;
            }
        },
        deletePM(page_id) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this") +
                    " " +
                    this.$t("Payout method") +
                    "?"}`,
                button: {
                    no: this.$t("No"),
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        axios
                            .delete("/api/admin/payout-methods/" + page_id)
                            .then(res => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text:
                                        this.$t("Payout method") +
                                        " " +
                                        this.$t("Deleted") +
                                        "."
                                });
                                this.fetchMethods();
                            })
                            .catch();
                    }
                }
            });
        },
        pmEdited() {
            this.editDialog = false;
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Saved"),
                text: this.$t("Payout method") + " " + this.$t("Updated") + "."
            });
            this.fetchMethods();
        },
        pmCreated() {
            this.methods = null;
            this.editDialog = false;
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Created"),
                text: this.$t("Payout method") + " " + this.$t("Updated") + "."
            });
            this.fetchMethods();
        }
    }
};
</script>
