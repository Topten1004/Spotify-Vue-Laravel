<template>
    <div class="notification-wrapper">
        <div class="notification__card" @click="showDialog = true">
            <div class="notification__avatar">
                <img :src="notification.data.from.avatar" alt="" />
            </div>
            <div class="info_and_options w-100">
                <div class="notification__title pb-1 align-center justify-between">
                    <div class="mr-2">
                        {{ notification.data.from.name }}
                    </div>
                    <div class="badges" v-if="notification.data.important">
                        <v-btn color="error" outlined x-small>
                            {{$t('Important')}}
                        </v-btn>
                    </div>
                </div>
                <div class="notification__sub">
                    {{ notification.data.title }}
                </div>
                <div class="notification__detail">
                    {{$t('Click to see details')}}
                </div>
            </div>
        </div>
        <v-dialog v-model="showDialog" max-width="800">
            <v-card class="p-3 mx-auto" :class="{  'dark-backround' : $vuetify.theme.dark }">
                <v-card-title class="bold">
                    <div class="justify-between w-100">
                        <div class="notification__title">
                            {{ notification.data.title }}
                        </div>
                        <v-btn @click="markAsRead" class="success" small v-if="!notification.read_at">{{
                            $t("Mark as read")
                        }}</v-btn>
                        <v-btn v-else outlined color="success">{{ $t('Read') }}</v-btn>
                    </div>
                </v-card-title>
                <v-divider></v-divider>
                <v-row class="px-5 pt-5" justify="space-between">
                    <v-col v-html="notification.data.message"></v-col>
                </v-row>
                <v-row>
                    <v-col cols="12">
                        <div class="px-">
                            <div class="sender">
                            {{ $t("Message from") }}
                        </div>
                        <v-divider></v-divider>
                        <div class="align-center  pt-3">
                        <div class="notification__avatar">
                            <img :src="notification.data.from.avatar" alt="" />
                        </div>
                        <div class="info_and_options justify-between align-center w-100">
                            <div class="notification__title d-flex">
                                <div class="sender-name mr-2">
                                    {{ notification.data.from.name }}
                                </div>
                                <div class="sender-title sub">
                                   <v-btn x-small class="ml-2" color="success" outlined>{{ notification.data.from.role }}</v-btn>
                                </div>
                            </div>
                            <div title="User Email sub" class="user-email" v-if="notification.data.from.email">
                                {{ notification.data.from.email }}
                            </div>
                        </div>
                        </div>
                        </div>
                    </v-col>
                </v-row>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
export default {
    props: ["notification"],
    data() {
        return {
            showDialog: false
        };
    },
    methods: {
        markAsRead() 
        {
            axios.post('/api/mark-as-read/' + this.notification.id)
            .finally(() => {
                this.notification.read_at = new Date();
                this.showDialog = false;
                this.$emit('read')
            })
        }
    }
};
</script>
<style lang="scss" scoped>
.notification__card {
    display: flex;
    align-items: center;
    padding: 0.3rem 0.3rem;
}
.notification {
    &__avatar {
        margin-right: 1em;
        img {
            border-radius: 50%;
            width: 3.3em;
            height: 3.3em;
        }
    }
    &__title {
        // font-weight: 600;
        // margin-bottom: 0.2rem;
    }
    &__sub {
        // font-size: 0.8em;
        // opacity: 0.75;
        font-weight: bold;
    }
    &__detail {
        font-size: 0.7em;
        margin-top: 0.42em;
        opacity: 0.75;
    }
    &__buttons {
        margin-top: 0.3em;
        .buttons {
            display: flex;
            & > * {
                flex-basis: 50%;
            }
            & > .btn-approve {
                margin-right: 1.5em;
            }
        }
    }
}
</style>
