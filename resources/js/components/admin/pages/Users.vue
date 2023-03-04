<template>
    <div class="users-wrapper">
        <v-card outlined>
            <v-card-title>
                <v-icon color="primary" x-large>$vuetify.icons.account-circle</v-icon>
                <v-btn
                    class="mx-2"
                    dark
                    small
                    color="primary"
                    @click="editUser('new')"
                    v-if="hasPermission('Create users')"
                >
                    <v-icon>$vuetify.icons.plus</v-icon> {{ $t("New") }}
                </v-btn>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <div class="admin-search-bar">
                    <v-text-field
                        v-model="search"
                        append-icon="mdi-magnify"
                        :label="$t('Search')"
                        single-line
                        hide-details
                    ></v-text-field>
                </div>
            </v-card-title>
            <v-data-table
        :no-data-text="$t('No data available')"
                :loading-text="$t('Fetching data') + '...'"
                :headers="headers"
                :items="users || []"
                :items-per-page="25"
                :search="search"
                :loading="!users"
                class="elevation-1"
            >
                <template v-slot:item.avatar="{ item }">
                    <div class="img-container py-2">
                        <v-img
                            :src="item.avatar"
                            :alt="item.name"
                            class="user-avatar"
                            width="50"
                            height="50"
                        ></v-img>
                    </div>
                </template>
                <template v-slot:item.roles="{ item }">
                    <span v-for="(role, i) in item.roles" :key="i">
                        <v-chip
                            class="mx-1"
                            small
                            color="orange"
                            v-if="role.name === 'admin'"
                            text-color="white"
                            :title="$t('admin')"
                        >
                            <v-avatar>
                                <v-icon>$vuetify.icons.account-circle</v-icon>
                            </v-avatar>
                        </v-chip>
                        <v-chip
                            class="mx-1"
                            small
                            color="primary"
                            v-if="role.name === 'artist'"
                            text-color="white"
                            :title="$t('artist')"
                        >
                            <v-avatar>
                                <v-icon>$vuetify.icons.account-music</v-icon>
                            </v-avatar>
                        </v-chip>
                    </span>
                </template>
                <template v-slot:item.operations="{ item }">
                    <v-btn
                        class="mx-2"
                        @click="editUser(item)"
                        v-if="hasPermission('Edit users')"
                        x-small
                        fab
                        dark
                        color="info"
                    >
                        <v-icon>$vuetify.icons.pencil</v-icon>
                    </v-btn>
                    <v-btn
                        class="mx-2"
                        @click="deleteUser(item.id)"
                        v-if="hasPermission('Delete users')"
                        x-small
                        fab
                        dark
                        color="error"
                    >
                        <v-icon>$vuetify.icons.delete</v-icon>
                    </v-btn>
                </template>
                <template v-slot:item.created_at="{ item }">
                    {{ moment(item.created_at).format("ll") }}
                </template>
            </v-data-table>
        </v-card>
        <v-dialog v-model="editDialog" max-width="500">
            <edit-user-dialog
                v-if="editDialog"
                @updated="userEdited"
                @close="editUser(null)"
                :user="editingUser"
            />
        </v-dialog>
    </div>
</template>
<script>
import editUserDialog from "../../dialogs/admin/edit/User";
export default {
    components: {
        editUserDialog
    },
    data() {
        return {
            users: null,
            search: "",
            roles: null,
            headers: [
                {
                    text: this.$t("Avatar"),
                    align: "start",
                    sortable: false,
                    value: "avatar"
                },
                { text: this.$t("Email"), value: "email" },
                { text: this.$t("Name"), value: "name" },
                { text: this.$t("Roles"), value: "roles", align: "center" },
                { text: this.$t("Created At"), value: "created_at" },
                {
                    text: this.$t("Operations"),
                    value: "operations",
                    align: "center"
                }
            ],
            editDialog: false,
            editingUser: null
        };
    },
    created() {
        this.fetchUsers();
        this.fetchRoles();
    },
    methods: {
        fetchUsers() {
            axios.get("/api/admin/users").then(res => {
                this.users = res.data;
            });
        },
        fetchRoles() {
            axios.get("/api/admin/roles").then(res => {
                this.roles = res.data;
            });
        },
        makeAdmin(id) {
            var self = this;
            axios
                .post("/api/admin/make-admin", {
                    user_id: id
                })
                .then(res => {
                    var user = self.users.find(x => x.id == id);
                    user.roles.push(res.data);
                })
                .catch(() => {});
        },
        takeAdmin(id) {
            var self = this;
            axios
                .post("/api/admin/take-admin", {
                    user_id: id
                })
                .then(res => {
                    var user = self.users.find(x => x.id == id);
                    user.roles.splice(user.roles.indexOf(res.data), 1);
                })
                .catch();
        },
        deleteUser(id) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this") +
                    " " +
                    this.$t("user") +
                    "?"}`,
                button: {
                    no: "No",
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        axios
                            .delete("/api/admin/users/" + id, {
                                user_id: id
                            })
                            .then(res => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text:
                                        this.$t("User") +
                                        " " +
                                        this.$t("Deleted") +
                                        "."
                                });
                                this.fetchUsers();
                            })
                            .catch();
                    }
                }
            });
        },
        editUser(user) {
            if (!user) {
                this.editDialog = false;
                this.editingUser = null;
            } else if (user === "new") {
                var userRole = this.roles.find(role => role.name === "user");
                this.editingUser = {
                    new: true,
                    roles: [userRole],
                    permissions: userRole.current_permissions
                };
                this.editDialog = true;
            } else {
                this.editingUser = user;
                this.editDialog = true;
            }
        },
        userEdited() {
            this.editDialog = false;
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Saved"),
                text: this.$t("User") + " " + this.$t("Updated") + "."
            });
            this.fetchUsers();
        }
    }
};
</script>
