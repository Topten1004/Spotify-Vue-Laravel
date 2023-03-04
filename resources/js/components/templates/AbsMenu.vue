<template>
    <v-card class="abs-menu-container" outlined max-width="500" :light="theme">
        <slot></slot>
    </v-card>
</template>

<script>
export default {
    props: ["theme"]
};
</script>

<style lang="scss">
.abs-menu-container {
    position: absolute;
    min-width: auto;
    border-radius: 5px;
    z-index: 9999;
    &__list {
        padding: 0;
        width: 100%;
        list-style: none;
        font-size: 0.95rem;
        li {
            padding: 0.2rem 0.5rem;
            display: flex;
            white-space: nowrap;
            align-items: center;
            cursor: pointer;
            border-radius: 5px;
            i {
                margin-right: 0.5em;
            }
            &:hover {
                background-color: rgba(104, 104, 104, 0.466);
            }
        }
    }
}
</style>
