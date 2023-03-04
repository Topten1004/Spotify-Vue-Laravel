<template>
    <div class="page-loading" :style="{ height: height || '50vh' }">
        <v-progress-circular
            :size="size || 70"
            :width="width || 7"
            color="primary"
            indeterminate
        ></v-progress-circular>
    </div>
</template>
<script>
export default {
    props: ['height', 'size', 'width']
}
</script>
<style>
.page-loading {
    position: relative;
}
.page-loading > * {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>
