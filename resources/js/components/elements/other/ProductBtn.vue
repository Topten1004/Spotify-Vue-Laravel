<template>
    <div class="owned" v-if="isOwned && item.product">
        <div class="premium" :title="$t('Purchased(Premium)')">
            <v-btn outlined small color="#FFA500" dark v-if="size == 'large'">
                <v-icon left dark>$vuetify.icons.crown</v-icon>
                {{ $t('Premium') }}
            </v-btn>
            <v-icon :left="size == 'large'" dark v-else color="#FFA500">$vuetify.icons.crown</v-icon>
        </div>
    </div>
    <div class="purchase" v-else-if="isPurchasable" @click.stop="purchase">
        <v-btn
            :small="size !== 'small'"
            :x-small="size === 'small'"
            :title="$t('Purchase')"
            color="#FF8F00"
            dark
            class="mr-2"
            :fab="size !== 'large'"
            :dense="size !== 'large'"
        >
            <v-icon :left="size == 'large'" :small="size === 'small'">$vuetify.icons.cart</v-icon>
            <template v-if="size === 'large'">
                {{ $t('Purchase') }}
            </template>
        </v-btn>
    </div>
</template>

<script>
import Billing from '../../../mixins/billing/billing';
import helpers from '../../../helpers';
export default {
    props: ["item", "size"],
    mixins: [Billing],
    computed: {
        isPurchasable() {
            return this.item.product && !this.isPurchased(this.item);
        },
        isOwned() {
            return (
                (this.item.artist &&
                    (this.$store.getters.getUser && this.$store.getters.getUser.artist) && 
                    this.item.artist.id ===
                        this.$store.getters.getUser.artist.id) ||
                (this.item.product && this.isPurchased(this.item))
            );
        }
    },
    methods: {
        async purchase() {
            if (!this.$store.getters.isLogged) {
                // if the user is not logged
                await helpers.loginOrCancel();
            }
            this.$store.commit('purchase/setSellingAsset', this.item)
        }
    }
};
</script>

<style></style>
