<template>
    <v-card class="chats-wrapper absolute-panel" rounded="lg">
        <div class="card-title-medium">{{ $t('Chats') }}</div>
        <div class="chats" v-if="!isLoading">
            <ul v-if="isThereASession">
                <template v-for="friend in friends">
                    <li
                        :key="friend.id"
                        v-if="friend.session"
                        @click="openChat(friend)"
                    >
                        <div class="avatar">
                            <img :src="friend.avatar" alt="" />
                        </div>
                        <div class="infos">
                            <div class="name-unread">
                                <div class="name max-1-lines">{{ friend.name }}</div>
                                <div
                                    class="unread-messages-count"
                                    v-if="friend.session.unreadCount > 0"
                                >
                                    {{ friend.session.unreadCount }}
                                </div>
                            </div>
                            <div
                                class="last_message"
                                :class="{
                                    unread: friend.session.unreadCount > 0
                                }"
                                v-if="friend.session.last_message.length"
                            >
                                {{ friend.session.last_message[0].content }}
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
            <empty-page
                v-else
                :headline="$t('No chats to show!')"
                :sub="$t('Add some friends and start chatting!')"
            />
        </div>
        <div class="loading" v-else>{{$t('Opening chat...Please wait!')}}</div>
        <template v-if="currentOpenChat">
            <message-component
                @close="close(currentOpenChat)"
                :friend="currentOpenChat"
            ></message-component>
        </template>
    </v-card>
</template>
<script>
export default {
    props: ["whoToOpen", "amIAlive"],
    components: { MessageComponent: () => import("./MessageComponent") },
    data() {
        return {
            currentOpenChat: null,
            friends: this.$store.getters.getFriends,
            isLoading: false
        };
    },
    methods: {
        close(currentOpenChat) {
            if (currentOpenChat) {
                var index = this.friends.findIndex(
                    friend => friend.id == currentOpenChat.id
                );
                this.friends[index].session.open = false;
            }

            this.currentOpenChat = null;
        },
        openChat(friend) {
            this.isLoading = true;
            if (friend.session) {
                this.friends.forEach(friend =>
                    friend.session ? (friend.session.open = false) : ""
                );
                friend.session.open = true;
                this.currentOpenChat = friend;
                friend.session.unreadCount = 0;
                this.isLoading = false;
            } else {
                this.createSession(friend);
            }
        },
        createSession(friend) {
            axios
                .post("/api/sessions/create", { friend_id: friend.id })
                .then(res => {
                    (friend.session = res.data),
                        ((friend.session.open = true),
                        (this.currentOpenChat = friend),
                        (this.isLoading = false));
                });
        },
        listenForEverySession(friend) {
            window.Echo.private(`Chat.${friend.session.id}`).listen(
                "PrivateChatEvent",
                e => {
                    if (!friend.session.open) {
                        if (!this.amIAlive) {
                            this.$emit("unread", 1);
                        }
                        friend.session.unreadCount++;
                        friend.session.last_message[0].content = e.content;
                    }
                }
            );
        }
    },
    computed: {
        isThereASession() {
            return this.friends.some(friend => friend.session);
        }
    },
    async created() {
        if (this.whoToOpen) {
            this.openChat(
                this.friends.find(friend => friend.id == this.whoToOpen)
            );
            this.$emit("chatOpened");
        }
        this.friends.forEach(friend => {
            this.listenForEverySession(friend);
        });
        var unread = this.friends.reduce(
            (a, b) => a + b.session.unreadCount,
            0
        );
        if (!this.amIAlive) {
            this.$emit("unread", unread);
        }
        window.Echo.channel("Chat").listen("HearingEvent", e => {
            let friend = this.friends.find(friend => friend.id == e.user_id);
            if (friend) {
                friend.isPlaying = e.is_playing;
            }
        });
        window.Echo.channel("Chat").listen("SessionEvent", e => {
            let friend = this.friends.find(friend => friend.id == e.session_by);
            friend.session = e.session;
        });
    },
    watch: {
        whoToOpen(val) {
            if (val) {
                this.openChat(this.friends.find(friend => friend.id == val));
                this.$emit("chatOpened");
            }
        },
        amIAlive(val) {
            if (!val) {
                this.close(this.currentOpenChat);
            }
        }
    }
};
</script>
<style lang="scss" scoped>
.chats-wrapper {
    min-width: 350px;
    max-width: 500px;
    position: relative;
    min-height: 40vh;
    border-radius: 10px;
    padding: 0.5rem;
    @media screen and (max-width: 500px) {
        min-width: auto;
    }
    .title-wrapper {
        padding-left: 0.5em;
    }
    ul {
        list-style: none;
        li {
            padding: 0.5rem 0.3rem;
            border-radius: 5px;
            border-radius: 15px;
            word-break: break-all;
            display: flex;
            cursor: pointer;
            .avatar {
                margin-right: 1rem;
                img {
                    border-radius: 50%;
                    width: 3em;
                }
            }
            .infos {
                flex-grow: 2;
                .name-unread {
                    display: flex;
                    align-items: center;
                    .name {
                        font-weight: 600;
                        margin-right: 1em;
                        max-width: 100%;
                    }
                    .unread-messages-count {
                        height: 1.6em;
                        width: 1.6em;
                        font-weight: 600;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        border-radius: 50%;
                        background-color: purple;
                        font-size: 0.55em;
                    }
                }
                .last_message {
                    opacity: 0.8;
                }
                .unread {
                    color: purple;
                    font-weight: 600;
                }
            }
            &:hover {
                background-color: rgba(107, 107, 107, 0.26);
            }
        }
    }
}
</style>
