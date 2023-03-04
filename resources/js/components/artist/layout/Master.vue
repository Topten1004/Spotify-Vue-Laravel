<template>
    <div id="artist-master" v-if="$store.getters.getArtist && $store.getters.getArtist.id">
        <NavbarArtist @toggle-sidebar="sidebar = !sidebar" id="artist-header" :artist="$store.getters.getArtist" />
        <SidebarArtist :style="{ transform: !sidebar ? 'translateX(-100%)' : '' }" id="artist-sidebar" @clickOnItem="screenWidth < 750 ? sidebar = false: ''" />
        <div id="artist-main-container" :style="{ paddingLeft: !sidebar ? '0.8em' : '19.8em' }">
            <router-view></router-view>
        </div>
    </div>
    <new-artist-form v-else-if="$store.getters.getArtist !== null" :artist="$store.getters.getArtist"></new-artist-form>
</template>

<script>
import SidebarArtist from "./Sidebar";
import NavbarArtist from "./Navbar";
import NewArtistForm from "../pages/NewArtistForm";
export default {
    created() {
        this.screenWidth < 750 ? (this.sidebar = false) : ''
    },
    metaInfo: {
        title: window.Settings.find(set => set.key === 'appName').value + ' - Artist Dashboard'
    },
    data() {
        return {
            sidebar: true,
            screenWidth: window.innerWidth
        }
    },
    components: {
        SidebarArtist,
        NavbarArtist,
        NewArtistForm
    },
    created() {
        this.$store.dispatch('fetchArtist')
    }
};
</script>

<style lang="scss" scoped>
#artist-master {
    position: relative;
    height: 100vh;
    overflow: hidden;
    #artist-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 3;
    }
    #artist-sidebar {
        position: absolute;
        top: 0em;
        left: 0;
        z-index: 2;
        width: 18em;
        bottom: 0;
        overflow-y: auto;
        transition: transform .4s ease-in;
    }
    #artist-main-container {
        padding: 80px 1.5em 0 19.8em;
        overflow: auto;
        height: 100%;
        @media screen and (max-width: 750px) {
            padding: 0 !important;
            padding-top: 7em !important;
        }
    }
}
.artist-search-bar {
    max-width: 300px;
    margin-left: auto;
}
</style>
