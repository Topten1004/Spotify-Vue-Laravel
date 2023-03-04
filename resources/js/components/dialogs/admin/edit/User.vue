<template>
    <edit-dialog
        @callToAction="saveUser"
        @cancel="closeWindow"
        :loading="isLoading"
        editing="User"
    >
        <template v-slot:body>
            <v-container>
                <v-row>
                    <v-col cols="5">
                        <upload-image
                            @imageReady="imageReady($event)"
                            :source="editedUser.avatar || defaultAvatarPath"
                        />
                    </v-col>
                    <v-col cols="7">
                        <v-text-field
                           :label="$t('Email')"
                            v-model="editedUser.email"
                        ></v-text-field>
                        <v-text-field
                           :label="$t('Password')"
                            class="mt-4"
                            type="password"
                            v-model="editedUser.password"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                           :label="$t('Name')"
                            v-model="editedUser.name"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <div class="card-bb-header">
                            <div class="header">
                                <div class="title">{{$t('Roles')}}</div>
                                <div class="buttons">
                                    <v-btn
                                        @click="rolesDialog = true"
                                        x-small
                                        fab
                                        dark
                                        color="info"
                                        v-if="hasPermission('Edit user roles')"
                                    >
                                        <v-icon>$vuetify.icons.pencil</v-icon>
                                    </v-btn>
                                </div>
                            </div>
                            <v-divider class="py-2"></v-divider>
                            <div class="body">
                                <v-chip-group>
                                    <v-chip
                                        @click:close="user.roles.splice(i, 1)"
                                        :close="
                                            hasPermission('Edit user roles') &&
                                                role.name !== 'user'
                                        "
                                        v-for="(role, i) in roles_comp"
                                        :key="i"
                                    >
                                        {{ role.name }}
                                    </v-chip>
                                </v-chip-group>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12">
                        <div class="card-bb-header">
                            <div class="header">
                                <div class="title">{{$t('Permissions')}}</div>
                                <div class="buttons">
                                    <v-btn
                                        @click="editPermissions"
                                        x-small
                                        fab
                                        dark
                                        color="info"
                                        v-if="
                                            hasPermission(
                                                'Edit user permissions'
                                            )
                                        "
                                    >
                                        <v-icon>$vuetify.icons.pencil</v-icon>
                                    </v-btn>
                                </div>
                            </div>
                            <v-divider class="py-2"></v-divider>
                            <div class="body">
                                <div
                                    class="item"
                                    v-for="(permission, i) in permissions_comp"
                                    :key="i"
                                >
                                    <v-chip
                                        :close="
                                            hasPermission(
                                                'Edit user permissions'
                                            )
                                        "
                                        @click:close="
                                            user.permissions.splice(i, 1)
                                        "
                                        >{{ permission.name }}</v-chip
                                    >
                                </div>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>{{ $t('Storage Space') }} </v-card-title>
                            <v-divider></v-divider>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="9">
                                            <v-text-field
                                               :label="$t('Size')"
                                                type="number"
                                                v-model="available_disk_space"
                                                :messages="
                                                    editedUser.used_disk_space
                                                        ? (
                                                              editedUser.used_disk_space /
                                                              1024 /
                                                              1024
                                                          ).toFixed(1) +
                                                          + ' ' + $t('MB') + ' ' + $t('used already.')
                                                        : ''
                                                "
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="3">
                                            <veutify-select
                                                type="number"
                                                :items="['MB', 'GB', 'KB']"
                                                v-model="
                                                    available_disk_space_unit
                                                "
                                            ></veutify-select>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </template>
        <template v-slot:dialogs>
            <v-dialog v-model="rolesDialog" max-width="500px">
                <v-card>
                    <v-card-title>
                        <div>{{$t('Choose Roles')}}</div>
                        <v-spacer></v-spacer>
                    </v-card-title>
                    <v-card-text>
                        <v-list-item-group>
                            <v-list-item
                                v-for="(role, i) in availableRoles_comp"
                                :key="i"
                            >
                                <v-list-item-action>
                                    <v-checkbox
                                        v-model="roles_comp"
                                        :value="role"
                                        :disabled="role.name === 'user'"
                                    ></v-checkbox>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>{{
                                        role.name
                                    }}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-item-group>
                    </v-card-text>
                    <v-card-actions>
                        <v-col>
                            <div class="dialog-actions">
                                <v-btn
                                    @click="rolesDialog = false"
                                    class="success"
                                    >{{ $t('Save') }}</v-btn
                                >
                            </div>
                        </v-col>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="permissionsDialog" max-width="500">
                <edit-permissions-dialog
                    v-if="permissionsDialog"
                    @update="updatePermissions($event)"
                    @close="permissionsDialog = false"
                    :available_permissions="availablePermissions_comp"
                    :current_permissions="permissions_comp"
                />
            </v-dialog>
        </template>
    </edit-dialog>
</template>

<script>
import { VSelect } from "vuetify/lib";
import EditPermissionsDialog from "../../../dialogs/admin/edit/Permissions";
export default {
    props: ["user"],
    components: {
        veutifySelect: VSelect,
        EditPermissionsDialog
    },
    created() {
        axios.get("/api/admin/roles").then(res => {
            this.availableRoles = res.data;
        });
    },
    data() {
        return {
            availableRoles: [],
            permissionsDialog: false,
            editedUser: this.user,
            firstCopy: JSON.parse(JSON.stringify(this.user)),
            isLoading: false,
            defaultAvatarPath: "/storage/defaults/images/user_avatar.png",
            availablePermissions: [],
            rolesDialog: false,
            available_disk_space:
                parseInt(this.user.available_disk_space) ||
                parseInt(this.$store.getters.getSettings.availableUserDiskSpace) || 0,
            available_disk_space_unit: "MB"
        };
    },
    computed: {
        roles_comp: {
            set(val) {
                this.user.roles = this.availableRoles.filter(role =>
                    val.find(x => x.id === role.id)
                );
            },
            get() {
                return this.user.roles.map(role => {
                    return {
                        name: role.name,
                        id: role.id
                    };
                });
            }
        },
        availableRoles_comp() {
            return this.availableRoles.map(role => {
                return {
                    name: role.name,
                    id: role.id
                };
            });
        },
        permissions_comp: {
            set(val) {
                this.user.permissions = this.availablePermissions_comp.filter(
                    permission => val.find(x => x.id === permission.id)
                );
            },
            get() {
                return this.user.permissions;
            }
        },
        availablePermissions_comp() {
            return this.availableRoles.filter(role => this.user.roles.find( r => r.id == role.id)).reduce((acc, val) => {
                acc = acc.concat(val.available_permissions);
                return acc;
            }, []);
        },
        currentPermissions_comp() {
            return this.availableRoles.filter(role => this.user.roles.find( r => r.id == role.id)).reduce((acc, val) => {
                acc = acc.concat(val.current_permissions);
                return acc;
            }, []);
        }
    },
    watch: {
        roles_comp() {
            this.permissions_comp = this.currentPermissions_comp;
        }
    },
    methods: {
        imageReady(e) {
            this.editedUser.avatar = e;
        },
        editPermissions() {
            this.permissionsDialog = true;
        },
        updatePermissions(permissions) {
            this.user.permissions = permissions;
            this.permissionsDialog = false;
        },
        closeWindow() {
            let changed = false;
            if (this.editedUser.name != this.firstCopy.name) {
                changed = true;
            }
            if (this.editedUser.email != this.firstCopy.email) {
                changed = true;
            }
            if (
                this.editedUser.roles
                    .map(role => {
                        return {
                            name: role.name,
                            id: role.id
                        };
                    })
                    .join("") !=
                this.firstCopy.roles
                    .map(role => {
                        return {
                            name: role.name,
                            id: role.id
                        };
                    })
                    .join("")
            ) {
                changed = true;
            }
            if (this.editedUser.avatar != this.firstCopy.avatar) {
                changed = true;
            }
            if (changed) {
                this.$confirm({
                    message: `${this.$t("Are you sure you wanna quit without saving the changes ?")}`,
                    button: {
                        no: this.$t("Cancel"),
                        yes: this.$t("Discard")
                    },
                    callback: confirm => {
                        if (confirm) {
                            this.editedUser.name = this.firstCopy.name;
                            this.editedUser.roles = this.firstCopy.roles;
                            this.editedUser.email = this.firstCopy.email;
                            this.editedUser.avatar = this.firstCopy.avatar;
                            this.$emit("close");
                        }
                    }
                });
            } else {
                this.$emit("close");
            }
        },
        saveUser() {
            var formData = new FormData();
            this.isLoading = true;
            formData.append("email", this.editedUser.email);
            if (this.editedUser.password) {
                formData.append("password", this.editedUser.password);
            }
            formData.append("name", this.editedUser.name);
            if (this.available_disk_space_unit === "GB") {
                var available_disk_space = this.available_disk_space * 1024;
            } else if (this.available_disk_space_unit === "KB") {
                var available_disk_space = this.available_disk_space / 1024;
            } else {
                var available_disk_space = this.available_disk_space;
            }
            this.editedUser.available_disk_space = available_disk_space;
            formData.append("available_disk_space", available_disk_space);
            formData.append("roles", JSON.stringify(this.user.roles));
            formData.append(
                "permissions",
                JSON.stringify(this.user.permissions)
            );
            if (this.editedUser.avatar && this.editedUser.avatar.data) {
                formData.append(
                    "avatar",
                    this.editedUser.avatar.data,
                    this.editedUser.avatar.title
                );
            } else if (this.editedUser.avatar && !this.editedUser.avatar.data) {
                // no avatar was picked, the value is stored as a string
                formData.append("avatar", this.editedUser.avatar);
            } else {
                formData.append("avatar", this.defaultAvatarPath);
            }
            if (this.editedUser.new) {
                axios
                    .post("/api/admin/users", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    })
                    .then(() => {
                        this.$emit("updated");
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
                formData.append("user_id", this.editedUser.id);
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/users/" + this.editedUser.id, formData, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    })
                    .then(res => {
                        this.$emit("updated");
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
