<template>
    <edit-dialog
        @callToAction="savePlan"
        @cancel="closeWindow"
        :disableCallToAction="!editedPlan.name"
        :loading="isLoading"
        editing="Plan"
    >
        <template v-slot:body>
            <v-container>
                <v-row>
                    <v-col v-if="!editedPlan.new">
                        <v-alert dense type="warning"
                            >{{$t('Some values can not be updated after the plan creation')}}</v-alert
                        >
                    </v-col>
                    <v-col v-if="noBilling">
                        <v-alert dense type="warning"
                            >{{$t('Enable billing to create non-free plans')}}</v-alert
                        >
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                           :label="$t('Name')"
                            v-model="editedPlan.name"
                            outlined
                        ></v-text-field>
                        <v-row>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="editedPlan.active"
                                   :label="$t('Active')"
                                    :color="editedPlan.active ? 'success': 'error'"
                                ></v-switch>
                            </v-col>
                             <v-col cols="6" sm="4">
                                <v-switch
                                    :disabled="!editedPlan.new || noBilling"
                                    v-model="editedPlan.free"
                                   :label="$t('Free')"
                                ></v-switch>
                            </v-col>
                            <v-col cols="6" sm="4">
                                <v-switch
                                    v-model="editedPlan.recommended"
                                   :label="$t('Recommended')"
                                ></v-switch>
                            </v-col>
                        </v-row>
                        <template v-if="!editedPlan.free">
                            <v-row>
                                <v-col cols="6">
                                    <veutifySelect
                                        :disabled="!editedPlan.new"
                                        class="mt-5"
                                        :items="currencies"
                                        item-text="text"
                                       :label="$t('Currency')"
                                        return-object
                                        v-model="editedPlan.currency"
                                    />
                                </v-col>
                                <v-col cols="6" class="align-center">
                                    <div class="plan__price">
                                        <div class="plan__price__currency">
                                            {{ currency_symbol }}
                                        </div>
                                        <div class="plan__price__amount">
                                            {{ price(editedPlan.amount) }}
                                        </div>
                                        <div class="plan__price__interval">
                                            / {{ $t(editedPlan.interval) }}
                                        </div>
                                    </div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field
                                       :label="$t('Amount')"
                                        class="mt-4"
                                        type="number"
                                        v-model="editedPlan.amount"
                                        :disabled="!editedPlan.new"
                                        :messages="$t('Amount in cents (can not be changed later)')"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <veutifySelect
                                        :disabled="!editedPlan.new"
                                        class="mt-5"
                                        :messages="$t('The frequency of the subscription billing')"
                                        :items="intervals"
                                        item-value="value"
                                        item-text="text"
                                       :label="$t('Interval')"
                                        v-model="editedPlan.interval"
                                /></v-col>
                            </v-row>
                            <v-row> </v-row>
                        </template>
                    </v-col>
                    <v-col>
                        <v-text-field
                           :label="$t('Position')"
                            class="mt-4"
                            type="number"
                            :messages="$t('Display order of the plans(lower first)')"
                            v-model="editedPlan.position"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <div class="card-bb-header">
                            <div class="header">
                                <div class="title">{{$t('Features')}}</div>
                            </div>
                            <v-divider class="pt-2"></v-divider>
                            <div class="sub">
                                {{ $t('Permissions to grant for subscribed users') }}
                            </div>
                            <div class="body">
                                <v-container fluid>
                                    <v-row>
                                        <v-col
                                            cols="6"
                                            sm="4"
                                            v-for="permission in userPermissions"
                                            :key="permission.id"
                                        >
                                            <v-checkbox
                                                v-model="editedPlan.permissions"
                                                :label="$t(permission.name)"
                                                :value="permission.id"
                                                :messages="
                                                    $t(permission.description)
                                                "
                                                color="primary"
                                            ></v-checkbox>
                                        </v-col>
                                        <v-col cols="6" sm="4">
                                            <v-checkbox
                                                v-model="editedPlan.roles"
                                                :label="$t('Artist account')"
                                                color="primary"
                                                :value="2"
                                            ></v-checkbox>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            v-if="editedPlan.roles[0]"
                                        >
                                            <v-expansion-panels>
                                                <v-expansion-panel>
                                                    <v-expansion-panel-header
                                                        >{{$t('Artist Permissions')}}</v-expansion-panel-header
                                                    >
                                                    <v-expansion-panel-content>
                                                        <v-checkbox
                                                            v-for="(permission,
                                                            i) in artistPermissions"
                                                            :key="i"
                                                            v-model="
                                                                editedPlan.permissions
                                                            "
                                                            :value="
                                                                permission.id
                                                            "
                                                            :label="
                                                                permission.name
                                                            "
                                                            :messages="
                                                                permission.description
                                                            "
                                                            color="primary"
                                                        ></v-checkbox>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-card>
                                                <v-card-title>
                                                    {{$t('Storage Space')}}
                                                </v-card-title>
                                                <v-divider></v-divider>
                                                <v-card-text>
                                                    <v-container>
                                                        <v-row>
                                                            <v-col cols="9">
                                                                <v-text-field
                                                                   :label="$t('Size')"
                                                                    type="number"
                                                                    v-model="
                                                                        storage_space
                                                                    "
                                                                    :messages="
                                                                        editedPlan.used_disk_space
                                                                            ? (
                                                                                  editedPlan.used_disk_space /
                                                                                  1024 /
                                                                                  1024
                                                                              ).toFixed(
                                                                                  1
                                                                              ) +
                                                                              + ' ' + $t('MB') + + ' ' + $t('user already.')
                                                                            : ''
                                                                    "
                                                                ></v-text-field>
                                                            </v-col>
                                                            <v-col cols="3">
                                                                <veutify-select
                                                                    type="number"
                                                                    :items="[
                                                                        'MB',
                                                                        'GB',
                                                                        'TB'
                                                                    ]"
                                                                    v-model="
                                                                        storage_space_unit
                                                                    "
                                                                ></veutify-select>
                                                            </v-col>
                                                        </v-row>
                                                    </v-container>
                                                </v-card-text>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12">
                        <div class="card-bb-header">
                            <div class="header">
                                <div class="title">{{$t('Card Features')}}</div>
                            </div>
                            <v-divider class="pt-2"></v-divider>
                            <div class="sub">
                                {{$t('Features that will display on the plan card')}}
                                {{$t('This does not affect the script')}}
                            </div>
                            <div class="body">
                                <v-container fluid>
                                    <draggable
                                        class="container"
                                        v-model="editedPlan.displayed_features"
                                    >
                                        <v-row
                                            v-for="(displayed_feature,
                                            i) in editedPlan.displayed_features"
                                            :key="i"
                                        >
                                            <v-col cols="auto">
                                                <v-icon
                                                    small
                                                    class="page__grab-icon"
                                                >
                                                    $vuetify.icons.arrow-all
                                                </v-icon>
                                            </v-col>
                                            <v-col>
                                                {{ displayed_feature }}
                                            </v-col>
                                            <v-col cols="auto">
                                                <v-icon
                                                    color="error"
                                                    @click="
                                                        spliceDisplayedFeature(
                                                            i
                                                        )
                                                    "
                                                    >$vuetify.icons.close</v-icon
                                                >
                                            </v-col>
                                        </v-row>
                                    </draggable>
                                    <div class="add_feature mt-5">
                                        <v-container>
                                            <v-row
                                                ><v-text-field
                                                   :label="$t('Add displayed feature')"
                                                    hide-details
                                                    dense
                                                    outlined
                                                    v-model="
                                                        displayed_feature_input_value
                                                    "
                                                ></v-text-field>
                                                <v-btn
                                                    class="success"
                                                    @click="addDisplayedFeature"
                                                    >{{ $t('Add') }}</v-btn
                                                >
                                            </v-row>
                                        </v-container>
                                    </div>
                                </v-container>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12">
                        <div class="card-bb-header">
                            <div class="header">
                                <div class="title">{{$t('Interface')}}</div>
                            </div>
                            <v-divider class="py-2"></v-divider>
                            <div class="body">
                                <v-container fluid>
                                    <v-row>
                                        <v-col>
                                            <v-switch
                                                :messages="$t('Show the upgrade button after subscription. Switch on if there is a better plan than this.')"
                                               :label="$t('Upgradable')"
                                                v-model="editedPlan.upgradable"
                                            ></v-switch>
                                        </v-col>
                                        <v-col>
                                            <div class="title">{{$t('Badge')}}</div>
                                            <v-btn-toggle
                                                v-model="editedPlan.badge"
                                                icon
                                                color="deep-purple accent-3"
                                                group
                                            >
                                                <v-btn value="star">
                                                    <v-icon>$vuetify.icons.star</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    value="professional-hexagon"
                                                >
                                                    <v-icon
                                                        >$vuetify.icons.professional-hexagon</v-icon
                                                    >
                                                </v-btn>
                                                <v-btn value="check-decagram">
                                                    <v-icon
                                                        >$vuetify.icons.check-decagram</v-icon
                                                    >
                                                </v-btn>
                                                <v-btn value="star-four-points">
                                                    <v-icon
                                                        >$vuetify.icons.star-four-points</v-icon
                                                    >
                                                </v-btn>
                                                <v-btn value="star-circle">
                                                    <v-icon
                                                        >$vuetify.icons.star-circle</v-icon
                                                    >
                                                </v-btn>
                                            </v-btn-toggle>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </template>
    </edit-dialog>
</template>
<script>
import { VSelect } from "vuetify/lib";
import draggable from "vuedraggable";
import currencies from "../../../../data/stripeSupportedCurrencies";
export default {
    props: ["plan"],
    components: {
        veutifySelect: VSelect,
        draggable
    },
    created() {
        axios.get("/api/admin/roles").then(res => {
            this.artistRole = res.data.find(role => role.name === "artist");
            this.artistPermissions = res.data.find(
                role => role.name === "artist"
            ).available_permissions;
            this.userPermissions = res.data.find(
                role => role.name === "user"
            ).available_permissions;
        });
        this.editedPlan.displayed_features =
            this.editedPlan.displayed_features || [];
    },
    data() {
        return {
            currencies,
            artistRole: {},
            displayed_feature_input_value: "",
            artistPermissions: [],
            editedPlan: this.plan,
            userPermissions: [],
            firstCopy: JSON.parse(JSON.stringify(this.plan)),
            isLoading: false,
            storage_space: this.plan.storage_space,
            storage_space_unit: "MB",
            intervals: [
                {  
                    text: this.$t("Day"),
                    value: 'day'
                },
                {  
                    text: this.$t("Week"),
                    value: 'week'
                },
                {  
                    text: this.$t("Month"),
                    value: 'month'
                },
                {  
                    text: this.$t("Year"),
                    value: 'year'
                },
            ]
        };
    },
    computed: {
        currency_symbol() {
            return this.editedPlan.currency_symbol
                ? this.editedPlan.currency_symbol
                : this.editedPlan.currency.symbol
                ? this.editedPlan.currency.symbol
                : this.editedPlan.currency.value || this.editedPlan.currency;
        },
        noBilling() {
            return !this.$store.getters.getSettings.enableBilling;
        }
    },
    methods: {
        addDisplayedFeature() {
            this.editedPlan.displayed_features.push(
                this.displayed_feature_input_value
            );
            this.displayed_feature_input_value = "";
        },
        spliceDisplayedFeature(i) {
            this.editedPlan.displayed_features.splice(i, 1);
        },
        closeWindow() {
            this.$emit("close");
        },
        savePlan() {
            var formData = new FormData();
            this.isLoading = true;
            formData.append("name", this.editedPlan.name);
            formData.append("free", this.editedPlan.free ? 1 : 0);
            formData.append("active", this.editedPlan.active ? 1 : 0);
            formData.append("recommended", this.editedPlan.recommended ? 1 : 0);
            formData.append("currency", this.editedPlan.currency.value);
            formData.append(
                "currency_symbol",
                this.editedPlan.currency.symbol || ""
            );
            formData.append("amount", this.editedPlan.amount);
            formData.append("position", this.editedPlan.position);
            formData.append("interval", this.editedPlan.interval);
            formData.append("upgradable", this.editedPlan.upgradable ? 1 : 0);
            formData.append("badge", this.editedPlan.badge || '');
            formData.append(
                "displayed_features",
                JSON.stringify(this.editedPlan.displayed_features)
            );
            formData.append(
                "permissions",
                JSON.stringify(this.editedPlan.permissions)
            );
            formData.append("roles", JSON.stringify(this.editedPlan.roles));
            // converting storage space to MB
            if (this.storage_space_unit === "GB") {
                var storage_space = this.storage_space * 1024;
            } else if (this.storage_space_unit === "TB") {
                var storage_space = this.storage_space * 1024 * 2014;
            } else {
                var storage_space = this.storage_space;
            }
            this.editedPlan.storage_space = storage_space;
            formData.append("storage_space", this.editedPlan.storage_space);

            if (this.editedPlan.new) {
                axios
                    .post("/api/admin/plans", formData)
                    .then(() => {
                        this.$emit("updated");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.errors = e.response.data.errors;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    });
            } else {
                formData.append("plan_id", this.editedPlan.id);
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/plans/" + this.editedPlan.id, formData)
                    .then(() => {
                        this.$emit("updated");
                        this.isLoading = false;
                    })
                    .catch(e => {
                        this.isLoading = false;
                        this.errors = e.response.data.errors;
                        // this.$notify({
                        //     group: "foo",
                        //     type: "error",
                        //     title: this.$t("Error"),
                        //     text: Object.values(e.response.data.errors).join(
                        //         "<br />"
                        //     )
                        // });
                    });
            }
        }
    }
};
</script>