<template>
    <div class="new-artist-account" v-if="artist && artist.id">
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                     <v-card>
                        <v-card-title>
                            <v-icon color="primary" large class="mr-3"
                                >$vuetify.icons.cash-account</v-icon
                            >
                            {{ $t("Funds") }}
                            <v-spacer></v-spacer>
                            <v-spacer></v-spacer>
                            <div
                                class="total-earnings__value price bold success--text"
                            >
                                {{
                                    price(artist.funds) + defaultCurrency.symbol
                                }}
                            </div>
                        </v-card-title>
                     </v-card>
                </v-col>
                <v-col cols="12">
                    <v-card>
                        <div class="payouts">
                            <v-card-title>
                                <v-icon color="primary" large class="mr-3">
                                    $vuetify.icons.cash-check
                                </v-icon>
                                {{ $t("Payouts") }}
                                <v-spacer></v-spacer>
                                <v-spacer></v-spacer>
                                <v-btn
                                    class="success"
                                    small
                                    :disabled="!artist.funds"
                                    @click="requestPayoutDialog = true"
                                    >{{ $t("Request Payout") }}</v-btn
                                >
                            </v-card-title>
                            <v-simple-table>
                                <template v-slot:default>
                                    <thead>
                                        <tr>
                                            <th class="text-left">
                                                {{$t('Payout N°')}}
                                            </th>
                                            <th class="text-left">
                                                {{$t('Amount')}}
                                            </th>
                                            <th class="text-left">
                                                {{$t('Status')}}
                                            </th>
                                            <th class="text-left">
                                                {{$t('Details')}}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="payout in artist.payouts"
                                            :key="payout.id"
                                        >
                                            <td class="text-left">
                                                {{ payout.id }}
                                            </td>
                                            <td class="text-left bold success--price">
                                                {{
                                                    price(payout.amount) +
                                                        defaultCurrency.symbol
                                                }}
                                            </td>
                                            <td class="text-left">
                                                <v-btn
                                                    x-small
                                                    outlined
                                                    :color="
                                                        payout.status ===
                                                        'requested'
                                                            ? 'info'
                                                            : payout.status ===
                                                              'pending'
                                                            ? 'warning'
                                                            : payout.status === 'cancelled' ?
                                                            'error'  
                                                            : 'success'
                                                    "
                                                >
                                                    {{ payout.status }}
                                                </v-btn>
                                            </td>
                                            <td class="text-left">
                                                {{ payout.details ||  '-'}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <v-icon color="primary" large class="mr-3"
                                >$vuetify.icons.currency-usd</v-icon
                            >
                            {{ $t("Total Earnings") }}
                            <v-spacer></v-spacer>
                            <v-spacer></v-spacer>
                            <div
                                class="total-earnings__value price bold success--text"
                            >
                                {{
                                    price(totalSalesProfit + totalRoyaltiesProfit) + defaultCurrency.symbol
                                }}
                            </div>
                        </v-card-title>
                        <v-card-text>
                            <div class="title py-3">
                                {{ $t("Details") }}
                            </div>
                            <v-divider></v-divider>
                            <div class="details">
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-card>
                                                <v-container>
                                                    <v-row>
                                                        <v-col cols="12">
                                                            <v-card-title>
                                                                {{
                                                                    $t("Sales")
                                                                }}
                                                                <v-spacer></v-spacer>
                                                                <v-spacer></v-spacer>
                                                                <div
                                                                    class="total-earnings__value bold price small success--text"
                                                                >
                                                                    {{
                                                                        price(
                                                                            totalSalesProfit
                                                                        ) +
                                                                            defaultCurrency.symbol
                                                                    }}
                                                                </div>
                                                            </v-card-title>

                                                            <v-divider></v-divider>
                                                        </v-col>
                                                        <v-col cols="12">
                                                            <v-simple-table>
                                                                <template
                                                                    v-slot:default
                                                                >
                                                                    <thead>
                                                                        <tr>
                                                                            <th
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    $t(
                                                                                        "Product"
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                            <th
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    $t(
                                                                                        "License"
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                            <th
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    $t(
                                                                                        "Price"
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                            <th
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    $t(
                                                                                        "Earned"
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr
                                                                            v-for="(sale,
                                                                            n) in artist.sales"
                                                                            :key="
                                                                                n
                                                                            "
                                                                        >
                                                                            <td>
                                                                                <v-list-item>
                                                                                    <v-list-item-avatar
                                                                                        width="35"
                                                                                        height="35"
                                                                                        rounded="0"
                                                                                        class="asset-shadow"
                                                                                    >
                                                                                        <img
                                                                                            :src="
                                                                                                sale.cover
                                                                                            "
                                                                                        />
                                                                                    </v-list-item-avatar>
                                                                                    <v-list-item-content>
                                                                                        <v-list-item-title
                                                                                            >{{
                                                                                                sale.itemTitle
                                                                                            }}</v-list-item-title
                                                                                        >
                                                                                    </v-list-item-content>
                                                                                </v-list-item>
                                                                            </td>
                                                                            <td
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    sale.priceName
                                                                                }}
                                                                            </td>
                                                                            <td
                                                                                class="text-left"
                                                                            >
                                                                                <div
                                                                                    class="price success--text bold"
                                                                                >
                                                                                    {{
                                                                                        price(
                                                                                            sale.amount
                                                                                        ) +
                                                                                            sale.priceSymbol
                                                                                    }}
                                                                                </div>
                                                                            </td>
                                                                            <td
                                                                                class="text-left"
                                                                            >
                                                                                <div
                                                                                    class="price success--text bold"
                                                                                >
                                                                                    {{
                                                                                        price(
                                                                                            (sale.amount *
                                                                                                sale.artist_cut) /
                                                                                                100
                                                                                        ) +
                                                                                            sale.priceSymbol
                                                                                    }}
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </template>
                                                            </v-simple-table>
                                                        </v-col>
                                                    </v-row>
                                                </v-container>
                                            </v-card>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-card>
                                                <v-container>
                                                    <v-row>
                                                        <v-col cols="12">
                                                            <v-card-title>
                                                                {{
                                                                    $t(
                                                                        "Royalties"
                                                                    )
                                                                }}
                                                                <v-spacer></v-spacer>
                                                                <v-spacer></v-spacer>
                                                                <div
                                                                    class="total-earnings__value price small bold success--text"
                                                                >
                                                                    {{
                                                                        price(
                                                                            totalRoyaltiesProfit
                                                                        ) +
                                                                            defaultCurrency.symbol
                                                                    }}
                                                                </div>
                                                            </v-card-title>

                                                            <v-divider></v-divider>
                                                        </v-col>
                                                        <v-col cols="12">
                                                            <v-simple-table>
                                                                <template
                                                                    v-slot:default
                                                                >
                                                                    <thead>
                                                                        <tr>
                                                                            <th
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    $t(
                                                                                        "Total Plays"
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                            <th
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    $t(
                                                                                        "Artist Royalty"
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                            <th
                                                                                class="text-left"
                                                                            >
                                                                                {{
                                                                                    $t(
                                                                                        "Total"
                                                                                    )
                                                                                }}
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr
                                                                            v-for="(royaltyGroup,
                                                                            i) in artist.royalties"
                                                                            :key="
                                                                                i
                                                                            "
                                                                        >
                                                                            <td>
                                                                                {{
                                                                                    royaltyGroup.total_royalties
                                                                                }}
                                                                            </td>
                                                                            <td>
                                                                                {{
                                                                                    price(
                                                                                        royaltyGroup.price
                                                                                    ) +
                                                                                        defaultCurrency.symbol
                                                                                }}
                                                                                {{
                                                                                    "(" +
                                                                                        $t(
                                                                                            "for each 100 play"
                                                                                        ) +
                                                                                        ")"
                                                                                }}
                                                                            </td>
                                                                            <td>
                                                                                <div
                                                                                    class="price success--text bold"
                                                                                >
                                                                                    {{
                                                                                        price(
                                                                                            royaltyGroup.total_royalties *
                                                                                                (royaltyGroup.price /
                                                                                                    100)
                                                                                        ) +
                                                                                            defaultCurrency.symbol
                                                                                    }}
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </template>
                                                            </v-simple-table>
                                                        </v-col>
                                                    </v-row>
                                                </v-container>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
        <v-dialog v-model="requestPayoutDialog" max-width="500">
            <v-card>
                <v-card-title>
                    {{ $t("Request Payout") }}
                </v-card-title>
                <v-card-text>
                    <div class="card-content">
                        <div class="payout-option">
                            <v-select
                                :items="payoutOptions"
                                item-text="name"
                                :label="$t('Select payout method')"
                                item-value="id"
                                v-model="artist.payout_method"
                                return-object
                            ></v-select>
                        </div>
                        <v-text-field
                            :label="$t('Address')"
                            v-model="payoutDetails"
                            v-if="artist.payout_method.id"
                        ></v-text-field>
                        <div class="align-justify-center"  v-if="artist.payout_method.id">
                            <v-text-field
                                :label="$t('Enter payout amount(in cents)')"
                                type="number"
                                class="pr-2"
                                :messages="
                                    $t('Minimum') + ': ' +
                                        price(artist.payout_method.minimum) +
                                        defaultCurrency.symbol +
                                        ' | ' +
                                        $t('Available') + ': ' +
                                        price(artist.funds) +
                                        defaultCurrency.symbol
                                "
                                v-model="artist.payoutAmount"
                            ></v-text-field>
                            <div>
                                <div class="plan__price">
                                    <div class="plan__price__currency">
                                        {{ defaultCurrency.symbol }}
                                    </div>
                                    <div class="plan__price__amount">
                                        {{ price(artist.payoutAmount) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="description pt-5" v-html="artist.payout_method.description"></div>
                    </div>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        class="success"
                        @click="requestPayout"
                        :loading="payoutRequestLoading"
                        :disabled="payoutRequestLoading"
                        >{{ $t("Request") }}</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
    <page-loading v-else />
</template>
<script>
import Billing from "@mixins/billing/billing";
export default {
    created() {
        axios
            .get("/api/artist")
            .then(res => {
                (this.artist = res.data)
                if( this.artist.payout_method ) {
                    this.payoutDetails = this.artist.payout_method.pivot.payout_details;
                }
            })
            .catch(e => (this.artist = {}));

        axios
            .get("/api/artist/payout-options")
            .then(res => (this.payoutOptions = res.data))
            .catch(e => (this.payoutOptions = {}));
    },
    mixins: [Billing],
    data() {
        return {
            artist: null,
            loading: false,
            requestPayoutDialog: false,
            payoutRequestLoading: false,
            payoutOptions: [],
            payoutDetails: ''
        };
    },
    computed: {
        totalSalesProfit() {
            return this.artist.sales.reduce(
                (acc, val) => acc + (val.amount * val.artist_cut) / 100,
                0
            );
        },
        totalRoyaltiesProfit() {
            return this.artist.royalties.reduce(
                (acc, val) => acc + val.total_royalties * (val.price / 100),
                0
            );
        }
    },
    methods: {
        requestPayout() {
            if (!this.artist.payout_method.id) {
                return this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Oops!"),
                    text: this.$t("Please select a payout method.")
                });
            }
            if (!this.payoutDetails) {
                return this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Oops!"),
                    text: this.$t("Please enter your payout details.")
                });
            }
            this.payoutRequestLoading = true;
            axios
                .post("/api/artist/request-payout", {
                    amount: this.artist.payoutAmount,
                    payoutOption: {
                        id: this.artist.payout_method.id,
                        details: this.payoutDetails,
                        minimum: this.artist.payout_method.minimum
                    }
                })
                .then(res => {
                    this.artist.payouts = res.data;
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Submitted"),
                        text: this.$t("Payout requet submitted successfully.")
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                })
                .catch(e => {
                    return this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Oops!"),
                        text: e.response.data
                    });
                })
                .finally(() => {
                    this.requestPayoutDialog = false;
                    this.payoutRequestLoading = false;
                });
        },
        imageReady(e) {
            this.artist.avatar = e;
        },
        save() {
            var formData = new FormData();
            this.loading = true;
            formData.append("id", this.artist.id);
            formData.append("firstname", this.artist.firstname);
            formData.append("lastname", this.artist.lastname);
            formData.append("displayname", this.artist.displayname);
            if (this.artist.avatar && this.artist.avatar.data) {
                formData.append(
                    "avatar",
                    this.artist.avatar.data,
                    this.artist.avatar.title
                );
            }
            axios
                .post("/api/artist/save-personal-infos", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(() => {
                    this.loading = false;
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Success"),
                        text:
                            this.$t("Artist account") +
                            " " +
                            this.$t("updated successfully.")
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                })
                .catch(e => {
                    this.loading = false;
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: Object.values(e.response.data.errors).join(
                            "<br />"
                        )
                    });
                });
        }
    }
};
</script>
