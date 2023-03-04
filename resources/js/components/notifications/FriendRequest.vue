<template>
    <div class="friend-request-notification-wrapper">
        <div class="user-avatar">
            <img :src="notification.data.avatar" alt="" />
        </div>
        <div class="info_and_options">
            <div class="name">
                {{ notification.data.name }}
            </div>
            <div class="buttons">
                <div class="btn-approve">
                    <v-btn
                        class="primary"
                        small
                        @click="approveUserRequest()"
                        >{{$t('Approve')}}</v-btn
                    >
                </div>
                <div class="btn-reject">
                    <v-btn
                        class="secondary"
                        small
                        @click="declineUserRequest()"
                        >{{$t('Reject')}}</v-btn
                    >
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["notification"],
    methods: {
        approveUserRequest() {
            axios
                .post("/api/user/accept-friendship", {
                    user_id: this.notification.data.id,
                    friend_id: this.$store.getters.getUser.id,
                    notification_id: this.notification.id
                })
                .then(res => {
                    this.$store.dispatch("fetchNotifications");
                    this.$store.commit("setFriends", null);
                    this.$store.dispatch("fetchFriends");
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Request Accepted"),
                        text:
                            this.notification.data.name + ' ' + 
                            this.$t("has been added to your friend list")
                    });
                    this.$emit("handled");
                });
        },
        declineUserRequest() {
            axios
                .post("/api/user/reject-friendship", {
                    notification_id: this.notification.id
                })
                .then(res => {
                    this.$store.dispatch("fetchNotifications");
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Request Rejected"),
                        text:
                            this.$t("Friend request by") + ' ' + 
                            this.notification.data.name + ' ' + 
                            this.$t("was rejected.") 
                    });
                    this.$emit("handled");
                });
        }
    }
};
</script>
<style lang="scss">
.friend-request-notification-wrapper {
    display: flex;
    align-items: center;
    padding: 0.3rem 0.3rem;
    .user-avatar {
        margin-right: 1em;
        img {
            border-radius: 50%;
            width: 3.3em;
            height: 3.3em;
        }
    }
    .info_and_options {
        .name {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .buttons {
            display: flex;
            & > * {
                flex-basis: 50%;
            }
            & > .btn-approve {
                margin-right: 1em;
            }
        }
    }
}
</style>
