<template>
    <v-card class="page">
        <v-card-title>
            <div class="page__title">
                <v-icon color="primary" x-large>$vuetify.icons.airplane-landing</v-icon>
                {{ $t("Landing Page") }}
            </div>
            <div class="page__options">
                <v-btn
                    small
                    class="info white--text"
                    @click="reset"
                    :disabled="isLoading"
                    >{{ $t("Reset") }}</v-btn
                >
                <v-btn
                    small
                    class="success"
                    @click="save"
                    :disabled="isLoading"
                    >{{ $t("Save") }}</v-btn
                >
            </div>
        </v-card-title>
        <v-container>
            <v-row class="pb-3">
                <v-col>
                    <v-switch
                        :label="$t('Enable Landing Page')"
                        :messages="
                            $t(
                                'The welcome page. Disable if you want the visitors to access the player directly.'
                            )
                        "
                        v-model="$store.getters.getSettings.enableLandingPage"
                    ></v-switch>
                </v-col>
            </v-row>
        </v-container>
        <v-container v-if="$store.getters.getSettings.enableLandingPage">
            <v-card>
                <v-card-title> {{ $t("Header") }} </v-card-title>
                <v-row class="px-3">
                    <v-col cols="6">
                        <v-text-field
                            outlined
                            v-model="landingPage.callToAction"
                            :label="$t('Call to action button')"
                        ></v-text-field>
                    </v-col>
                        <v-col cols="6">
                            <v-text-field
                                outlined
                                v-model="landingPage.headerTitle"
                                :label="$t('Header Title')"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="6">
                                                      <v-textarea
                                outlined
                                hide-details
                                v-model="landingPage.headerDescription"
                                :label="$t('Header Subtitle')"
                            ></v-textarea>
                        </v-col>
                        <v-col cols="6">
                            <image-picker
                                :image_name="$t('Background Image')"
                                :src="
                                    typeof landingPage.headerBackground ===
                                    'string'
                                        ? landingPage.headerBackground
                                        : ''
                                "
                                :cogIcon="false"
                                @image="
                                    landingPage.headerBackground = $event.file
                                "
                            />
                        </v-col>
                        <v-col cols="6">
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-card outlined>
                                <div class="color flex align-center p-2">
                                    <div class="color__sample mr-2">
                                        <v-avatar
                                            size="35"
                                            :color="
                                                landingPage
                                                    .headerBackgroundLayerColor ?
                                                    landingPage
                                                    .headerBackgroundLayerColor .hexa
                                                    ? landingPage
                                                          .headerBackgroundLayerColor
                                                          .hexa
                                                    : landingPage.headerBackgroundLayerColor
                                                    : ''
                                            "
                                        ></v-avatar>
                                    </div>
                                    <div class="color__details">
                                        <div class="color__name">
                                            {{ $t("Background Layer Color") }}
                                        </div>
                                        <div
                                            class="color__details opacity-high"
                                        ></div>
                                    </div>
                                    <div class="color__picker ml-auto">
                                        <v-menu
                                            top
                                            :close-on-content-click="false"
                                        >
                                            <template
                                                v-slot:activator="{ on, attrs }"
                                            >
                                                <v-btn
                                                    text
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    small
                                                >
                                                    {{ $t("Change") }}
                                                </v-btn>
                                            </template>
                                            <v-color-picker
                                                dot-size="25"
                                                swatches-max-height="200"
                                                mode="rgba"
                                                v-model="
                                                    landingPage.headerBackgroundLayerColor
                                                "
                                            ></v-color-picker>
                                        </v-menu>
                                    </div>
                                </div>
                            </v-card>
                        </v-col>
                </v-row>
            </v-card>
            <v-divider class="py-3"></v-divider>
            <v-row class="my-2">
                <v-col>
                    <v-card>
                        <v-card-title>
                            {{ $t("Sections") }}
                            <v-btn
                                class="mx-2"
                                dark
                                small
                                color="primary"
                                @click="newSection"
                            >
                                <v-icon>$vuetify.icons.plus</v-icon> {{ $t("Add") }}
                            </v-btn>
                        </v-card-title>
                        <v-container>
                            <v-row
                                v-for="(section, i) in landingPage.sections"
                                :key="i"
                            >
                                <v-col>
                                    <v-textarea
                                        outlined
                                        v-model="section.title"
                                        :label="$t('Title')"
                                    ></v-textarea>
                                </v-col>
                                <v-col>
                                    <v-textarea
                                        outlined
                                        v-model="section.description"
                                        :label="$t('Description')"
                                    ></v-textarea>
                                </v-col>
                                <v-col>
                                    <image-picker
                                        :image_name="
                                            $t('Image') + ' ' + (i + 1)
                                        "
                                        :src="
                                            typeof section.background ===
                                            'string'
                                                ? section.background
                                                : ''
                                        "
                                        :cogIcon="false"
                                        @image="
                                            section.background = $event.file
                                        "
                                    />
                                </v-col>
                                <v-btn icon @click="deleteSection(i)">
                                    <v-icon>$vuetify.icons.close</v-icon>
                                </v-btn>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-container class="my-2">
                <v-row>
                    <v-col>
                        <v-switch
                            :messages="
                                $t(
                                    'Make sure to add your SMTP credentials in the Email settings.'
                                )
                            "
                            :label="$t('Show Contact Us form')"
                            v-model="landingPage.showContactUs"
                        ></v-switch>
                    </v-col>
                </v-row>
            </v-container>
            <v-divider></v-divider>
            <v-row>
                <v-col>
                    <v-card>
                        <v-card-title>{{ $t("Footer") }}</v-card-title>
                        <v-container>
                            <v-row>
                                <v-col>
                                    <v-textarea
                                        outlined
                                        v-model="landingPage.footerTitle"
                                        :label="$t('Title')"
                                    ></v-textarea>
                                </v-col>
                                <v-col>
                                    <v-textarea
                                        outlined
                                        v-model="landingPage.footerDescription"
                                        :label="$t('Description')"
                                    ></v-textarea>
                                </v-col>
                                <v-col>
                                    <image-picker
                                        :image_name="$t('Footer Background')"
                                        :src="
                                            typeof landingPage.footerBackground ===
                                            'string'
                                                ? landingPage.footerBackground
                                                : ''
                                        "
                                        :cogIcon="false"
                                        @image="
                                            landingPage.footerBackground =
                                                $event.file
                                        "
                                    />
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-card>
</template>
<script>
import imagePicker from "../../elements/ImagePickerSetting";
export default {
    components: {
        imagePicker
    },
    created() {
        this.landingPage.sections = JSON.parse(this.landingPage.sections);
        this.landingPage.showContactUs = Boolean(
            parseInt(this.landingPage.showContactUs)
        );
    },
    data() {
        return {
            isLoading: false,
            headerBackground: "",
            headerBackgroundLayerColor: "",
            landingPage: JSON.parse(JSON.stringify(this.$store.getters.getSettings.landingPage)) || {},
            defaultLandingPage: {
                customLandingPageHeader: "1",
                headerBackground: "",
                headerBackgroundLayerColor: "rgba(255,255,255,0)",
                headerTitle: "",
                headerDescription: "",
                callToAction: "Play now",
                sections: [],
                showContactUs: true,
                footerTitle:
                    "Are you an Artist? Do you want to share your content?",
                footerDescription:
                    "Sign up on Muzzie now and request an Artist account. Get up to 500 MB of free space and more!",
                footerBackground: "/images/background.png"
            }
        };
    },
    methods: {
        async save() {
            this.isLoading = true;
            var formData = new FormData();
            if( this.$store.getters.getSettings.enableLandingPage ) {
                 this.landingPage.headerBackgroundLayerColor = this.landingPage
                .headerBackgroundLayerColor
                ? this.landingPage.headerBackgroundLayerColor.hexa
                    ? this.landingPage.headerBackgroundLayerColor.hexa
                    : this.landingPage.headerBackgroundLayerColor
                : "";
            Object.keys(this.landingPage).forEach(key => {
                if (key !== "sections") {
                    formData.append(
                        key,
                        this.landingPage[key] === true
                            ? 1
                            : this.landingPage[key] === false
                            ? 0
                            : this.landingPage[key] || ""
                    );
                }
            });
            const sections = await Promise.all(
                this.landingPage.sections.map(async function(section) {
                    var formData = new FormData();
                    formData.append("title", section.title || "");
                    formData.append("description", section.description || "");
                    formData.append("background", section.background || "");
                    const res = await axios.post(
                        "/api/admin/upload-section-background",
                        formData,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            }
                        }
                    );
                    return res.data;
                })
            );
            formData.append("sections", JSON.stringify(sections));
            }
            formData.append("enableLandingPage", this.$store.getters.getSettings.enableLandingPage? 1 : 0);
            axios
                .post("/api/admin/save-landing-page-settings", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Updated"),
                        text: this.$t("Settings updated successfully.")
                    })
                    setTimeout(() => {
                        // location.reload()
                    }, 500);
                })
                .finally(() => this.isLoading = false)
        },
        newSection() {
            this.landingPage.sections
                ? this.landingPage.sections.push({
                      title: "",
                      description: "",
                      background: ""
                  })
                : ((this.landingPage.sections = []),
                  this.landingPage.sections.push({
                      title: "",
                      description: "",
                      background: ""
                  }));
        },
        deleteSection(i) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this") +
                    " " +
                    this.$t("Section") +
                    "?"}`,
                button: {
                    no: this.$t("No"),
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        this.landingPage.sections.splice(i, 1);
                    }
                }
            });
        },
        reset() {
            this.$confirm({
                message: `${this.$t(
                    "Are you sure you wanna reset to default settings?"
                )}`,
                button: {
                    no: this.$t("No"),
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        this.isLoading = true;
                        this.landingPage = this.defaultLandingPage;
                        this.save();
                    }
                }
            });
        }
    }
};
</script>
