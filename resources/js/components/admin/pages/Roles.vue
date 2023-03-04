<template>
  <div class="settings-container" v-if="roles">
    <v-container fluid>
      <v-card>
        <v-row justify="space-between" class="px-4 pt-2 pb-2">
          <div class="header">
            <v-icon color="primary" x-large>$vuetify.icons.account-network</v-icon>
            <span class="title">{{$t('Roles')}}</span>
          </div>
          <div class="options">
            <v-btn
              class="info"
              v-if="hasPermission('Edit roles')"
              :disabled="loading"
              @click="reset"
              >{{ $t('Reset') }}</v-btn
            >
            <v-btn
              class="success"
              @click="save"
              v-if="hasPermission('Edit roles')"
              :disabled="loading"
              >{{$t('Save Changes')}}</v-btn
            >
          </div>
        </v-row>
      </v-card>
      <v-row justify="space-between" class="px-4 pt-2 pb-2">
        <v-alert
          border="top"
          colored-border
          type="info"
          elevation="2"
          color="primary"
        >
         Here you can set the default permissions for the different roles, Keep in mind that changing permissions here will only take impact on the next role attachments or new user accounts. If you want to remove/add permissions for specific users you can do it on:
          <code>users > user > edit permissions</code>.
          <br />
          <br />
          <strong>CED</strong> : Create, edit and delete.
        </v-alert>
      </v-row>
      <v-row>
        <v-col lg="4" sm="12" v-for="role in roles" :key="role.id">
          <v-card>
            <v-card-title>
              <v-icon color="primary">$vuetify.icons.account</v-icon> {{ role.name }}
            </v-card-title>
            <v-container class="pl-2" fluid>
              <div class="card-bb-header">
                <div class="header">
                  <div class="title">{{$t('Permissions')}}</div>
                  <div class="buttons">
                    <v-btn
                      class="info white--text"
                      small
                      @click="editRole(role)"
                      v-if="hasPermission('Edit roles')"
                    >
                      <v-icon small>$vuetify.icons.plus</v-icon> {{$t('Edit')}}
                    </v-btn>
                  </div>
                </div>
                <v-divider class="py-2"></v-divider>
                <div class="body">
                  <div
                    class="item"
                    v-for="(permission, i) in role.current_permissions"
                    :key="permission.id"
                  >
                    <v-chip
                      :close="hasPermission('Edit roles')"
                      @click:close="role.current_permissions.splice(i, 1)"
                      >{{ permission.name }}</v-chip
                    >
                  </div>
                </div>
              </div>
            </v-container>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <v-dialog v-model="showDialog" max-width="600">
      <edit-role-dialog
        v-if="showDialog && editingRole"
        @update="updateRolePermission($event)"
        @close="showDialog = false"
        :role="editingRole"
      />
    </v-dialog>
  </div>
</template>

<script>
import editRoleDialog from "../../dialogs/admin/edit/Role";
export default {
  components: {
    editRoleDialog,
  },
  created() {
    this.fetchRoles();
  },
  data() {
    return {
      roles: null,
      editingRole: null,
      showDialog: false,
      loading: false,
    };
  },
  methods: {
    fetchRoles() {
      return axios
        .get("/api/admin/roles")
        .then((res) => (this.roles = res.data));
    },
    save() {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna save the current permissions?")}`,
        button: {
          no: this.$t("Cancel"),
          yes: "Save",
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            this.loading = true;
            axios
              .post("/api/admin/save-roles", { roles: this.roles })
              .then(() => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Updated"),
                  text: this.$t('Roles') + ' ' + this.$t('updated successfully.'),
                });
              })
              .finally(() => (this.loading = false));
          }
        },
      });
    },
    reset() {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna reset to the default settings?")}`,
        button: {
          no: this.$t("Cancel"),
          yes: "Reset",
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            this.loading = true;
            axios
              .post("/api/admin/reset-roles")
              .then(() => {
                this.fetchRoles().then(() => {
                  this.$notify({
                    group: "foo",
                    type: "success",
                    title: this.$t("Success"),
                    text: this.$t('Roles reset successfully.'),
                  });
                });
              })
              .finally(() => (this.loading = false));
          }
        },
      });
    },
    editRole(role) {
      this.editingRole = role;
      this.showDialog = true;
    },
    updateRolePermission(role) {
      this.roles.splice(
        this.roles.findIndex((r) => r.id === role.id),
        1,
        role
      );
      this.showDialog = false;
    },
  },
};
</script>