<template>
    <edit-dialog
        @callToAction="savePage"
        @cancel="closeWindow"
        :loading="isLoading"
        editing="Page"
        :fullscreen="true"
    >
        <template v-slot:body>
            <v-container class="container">
                <div class="page-infos">
                    <v-row>
                        <v-col cols="6"
                            ><v-text-field
                                v-model="page.name"
                                :label="$t('Name')"
                                outlined
                            ></v-text-field
                        ></v-col>
                        <v-col cols="6"
                            ><v-text-field
                                v-model="page.title"
                                :label="$t('Title')"
                                outlined
                            ></v-text-field
                        ></v-col>
                        <v-col cols="6"
                            ><v-text-field
                                v-model="page.description"
                                :label="$t('Description')"
                                outlined
                            ></v-text-field
                        ></v-col>
                        <v-col cols="6"
                            ><v-text-field
                                v-model="page.icon"
                                :label="$t('Icon')"
                                outlined
                            ></v-text-field
                        ></v-col>
                        <v-col cols="12"
                            ><v-text-field
                                v-model="page.path"
                                hide-details=""
                                :label="$t('Path')"
                                outlined
                            ></v-text-field
                        ></v-col>
                        <v-col cols="6" v-if="page.isEditable"
                            ><v-switch
                                v-model="page.blank"
                                :label="$t('Custom')"
                                outlined
                            ></v-switch
                        ></v-col>
                        <v-col cols="6" v-if="page.isEditable"
                            ><v-switch
                                v-model="page.showLinkOnTheRightSidebar"
                                :label="$t('Show link on the right-sidebar')"
                                outlined
                            ></v-switch
                        ></v-col>
                    </v-row>
                </div>
                <template v-if="page.blank">
                    <TextEditor @content="page.content = $event" :initialContent="page.content" placeholder="Add your page content here" />
                </template>
                <template v-else>
                                        <v-row>
                        <v-col>
                            <v-btn
                                class="info my-3"
                                @click="createSection"
                                dark
                                small
                            >
                                <v-icon class="mr-1"
                                    >$vuetify.icons.plus</v-icon
                                >
                                {{ $t("Add Section") }}
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-card class="page px-3 py-5" elevation="lg">
                        <div class="content-page">
                            <div class="icon-header">
                                <div class="cover">
                                    <v-icon dark
                                        >$vuetify.icons.{{ page.icon }}</v-icon
                                    >
                                </div>
                                <div class="title">
                                    <div class="headline">
                                        {{ page.title }}
                                    </div>
                                    <div class="sub">
                                        {{ page.description }}
                                    </div>
                                </div>
                            </div>
                            <page-loading
                                v-if="!page.sections || isLoading"
                                class="loading"
                                height="20vh"
                            />
                            <empty-page
                                :headline="$t('No Sections!')"
                                :sub="$t('Page empty. Add some sections.')"
                                v-else-if="
                                    page.sections && !page.sections.length
                                "
                            >
                            </empty-page>
                            <div class="sections p-4" v-else>
                                <draggable
                                    class="list-group mt-5"
                                    v-model="page.sections"
                                    v-bind="dragOptions"
                                    @start="isDragging = true"
                                    @end="isDragging = false"
                                    handle=".handle"
                                >
                                    <transition-group name="flip-list">
                                        <template
                                            v-for="section in page.sections"
                                        >
                                            <swiper-section-admin
                                                :section="section"
                                                v-if="
                                                    section.layout === 'section'
                                                "
                                                :key="section.id"
                                                @edit="editSection(section)"
                                                @delete="
                                                    deleteSection(section.id)
                                                "
                                            ></swiper-section-admin>
                                            <v-card
                                                class="section-skeleton"
                                                rounded
                                                :key="section.id"
                                                v-else-if="
                                                    section.layout === 'list'
                                                "
                                            >
                                                <v-card
                                                    outlined
                                                    class="section-option section-option__drag handle"
                                                >
                                                    <v-icon
                                                        >$vuetify.icons.cursor-move</v-icon
                                                    >
                                                </v-card>
                                                <v-card
                                                    @click="
                                                        editSection(section)
                                                    "
                                                    outlined
                                                    class="section-option section-option__edit"
                                                >
                                                    <v-icon color="#039BE5"
                                                        >$vuetify.icons.pencil</v-icon
                                                    >
                                                </v-card>
                                                <v-card
                                                    @click="
                                                        deleteSection(
                                                            section.id
                                                        )
                                                    "
                                                    outlined
                                                    class="section-option section-option__delete"
                                                >
                                                    <v-icon color="error"
                                                        >$vuetify.icons.delete</v-icon
                                                    >
                                                </v-card>
                                                <List
                                                    :section="section"
                                                    :key="section.id"
                                                    :isOnEditPage="true"
                                                    @edit="editSection(section)"
                                                    @delete="
                                                        deleteSection(
                                                            section.id
                                                        )
                                                    "
                                                ></List>
                                            </v-card>
                                            <v-card
                                                class="section-skeleton"
                                                rounded
                                                :key="section.id"
                                                v-else-if="
                                                    section.layout === 'cards'
                                                "
                                            >
                                                <v-card
                                                    outlined
                                                    class="section-option section-option__drag handle"
                                                >
                                                    <v-icon
                                                        >$vuetify.icons.cursor-move</v-icon
                                                    >
                                                </v-card>
                                                <v-card
                                                    @click="
                                                        editSection(section)
                                                    "
                                                    outlined
                                                    class="section-option section-option__edit"
                                                >
                                                    <v-icon color="#039BE5"
                                                        >$vuetify.icons.pencil</v-icon
                                                    >
                                                </v-card>
                                                <v-card
                                                    @click="
                                                        deleteSection(
                                                            section.id
                                                        )
                                                    "
                                                    outlined
                                                    class="section-option section-option__delete"
                                                >
                                                    <v-icon color="error"
                                                        >$vuetify.icons.delete</v-icon
                                                    >
                                                </v-card>
                                                <Featured
                                                    :section="section"
                                                    :key="section.id"
                                                    :isOnEditPage="true"
                                                    @edit="editSection(section)"
                                                    @delete="
                                                        deleteSection(
                                                            section.id
                                                        )
                                                    "
                                                ></Featured>
                                            </v-card>
                                        </template>
                                    </transition-group>
                                </draggable>
                            </div>
                        </div>
                        <v-dialog
                            v-model="editDialog"
                            max-width="950"
                            persistent
                        >
                            <edit-section-dialog
                                v-if="editingSection"
                                :page="page"
                                @id="page.id = $event"
                                @close="closeSectionDialog"
                                @created="updateSections"
                                @updated="updateSections"
                                :section="editingSection"
                            ></edit-section-dialog>
                        </v-dialog>
                    </v-card>
                </template>
            </v-container>
        </template>
    </edit-dialog>
</template>
<script>
import EditSectionDialog from "./Section";
import draggable from "vuedraggable";
import Featured from "../../../elements/sections/layouts/Featured.vue";
import SwiperSectionAdmin from "../../../admin/elements/SwiperSection";
const TextEditor = () => import(/* webpackChunkName: "textEditor" */ '../../../elements/other/TextEditor.vue');
export default {
    components: {
        TextEditor,
        draggable,
        EditSectionDialog,
        Featured,
        SwiperSectionAdmin
    },
    
    props: ["page"],
    data() {
        return {
            editingSection: null,
            isLoading: false,
            editDialog: false
        };
    },
    computed: {
        dragOptions() {
            return {
                animation: 0,
                disabled: false,
                ghostClass: "ghost"
            };
        }
    },
    beforeRouteLeave(to, from, next) {
        if (
            JSON.stringify(this.page.sections) !==
            JSON.stringify(this.sectionsFirstCopy)
        ) {
            this.$confirm({
                message: `${this.$t(
                    "Are you sure you wanna reset to the default settings ?"
                )}`,
                button: {
                    no: this.$t("Cancel"),
                    yes: this.$t("Discard")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        next();
                    }
                }
            });
        } else {
            next();
        }
    },
    methods: {
        savePage() {
            this.isLoading = true;
            const formData = new FormData();
            formData.append("title", this.page.title || "");
            formData.append("description", this.page.description || "");
            formData.append("name", this.page.name || "");
            formData.append("icon", this.page.icon || "");
            formData.append("path", this.page.path || "");
            formData.append("content", this.page.content || "");
            formData.append("blank", this.page.blank ? 1 : 0);
            formData.append("showLinkOnTheRightSidebar", this.page.showLinkOnTheRightSidebar ? 1 : 0);
            formData.append(
                "sections",
                JSON.stringify(this.page.sections) || ""
            );
            if (this.page.new && !this.page.id) {
                axios
                    .post("/api/admin/pages", formData)
                    .then(() => this.$emit("created"))
                    .catch(e => {
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    })
                    .finally(() => (this.isLoading = false));
            } else {
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/pages/" + this.page.id, formData)
                    .then(() => this.$emit("updated"))
                    .catch(e => {
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    })
                    .finally(() => (this.isLoading = false));
            }
        },
        editSection(section) {
            this.editingSection = section;
            this.editDialog = true;
        },
        createSection() {
            this.editingSection = {
                new: true,
                title: "Section name",
                name: "Custom",
                source: "local",
                layout: "section",
                nb_labels: 6,
                content: [null, null, null, null, null],
                rank: this.page.sections.length,
                showLinkOnTheRightSidebar: false,
                endpoint: null,
                id: Math.round(Math.random() * 100)
            };
            this.editDialog = true;
        },
        closeSectionDialog() {
            this.editDialog = false;
            this.editingSection = null;
        },
        closeWindow() {
            this.$emit("close");
        },
        deleteSection(id) {
            this.$confirm({
                message: `${this.$t("Are you sure you wanna delete this") +
                    " " +
                    this.$t("section") +
                    "?"}`,
                button: {
                    no: this.$t("Cancel"),
                    yes: "Delete"
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: confirm => {
                    if (confirm) {
                        this.isLoading = true;
                        axios
                            .delete("/api/admin/sections/" + id)
                            .then(() => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text:
                                        this.$t("Section") +
                                        " " +
                                        this.$t("Deleted") +
                                        "."
                                });
                            })
                            .then(() => this.fetchSections());
                    }
                }
            });
        },
        updateSections() {
            this.editDialog = false;
            this.editingSection = false;
            this.fetchSections();
        },
        fetchSections() {
            if (this.page.id) {
                this.isLoading = true;
                return axios
                    .get("/api/admin/pages/" + this.page.id)
                    .then(res => {
                        this.page.sections = res.data.sections;
                    })
                    .finally(() => (this.isLoading = false));
            }
        }
    }
};
</script>

<style lang="scss" scoped>
@import "../../../../../sass/variables";
.page-dialog-wrapper {
    .swiper-container {
        padding: 0.5em 1.5em !important;
    }
    .section-skeleton .section-header {
        border-radius: 5px !important;
    }
}
.icon-header {
    .cover {
        background: linear-gradient(45deg, $color-secondary, $color-primary);
    }
}
</style>
