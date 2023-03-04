<template>
  <v-container class="artist-container" v-if="artist">
    <v-row>
        <v-col cols="auto">
            <v-avatar :src="artist.avatar"></v-avatar>
        </v-col>
        <v-col>
            <div class="artist-dsiplayname">
                <router-link v-text="artist.displayname" :to="{ name: 'aritst', params: {id: artist.id} }"></router-link>
            </div>
        </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
    props: {
        artist: {
            type: Object
        }
    }
}
</script>

<style>

</style>