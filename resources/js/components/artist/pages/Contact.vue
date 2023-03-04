<template>
    <v-container>
        <v-card max-width="800px" class="mx-auto" outlined>
            <v-card-title>{{  $t('Contact Us') }}</v-card-title>
            <div class="card-body pb-3">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                :label="$t('Subject')"
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
                                :initialContent="contact.message"
                                :placeholder="'Add your message here'"
                            />
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="text-center">
                            <v-btn class="success py-3" @click="sendMessage" min-width="100" :disabled="sent || loading" :loading="loading">{{
                                $t("Send")
                            }}</v-btn>
                        </v-col>
                    </v-row>
                    <!-- <v-row>
                        {{ $t('NB') + ': ' + $t('You can send maximum 1 message per day') }}
                    </v-row> -->
                </v-container>
            </div>
        </v-card>
    </v-container>
</template>

<script>
import SelectUsers from "../../elements/select/Users.vue";
const TextEditor = () => import(/* webpackChunkName: "textEditor" */ '../../elements/other/TextEditor.vue');
export default {
    components: { SelectUsers, TextEditor },
    data() {
        return {
            contact: {
                to: 'admins',
                title: "",
                message: ""
            },
            loading: false,
            sent: false
        };
    },
    methods: {
        sendMessage() {
            this.loading = true
            axios.post('/api/artist/contact', {
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
                    text: this.$t('Some error occured. Please try again!')
                });
            })
        }
    }
};
</script>

<style></style>
