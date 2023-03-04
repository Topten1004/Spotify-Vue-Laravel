<template>
  <v-container>
    <v-row justify="center">
        <v-btn @click="$emit('clicked')" color="primary" :disabled="disabled || loading" :loading="loading">
            {{ text }}
        </v-btn>
    </v-row>
  </v-container>
</template>

<script>
export default {
    props:[ 'text', 'disabled', 'loading' ]
}
</script>

<style>

</style>