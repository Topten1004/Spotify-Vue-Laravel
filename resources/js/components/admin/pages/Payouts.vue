<template>
    <div class="payouts-wrapper">
        <v-card outlined>
            <v-card-title>
                <v-icon color="primary" x-large
                    >$vuetify.icons.cash-check</v-icon
                >
                {{ $t("Payouts") }}
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
                :items="payouts || []"
                :items-per-page="25"
                class="elevation-1"
                :loading="!payouts"
            >
                <template v-slot:item.artist="{ item }">
                    <template v-if="item.artist">
                        <router-link
                            class="router-link"
                            :to="{ name: 'artist', params: { id: item.id } }"
                            target="_blank"
                        >
                            <div class="avatar">
                                <v-avatar size="35">
                                    <v-img :src="item.artist.avatar"></v-img>
                                </v-avatar>
                            </div>
                            <div class="artist-name">
                                {{ item.artist.displayname }}
                            </div>
                        </router-link>
                    </template>
                    <template v-else> - </template>
                </template>
                <template v-slot:item.amount="{ item }">
                    <div class="price bold success--text">
                        {{ price(item.amount) + defaultCurrency.symbol }}
                    </div>
                </template>
                <template v-slot:item.status="{ item }">
                    <v-btn
                        x-small
                        dense
                        outlined
                        v-if="item.status === 'payed'"
                        color="success"
                        >{{ $t("Payed") }}</v-btn
                    >
                    <v-btn
                        x-small
                        dense
                        outlined
                        v-else-if="item.status === 'cancelled'"
                        color="error"
                        >{{ $t("Rejected") }}</v-btn
                    >
                    <v-btn
                        x-small
                        dense
                        outlined
                        v-else-if="item.status === 'pending'"
                        color="warning"
                        >{{ $t("Pending") }}</v-btn
                    >
                    <v-btn
                        x-small
                        dense
                        outlined
                        v-else-if="item.status === 'requested'"
                        color="info"
                        >{{ $t("Requested") }}</v-btn
                    >
                </template>
                <template v-slot:item.created_at="{ item }">
                    <template v-if="item.created_at">
                        {{ moment(item.created_at).format("ll") }}
                    </template>
                    <template v-else>
                        -
                    </template>
                </template>
                <template v-slot:item.operations="{ item }">
                    <v-btn
                        class="mx-2"
                        @click="editPayout(item)"
                        v-if="hasPermission('Edit payouts')"
                        x-small
                        fab
                        dark
                        color="info"
                    >
                        <v-icon>$vuetify.icons.pencil</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
        <v-dialog v-model="editDialog" max-width="1200">
            <edit-payout-dialog :payout="editingPayout" @close="editDialog = false" @fetchPayouts="fetchPayouts">

            </edit-payout-dialog>
        </v-dialog>
    </div>
</template>
<script>
import EditPayoutDialog from "../../dialogs/admin/edit/Payout.vue";
import Billing from "@mixins/billing/billing";
export default {
    components: {
        EditPayoutDialog
    },
    mixins: [Billing],
    data() {
        return {
            payouts: null,
            search: "",
            headers: [
                { text: this.$t("Payout N°"), value: "id", align: "center" },
                { text: this.$t("َArtist"), value: "artist", align: "center" },
                { text: this.$t("Amount"), value: "amount", align: "center" },
                { text: this.$t("Status"), value: "status", align: "center" },
                {
                    text: this.$t("Created At"),
                    value: "created_at",
                    align: "center"
                },
                {
                    text: this.$t("Operations"),
                    value: "operations",
                    align: "center"
                }
            ],
            editDialog: false,
            editingPayout: null
        };
    },
    created() {
        this.fetchPayouts();
    },
    methods: {
        fetchPayouts() {
            axios.get("/api/admin/payouts").then(res => {
                this.payouts = res.data;
            });
        },
        editPayout(payout) {
            this.editDialog = true;
            this.editingPayout = payout;
        }
    }
};
</script>
