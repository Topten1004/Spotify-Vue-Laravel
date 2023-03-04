<template>
    <v-autocomplete
        v-model="selectedUsers"
        :items="searchedUsers"
        :loading="isSearchUserLoading"
        :search-input.sync="searchUsers"
        :label="multiple ? $t('Users') : $t('User')"
        item-text="name"
        return-object
        hide-details
        :placeholder="$t('Start typing to Search')"
        :multiple="multiple"
        outlined
    >
        <template v-slot:selection="{ item }">
            <v-chip                
                close
                @click:close="remove(item.id)"
            >
                <v-avatar left>
                    <v-img :src="item.avatar" v-if="item.avatar"></v-img>
                    <v-icon  v-else>$vuetify.icons.account-music</v-icon>
                </v-avatar>
                {{ item.name + '(' +  item.email +')' }}
            </v-chip>
        </template>
        <template v-slot:item="{ item }">
            <template>
                <v-list-item-avatar>
                    <v-img :src="item.avatar" v-if="item.avatar"></v-img>
                    <v-icon  v-else>$vuetify.icons.account-music</v-icon>
                </v-list-item-avatar>
                <v-list-item-content>
                <v-list-item-title v-html="item.name"></v-list-item-title>
                <!-- <v-list-item-subtitle v-html="item.group"></v-list-item-subtitle> -->
                </v-list-item-content>
            </template>
        </template>
    </v-autocomplete>
</template>

<script>
export default {
    props: ['users', 'multiple'],
    data() {
        return {
            searchedUsers: JSON.parse(JSON.stringify(this.users)),
            selectedUsers: JSON.parse(JSON.stringify(this.users)),
            isSearchUserLoading: false,
            searchUsers: null
        };
    },
    watch: {
        searchUsers(val) {
            if (this.isSearchUserLoading) return;
            this.isSearchUserLoading = true;
            axios
                .get("/api/match-users" + "?query=" + val)
                .then(res => {
                    res.data.forEach(ar => {
                        this.searchedUsers.push(ar);
                    });
                })
                .catch()
                .finally(() => (this.isSearchUserLoading = false));
        },
        selectedUsers(val) {
            this.$emit('users', val)
        }
    },
    methods: {
        pushUser(item) {
            this.selectedUsers.push(item)
        },
        remove(id) {
            if( this.multiple ) {
                const index = this.selectedUsers.findIndex(item => item.id === id);
                if (index >= 0) this.selectedUsers.splice(index, 1);
            } else {
                this.selectedUsers = null
            }
           
        }
    }
};
</script>

<style></style>
