<template>
    <div class="wrapper">
        <div class="selected-value" @click="showMenu = !showMenu">
            {{ selectedValue }}
            <v-icon>$vuetify.icons.chevron-down</v-icon>
        </div>
        <div class="options" v-show="showMenu">
            <abs-menu>
                <ul>
                    <li
                        v-for="(option, k) in options"
                        :key="k"
                        @click="select(option)"
                    >
                        {{ option.text}}
                    </li>
                </ul>
            </abs-menu>
        </div>
    </div>
</template>
<script>
export default {
    props: ["options"],
    data() {
        return {
            selectedValue: this.options[0].text,
            showMenu: false
        };
    },
    methods: {
        select(option) {
            this.showMenu = false;
            this.selectedValue = option.text;
            this.$emit("select", option.value);
        }
    }
};
</script>
<style lang="scss" scoped>
.wrapper {
    position: relative;
    .selected-value {
        display: flex;
        align-items: center;
        cursor: pointer;
        img {
            margin-left: 0.5rem;
            width: 1rem;
        }
    }
    .options {
        ul {
            list-style: none;
            li {
                padding: 0.2rem 1rem;
                font-size: 0.8em;
                cursor: pointer;
                white-space: nowrap;
            }
        }
    }
}
</style>
