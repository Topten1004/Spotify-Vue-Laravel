<template>
    <v-app-bar v-if="user">
        <v-app-bar-nav-icon
            @click="$emit('toggle-sidebar')"
        ></v-app-bar-nav-icon>
        <v-toolbar-title>{{ $t("Admin Area") }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <div class="notifications" v-if="notifications">
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
                        @deleteNotification="fetchNotifications"
                        :notifications="notifications"
                    >
                    </notifications-box>
                </v-card>
            </v-menu>
        </div>
        <div class="admin-name ml-5 text-overflow-ellipsis">
            <div class="avatar">
                <img
                    :src="user.avatar"
                    alt=""
                    class="avatar-image"
                    width="35"
                />
            </div>
            {{ user.name }}
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
                        {{ $t("Player") }}</v-list-item-title
                    >
                </v-list-item>
                <v-list-item :to="{ name: 'account-settings' }">
                    <v-list-item-title>
                        <v-icon>$vuetify.icons.account-cog</v-icon>
                        {{ $t("Account Settings") }}
                    </v-list-item-title>
                </v-list-item>
                <v-list-item @click="changeTheme(!$vuetify.theme.dark)">
                    <v-list-item-title>
                        <v-icon>$vuetify.icons.brightness-4</v-icon>
                        {{
                            $t($vuetify.theme.dark ? "Light" : "Dark" + " Mode")
                        }}
                    </v-list-item-title>
                </v-list-item>
                <v-list-item @click="$store.dispatch('logout')">
                    <v-list-item-title>
                        <v-icon>$vuetify.icons.logout</v-icon>
                        {{ $t("Logout") }}</v-list-item-title
                    >
                </v-list-item>
            </v-list>
        </v-menu>
    </v-app-bar>
</template>
<script>
import NotificationsBox from "../../notifications/Index.vue";
export default {
    components: {
        NotificationsBox
    },
    created() {
        this.fetchNotifications();
    },
    data() {
        return {
            notifications: null
        };
    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        },
        isThereUnreadNotifications() {
            return  this.notifications && this.notifications.length && (this.notifications.filter(not => not.read_at).length < this.notifications.length);
        }
    },
    methods: {
        fetchNotifications() {
            axios
                .get("/api/admin/notifications")
                .then(res => (this.notifications = res.data));
        }
    }
};
</script>

<style lang="scss" scoped>
.admin-name {
    max-width: 13em;
    display: flex;
    align-items: center;
}
.avatar {
    border-radius: 50%;
    width: 35px;
    overflow: hidden;
    margin-right: 0.3rem;
    &-image {
        height: 100%;
        width: 100%;
    }
}
</style>
