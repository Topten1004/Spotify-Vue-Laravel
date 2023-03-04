<template>
    <v-app-bar v-if="artist">
        <v-app-bar-nav-icon
            @click="$emit('toggle-sidebar')"
        ></v-app-bar-nav-icon>
        <v-toolbar-title>{{$t('Artist Area')}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <div class="notifications mr-4" v-if="notifications">
            <v-menu
                bottom
                left
                nudge-bottom="50"
                :close-on-click="false"
                max-height="20em"
                :close-on-content-click="false"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn small v-bind="attrs" v-on="on" icon>
                        <v-badge
                            left
                            overlap
                            dot
                            :color="
                                isThereUnreadNotifications
                                    ? 'error'
                                    : 'transparent'
                            "
                        >
                            <v-icon>$vuetify.icons.bell</v-icon>
                        </v-badge>
                    </v-btn>
                </template>
                <v-card class="panel">
                    <notifications-box
                        @handled="show('notifications')"
                        :notifications="notifications"
                    >
                    </notifications-box>
                </v-card>
            </v-menu>
        </div>
        <div class="text-overflow-ellipsis">
            <v-avatar color="primary" class="mr-2" size="35">
                <v-img :src="artist.avatar" />
            </v-avatar>
            {{ artist.displayname }}
        </div>
        <v-menu left bottom>
            <template v-slot:activator="{ on, attrs }">
                <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon>$vuetify.icons.dots-vertical</v-icon>
                </v-btn>
            </template>
            <v-list>
                <v-list-item
                    :to="{ path: $store.getters.getSettings.playerLanding }"
                >
                    <v-list-item-title>
                        <v-icon>$vuetify.icons.music-note-eighth</v-icon>
                        {{$t('Player')}}</v-list-item-title
                    >
                </v-list-item>
                <v-list-item
                    @click="$vuetify.theme.dark = !$vuetify.theme.dark"
                >
                    <v-list-item-title>
                        <v-icon>$vuetify.icons.brightness-4</v-icon>
                        {{ $t(($vuetify.theme.dark ? "Light" : "Dark") + ' Mode')}}</v-list-item-title
                    >
                </v-list-item>
                <v-list-item @click="$store.dispatch('logout')">
                    <v-list-item-title>
                        <v-icon>$vuetify.icons.logout</v-icon> {{$t('Logout')}}</v-list-item-title
                    >
                </v-list-item>
            </v-list>
        </v-menu>
    </v-app-bar>
</template>

<script>
import NotificationsBox from "../../notifications/Index.vue";
export default {
    props: ["artist"],
    components: {NotificationsBox},
    data() {
        return {
            notifications: null
        }
    },
    created() {
        axios.get('/api/artist/notifications')
        .then((res) => {
            this.notifications = res.data
        })
    },
    computed: {
        isThereUnreadNotifications() {
            if( this.notifications ) {
                return this.notifications && this.notifications.length && (this.notifications.filter(not => not.read_at).length < this.notifications.length);
            }
        },
    }
};
</script>
