<template>
    <div class="image-picker">
        <div class="image-picker__title">{{ image_name }}</div>
        <div class="img-picker">
            <div class="upload">
                <input
                    type="file"
                    accept="image/*"
                    :id="'image-input-' + image_name"
                    @change="pickImage($event)"
                    hidden
                />
                <label :for="'image-input-' + image_name"></label>
            </div>
            <div class="image-container">
                <v-img :src="base64 || src" class="img" width="75"></v-img>
            </div>
            <v-menu bottom v-if="cogIcon">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        icon
                        v-bind="attrs"
                        v-on.stop="on"
                        class="absolute top right"
                        style="z-index: 2"
                    >
                        <v-icon small>$vuetify.icons.cog</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item @click="resetDefault" style="z-index: 3">
                        <v-list-item-title>{{$t('Use Default')}}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        src: String,
        image_name: String,
        cogIcon: { type: Boolean, default: true }
    },
    data() {
        return {
            base64: null
        };
    },
    methods: {
        resetDefault() {
            this.base64 = null;
            this.$emit('default')
        },
        pickImage(e) {
            const self = this;
            if (e.target.files.length) {
                if (
                    e.target.files[0].size >
                    this.$store.getters.getSettings.maxImageSize * 1024 * 1024
                ) {
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text:
                            this.$t("Image must be less then") + " " + 
                            this.$store.getters.getSettings.maxImageSize +
                            this.$t("MB")
                    });
                    return;
                }
                var file = e.target.files[0];
                const reader = new FileReader();
                reader.onload = e => {
                    if (e.target.result) {
                        let isAnImage = e.target.result.match("data:image");
                        if (isAnImage) {
                            self.base64 = e.target.result;
                            self.$emit("image", {
                                base64: e.target.result,
                                file
                            });
                        } else {
                            this.$notify({
                                group: "foo",
                                type: "error",
                                title: this.$t("Error"),
                                text: this.$t("Please choose an image")
                            });
                        }
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        }
    }
};
</script>
<style lang="scss" scoped>
.image-picker {
    position: relative;
    &__title {
        position: absolute;
        top: 5px;
        left: 5px;
    }
    .img-picker {
        width: 100%;
        top: 0;
        height: 9em;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        .upload {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(78, 78, 78, 0.61);
            color: white;
            border: 1px solid rgba(128, 128, 128, 0.384);
            text-align: center;
            z-index: 2;
            cursor: pointer;
            background-color: transparent;
            border-radius: 5px;
            transition: all 1s;
            height: 100%;
            label {
                z-index: 2;
                cursor: pointer;
                display: flex;
                justify-content: center;
                padding: 0.2rem 0;
                margin-bottom: 0;
             }
            &:hover {
                background-color: rgba(255, 255, 255, 0.267);
            }
        }
        label {
            height: 100%;
        }
        .img {
            max-width: 40%;
            min-width: 6em;
        }
    }
}
</style>
