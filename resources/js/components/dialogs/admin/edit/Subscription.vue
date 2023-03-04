<template>
    <edit-dialog
        @callToAction="saveSubscription"
        @cancel="closeWindow"
        :loading="isLoading"
        :disableCallToAction="
            Boolean(
                editedSubscription.owner &&
                    editedSubscription.owner.subscription
            )
        "
        editing="subscription"
    >
        <template v-slot:body>
            <v-container>
                <v-row
                    justify="center"
                    v-if="
                        editedSubscription.owner &&
                            editedSubscription.owner.subscription
                    "
                >
                    <v-alert type="error">
                        {{$t('User already has an active subscription.')}}</v-alert
                    >
                </v-row>
                <v-row>
                    <v-col>
                        <template>
                            <v-card>
                                <v-row justify="space-between">
                                    <v-col cols="8">
                                        <v-card-title>
                                            {{$t('Owner')}}
                                        </v-card-title>
                                        <div
                                            v-if="
                                                editedSubscription.owner &&
                                                    editedSubscription.owner.id
                                            "
                                        >
                                            <!-- <div class="user-avatar">
                                        <v-img :src="editedSubscription.user.avatar"></v-img>
                                    </div> -->
                                            <div class="user-details p-2 pl-3">
                                                <v-container>
                                                    <v-row>
                                                        <div class="name">
                                                            <strong
                                                                >{{$t('Name')}}:
                                                            </strong>
                                                            {{
                                                                editedSubscription
                                                                    .owner.name
                                                            }}
                                                        </div>
                                                    </v-row>
                                                    <v-row>
                                                        <div class="email">
                                                            <strong
                                                                >{{$t('Email')}}:
                                                            </strong>
                                                            {{
                                                                editedSubscription
                                                                    .owner.email
                                                            }}
                                                        </div>
                                                    </v-row>
                                                </v-container>
                                            </div>
                                        </div>
                                    </v-col>
                                    <v-col
                                        cols="3"
                                        class="p-2"
                                        v-if="
                                            editedSubscription.owner &&
                                                editedSubscription.owner.id
                                        "
                                    >
                                        <v-img
                                            :src="
                                                editedSubscription.owner.avatar
                                            "
                                        ></v-img>
                                    </v-col>
                                </v-row>

                                <div
                                    class="p-2"
                                    v-if="
                                        !editedSubscription.owner ||
                                            !editedSubscription.owner.id
                                    "
                                >
                                    <AttachAssetDialog
                                        @asset="
                                            $set(
                                                editedSubscription,
                                                'owner',
                                                $event
                                            )
                                        "
                                        type="user"
                                    />
                                </div>
                            </v-card>
                        </template>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <veutifySelect
                           :label="$t('Plan')"
                            :items="plans"
                            item-value="id"
                            v-model="editedSubscription.plan_id"
                        >
                            <template v-slot:item="{ item }">
                                {{ item.name }} ({{
                                    planCurrencySymbol(item) +
                                        price(item.amount) +
                                        "/" +
                                        item.interval
                                }})
                            </template>
                            <template v-slot:selection="{ item }">
                                {{ item.name }} ({{
                                    planCurrencySymbol(item) +
                                        price(item.amount) +
                                        "/" +
                                        item.interval
                                }})
                            </template>
                        </veutifySelect>
                    </v-col>
                    <!-- upcoming feature -->
                    <!-- <v-col>
                        <v-menu
                            ref="menu"
                            v-model="dateMenu"
                            :close-on-content-click="false"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="editedSubscription.renews_at"
                                   :label="$t('Release date')"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                ref="picker"
                                v-model="editedSubscription.renews_at"
                                :max="new Date().toISOString().substr(0, 10)"
                                :min="moment()"
                                @change="saveReleaseDate"
                            ></v-date-picker>
                        </v-menu>
                    </v-col> -->
                </v-row>
            </v-container>
        </template>
    </edit-dialog>
</template>

<script>
import AttachAssetDialog from "../AttachAssetDialog";
import { VSelect } from "vuetify/lib";
export default {
    props: ["subscription"],
    components: {
        veutifySelect: VSelect,
        AttachAssetDialog
    },
    created() {
        axios.get("/api/plans").then(res => {
            this.plans = res.data;
        });
    },
    data() {
        return {
            usersFocused: false,
            editedSubscription: this.subscription,
            firstCopy: JSON.parse(JSON.stringify(this.subscription)),
            isLoading: false,
            users: [],
            dateMenu: false,
            plans: []
        };
    },
    watch: {
        dateMenu(val) {
            val && setTimeout(() => (this.$refs.picker.activePicker = "YEAR"));
        }
    },
    methods: {
        closeWindow() {
            this.$emit("close");
        },
        saveReleaseDate(date) {
            this.$refs.menu.save(date);
        },
        fetchUsers(search, loading) {
            if (search) {
                loading(true),
                    axios
                        .get("/api/match-users/" + search)
                        .then(
                            res =>
                                (this.users = res.data.map(user => ({
                                    id: user.id,
                                    email: user.displayname,
                                    avatar: user.avatar
                                })))
                        )
                        .finally(() => loading(false));
            }
        },
        saveSubscription() {
            this.isLoading = true;
            if (this.editedSubscription.new) {
                axios
                    .post("/api/admin/subscriptions", {
                        gateway: 'local',
                        user_id: this.editedSubscription.owner.id,
                        plan_id: this.editedSubscription.plan_id,
                        renews_at: this.editedSubscription.renews_at
                    })
                    .then(() => {
                        this.$emit("update");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.errors = e.response.data.errors;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    });
            } else {
                formData.append("subscription_id", this.editedSubscription.id);
                formData.append("_method", "PUT");
                axios
                    .post(
                        "/api/admin/subscriptions/" +
                            this.editedSubscription.id,
                        {
                            gateway: 'local',
                            user_id: this.editedSubscription.owner.id,
                            plan_id: this.editedSubscription.plan_id,
                            renews_at: this.editedSubscription.renews_at
                        }
                    )
                    .then(() => {
                        this.$emit("update");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.errors = e.response.data.errors;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    });
            }
        }
    }
};
</script>
