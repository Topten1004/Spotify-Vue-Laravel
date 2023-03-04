<template>
    <edit-dialog
        @callToAction="$emit('update', ps)"
        @cancel="$emit('close')"
        callToActionText="Confirm"
        :isLoading="false"
        editing="permissions"
    >
        <template v-slot:body>
            <v-container>
                <div
                    class="permission"
                    v-for="permission in available_permissions"
                    :key="permission.id"
                >
                    <div class="checkbox">
                        <v-checkbox
                            :value="permission"
                            :messages="permission.description"
                            v-model="ps"
                        >
                            <template v-slot:label>
                                <div class="permission_label">
                                    {{ permission.name }}
                                </div>
                            </template>
                        </v-checkbox>
                    </div>
                </div>
            </v-container>
        </template>
    </edit-dialog>
</template>

<script>
export default {
    props: ["available_permissions", "current_permissions"],
    data() {
        return {
            ps : this.current_permissions
        }
    }
};
</script>
