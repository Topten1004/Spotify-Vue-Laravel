<template>
    <div class="notifications-wrapper">
        <div class="card-title-medium">
            {{ title }}
        </div>
        <div class="notifications">
            <slot v-if="!notifications"></slot>
            <template v-else-if="notifications && notifications.length">
                <div
                    v-for="(notification, i) in notifications"
                    :key="notification.id"
                    class="notification"
                >
                    <artist-request
                        v-if="
                            notification.type ===
                                'App\\Notifications\\ArtistRequest'
                        "
                        @handled="$emit('handled')"
                        @delete-notification="$emit('deleteNotification')"
                        :notification="notification"
                    ></artist-request>
                    <friend-request
                        v-if="
                            notification.type ===
                                'App\\Notifications\\FriendRequest'
                        "
                        @handled="$emit('handled')"
                        :notification="notification"
                    ></friend-request>
                    <message-notification
                        v-if="
                            notification.type ===
                                'App\\Notifications\\Message' ||
                                notification.type ===
                                    'App\\Notifications\\ArtistMessage'
                        "
                        @read="$emit('handled')"
                        @handled="show('notifications')"
                        :notification="notification"
                    ></message-notification>
                    <v-divider v-if="notifications[i + 1]"></v-divider>
                </div>
            </template>
            <template v-else>
                <empty-page
                    class="px-4"
                    :headline="$t('Nothing New!')"
                    :sub="$t('No new notifications.')"
                />
            </template>
        </div>
    </div>
</template>
<script>
import friendRequest from "./FriendRequest.vue";
import artistRequest from "./ArtistRequest";
import messageNotification from "./Message.vue";
export default {
    props: ["title", "notifications"],
    components: {
        friendRequest,
        artistRequest,
        messageNotification
    }
};
</script>
<style lang="scss" scoped>
.notifications-wrapper {
    width: 20em;
    border-radius: 10px;
    max-height: 40em;
    padding: 0.5rem;
    .title-wrapper {
        padding: 0.1em 0.35em;
    }
    .notification {
        border-radius: 5px;
        cursor: pointer;
        &:not(:last-child) {
        }
        &:hover {
            background-color: rgba(105, 105, 105, 0.308);
        }
    }
}
</style>
