<template>
  <div class="chat-wrapper" v-if="friend">
    <div class="chat-header">
      <!-- upcoming feature -->
      <!-- <b :class="{'text-danger':session.block}">
                {{friend.name}} <span v-if="isTyping">is Typing . . .</span>
                <span v-if="session.block">(Blocked)</span>
            </b> -->
      <!-- Close Button -->
      <div class="back pointer" @click.prevent="close">
        <v-icon>$vuetify.icons.chevron-left</v-icon>
      </div>
      <div class="friend-cover">
        <img :src="friend.avatar" alt="" />
      </div>
      <div class="friend-name max-1-lines">
        {{ friend.name }}
      </div>
      <div class="dots">
        <v-menu left bottom :position-y="125" offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon small>$vuetify.icons.dots-horizontal</v-icon>
            </v-btn>
          </template>
           <v-list
          class="list"
          :class="{ 'dark-background': $vuetify.theme.dark }"
        >
          <v-list-item
            v-if="session.blocked_by" @click="unblock"
          >
             {{ $t("Unblock") }}
          </v-list-item>
          <v-list-item
            v-else
            @click="block"
          >
             {{ $t("Block") }}
          </v-list-item>
           </v-list>
        </v-menu>
        <v-dialog v-model="blockFriendConfirmationDialog" max-width="400">
          <v-card>
            {{ $t("Are you sure you wanna block") }} {{ friend.name }} ?
            <v-card-actions>
              <v-btn color="error" text @click="block">{{ $t("Block") }}</v-btn>
              <v-btn @click="blockFriendConfirmationDialog = false" text>{{
                $t("Cancel")
              }}</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
    </div>
    <div class="chat-body">
      <div
        class="msg"
        v-for="(chat, i) in chats"
        :key="chat.id"
        :class="{ 'my-msg': chat.type == 0 }"
      >
        <div class="msg-time" v-show="i == chats.length - 1">
          {{ chat.send_at }}
        </div>
        <div class="msg-text">
          <div class="text">
            {{ chat.message }}
          </div>
        </div>
        <div class="seen" v-show="chat.read_at" v-if="i == chats.length - 1">
          {{ $t("Seen") }}
        </div>
      </div>
    </div>
    <div class="chat-footer">
      <form @submit.prevent="send">
        <div class="chat-input text-center">
          <template v-if="!session.block">
            <input
              type="text"
              class="form-control m_input"
              placeholder="Write your message here"
              v-model="message"
            />
            <div class="send-icon pointer" @click="send">
              <img src="/svg/send.svg" alt="" class="svg-image" />
            </div>
          </template>
          <template v-else>
            <v-btn
              block
              class="error"
              @click="unblock"
              v-if="session.blocked_by == $store.getters.getUser.id"
              >{{ $t("Unblock") }}</v-btn
            >
            <span v-else>{{
              $t("You can no longer chat with this person.")
            }}</span>
          </template>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
export default {
  props: ["friend"],
  data() {
    return {
      chats: [],
      message: null,
      isTyping: false,
      chatMenuDialog: false,
      blockFriendConfirmationDialog: false,
    };
  },
  computed: {
    session() {
      return this.friend.session;
    },
  },
  methods: {
    send() {
      if (this.message) {
        this.pushToChats(this.message);
        axios.post(`/api/send/${this.friend.session.id}`, {
          content: this.message,
          to_user: this.friend.id,
        });
        if (!this.friend.session.last_message[0]) {
          this.friend.session.last_message[0] = {};
        }
        if (
          this.$store.getters.getSettings.ga4 &&
          this.$store.getters.getSettings.analytics_fileUpload_event
        ) {
          emitAnalyticsEvent({
            action: "Chat Message",
            category: "message",
            label: this.message,
          });
        }
        this.friend.session.last_message[0].content =
          this.$t("You") + ": " + this.message;
        this.message = null;
      }
    },
    pushToChats(message) {
      this.chats.push({
        message: message,
        type: 0,
        read_at: null,
        sent_at: this.$t("Just Now"),
      });
    },
    close() {
      this.$emit("close");
    },
    clear() {
      axios
        .post(`/api/sessions/${this.friend.session.id}/clear`)
        .then(() => (this.chats = []));
    },
    block() {
      if (!this.session.block) {
        this.session.block = true;
        this.session.blocked_by = this.$store.getters.getUser.id;
        this.chatMenuDialog = !this.chatMenuDialog;
        axios.post(`/api/sessions/${this.friend.session.id}/block`);
      }
    },
    unblock() {
      if (this.session.blocked_by == this.$store.getters.getUser.id) {
        this.session.block = false;
        this.session.blocked_by = null;
        axios.post(`/api/sessions/${this.friend.session.id}/unblock`);
      }
      this.chatMenuDialog = !this.chatMenuDialog;
    },
    getAllMessages() {
      axios
        .post(`/api/sessions/${this.friend.session.id}/chats`)
        .then((res) => (this.chats = res.data));
    },
    read() {
      axios.post(`/api/sessions/${this.friend.session.id}/read`);
    },
  },
  created() {
    this.read();

    this.getAllMessages();

    window.Echo.private(`Chat.${this.friend.session.id}`).listen(
      "PrivateChatEvent",
      (e) => {
        this.friend.session.open ? this.read() : "";
        this.chats.push({
          message: e.content,
          type: 1,
          sent_at: this.$t("Just Now"),
        });
        this.friend.session.last_message[0].content = e.content;
      }
    );
    window.Echo.private(`Chat.${this.friend.session.id}`).listen(
      "MsgReadEvent",
      (e) =>
        this.chats.forEach((chat) =>
          chat.id == e.chat.id ? (chat.read_at = e.chat.read_at) : ""
        )
    );
    window.Echo.private(`Chat.${this.friend.session.id}`).listen(
      "BlockEvent",
      (e) => (
        (this.session.block = e.blocked),
        (this.session.blocked_by = e.blocked_by)
      )
    );
    window.Echo.private(`Chat.${this.friend.session.id}`).listenForWhisper(
      "typing",
      (e) => {
        this.isTyping = true;
        setTimeout(() => {
          this.isTyping = false;
        }, 2000);
      }
    );
  },
};
</script>
<style lang="scss">
@import "../../../../sass/variables";
.chat-wrapper {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  background-color: $light-theme-panel-bg-color;
  display: flex;
  flex-direction: column;
}
.chat-header {
  display: flex;
  align-items: center;
  padding: 0.4em 1em;
  box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2),
    0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
  .back {
    margin-right: 1rem;
    width: 0.8em;
  }
  .dots {
    justify-self: flex-end;
  }
  .friend-name {
    max-width: 100%;
    flex-grow: 2;
  }
  .friend-cover {
    margin-right: 0.5em;
    img {
      width: 2em;
      border-radius: 50%;
    }
  }
}

.chat-body {
  overflow-y: auto;
  padding: 0 0.5em;
  padding: 0.4em 1em;
  flex-grow: 5;
}
.chat-footer {
  padding: 0 1rem;
  padding-bottom: 0.5em;
  form {
    position: relative;
    .chat-input {
      position: relative;
      height: 2.25em;
      .error {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.95;
        z-index: 5;
      }
      width: 100%;
      input {
        width: 100%;
        color: black;
        background-color: rgb(233, 233, 233);
        border-radius: 5px;
        padding: 0.4em 1rem;
      }
    }
    .send-icon {
      position: absolute;
      right: 0.6em;
      top: 50%;
      z-index: 4;
      transform: translateY(-50%);
    }
  }
}

.msg {
  margin: 1em 0;
  position: relative;
  word-break: break-word;
  .msg-time {
    text-align: center;
    margin-bottom: 1.3em;
    font-size: 0.6em;
  }
  .seen {
    position: absolute;
    top: 105%;
    left: 5%;
    font-size: 0.85em;
    opacity: 0.65;
  }
}
.msg-text {
  background-color: rgb(167, 167, 167);
  border-radius: 17px;
  padding: 0.3em 0.8em;
  color: white;
  display: flex;
  width: auto;
  width: fit-content;
  .text {
    align-self: flex-start;
  }
}
.my-msg {
  text-align: right;
  .seen {
    left: 90%;
  }
  .msg-text {
    background-color: $color-primary;
    color: white;
    margin-left: auto;
    .text {
      align-self: flex-end;
    }
  }
}
.player--dark {
  .chat-wrapper {
    background-color: $dark-theme-panel-bg-color;
  }
}
</style>
