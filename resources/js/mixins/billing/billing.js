export default {
    computed: {
        defaultCurrency() {
            return this.$store.getters.getSettings.default_currency
        },
        purchasedSongs() {
            if( this.$store.getters['purchase/getPurchases'].songs ) {
                return this.$store.getters['purchase/getPurchases'].songs.concat(...this.$store.getters['purchase/getPurchases'].albums.map(album => album.songs));
            } else {
                return []
            }
        },
        defaultCurrencySymbol() {
            return this.$store.getters.getSettings.default_currency.symbol ?
            this.$store.getters.getSettings.default_currency.symbol :
            this.$store.getters.getSettings.default_currency.value + ' '
        }
    },
    methods: {
        isPurchased(item) {
            if( item.type === 'song' ) {
                return this.purchasedSongs.some( song => song.id === item.id )
            } else {
                return this.$store.getters['purchase/getPurchases'].albums.some(album => album.id === item.id);
            }  
        }
    }
}