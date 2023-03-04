<template>
    <div class="share-links d-flex">
        <ShareNetwork
            v-for="network in networks"
            :network="network.network"
            :key="network.network"
            :style="{ backgroundColor: network.color }"
            :url="item.url"
            :title="item.title"
        >
            <!-- :quote="sharing.quote"
        :hashtags="sharing.hashtags"
        :twitterUser="sharing.twitterUser" -->
            <v-icon class="share-network-icon" left dark>
                $vuetify.icons.{{ network.icon }}
            </v-icon>
            <span class="share-network-text">{{ network.name }}</span>
        </ShareNetwork>
        <!-- 
    <ShareNetwork
    network="facebook"
    url="https://news.vuejs.org/issues/180"
    title="Say hi to Vite! A brand new, extremely fast development setup for Vue."
    description="This week, I’d like to introduce you to 'Vite', which means 'Fast'. It’s a brand new development setup created by Evan You."
    quote="The hot reload is so fast it\'s near instant. - Evan You"
    hashtags="vuejs,vite"
  >
   <v-icon class="share-network-icon" left dark>
       $vuetify.icons.facebook
   </v-icon>
    Facebook
</ShareNetwork>
  <ShareNetwork
      network="twitter"
      url="https://news.vuejs.org/issues/180"
      title="Say hi to Vite! A brand new, extremely fast development setup for Vue."
      description="This week, I’d like to introduce you to 'Vite', which means 'Fast'. It’s a brand new development setup created by Evan You."
      quote="The hot reload is so fast it\'s near instant. - Evan You"
      hashtags="vuejs,vite,javascript"
      twitterUser="youyuxi"
    >
   <v-icon class="share-network-icon" left dark>
       $vuetify.icons.twitter
   </v-icon>
    <span>Twitter</span>
  </ShareNetwork> -->
    </div>
</template>

<script>
export default {
    props: ["item"],
    created() {
        if (this.$store.getters.getSettings.enable_share_facebook) {
            this.networks.push({
                network: "facebook",
                name: "Facebook",
                icon: "facebook",
                color: "#1877f2"
            });
        }
        if (this.$store.getters.getSettings.enable_share_twitter) {
            this.networks.push({
                network: "twitter",
                name: "Twitter",
                icon: "twitter",
                color: "#1da1f2"
            });
        }
        if (this.$store.getters.getSettings.enable_share_whatsapp) {
            this.networks.push({
                network: "whatsapp",
                name: "Whatsapp",
                icon: "whatsapp",
                color: "#25d366"
            });
        }
        if (this.$store.getters.getSettings.enable_share_telegram) {
            this.networks.push({
                network: "telegram",
                name: "Telegram",
                icon: "telegram",
                color: "#0088cc"
            });
        }
    },
    data() {
        return {
            networks: []
        };
    }
};
</script>

<style lang="scss" scoped>
a[class^="share-network-"] {
    flex: none;
    color: #ffffff;
    background-color: #333;
    border-radius: 3px;
    overflow: hidden;
    display: flex;
    flex-direction: row;
    align-content: center;
    align-items: center;
    cursor: pointer;
    padding-right: 0.4em;
    margin: 0 10px 10px 0;
    text-decoration: none;
    @media (max-width: 700px) {
        padding-right: 0;
    }
}
.share-network {
    // &-facebook {
    //     background-color: rgb(24, 119, 242) !important;
    // }
    // &-twitter {
    //     background-color: #1da1f2 !important;
    // }
    &-icon {
        background-color: rgba(0, 0, 0, 0.2);
        padding: 7px;
        flex: 0 1 auto;
        @media (max-width: 700px) {
            margin-right: 0 !important;
        }
    }
    &-text {
        @media (max-width: 700px) {
            display: none;
        }
    }
}
</style>
