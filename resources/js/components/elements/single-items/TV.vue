<template>
    <div id="container" :class="{ 'small-screen': smallScreen }">
        <div id="monitor">
            <div class="small-screen__icon">
                <v-icon v-if="!smallScreen" @click="switchToSmallScreen"
                    :title="$t('Small Window')"
                    dark
                    >$vuetify.icons.fullscreen-exit</v-icon
                >
                <v-icon dark v-else @click="switchToTVScreen">$vuetify.icons.fullscreen</v-icon>
            </div>
            <div id="monitorscreen">
                <slot></slot>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            smallScreen: true
        };
    },
    methods: {
        switchToSmallScreen() {
            this.$emit("small-screen");
            this.smallScreen = true;
        },
        switchToTVScreen() {
            this.$emit("large-screen");
            this.smallScreen = false;
        }
    }
};
</script>

<style lang="scss" scoped>
#container {
    max-width: 1024px;
    margin: auto;
    width: 800px;
    height: 450px;
    @media screen and (max-width: 900px) {
        width: 500px;
        height: 300px;
    }
    @media screen and (max-width: 600px) {
        width: 90%;
    }
}
#container.small-screen.phone-layout {
    @media screen and (max-width: 600px) {
        width: 95%;
        height: 300px;
    }
}
#container.small-screen {
    @media screen and (max-width: 600px) {
        width: 95%;
        height: 300px;
    }
}
#monitor {
    background: #000;
    width: 100%;
    height: 100%;
    position: relative;
    border-top: 3px solid #888;
    padding: 2% 2% 4% 2%;
    border-radius: 10px;
    border-bottom-left-radius: 50% 2%;
    border-bottom-right-radius: 50% 2%;
    transition: margin-right 1s;
}

#monitor:after {
    content: "";
    display: block;
    position: absolute;
    bottom: 3%;
    left: 36%;
    height: 0.5%;
    width: 28%;
    background: #ddd;
    border-radius: 50%;
    box-shadow: 0 0 3px 0 white;
}
.small-screen__icon {
    position: absolute;
    top: 3px;
    right: 3px;
}
#monitorscreen {
    background-color: black;
    width: 100%;
    height: 100%;
}

#container.small-screen {
    width: auto;
    height: auto;
    #monitor:after {
        display: none;
    }
    #monitor {
        background: transparent;
        position: relative;
        border-top: none;
        margin: 0;
        padding: 0;
        border-radius: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    #monitorscreen {
        background-color: transparent;
        border-top: none;
        border-radius: 0px;
    }
}
</style>
