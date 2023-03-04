<template>
  <div class="control-layer">
    <div class="actions">
      <div class="move-left">
        <v-btn dark icon large @click.stop="$emit('moveLeft')">
          <v-icon> $vuetify.icons.arrow-left </v-icon>
        </v-btn>
      </div>
      <div class="remove">
        <v-btn dark icon large @click.stop="$emit('delete')">
          <v-icon color="error"> $vuetify.icons.delete </v-icon>
        </v-btn>
      </div>
      <div class="move-right">
        <v-btn dark icon large @click.stop="$emit('moveRight')">
          <v-icon> $vuetify.icons.arrow-right </v-icon>
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss">
.control-layer {
  .actions {
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
}
</style>