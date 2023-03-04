<template>
    <div id="admin-master">
        <NavbarAdmin @toggle-sidebar="sidebar = !sidebar" id="admin-header" />
        <SidebarAdmin :style="{ transform: !sidebar ? 'translateX(-100%)' : '' }" id="admin-sidebar" @clickOnItem="screenWidth < 750 ? sidebar = false: ''" />
        <div :style="{ paddingLeft: !sidebar ? '0.8em' : '19.8em' }"  id="admin-main-container">
            <v-container class="admin-router-container">
                <router-view></router-view>
            </v-container>
        </div>
    </div>
</template>
<script>
import SidebarAdmin from "./Sidebar";
import NavbarAdmin from "./Navbar";
export default {
    created() {
        // hide the sidebar if the screen width is less than 750px
        this.screenWidth < 750 ? (this.sidebar = false) : ''
    },
    metaInfo: {
        title: window.Settings.find(set => set.key === 'appName').value + ' - '   + 'Admin Area'
    },
    components: {
        SidebarAdmin,
        NavbarAdmin
    },
    data() {
        return {
            sidebar: true,
            screenWidth: window.innerWidth
        }
    }
};
</script>
<style lang="scss">
@import '../../../../sass/admin-artist-areas/main.scss';
#admin-master {
    position: relative;
    height: 100vh;
    overflow: hidden;
    #admin-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 3;
    }
    #admin-sidebar {
        position: absolute;
        top: 0em;
        left: 0;
        z-index: 2;
        width: 18em;
        bottom: 0;
        overflow-y: auto;
        transition: transform .4s ease-in;
    }
    #admin-main-container {
        padding: 80px 1.5em 0 19.8em;
        overflow: auto;
        height: 100%;
        @media screen and (max-width: 750px) {
            padding: 0 !important;
            padding-top: 7em !important;
        }
    }
}
.admin-search-bar {
    max-width: 300px;
    margin-left: auto;
}
.appearance-container, .settings-container {
  .pages {
    padding: 2em 1.5em;
    .page {
      max-width: 800px;
    }
  }
  .page__options {
    margin-left: auto;
  }
}
.admin-router-container {
    max-width: 1200px !important;
}
</style>
