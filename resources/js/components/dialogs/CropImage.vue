<template>
    <dialog-wrapper>
        <v-card class="cropper-wrapper">
            <div class="header">
                <div class="card-title-medium">
                    {{ $t("Crop Image") }}
                </div>
                <div class="cancel">
                    <v-btn
                        @click="$emit('cancel')"
                        icon
                        :dark="
                            $store.state.darkTheme ||
                                $store.getters.getSettings.defaultTheme ==
                                    'dark'
                        "
                    >
                        <v-icon>$vuetify.icons.close</v-icon>
                    </v-btn>
                </div>
            </div>
            <div class="cropper">
                <img
                    class="crop"
                    ref="crop"
                    style="display:block;width:100%"
                    :src="image.base64"
                />
            </div>
            <v-btn
                @click="save()"
                class="primary mt-2"
                small
                :loading="isLoading"
                :disabled="isLoading"
            >
                <template v-slot:loader>
                    <span class="custom-loader">
                        <v-icon light>$vuetify.icons.cached</v-icon>
                    </span>
                </template>
                {{ $t("Pick") }}</v-btn
            >
        </v-card>
    </dialog-wrapper>
</template>

<script>
import Cropper from "cropperjs";
export default {
    props: ["image", "ratio"],
    mounted() {
        const cropContainer = this.$refs.crop;
        this.cropper = new Cropper(cropContainer, {
            aspectRatio: this.ratio || 4 / 4,
            corpstart: true
        });
    },
    data() {
        return {
            cropper: null,
            isLoading: false
        };
    },
    methods: {
        save() {
            this.isLoading = true;
            var self = this;
            let croppedImage = this.cropper.getCroppedCanvas();
            let imageBase64 = croppedImage.toDataURL("image/jpeg");
            croppedImage.toBlob(blob => {
                self.$emit("imageData", {
                    data: blob,
                    title: self.image.name,
                    image: imageBase64
                });
                this.isLoading = false;
            }, "image/jpeg");
        }
    }
};
</script>
