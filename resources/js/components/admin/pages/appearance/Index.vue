<template>
    <div class="appearance-container">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-sheet elevation="6">
                        <v-tabs center-active grow>
                            <v-tab
                                v-for="tab in tabs"
                                :key="tab.routeName"
                                :to="{ name: tab.routeName }"
                            >
                                <v-icon class="mr-2">$vuetify.icons.{{ tab.icon }}</v-icon>
                                <span>{{ $t(tab.text) }}</span>
                            </v-tab>
                        </v-tabs>
                    </v-sheet>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <router-view></router-view>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>
<script>
export default {
    data() {
        return {
            tabs: [
                {
                    text: 'General',
                    icon: "application-cog",
                    routeName: "appearance.general"
                },
                {
                    text: 'Landing',
                    icon: "airplane-landing",
                    routeName: "appearance.landing"
                },
                {
                    text: 'Navigation',
                    icon: "navigation-outline",
                    routeName: "appearance.navigation"
                },
                {
                    text: 'Themes',
                    icon: "palette",
                    routeName: "appearance.themes"
                },
                {
                    text: 'Preview',
                    icon: "eye-outline",
                    routeName: "appearance.preview"
                }
            ]
        };
    }
};
</script>
<style lang="scss">
@import "../../../../../sass/variables";
.appearance-container {
    .loading {
        height: 15vh;
    }
}
</style>
