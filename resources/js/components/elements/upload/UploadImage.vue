<template>
    <v-card outlined rounded max-width="200">
        <div class="img-picker">
            <slot name="icon"></slot>
            <div class="img-cover">
                <v-img
                    :src="croppedImage || source"
                    class="img"
                    :aspect-ratio="!ratio ? '1' : '2.52'"
                >
                    <template v-slot:placeholder>
                        <div class="fixed-area__image-skeleton">
                            <content-placeholders :rounded="true">
                                <content-placeholders-img />
                            </content-placeholders>
                        </div>
                    </template>
                </v-img>
            </div>
            <div class="upload">
                <form>
                    <label :for="'image-input-' + id">
                        <v-icon class="mr-2" small dark>$vuetify.icons.camera</v-icon>
                        {{$t('Choose Image')}}</label
                    >
                    <input
                        type="file"
                        class="form-control-file"
                        name="image"
                        ref="input"
                        hidden
                        accept="image/*"
                        :id="'image-input-' + id"
                        @change="cropImage($event)"
                        @click="$refs.input.value = null"
                        required
                    />
                </form>
            </div>
            <crop-image-dialog
                v-if="crop"
                @imageData="upload($event)"
                @cancel="crop = false"
                :image="image"
                :ratio="ratio"
            />
        </div>
    </v-card>
</template>
<script>
import cropImageDialog from "../../dialogs/CropImage";
export default {
    props: ["source", "id", "ratio"],
    data() {
        return {
            crop: false,
            croppedImage: null,
            image: {
                name: "",
                base64: null
            }
        };
    },
    components: {
        cropImageDialog
    },
    methods: {
        cropImage(e) {
            var self = this;
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
                const reader = new FileReader();
                reader.onload = e => {
                    if (e.target.result) {
                        let isAnImage = e.target.result.match("data:image");

                        if (isAnImage) {
                            self.image = {
                                name: self.$refs.input.files[0].name,
                                base64: e.target.result
                            };
                            self.crop = true;
                        } else {
                            this.$notify({
                                group: "foo",
                                type: "error",
                                title: this.$t("Error"),
                                text: this.$t('Please choose an image')
                            });
                        }
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        },
        upload(formData) {
            this.croppedImage = formData.image;
            this.$emit("imageReady", formData);
            this.crop = false;
        }
    }
};
</script>
<style lang="scss" scoped>
.img-picker {
    overflow: hidden;
    position: relative;
    width: 100%;
    max-width: 400px;
    .img-cover {
        border-radius: 0;
        .img {
            width: 14em;
        }
    }
    .upload {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(78, 78, 78, 0.61);
        color: white;
        text-align: center;
        z-index: 2;
        cursor: pointer;
        .img {
            width: 1.2rem;
            margin-right: 0.2rem;
        }
        label {
            z-index: 999999;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.2rem 0;
            margin-bottom: 0;
        }
    }
    img {
        width: 100%;
    }
}
</style>
