<template>
    <div class="">
        <v-card outlined>
            <v-card-title class="justify-space-between">
                <div class="left">
                    <v-icon color="primary" x-large>$vuetify.icons.translate</v-icon>
                    <v-btn
                        class="mx-2"
                        dark
                        small
                        color="primary"
                        @click="createLangDialog = true"
                        v-if="hasPermission('CED translations')"
                    >
                        <v-icon>$vuetify.icons.plus</v-icon> {{ $t("New Language") }}
                    </v-btn>
                </div>
            </v-card-title>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>
        </v-card>
        <v-card outlined class="p-3 my-3">
            <v-container class="pb-4">
                <swiper
                    ref="mySwiper"
                    :options="swiperOptions"
                    :slidesPerColumn="2"
                >
                    <swiper-slide v-for="(language, i) in locales" :key="i">
                        <div
                            class="disabledCoverLayer px-1 py-2"
                            v-if="language.status == -1"
                        >
                            <v-btn
                                color="success"
                                @click.stop="activateLanguage(language)"
                                :loading="activatingLangLoading"
                                width="150px"
                            >
                                {{ $t("Activate") }}
                                <template v-slot:loader>
                                    {{$t('Please wait') }}
                                    <span class="custom-loader">
                                        <v-icon light>$vuetify.icons.cached</v-icon>
                                    </span>
                                </template>
                            </v-btn>
                        </div>
                        <div
                            class="px-1 py-2"
                            :class="{ disabledCard: language.status == -1 }"
                        >
                            <v-card
                                hover
                                link
                                @click="switchLang(language.locale)"
                            >
                                <div
                                    class="absolute fill active-layer"
                                    v-if="language.locale === currentLocale"
                                ></div>
                                <div class="d-flex px-3 py-2">
                                    <div class="details  align-center">
                                        <v-img
                                            max-width="40"
                                            :src="
                                                flagsPath +
                                                    language.flag +
                                                    '.svg'
                                            "
                                        ></v-img>
                                        <div class="ml-2">
                                            <div class="title language-title">
                                                {{ language.name }}
                                            </div>
                                            <div class="sub">
                                                {{ language.locale }}
                                            </div>
                                        </div>
                                    </div>
                                    <v-card-actions class="top-right">
                                        <v-btn
                                            x-small
                                            icon
                                            @click.stop="
                                                setupEditLangDialog(language)
                                            "
                                        >
                                            <v-icon>$vuetify.icons.pencil</v-icon>
                                        </v-btn>
                                    </v-card-actions>
                                    <v-card-actions
                                        class="bottom-right"
                                        v-if="language.isDeletable"
                                    >
                                        <v-btn
                                            x-small
                                            icon
                                            @click.stop="deleteLang(language)"
                                            color="error"
                                        >
                                            <v-icon>$vuetify.icons.delete</v-icon>
                                        </v-btn>
                                    </v-card-actions>
                                </div>
                                <div class="status pb-2 pl-2">
                                    <v-btn
                                        x-small
                                        outlined
                                        dense
                                        v-if="language.isDefault"
                                        color="primary"
                                        >{{ $t("Default") }}</v-btn
                                    >
                                    <v-btn
                                        x-small
                                        outlined
                                        dense
                                        v-if="language.status == 1"
                                        color="success"
                                        >{{ $t("Activated") }}</v-btn
                                    >
                                    <v-btn
                                        x-small
                                        outlined
                                        dense
                                        v-else
                                        color="error"
                                        >{{ $t("Deactivated") }}</v-btn
                                    >
                                </div>
                            </v-card>
                        </div>
                    </swiper-slide>
                    <div class="swiper-pagination" slot="pagination"></div>
                </swiper>
            </v-container>
        </v-card>
        <v-card outlined>
            <v-card-title>
                <v-card-actions>
                    <v-btn
                        class="mx-2"
                        dark
                        small
                        color="primary"
                        @click="editMessageDialog = true"
                        v-if="hasPermission('Create artists')"
                    >
                        <v-icon>$vuetify.icons.plus</v-icon> {{ $t("New Translation") }}
                    </v-btn>
                    <v-btn
                        class="mx-2"
                        small
                        color="success"
                        @click="updateMessages"
                        :loading="isUpdateMessagesLoading"
                        :disabled="isUpdateMessagesLoading"
                    >
                        <template v-slot:loader>
                            <span class="custom-loader">
                                <v-icon light>$vuetify.icons.cached</v-icon>
                            </span>
                        </template>
                        {{ $t("Update") }}
                    </v-btn>
                </v-card-actions>
                <div class="admin-search-bar">
                    <v-text-field
                        v-model="search"
                        append-icon="mdi-magnify"
                        :label="$t('Search Translation')"
                        single-line
                        hide-details
                    ></v-text-field>
                </div>
            </v-card-title>
            <v-data-table
                :no-data-text="$t('No data available')"
                :loading-text="$t('Fetching data') + '...'"
                :headers="headers"
                :items="messages.filter(message => message.status !== -1)"
                :items-per-page="100"
                :search="search"
                :loading="isDataLoading"
                class="elevation-1"
            >
                <template v-slot:item.delete="{ item }">
                    <v-btn x-small icon @click="deleteTrans(item)">
                        <v-icon>$vuetify.icons.delete</v-icon>
                    </v-btn>
                </template>
                <template v-slot:item.value="{ item }">
                    <v-text-field
                        v-model="item.value"
                        hide-details
                    ></v-text-field>
                </template>
            </v-data-table>
            <v-dialog
                v-model="createLangDialog"
                max-width="500"
                :persistent="createLangDialogLoading"
                @click:outside="createLangDialogLoading ? '' :resetEditDialog"
            >
                <edit-dialog
                    @callToAction="editedLang.new ? createLang() : editLang()"
                    @cancel="resetEditDialog"
                    v-if="createLangDialog"
                    callToActionText="Save"
                    :loading="createLangDialogLoading"
                    :title="
                        editedLang.new
                            ? $t('Add New Language')
                            : $t('Edit Language')
                    "
                >
                    <template v-slot:body>
                        <v-container>
                            <v-container>
                                <v-text-field
                                    :label="$t('Name')"
                                    name="locale"
                                    outlined
                                    v-model="editedLang.name"
                                ></v-text-field>
                                <v-select
                                    :label="$t('Select Similar Language')"
                                    messages="Select a similar language to fill the translations."
                                    :items="locales"
                                    item-value="locale"
                                    item-text="name"
                                    outlined
                                    v-if="editedLang.new"
                                    v-model="editedLang.similar"
                                />
                                <v-switch
                                    v-model="editedLang.status"
                                    :label="$t('Active')"
                                    @change="
                                        !$event
                                            ? (editedLang.isDefault = false)
                                            : ''
                                    "
                                ></v-switch>
                                <v-switch
                                    v-if="editedLang.status"
                                    v-model="editedLang.isDefault"
                                    :label="$t('Default')"
                                ></v-switch>
                            </v-container>
                        </v-container>
                    </template>
                </edit-dialog>
            </v-dialog>
            <v-dialog max-width="500" v-model="editMessageDialog">
                <edit-dialog
                    @callToAction="addMessage($event)"
                    @cancel="editMessageDialog = false"
                    callToActionText="Add"
                    :loading="false"
                    :title="$t('Add New Translation')"
                >
                    <template v-slot:body>
                        <v-container>
                            <v-text-field
                                :label="$t('key')"
                                v-model="newTranslation.key"
                            ></v-text-field>
                            <v-text-field
                                :label="$t('value')"
                                v-model="newTranslation.value"
                            ></v-text-field>
                        </v-container>
                    </template>
                </edit-dialog>
            </v-dialog>
        </v-card>
    </div>
</template>

<script>
import { swiper, swiperSlide } from "vue-awesome-swiper";
import "swiper/css/swiper.css";
import axios from "axios";
export default {
    components: {
        swiper,
        swiperSlide
    },
    created() {
        this.fetchLangs();
    },
    data() {
        return {
            newTranslation: {
                key: "",
                value: ""
            },
            activatingLangLoading: false,
            search: "",
            headers: [
                { text: this.$t("Key"), value: "key", sortable: false },
                {
                    text: this.$t("Translation"),
                    value: "value",
                    sortable: false
                },
                {
                    text: this.$t("Operations"),
                    value: "delete",
                    sortable: false
                }
            ],
            editMessageDialog: false,
            createLangDialog: false,
            locales: [],
            messages: [],
            currentLocale: null,
            editedLang: {
                name: "",
                similar: { locale: 'en',
                    name: 'English'},
                new: true,
                status: true,
                isDefault: false
            },
            isSaveLocalesLoading: false,
            isUpdateMessagesLoading: false,
            createLangDialogLoading: false,
            isDataLoading: true,
            swiperOptions: {
                slidesPerView: 1,
                spaceBetween: 20,
                slidesPerColumn: 2,
                breakpoints: {
                    1920: {
                        slidesPerView: 4,
                        spaceBetweenSlides: 10
                    },
                    1680: {
                        slidesPerView: 4,
                        spaceBetweenSlides: 10
                    },
                    1300: {
                        slidesPerView: 3,
                        spaceBetweenSlides: 10
                    },

                    1050: {
                        slidesPerView: 2,
                        spaceBetweenSlides: 10
                    },
                    500: {
                        slidesPerView: 1,
                        spaceBetweenSlides: 10
                    }
                },
                // pagination: {
                //     el: ".swiper-pagination"
                // }
            },
            flagsPath: "/storage/defaults/icons/flags/"
        };
    },
    methods: {
        fetchLangs() {
            return axios
                .get("/api/admin/locales/language")
                .then(res => {
                    this.locales = res.data;
                    this.currentLocale = this.locales.find(
                        locale => locale.isDefault
                    ).locale;
                    this.fetchMessages(this.currentLocale);
                })
                .then(() =>
                    // sort the languages so the selected one before will be the first
                    this.locales.sort((x, y) =>
                        x.isDefault ? -1 : y.isDefault ? 1 : 0
                    )
                );
        },
        createLang() {
            this.createLangDialogLoading = true;
            axios
                .post("/api/admin/locales/language", {
                    name: this.editedLang.name,
                    locale: this.editedLang.name,
                    similar: this.editedLang.similar,
                    status: 1,
                    isDeletable: 1,
                    isDefault: this.editedLang.isDefault
                })
                .then(res => {
                    this.fetchLangs();
                    this.currentLocale = res.data.locale;
                    this.fetchMessages(this.currentLocale);
                })
                .finally(() => {
                    this.createLangDialogLoading = false;
                    this.editedLang = {};
                    this.editedLang.new = true;
                    this.createLangDialog = false;
                });
        },
        setupEditLangDialog(language) {
            this.editedLang = {
                new: false,
                similar: null,
                name: language.name,
                status: language.status,
                isDefault: language.isDefault,
                id: language.id
            };
            this.createLangDialog = true;
        },
        editLang() {
            this.createLangDialogLoading = true;
            axios
                .post(
                    "/api/admin/locales/language/" +
                        this.editedLang.id +
                        "/update",
                    {
                        name: this.editedLang.name,
                        status: this.editedLang.status,
                        isDefault: this.editedLang.isDefault
                    }
                )
                .then(res => {
                    this.fetchLangs();
                    if( this.editedLang.isDefault ) {
                        localStorage.setItem('pref-locale', res.data.locale)
                    }
                    this.currentLocale = res.data.locale;
                    this.fetchMessages(this.currentLocale);
                    setTimeout(() => {
                        location.reload()
                    }, 500);
                })
                .finally(() => {
                    this.editedLang = {};
                    this.editedLang.new = true;
                    this.createLangDialogLoading = false;
                    this.createLangDialog = false;
                });
        },
        activateLanguage(lang) {
            this.activatingLangLoading = true;
            axios
                .post("/api/admin/locales/activate-language/" + lang.id)
                .then(() => {
                    this.switchLang(lang.locale);
                    this.locales.find(l => lang.id === l.id).status = 1
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Activated"),
                        text:
                            this.$t("Language") +
                            " " +
                            this.$t("activated successfully.")
                    });
                })
                .finally(() => {
                    this.activatingLangLoading = false;
                });
        },
        deleteLang(lang) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this") +
                    " " +
                    this.$t("Language") +
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
                        axios
                            .delete("/api/admin/locales/language/" + lang.id)
                            .then(res => {
                                this.fetchLangs();
                                this.currentLocale = res.data.locale;
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text:
                                        this.$t("Language") +
                                        " " +
                                        this.$t("deleted successfully.")
                                });
                            });
                    }
                }
            });
        },
        addMessage() {
            this.messages.unshift({
                key: this.newTranslation.key,
                value: this.newTranslation.value,
                locale: this.currentLocale,
                group: "_json",
                status: 1
            });
            this.editMessageDialog = false;
            this.newTranslation = {};
        },
        deleteTrans(trans) {
            let transIndex = this.messages.findIndex(
                translation => translation.id === trans.id
            );
            this.messages[transIndex].status = -1;
        },
        switchLang(locale) {
            this.currentLocale = locale;
            this.fetchMessages(locale);
        },
        fetchMessages(locale) {
            this.isDataLoading = true;
            axios
                .get("/api/admin/locales/" + locale + "/messages")
                .then(res => (this.messages = res.data))
                .finally(() => (this.isDataLoading = false));
        },
        updateMessages() {
            this.isUpdateMessagesLoading = true;
            axios
                .post(
                    "/api/admin/locales/save-messages/" + this.currentLocale,
                    {
                        messages: this.messages
                    }
                )
                .then(res => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Updated"),
                        text:
                            this.$t("Messages") +
                            " " +
                            this.$t("updated successfully.")
                    });
                    this.messages = res.data;
                })
                .finally(() => (this.isUpdateMessagesLoading = false));
        },
        resetEditDialog() {
            this.editedLang = {};
            this.editedLang.new = true;
            this.createLangDialog = false;
        }
    }
};
</script>

<style lang="scss" scoped>
.top-right {
    position: absolute;
    top: 0;
    right: 0;
}
.swiper-pagination{
    top: 0;
}
.bottom-right {
    position: absolute;
    bottom: 0;
    right: 0;
}
.language-title {
    line-height: 1.1 !important;
}
.active-layer {
    opacity: 0.15;
    background-color: var(--color-primary);
}
.disabledCard {
    pointer-events: none;
}
.disabledCoverLayer {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    background-color: rgba(83, 83, 83, 0.363);
    display: flex;
    align-items: center;
    z-index: 2;
    justify-content: center;
}
</style>
