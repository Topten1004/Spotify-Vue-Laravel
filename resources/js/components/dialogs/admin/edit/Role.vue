<template>
    <edit-dialog
        @callToAction="$emit('update', editingRole)"
        @cancel="$emit('close')"
        :callToActionText="$t('Confirm')"
        :editing="$t(editingRole.name) + ' ' + $t('permissions')"
    >
        <template v-slot:body>
            <div class="edit-dialog__body">
                <v-container>
                    <div
                        class="permission"
                        v-for="permission in editingRole.available_permissions"
                        :key="permission.id"
                    >
                        <div class="checkbox">
                            <v-checkbox
                                :value="permission"
                                :messages="permission.description"
                                v-model="editingRole.current_permissions"
                            >
                                <template v-slot:label>
                                    <div class="permission_label">
                                        {{ $t(permission.name) }}
                                    </div>
                                </template>
                            </v-checkbox>
                        </div>
                    </div>
                </v-container>
            </div>
        </template>
    </edit-dialog>
</template>

<script>
export default {
    props: ["role"],
    data() {
        return {
            editingRole: JSON.parse(JSON.stringify(this.role))
        };
    }
};
</script>
