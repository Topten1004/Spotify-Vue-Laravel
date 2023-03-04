<template>
    <v-container>
        <v-card max-width="800px" class="mx-auto" outlined>
            <v-card-title>{{$t('Contact')}}</v-card-title>
            <div class="card-body pb-3">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-select
                                :items="contactOptions"
                                v-model="contact.to"
                                outlined
                                item-text="text"
                                item-value="value"
                                :label="$t('Send to')"
                                hide-details=""
                            ></v-select>
                        </v-col>
                        <template v-if="contact.to === 'specific_users'">
                            <v-col cols="12">
                                <select-users
                                    :multiple="true"
                                    :users="contact.usersToBeContacted"
                                    @users="contact.usersToBeContacted = $event"
                                ></select-users>
                            </v-col>
                        </template>
                         <template v-if="contact.to === 'specific_artists'">
                            <v-col cols="12">
                                <select-artists
                                    :multiple="true"
                                    :engines="'local'"
                                    :artists="contact.usersToBeContacted"
                                    @artists="contact.usersToBeContacted = $event"
                                ></select-artists>
                            </v-col>
                        </template>

                        <v-col cols="12">
                            <v-text-field
                                :label="$t('Title')"
                                v-model="contact.title"
                                outlined
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <div class="message py-2">
                                <div class="title">{{ $t('Message') }}</div>
                                <v-divider></v-divider>
                            </div>
                            <TextEditor
                                @content="contact.message = $event"
                                :placeholder="$t('Add your message here')"
                                :initialContent="contact.message"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-switch :label="$t('Mark As Important Message')" v-model="contact.important"></v-switch>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="text-center">
                            <v-btn class="success py-3" @click="sendMessage" min-width="100" :disabled="sent || loading" :loading="loading">{{
                                $t("Send")
                            }}</v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </div>
        </v-card>
    </v-container>
</template>

<script>
import SelectUsers from "../../elements/select/Users.vue";
import SelectArtists from "../../elements/select/Artists.vue";
const TextEditor  = () =>  import(/* webpackChunkName: "textEditor" */ "../../elements/other/TextEditor.vue");
export default {
    components: { SelectUsers, TextEditor, SelectArtists },
    data() {
        return {
            contactOptions: [
                {
                    text: this.$t("All Users"),
                    value: "all_users"
                },
                {
                    text: this.$t("All Artists"),
                    value: "all_artists"
                },
                {
                    text: this.$t("Specific Users"),
                    value: "specific_users"
                },
                {
                    text: this.$t("Specific Artists"),
                    value: "specific_artists"
                }
            ],
            contact: {
                to: null,
                title: "",
                message: "",
                usersToBeContacted: []
            },
            loading: false,
            sent: false
        };
    },
    methods: {
        sendMessage() {
            this.loading = true
            axios.post('/api/admin/contact', {
                ...this.contact
            })
            .then(() => {
                this.loading = false;
                this.sent = true
                this.$notify({
                    group: "foo",
                    type: "success",
                    title: this.$t("Sent"),
                    text: this.$t('Message') + ' ' + this.$t('sent successfully.'),
                });
            })
            .catch(() => {
                this.loading = false;
                this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Error"),
                    text: this.$t('Some error occurred. Please try again!')
                });
            })
        }
    }
};
</script>

<style></style>
