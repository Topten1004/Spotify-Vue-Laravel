<template>
  <v-card color="purchase-dialog">
      <v-card-title class="card-header">
            <div>
                <v-icon color="#FF8F00">$vuetify.icons.cart</v-icon>
            </div>
            <div class="title">
                {{$t('Get License')}}
            </div>
      </v-card-title>
      <div class="body">
            <v-list>
                <v-list-item>
                    <v-list-item-avatar
                        width="85"
                        height="85"
                        rounded="0"
                        class="asset-shadow"
                    >
                        <img :src="asset.cover" />
                    </v-list-item-avatar>
                    <v-list-item-content>
                        <v-list-item-title>{{
                            asset.title
                        }}</v-list-item-title>
                        <v-list-item-subtitle>{{
                            getMainArtist(asset)
                        }}</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
          <v-divider></v-divider>
          <div class="licenses">
               <v-radio-group v-model="license">
                <v-radio
                    v-for="(l, n) in asset.product.prices"
                    :key="n"
                    :value="l"
                    :label="l.name"

                    class="license"
                >
                    <template v-slot:label>
                        <div class="label-container">
                            <div class="license-header">
                                <div class="license-name">
                                    {{ l.name }}
                                </div>
                                 <div class="license-price">
                                    {{ price(l.amount) }} {{ defaultCurrency.symbol }}
                                </div>
                            </div>
                            <div class="license-body">
                                <div class="license-description">
                                   {{ l.description }}
                                </div>
                            </div>
                        </div>
                    </template>
                </v-radio>
            </v-radio-group>
          </div>
      </div>
      <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="primary" @click="pushToCart" :disabled="!license.name">
              <v-icon left>$vuetify.icons.cart</v-icon>
              {{ $t('Add To Cart') }}
          </v-btn>
          <v-spacer></v-spacer>
      </v-card-actions>
  </v-card>
</template>

<script>
import BillingMixin from '../../../mixins/billing/billing';
export default {
    props: ['asset'],
    mixins: [
        BillingMixin
    ],
    data() {
        return {
            license: {}
        }
    },
    methods: {
        pushToCart() {
            this.$store.dispatch('purchase/updateCart',  {
                product: this.asset.product,
                price: this.license
            })
        }
    }
}
</script>

<style lang="scss" scoped>
    .purchase-dialog {
        .card-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .body {
            padding: 1em 1.5em;
        }
        .asset-information {
            padding: 1em 0;
            .asset-title {
                font-size: 1.3em;
            }
            .asset-artist {
                opacity: .85;
                padding-top: .4em;
            }
        }
        .licenses {
            padding: 1em 0;
        }
        .license {
            padding: 0 1em;
            .label-container {
                width: 100%;
                .license-header {
                    display: flex;
                    justify-content: space-between;
                    line-height: 2;
                    align-items: center;
                    font-size: 1.15em;
                    font-weight: bold;
                }
                .license-body {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: .8em;
                    opacity: .85;
                }
            }
        }
    }

</style>