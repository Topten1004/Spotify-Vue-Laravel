<template>
    <div class="plans-wrapper">
        <v-card outlined>
            <v-card-title>
                <v-icon color="primary" x-large>$vuetify.icons.notebook-multiple</v-icon>
                <v-btn
                    class="mx-2"
                    dark
                    small
                    color="primary"
                    @click="addSubscription()"
                    v-if="hasPermission('CED plans')"
                >
                    <v-icon>$vuetify.icons.plus</v-icon> {{ $t('New') }}
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
                :items="plans || []"
                :items-per-page="25"
                class="elevation-1"
                :loading="!plans"
            >
                <template v-slot:item.active="{ item }">
                    <v-btn
                        x-small
                        dense
                        depressed
                        v-if="item.active"
                        class="success"
                        :title="$t('Active: the plan is available for users to subscribe')"
                        >{{$t('Active')}}</v-btn
                    >
                    <v-btn x-small dense depressed  :title="$t('Inactive: the plan is not available for subscriptions')" v-else class="error"
                        >{{$t('Inactive')}}</v-btn
                    >
                </template>
                <template v-slot:item.operations="{ item }">
                    <v-btn
                        class="mx-2"
                        @click="editPlan(item)"
                        v-if="hasPermission('CED plans')"
                        x-small
                        fab
                        dark
                        color="info"
                    >
                        <v-icon>$vuetify.icons.pencil</v-icon>
                    </v-btn>
                </template>
                <template v-slot:item.badge="{ item }">
                    <div class=" badge" v-if="item.badge">
                        <v-icon color="primary">$vuetify.icons.{{ item.badge }}</v-icon>
                    </div>
                    <template v-else>-</template>
                </template>
                <template v-slot:item.amount="{ item }">
                    <div class="plan__price" v-if="!item.free">
                        <div class="plan__price__currency">
                            {{ planCurrencySymbol(item) }}
                        </div>
                        <div class="plan__price__amount">
                            {{ price(item.amount) }}
                        </div>
                        <div class="plan__price__interval">
                            / {{ $t(item.interval) }}
                        </div>
                    </div>
                    <template v-else>{{ $t('Free') }}</template>
                </template>
                <template v-slot:item.updated_at="{ item }">
                    <div>
                        {{ moment(item.updated_at).fromNow() }}
                    </div>
                </template>
                <template v-slot:item.storage_space="{ item }">
                    <div>
                        {{ (item.storage_space / 1000).toFixed(2) }}
                    </div>
                </template>
            </v-data-table>
        </v-card>
        <v-dialog v-model="editDialog" max-width="900">
            <edit-plan-dialog
                v-if="editDialog"
                @updated="planUpdated"
                @close="closePlanDialog"
                :plan="editingPlan"
            />
        </v-dialog>
    </div>
</template>
<script>
import editPlanDialog from "../../dialogs/admin/edit/Plan";
export default {
    components: {
        editPlanDialog
    },
    data() {
        return {
            plans: null,
            search: "",
            headers: [
                { text: this.$t('Name'), value: "name" },
                { text: this.$t('Status'), value: "active", align: "center" },
                { text: this.$t('Price'), value: "amount", align: "center" },
                { text: this.$t('Badge'), value: "badge", align: "center" },
                { text: this.$t('Storage Space') + "(GB)", value: "storage_space", align: "center" },
                { text: this.$t('Operations'), value: "operations" }
            ],
            editDialog: false,
            editingPlan: null
        };
    },
    created() {
        this.fetchPlans();
    },
    methods: {
        fetchPlans() {
            axios.get("/api/admin/plans").then(res => {
                this.plans = res.data;
            });
        },
        // upcoming feature
        // deletePlan(id) {
        //     this.$confirm({
        //         message: `${this.$t("Deleting plans means new subscribers can’t be added. Existing subscribers aren’t affected. Are you sure you wanna delete this plan?")}`,
        //         button: {
        //             no: this.$t('No'),
        //             yes: this.$t("Yes")
        //         },
        //         /**
        //          * Callback Function
        //          * @param {Boolean} confirm
        //          */
        //         callback: confirm => {
        //             if (confirm) {
        //                 axios
        //                     .delete("/api/admin/plans/" + id)
        //                     .then(res => {
        //                         this.$notify({
        //                             group: "foo",
        //                             type: "success",
        //                             title: this.$t("Deleted"),
        //                             text: this.$t('Plan')  + " " + this.$t('Deleted') + "."
        //                         });
        //                         this.fetchPlans();
        //                     })
        //                     .catch();
        //             }
        //         }
        //     });
        // },
        editPlan(plan) {
            if (!plan) {
            } else if (plan === "new") {
            } else {
                this.editingPlan = plan;
                this.editDialog = true;
            }
        },
        addSubscription() {
            this.editingPlan = {
                // plan props
                new: true,
                storage_space: 100,
                amount: 0,
                interval: "month",
                recommended: false,
                free: true,
                active: true,
                displayed_features: [],
                roles: [],
                position: 1,
                permissions: [],
                currency: {
                    value: "USD",
                    text: 'United States Dollar(USD)',
                    symbol: "$"
                }
            };
            this.editDialog = true;
        },
        closePlanDialog() {
            this.editDialog = false;
            this.editingPlan = null;
        },
        planUpdated() {
            this.editDialog = false;
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Saved"),
                text: this.$t('Plan') + " " +  this.$t('saved successfully')
            });
            this.fetchPlans();
            this.$store.dispatch('fetchPlans');
        }
    }
};
</script>

<style lang="scss" scoped>
.plans-wrapper {
    .plan__price__amount {
        font-size: 1.5em;
    }
}
</style>
