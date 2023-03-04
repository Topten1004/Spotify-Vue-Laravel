<template>
    <v-menu offset-y>
        <template v-slot:activator="{ on }">
            <v-btn
                color="primary"
                v-on="on"
                small
                class="pr-0 cart-btn-larger-screens"
            >
                <v-icon left>$vuetify.icons.cart</v-icon>
                {{ $t('Cart') }}
                <v-badge
                    v-if="items.length > 0"
                    :content="items.length"
                    inline
                    color="primary"
                ></v-badge>
            </v-btn>

            <v-badge
                v-if="items.length > 0"
                :content="items.length"
                overlap
                color="primary"
                class="cart-btn-smaller-screens"
            >
                <v-btn color="primary" v-on="on" icon small>
                    <v-icon left>$vuetify.icons.cart</v-icon>
                </v-btn>
            </v-badge>
        </template>
        <v-card max-width="400" :class="{  'dark-backround' : $vuetify.theme.dark }">
            <v-card-title>
                {{ $t('Cart') }}
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text v-if="items.length > 0">
                <v-list :class="{  'dark-backround' : $vuetify.theme.dark }">
                    <v-list-item v-for="(product, idx) in items" :key="idx">
                       <v-list-item-avatar
                                width="45"
                                height="45"
                                rounded="0"
                                class="asset-shadow"
                            >
                                <img :src="product.item.cover" />
                            </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>{{
                                product.item.title
                            }}</v-list-item-title>
                            <v-list-item-subtitle>{{
                               getMainArtist(product.item)
                            }}</v-list-item-subtitle>
                        </v-list-item-content>

                        <v-list-item-action>
                            <v-btn icon small @click=" $store.dispatch('purchase/removeFromCart', { id: product.product_id })">
                                <v-icon small>
                                    $vuetify.icons.delete
                                </v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>

                <v-card-actions>
                    <v-btn
                        block
                        color="primary"
                        small
                        @click="
                            $store.commit('purchase/setCheckoutDialog', true)
                        "
                    >
                        {{$t('Proceed to checkout')}}
                    </v-btn>
                </v-card-actions>
            </v-card-text>

            <v-card-text v-else>
                <p>{{ $t('Empty Cart') }}</p>
            </v-card-text>
        </v-card>
    </v-menu>
</template>
<script>
export default {
    computed: {
        items() {
            return this.$store.getters["purchase/getCart"].items;
        },
        totalPrice() {
            return this.$store.getters["purchase/getCart"].total;
        }
    }
};
</script>

<style lang="scss" scoped>
.cart-btn-larger-screens {
    @media (max-width: 750px) {
        display: none;
    }
}
.cart-btn-smaller-screens {
    @media (min-width: 749px) {
        display: none;
    }
}
</style>
