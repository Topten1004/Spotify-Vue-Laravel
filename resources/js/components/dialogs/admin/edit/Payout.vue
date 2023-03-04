<template>
   <v-card v-if="payout">
    <v-card-title>
        {{ $t("Payout Details") }}
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-btn @click="$emit('close')" text>
            {{ $t("Close") }}
        </v-btn>
    </v-card-title>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>
                        {{ $t("Requested Payout") }}
                        <v-spacer></v-spacer>
                        <v-spacer></v-spacer>
                        <div class="payout-status">
                            <v-btn
                                x-small
                                dense
                                outlined
                                v-if="
                                    payout.status === 'payed'
                                "
                                color="success"
                                >{{ $t("Payed") }}</v-btn
                            >
                            <v-btn
                                x-small
                                dense
                                outlined
                                v-else-if="
                                    payout.status ===
                                        'cancelled'
                                "
                                color="error"
                                >{{ $t("Rejected") }}</v-btn
                            >
                            <v-btn
                                x-small
                                dense
                                outlined
                                v-else-if="
                                    payout.status ===
                                        'pending'
                                "
                                color="warning"
                                >{{ $t("Pending") }}</v-btn
                            >
                            <v-btn
                                x-small
                                dense
                                outlined
                                v-else-if="
                                    payout.status ===
                                        'requested'
                                "
                                color="info"
                                >{{ $t("Requested") }}</v-btn
                            >
                        </div>
                    </v-card-title>
                    <v-container>
                        <v-simple-table>
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <th class="text-left">
                                            {{ $t("Amount") }}
                                        </th>
                                        <th class="text-left">
                                            {{
                                                $t("Payout Method")
                                            }}
                                        </th>
                                        <th class="text-left">
                                            {{
                                                $t(
                                                    "Payout Information"
                                                )
                                            }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div
                                                class="price success--text bold"
                                            >
                                                {{
                                                    price(
                                                        payout.amount
                                                    ) +
                                                        defaultCurrency.symbol
                                                }}
                                            </div>
                                        </td>
                                        <td>
                                            {{
                                                payout.artist
                                                    .payout_method
                                                    .name
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                payout.artist
                                                    .payout_method
                                                    .pivot
                                                    .payout_details
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-container>
                    <v-card-actions>
                        <template
                            v-if="
                                payout.status !== 'payed' &&
                                    payout.status !==
                                        'cancelled'
                            "
                        >
                            <v-btn
                                class="success"
                                @click="status = 'payed'"
                            >
                                {{ $t("Mark As Payed") }}
                            </v-btn>
                            <v-btn
                                class="error"
                                @click="status = 'cancelled'"
                            >
                                {{ $t("Reject") }}
                            </v-btn>
                            <v-btn
                                class="warning"
                                v-if="
                                    payout.status !==
                                        'pending'
                                "
                                @click="status = 'pending'"
                            >
                                {{ $t("Mark As Pending") }}
                            </v-btn>
                        </template>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-card-title>
            {{ $t("Artist who request the payout") }}
        </v-card-title>
        <div>
            <v-card outlined class="artist-details p-2 pl-3">
                <v-card-title
                    ><v-icon color="primary" x-large class="mr-3"
                        >$vuetify.icons.account-artist</v-icon
                    >
                    {{ $t("Artist Information") }}
                </v-card-title>
                <v-container>
                    <v-row>
                        <v-col cols="auto">
                            <v-img
                                max-height="200"
                                max-width="200"
                                :src="payout.artist.avatar"
                            ></v-img>
                        </v-col>
                        <v-col>
                            <v-text-field
                                readonly
                                :label="$t('First Name')"
                                :value="
                                    payout.artist.firstname
                                "
                            ></v-text-field>
                            <v-text-field
                                readonly
                                :label="$t('Last Name')"
                                :value="
                                    payout.artist.lastname
                                "
                            ></v-text-field>
                            <v-text-field
                                readonly
                                :label="$t('Displayname')"
                                :value="
                                    payout.artist.displayname
                                "
                            ></v-text-field>
                            <v-text-field
                                readonly
                                :label="$t('Email')"
                                :value="payout.artist.email"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card>
            <ArtistEarningDetails :artist="payout.artist"></ArtistEarningDetails>
            <v-dialog v-model="payoutDetailsDialog" @click:outside="status = ''" max-width="500">
                <v-card class="p-3">
                    <v-container>
                        <v-row>
                             <v-col cols="12">
                                <div class="text-center">
                                    <div class="title">{{$t('Payout details')}}</div>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                  <v-textarea outlined v-model="payout.details" label="Enter payout details" ></v-textarea>
                            </v-col>
                            <v-col>
                                <div class="w-100 justify-center">
                                    <v-btn class="success" @click="markAs(status)">{{ $t('Update') }}</v-btn>
                                </div>

                            </v-col>
                        </v-row>
                    </v-container>
                </v-card>
            </v-dialog>
        </div>
    </v-container>
</v-card>
</template>

<script>
import Billing from '@mixins/billing/billing';
import ArtistEarningDetails from '../../../artist/EarningDetails.vue';
export default {
    mixins: [Billing],
    props: ["payout"],
    components: {
        ArtistEarningDetails
    },
    watch: {
        status() {
            this.payoutDetailsDialog = true;
        }
    },
    data() {
        return {
            payoutDetailsDialog: false,
            status: ''
        }
    },
    methods: {
        markAs(status) {
            this.$confirm({
                message: `${this.$t(
                    "Are you sure you wanna change the status of this payout to " +
                        status +
                        "?"
                )}`,
                button: {
                    no: this.$t("Cancel"),
                    yes: this.$t("Yes")
                },
                callback: (confirm) => {
                    if( confirm ) {
                        axios
                        .post("/api/admin/mark-payout", {
                            status,
                            payoutID: this.payout.id,
                            details: this.payout.details
                        })
                        .then(() => {
                            this.$notify({
                                group: "foo",
                                type: "success",
                                title: this.$t("Updated"),
                                text: this.$t(
                                    "Payout status updated successfully."
                                )
                            });
                            this.$emit('fetchPayouts')
                        })
                        .finally(() => {
                            this.editDialog = false;
                            this.payoutDetailsDialog = false;
                            this.editingPayout = null;
                        });
                    }
                }
            });
        }
    }
};
</script>
