<template>
    <v-card class="page">
        <v-card-title>
            <div class="page__title">
                <v-icon color="primary" x-large
                    >$vuetify.icons.navigation-outline</v-icon
                >
                {{ $t("Navigation") }}
            </div>
            <div class="page__options">
                <v-btn
                    small
                    class="info"
                    @click="resetToDefault"
                    :disabled="isLoading"
                    :loading="isLoading"
                    >{{ $t("Reset") }}
                    <template v-slot:loader>
                        <span class="custom-loader">
                            <v-icon light>$vuetify.icons.cached</v-icon>
                        </span>
                    </template>
                </v-btn>
                <v-btn
                    small
                    width="120"
                    class="success"
                    @click="saveSidebarSettings"
                    :disabled="isLoading"
                    :loading="isLoading"
                    >{{ $t("Save") }}
                    <template v-slot:loader>
                        <span class="custom-loader">
                            <v-icon light>$vuetify.icons.cached</v-icon>
                        </span>
                    </template>
                </v-btn>
            </div>
        </v-card-title>
        <v-container v-if="navigationItems">
            <!-- <v-row>
                <v-col>
                    <v-alert type="info">
                        {{
                            $t(
                                "This is the sidebar of the player. You can customize almost everything: Icon, text, and destination."
                            )
                        }}
                    </v-alert>
                </v-col>
            </v-row> -->
            <v-row>
                <v-col>
                    <div class="add_button">
                        <v-btn
                            class="info m-2"
                            dark
                            small
                            @click="addNavigationItem()"
                            ><v-icon class="mr-2">$vuetify.icons.plus</v-icon
                            >{{ $t("New Item") }}</v-btn
                        >
                    </div>
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <div class="navigation-items-container">
                        <div class="sidebar-like py-3 px-5">
                            <div class="sidebar-list-default">
                                <draggable
                                    class="list-group mt-5"
                                    v-model="navigationItems"
                                >
                                    <transition-group name="flip-list">
                                        <template
                                            v-for="(item, i) in navigationItems"
                                        >
                                            <v-card
                                                class="item-wrapper mb-3"
                                                :key="i"
                                                :class="{
                                                    fade: !item.visibility
                                                }"
                                            >
                                                <v-container>
                                                    <v-row
                                                        justify="space-between"
                                                        class="py-2"
                                                    >
                                                        <div class="prev">
                                                            <v-list-item
                                                                link
                                                                class="sidebar-item"
                                                            >
                                                                <div>
                                                                    <v-icon left
                                                                        >$vueitfy.icons.{{
                                                                            item.icon
                                                                        }}</v-icon
                                                                    >
                                                                </div>
                                                                <v-list-item-content>
                                                                    <v-list-item-title
                                                                        v-text="
                                                                            item.name
                                                                        "
                                                                    ></v-list-item-title>
                                                                </v-list-item-content>
                                                            </v-list-item>
                                                        </div>
                                                        <v-icon small
                                                            >$vuetify.icons.cursor-move</v-icon
                                                        >
                                                        <div class="ops">
                                                            <v-row>
                                                                                                                            <v-col
                                                            >
                                                                <v-menu top>
                                                                    <template
                                                                        v-slot:activator="{
                                                                            on,
                                                                            attrs
                                                                        }"
                                                                    >
                                                                        <v-btn
                                                                            color="primary"
                                                                            dark
                                                                            small
                                                                            v-bind="
                                                                                attrs
                                                                            "
                                                                            v-on="
                                                                                on
                                                                            "
                                                                        >
                                                                            <v-icon
                                                                                left
                                                                                >$vuetify.icons.tag-faces</v-icon
                                                                            >
                                                                            
                                                                            {{$t('Icon')}}
                                                                        </v-btn>
                                                                    </template>
                                                                    <v-sheet
                                                                        height="400px"
                                                                    >
                                                                        <v-card
                                                                            max-width="500px"
                                                                        >
                                                                            <v-container>
                                                                                <v-row>
                                                                                    <v-col
                                                                                        cols="1"
                                                                                        v-for="(icon,
                                                                                        i) in icons"
                                                                                        :key="
                                                                                            i
                                                                                        "
                                                                                    >
                                                                                        <v-icon
                                                                                            class="selectable-icon p-1"
                                                                                            @click="
                                                                                                item.icon = icon
                                                                                            "
                                                                                        >
                                                                                            $vuetify.icons.{{
                                                                                                icon
                                                                                            }}
                                                                                        </v-icon>
                                                                                    </v-col>
                                                                                </v-row>
                                                                            </v-container>
                                                                        </v-card>
                                                                    </v-sheet>
                                                                </v-menu>
                                                            </v-col>
                                                            <v-col
                                                                v-if="
                                                                    item.custom
                                                                "
                                                            >
                                                                <v-btn
                                                                    small
                                                                    max-width="120"
                                                                    v-if="
                                                                        !item.endpoint_protected
                                                                    "
                                                                    class="error"
                                                                    @click="
                                                                        deleteItem(
                                                                            i
                                                                        )
                                                                    "
                                                                >
                                                                    <v-icon left
                                                                        >$vuetify.icons.delete</v-icon
                                                                    >
                                                                    {{
                                                                        $t(
                                                                            "Delete"
                                                                        )
                                                                    }}
                                                                </v-btn>
                                                            </v-col>
                                                            <v-col
                                                                v-else
                                                            >
                                                                <template
                                                                    v-if="
                                                                        item.visibility
                                                                    "
                                                                >
                                                                    <v-btn
                                                                        small
                                                                        max-width="120"
                                                                        color="info"
                                                                        @click="
                                                                            hideItem(
                                                                                i
                                                                            )
                                                                        "
                                                                    >
                                                                        <v-icon
                                                                            left
                                                                            >$vuetify.icons.eye-outline</v-icon
                                                                        >
                                                                        {{
                                                                            $t(
                                                                                "Hide"
                                                                            )
                                                                        }}
                                                                    </v-btn>
                                                                </template>
                                                                <template
                                                                    v-else
                                                                >
                                                                    <v-btn
                                                                        small
                                                                        max-width="120"
                                                                        color="info"
                                                                        @click="
                                                                            hideItem(
                                                                                i
                                                                            )
                                                                        "
                                                                    >
                                                                        <v-icon
                                                                            left
                                                                            >$vuetify.icons.eye-off-outline</v-icon
                                                                        >
                                                                        {{
                                                                            $t(
                                                                                "Show"
                                                                            )
                                                                        }}
                                                                    </v-btn>
                                                                </template>
                                                            </v-col>
                                                            </v-row>
                                                        </div>
                                                    </v-row>
                                                    <v-divider
                                                        class="py-2"
                                                    ></v-divider>
                                                    <v-row>
                                                        <v-col
                                                            cols="6"
                                                            sm="4"
                                                            v-if="item.custom"
                                                        >
                                                            <v-select
                                                                :items="
                                                                    navigationOptions
                                                                "
                                                                class="pr-1"
                                                                v-model="
                                                                    item.navigatesTo
                                                                "
                                                                hide-details
                                                                dense
                                                                outlined
                                                                @change="
                                                                    $event ===
                                                                    'Custom Page'
                                                                        ? (item.path =
                                                                              selectedPage &&
                                                                              selectedPage.name
                                                                                  ? '/page/' +
                                                                                    selectedPage.name
                                                                                  : '')
                                                                        : ((item.path =
                                                                              ''),
                                                                          (item.endpoint_protected = 0))
                                                                "
                                                                :label="
                                                                    $t(
                                                                        'Navigates to'
                                                                    )
                                                                "
                                                            />
                                                        </v-col>
                                                        <v-col
                                                            cols="6"
                                                            sm="4"
                                                            v-if="
                                                                item.navigatesTo ===
                                                                    'Custom Page'
                                                            "
                                                        >
                                                            <v-select
                                                                :items="pages"
                                                                class="pr-1"
                                                                hide-details
                                                                dense
                                                                outlined
                                                                item-text="name"
                                                                :label="
                                                                    $t(
                                                                        'Select Page'
                                                                    )
                                                                "
                                                                v-model="
                                                                    item.page_id
                                                                "
                                                                item-value="id"
                                                                @change="
                                                                    item.path = pages.find(
                                                                        page =>
                                                                            page.id ===
                                                                            $event
                                                                    ).path;
                                                                    item.name = pages.find(
                                                                        page =>
                                                                            page.id ===
                                                                            $event
                                                                    ).name;
                                                                    item.icon = pages.find(
                                                                        page =>
                                                                            page.id ===
                                                                            $event
                                                                    ).icon;
                                                                "
                                                            />
                                                        </v-col>
                                                        <v-col cols="6" sm="4">
                                                            <v-text-field
                                                                v-model="
                                                                    item.name
                                                                "
                                                                class="pr-1"
                                                                hide-details
                                                                dense
                                                                outlined
                                                                :label="
                                                                    $t('Name')
                                                                "
                                                                >{{
                                                                    item.name
                                                                }}</v-text-field
                                                            >
                                                        </v-col>
                                                        <v-col cols="6" sm="4">
                                                            <v-text-field
                                                                v-model="
                                                                    item.path
                                                                "
                                                                hide-details
                                                                dense
                                                                outlined
                                                                class="pr-1"
                                                                :label="
                                                                    $t('Path')
                                                                "
                                                                :disabled="
                                                                    Boolean(
                                                                        !item.custom
                                                                    ) ||
                                                                        item.navigatesTo ===
                                                                            'Custom Page'
                                                                "
                                                                >{{
                                                                    item.path
                                                                }}</v-text-field
                                                            >
                                                        </v-col>
                                                    </v-row>
                                                </v-container>
                                            </v-card>
                                        </template>
                                    </transition-group>
                                </draggable>
                            </div>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-container>
        <page-loading v-else class="loading" />
    </v-card>
</template>

<script>
import draggable from "vuedraggable";
export default {
    components: {
        draggable
    },
    created() {
        axios
            .get("/api/navigation-items")
            .then(res => (this.navigationItems = res.data));
        axios
            .get("/api/admin/pages-infos")
            .then(res => (this.pages = res.data));
    },
    data() {
        return {
            navigationItems: null,
            isLoading: false,
            selectedPage: {},
            navigationOptions: [
                {
                    text: this.$t("Custom Page"),
                    value: "Custom Page"
                },
                {
                    text: this.$t("Custom URL"),
                    value: "Custom URL"
                }
            ],
            icons: Object.keys(this.$vuetify.icons.values),
            pages: []
        };
    },
    methods: {
        saveSidebarSettings() {
            this.isLoading = true;
            axios
                .post("/api/admin/save-navigation-items", {
                    items: this.navigationItems
                })
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Updated"),
                        text:
                            this.$t("Sidebar") +
                            " " +
                            this.$t("updated successfully.")
                    });
                })
                // .catch(() => {
                //     this.$notify({
                //         group: "foo",
                //         type: "error",
                //         title: this.$t("Error"),
                //         text: this.$t(
                //             "Failed to save! Fill the empty information."
                //         )
                //     });
                // })
                .finally(() => (this.isLoading = false));
        },
        resetToDefault() {
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
                        this.navigationItems = [];
                        axios
                            .post("/api/admin/reset-navigation-items")
                            .then(() => {
                                axios
                                    .get("/api/navigation-items")
                                    .then(
                                        res => (this.navigationItems = res.data)
                                    );
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Updated"),
                                    text:
                                        this.$t("Sidebar") +
                                        " " +
                                        this.$t("updated successfully.")
                                });
                            })
                            .finally(() => (this.isLoading = false));
                    }
                }
            });
        },
        deleteItem(i) {
            this.navigationItems.splice(i, 1);
            return;
        },
        hideItem(i) {
            this.navigationItems[i].visibility = !this.navigationItems[i]
                .visibility;
        },
        addNavigationItem() {
            this.navigationItems.push({
                custom: 1,
                name: "custom",
                path: "",
                icon: "link",
                rank: this.navigationItems[this.navigationItems.length - 1]
                    ? this.navigationItems[this.navigationItems.length - 1]
                          .rank + 1
                    : 0,
                visibility: 1,
                endpoint_protected: 0,
                navigatesTo: null
            });
            this.$notify({
                group: "foo",
                type: "success",
                title: this.$t("Added"),
                text: this.$t("Sidebar") + " " + this.$t("added successfully.")
            });
        }
    }
};
</script>

<style lang="scss" scoped>
.sidebar-item {
    padding: 0 2em !important;
}
.sidebar-list-default {
    padding-bottom: 3em;
}
.item-wrapper {
    padding: 1em 0;
    border-radius: 1px solid rgb(204, 204, 204);
    border-radius: 3px;
}
.selectable-icon {
    cursor: pointer;
    border-radius: 4px;
    &:hover {
        background-color: rgba(128, 128, 128, 0.171);
    }
}
</style>
