<template>
    <div class="notification-wrapper">
        <div class="notification__card pointer" @click="showDialog = true">
            <div class="notification__avatar">
                <img :src="notification.data.artist.avatar" alt="" />
            </div>
            <div class="info_and_options">
                <div class="notification__title">
                    {{$t('Artist account request')}}
                </div>
                <div class="notification__sub">
                    {{ notification.data.user.name }}
                </div>
                <div class="notification__details">
                    {{$t('Click to see details')}}
                </div>
            </div>
        </div>
        <v-dialog v-model="showDialog" max-width="800">
            <v-card class="p-3  mx-auto">
                <v-card-title>
                    <v-icon color="primary" x-large class="mr-3"
                        >$vuetify.icons.account-music</v-icon
                    >
                    {{$t('Artist Request')}} : {{$t('Personal Information')}}
                </v-card-title>
                <v-row class="px-4">
                    <v-col cols="auto" class="p-2">
                        <v-img :src="notification.data.artist.avatar" max-width="200px"></v-img>
                    </v-col>
                    <v-col>
                        <v-text-field
                           :label="$t('Firstname')"
                            v-model="notification.data.artist.firstname"
                            readonly
                        ></v-text-field>
                        <v-text-field
                           :label="$t('Lastname')"
                            readonly
                            v-model="notification.data.artist.lastname"
                        ></v-text-field>
                        <v-text-field
                           :label="$t('Displayname')"
                            v-model="notification.data.artist.displayname"
                            readonly
                        ></v-text-field>
                            <v-text-field
                            :label="$t('Country')"
                            readonly
                            v-model="notification.data.artist.country"
                        ></v-text-field>
                        <v-text-field
                            :label="$t('Address')"
                            readonly
                            v-model="notification.data.artist.lastname"
                        ></v-text-field>
                        <v-text-field
                            :label="$t('Email')"
                            readonly
                            v-model="notification.data.artist.email"
                        ></v-text-field>
                        <v-text-field
                            :label="$t('Phone Number')"
                            readonly
                            v-model="notification.data.artist.phone"
                            hint="+xxxxxxxxxx"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                         <edit-external-links
                            :item="notification.data.artist"
                            @spotify_link="notification.data.artist.spotify_link = $event"
                            @youtube_link="notification.data.artist.youtube_link = $event"
                            @soundcloud_link="notification.data.artist.soundcloud_link = $event"
                            @itunes_link="notification.data.artist.itunes_link = $event"
                            @deezer_link="notification.data.artist.deezer_link = $event"
                            :expanded="true"
                            :readonly="true"
                        />     
                    </v-col>
                    <v-col cols="12">
                        <v-row justify="end">
                            <v-btn
                                class="success mr-2"
                                small
                                @click="approveUserRequest()"
                                >{{$t('Approve')}}</v-btn
                            >
                            <v-btn
                                class="error"
                                small
                                @click="rejectUserRequest()"
                                >{{$t('Reject')}}</v-btn
                            >
                        </v-row>
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
            showDialog: false,
        };
    },
    methods: {
        approveUserRequest() {
            axios
                .post("/api/admin/approve-user-artist-request", {
                    notification_id: this.notification.id
                })
                .then(res => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Request Accepted"),
                        text:
                            this.notification.data.user.name + " " + 
                            this.$t("has an artist account now.")
                    });
                })
                .finally(
                    () => (
                        this.$emit("delete-notification"),
                        (this.showDialog = false)
                    )
                );
        },
        rejectUserRequest() {
            axios
                .post("/api/admin/reject-user-artist-request", {
                    notification_id: this.notification.id
                })
                .then(res => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Request Rejected"),
                        text:
                            this.notification.data.user.name + " " + 
                            "artist account request has been rejected."
                    });
                })
                .finally(
                    () => (
                        this.$emit("delete-notification"),
                        (this.showDialog = false)
                    )
                );
        }
    }
};
</script>
<style lang="scss">
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
    &__details {
        font-size: 0.7em;
        margin-top: 0.42em;
        opacity: 0.75;
    }
    &__title {
        font-weight: 600;
        margin-bottom: 0.2rem;
    }
    &__sub {
        font-size: 0.8em;
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
