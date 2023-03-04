<template>
    <section class="fixed-side-layout">
        <div class="fixed-area" :class="{ borderRight }">
            <div class="img-container" v-if="$slots.img">
                <slot name="img"></slot>
            </div>
            <div class="infos-container">
                <slot name="infos" v-if="$slots.infos"></slot>
                <slot name="buttons" v-if="$slots.buttons"></slot>
            </div>
        </div>
        <div class="main">
            <slot name="main"></slot>
        </div>
    </section>
</template>
<script>
export default {
    props: ["borderRight"]
};
</script>
<style lang="scss">
@import "../../../sass/player/pages/fixed-left";
</style>
