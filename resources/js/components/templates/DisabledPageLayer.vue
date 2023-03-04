<template>
    <div class="disabled-container">
        <div class="text-container">
            <div class="content-text">
                {{ text }}
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["text"]
};
</script>
<style lang="scss" scoped>
.disabled-container {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 2;
    background-color: rgba(107, 107, 107, 0.644);
    display: flex;
    align-items: center;
    justify-content: center;
    .text-container {
        max-width: 800px;
        font-size: 1.4em;
        font-weight: Bold;
        color: white;
    }
}
</style>
