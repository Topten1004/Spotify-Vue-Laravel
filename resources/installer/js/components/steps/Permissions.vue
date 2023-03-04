<template>
    <v-container>
        <v-simple-table v-if="permissions.permissions">
            <template v-slot:default>
                <thead>
                    <tr>
                        <th class="text-left" width="90%">
                          Directory
                        </th>
                        <th class="text-left">
                          Permission
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(permission, i) in permissions.permissions"
                        :key="i"
                    >
                        <td>
                            {{ permission.folder }}
                        </td>
                        <td class="text-center">
                            <div class="bold" :class="{ 'success--text': permission.isSet, 'error--text': !permission.isSet }">
                                {{ permission.permission }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </template>
        </v-simple-table>
        <NextButton
            @clicked="$emit('next')"
            text="Next"
            :disabled="!areAllpermissionsMet"
        />
    </v-container>
</template>
<script>
import axios from "axios";
import NextButton from "../NextButton.vue";
export default {
    components: { NextButton },
    created() {
        axios.get("/api/installer/permissions").then(res => {
          this.permissions = res.data;
          this.areAllpermissionsMet = (this.permissions.permissions.filter( permission => permission.isSet ).length === this.permissions.permissions.length) && !this.permissions.errors
        });
    },
    data: () => ({
        permissions: {},
        areAllpermissionsMet: false
    })
};
</script>

<style></style>
