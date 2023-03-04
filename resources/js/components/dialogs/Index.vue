<template>
    <div class="dialog">
        <div class="overlay" @click="hideDialog"></div>
        <div class="dialog__box">
            <slot></slot>
        </div>
    </div>
</template>
<script>
export default {
    computed: {
        dialog() {
            return this.$store.getters.getDialog;
        }
    },
    methods: {
        hideDialog() {
            this.$emit("end");
        }
    }
};
</script>
