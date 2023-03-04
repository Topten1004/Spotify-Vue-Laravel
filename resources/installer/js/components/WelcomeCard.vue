<template>
    <v-card max-width="500px">
        <div class="logo align-justify-center py-4">
            <v-img max-width="90px" src="/images/logo.png" alt="Logo Image">
            </v-img>
        </div>
        <div class="display-1 bold text-center primary--text py-4">
            Muzzie Installer
        </div>
        <div class="text px-10">
            <p class="text-center">
                This installation will initial your app, when it is done make
                sure to log into your admin account to check the settings and
                customize the app the way you like.
            </p>
        </div>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="primary" @click="$emit('startInstall')">
                Start
            </v-btn>
            <v-spacer></v-spacer>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {};
</script>

<style></style>
