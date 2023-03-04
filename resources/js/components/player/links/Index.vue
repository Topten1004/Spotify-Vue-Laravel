<template>
    <div class="links d-flex">
        <div class="link" v-if="content.spotify_link && $store.getters.getSettings.show_external_link_spotify">
            <a :href="content.spotify_link" rel="noreferrer" target="_blank">
                <v-icon color="#1DB954">$vuetify.icons.spotify</v-icon>
            </a>
        </div>
        <div class="link" v-if="content.youtube_link">
            <a :href="content.youtube_link" rel="noreferrer" target="_blank">
                <v-icon color="#FF0000">$vuetify.icons.youtube</v-icon>
            </a>
        </div>
        <div class="link" v-if="content.itunes_link">
            <a :href="content.itunes_link" rel="noreferrer" target="_blank">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    aria-hidden="true"
                    focusable="false"
                    width="25"
                    height="25"
                    style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);"
                    preserveAspectRatio="xMidYMid meet"
                    viewBox="0 0 24 24"
                >
                    <path
                        d="M7.85 17.07c-.82.1-4.35.6-3.79 3.19c.63 3.04 5.81 2.33 5.77-1.26c-.02-2.43 0-9.8 0-9.8s-.07-.67.6-.81l7.76-1.6s.64-.14.64.5v6.91s.07.63-.71.8c-.78.12-4.21.4-3.93 3c.31 3.07 5.81 2.65 5.81-.93V2.61s.04-.99-1.1-.74L9.5 3.78s-.84.18-.84.99v11.34s0 .85-.81.96z"
                        fill="#626262"
                    />
                </svg>
            </a>
        </div>
        <div class="link" v-if="content.soundcloud_link">
            <a :href="content.soundcloud_link" target="_blank" rel="noreferrer">
                <v-icon color="#FE5000">$vuetify.icons.soundcloud</v-icon>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: ["content"]
};
</script>

<style lang="scss" scoped>
.link {
    padding: 0 .25em ;
    i {
        font-size: 10px !important;
    }
}
</style>