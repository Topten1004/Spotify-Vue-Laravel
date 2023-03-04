<template>
    <div class="sales-wrapper">
        <v-card outlined>
            <v-card-title>
                <v-icon color="primary" x-large>$vuetify.icons.shopping</v-icon> {{ $t('Sales') }}
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
                :items="sales || []"
                :items-per-page="25"
                class="elevation-1"
                :loading="!sales"
            >
                <template v-slot:item.user="{ item }">
                    <template v-if="item.user">
                        <div class="avatar">
                            <v-avatar size="35">
                                <v-img :src="item.user.avatar"></v-img>
                            </v-avatar>
                        </div>
                        <div class="artist-name">
                            {{ item.user.email }}
                        </div>
                    </template>
                </template>
                <template v-slot:item.total_price="{ item }">
                    <div class="order-price success--text bold">
                        {{ price(item.total_price) + defaultCurrency.symbol }}
                    </div>
                </template>
                <template v-slot:item.gateway="{ item }">
                    <template v-if="item.gateway == 'paypal'">
                        <div class="justify-center">
                            <v-img
                                max-width="80"
                                src="/storage/defaults/images/billing/paypal-logo.png"
                            ></v-img>
                        </div>
                    </template>
                    <template v-else-if="item.gateway == 'stripe'">
                        <div class="justify-center">
                            <v-img
                                max-width="80"
                                src="/storage/defaults/images/billing/stripe.png"
                            ></v-img>
                        </div>
                    </template>
                    <template v-else>
                        -
                    </template>
                </template>
                <template v-slot:item.operations="{ item }">
                    <v-btn
                        x-small
                        fab
                        color="info"
                        @click="
                            sale = item;
                            detailsDialog = true;
                        "
                    >
                        <v-icon>$vuetify.icons.file-find</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
        <v-dialog v-model="detailsDialog" max-width="500">
            <v-card v-if="sale">
                <v-card-title>
                    {{ $t("Sale details") }}
                </v-card-title>
                <div class="p-3">
   <div cols="12">
                            {{ $t("Buyer") }}
                            <v-divider></v-divider>
                        </div>
                        <div cols="12">
                            <div class="d-flex align-center">
                                <div class="user-avatar">
                                    <v-avatar size="60">
                                        <v-img :src="sale.user.avatar"></v-img>
                                    </v-avatar>
                                </div>
                                <div class="user-details p-2 pl-3">
                                    <v-container>
                                        <v-row align="center">
                                            <div class="name">
                                                <strong
                                                    >{{ $t("Name") }}:
                                                </strong>
                                                {{ sale.user.name }}
                                            </div>
                                        </v-row>
                                        <v-row>
                                            <div class="email">
                                                <strong
                                                    >{{ $t("Email") }}:
                                                </strong>
                                                {{ sale.user.email }}
                                            </div>
                                        </v-row>
                                    </v-container>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3">
                            {{ $t("Bought items") }}
                            <v-divider></v-divider>
                        </div>
                        <div cols="12">
                            <v-simple-table>
                                <template v-slot:default>
                                    <thead>
                                        <tr>
                                            <th class="text-left">
                                                Product
                                            </th>
                                            <th class="text-left">
                                                License
                                            </th>
                                            <th class="text-left">
                                                Payed
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(product, n) in sale.products"
                                            :key="n"
                                        >
                                            <td>
                                                <v-list-item
                                                >
                                                    <v-list-item-avatar
                                                        width="35"
                                                        height="35"
                                                        rounded="0"
                                                        class="asset-shadow"
                                                    >
                                                        <img
                                                            :src="
                                                                product.item
                                                                    .cover
                                                            "
                                                        />
                                                    </v-list-item-avatar>
                                                    <v-list-item-content>
                                                        <v-list-item-title>{{
                                                            product.item.title
                                                        }}</v-list-item-title>
                                                        <v-list-item-subtitle>{{
                                                            getMainArtist(
                                                                product.item
                                                            )
                                                        }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </td>
                                            <td>{{ product.price.name }}</td>
                                            <td>
                                                <div
                                                    class="price success--text bold"
                                                >
                                                    {{
                                                        price(
                                                            product.price.amount
                                                        ) +
                                                            product.price
                                                                .currency_symbol
                                                    }}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                        </div>
                </div>
                     
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
import BillingMixin from "../../../mixins/billing/billing";
export default {
    mixins: [BillingMixin],
    data() {
        return {
            sales: null,
            search: "",
            headers: [
                { text: this.$t("User"), value: "user", align: "center" },
                {
                    text: this.$t("Total"),
                    value: "total_price",
                    align: "center"
                },
                { text: this.$t("Gateway"), value: "gateway", align: "center" },
                {
                    text: this.$t("Details"),
                    value: "operations",
                    align: "center"
                }
            ],
            detailsDialog: false,
            sale: null
        };
    },
    created() {
        this.fetchSales();
    },
    methods: {
        fetchSales() {
            axios.get("/api/admin/sales").then(res => {
                this.sales = res.data;
            });
        }
    }
};
</script>
