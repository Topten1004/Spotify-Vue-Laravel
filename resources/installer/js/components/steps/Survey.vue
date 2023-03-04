<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-switch
                    v-model="survey.podcasts"
                    label="I want to enable podcasts"
                    messages="Check if you want to enable podcasts on your app"
                    type="checkbox"
                    required
                ></v-switch>
            </v-col>
            <v-col cols="12">
                <v-switch
                    v-model="survey.artists"
                    label="I want to allow artist accounts"
                    messages="Check if you want to enable artist accounts"
                    type="checkbox"
                    required
                ></v-switch>
            </v-col>
            <v-col cols="12">
                <v-switch
                    v-model="survey.saas"
                    label="I want to use Muzzie as SaaS (Software as a service)"
                    messages="Check if you want to earn money through Muzzie."
                    type="checkbox"
                    required
                ></v-switch>
            </v-col>
            <template v-if="survey.saas">
                <v-col cols="12">
                    <v-select
                        dense
                        outlined
                        label="Select Currency"
                        messages="You will not be able to change this later."
                        :items="currencies"
                        item-value="value"
                        item-text="text"
                        v-model="survey.default_currency"
                        return-object
                    ></v-select>
                </v-col>
            </template>
            <template v-if="survey.saas">
                <v-col cols="12">
                    <v-switch
                        v-model="survey.sell"
                        label="I want to use Muzzie for selling content"
                        messages="Check if you want to sell songs & albums on Muzzie. If you enabled artist account, they will also be able to sell their content"
                        type="checkbox"
                        required
                    ></v-switch>
                </v-col>
                <template v-if="survey.sell && survey.artists">
                <v-col cols="12">
                    <v-text-field
                        v-model="survey.artist_sale_cut"
                        :label="'Artist Sale Cut'"
                        outlined
                        class="pr-2"
                        type="number"
                        messages="Artist cut after the sale of his product. Enter the amount in percentage (max. 100)"
                    ></v-text-field>
                </v-col>
                </template>
                <v-col cols="12">
                    <v-switch
                        v-model="survey.subscription"
                        label="I want to offer subscriptions to users"
                        messages="Check if you want to offer users subscription plans (free or payed)"
                        type="checkbox"
                        required
                    ></v-switch>
                </v-col>
                <v-col cols="12">
                    <v-switch
                        v-if="survey.artists"
                        v-model="survey.royalties"
                        label="I want to offer royalties to artists"
                        messages="Check if you want artists to earn money from streams of their songs"
                        type="checkbox"
                        required
                    ></v-switch>
                </v-col>
                <v-col cols="12" v-if="survey.royalties">
                    <div class="justify-center align-start">
                        <v-text-field
                            v-model="survey.artist_royalty"
                            label="Artist Royalties"
                            outlined
                            class="pr-2"
                            type="number"
                            :messages="'How much should an artist earn per 100 play. Enter the amount in cents'"
                        ></v-text-field>

                        <div class="price d-flex mt-3">
                            <div class="price__currency bold">
                                {{ defaultCurrencySymbol }}
                            </div>
                            <div class="price__amount bold">
                                {{ price(survey.artist_royalty)
                                }}{{ " for each " }}
                                <strong>100</strong>
                                {{ " Play" }}.
                            </div>
                        </div>
                    </div>
                </v-col>
            </template>
        </v-row>
        <next-button
            class="pt-4"
            @clicked="$emit('next', survey)"
            :text="'Next'"
        />
    </v-container>
</template>

<script>
import currencies from "../../../../js/data/stripeSupportedCurrencies";
import NextButton from "../NextButton.vue";
export default {
    components: { NextButton },
    data: () => ({
        survey: {
            sell: true,
            podcasts: true,
            artists: true,
            saas: true,
            artist_royalty: 50,
            artist_sale_cut: 90,
            royalties: true,
            subscription: true,
            default_currency: currencies[0]
        },
        currencies
    }),
    computed: {
                defaultCurrencySymbol() {
            return this.survey.default_currency.symbol ?
            this.survey.default_currency.symbol :
            (this.survey.default_currency.value + ' ')
        }
    },
    methods: {
        price(amount) {
            if (isNaN(amount)) return 0;
            return parseFloat((amount / 100).toFixed(2));
        }
    }
};
</script>

<style></style>
