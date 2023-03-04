<template>
    <v-card :class="{ 'dark-background': $vuetify.theme.dark }">
        <v-card-title>
            {{ $t("Share") }}
        </v-card-title>
        <v-card-text>
            <div class="d-flex">
                <div class="cover">
                    <v-img
                        width="150"
                        height="150"
                        class="asset-shadow"
                        :src="item.cover"
                    ></v-img>
                </div>
                <div class="details pl-4">
                    <div class="title" v-text="item.title"></div>
                    <div class="artist" v-text="item.artist"></div>
                </div>
            </div>
            <div class="url pt-5">
                <div class="align-center">
                    <v-text-field
                        outlined
                        readonly
                        :label="$t('Link')"
                        dense
                        hide-details=""
                        :value="item.url"
                    ></v-text-field>
                    <div class="px-2">
                        <v-btn
                            class="primary"
                            @click="copyToClipboard(item.url)"
                            small
                        >
                            <v-icon left :title="$t('Copy to clipboard')"
                                >$vuetify.icons.clipboard-outline</v-icon
                            >
                            {{ $t("Copy") }}</v-btn
                        >
                    </div>
                </div>
            </div>
            <div class="social-links pt-5">
                <ShareLinks :item="item"></ShareLinks>
            </div>
        </v-card-text>
        <v-card-actions>
            <v-btn text small @click="$emit('close')">{{$t('Cancel')}}</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import ShareLinks from "../player/ShareLinks.vue";
export default {
    components: {
        ShareLinks
    },
    computed: {
        item() {
            return this.$store.getters.getSharableItem;
        }
    }
};
</script>

<style></style>
