<template>
    <div class="elevate-page">
        <v-container
            class="account-settings-container"
            v-if="$store.getters.getUser"
        >
            <v-card class="p-3">
                <v-card-title class="header">
                    <v-icon color="primary" class=" pr-3" x-large
                        >$vuetify.icons.cog</v-icon
                    >
                    <span class="title">{{ $t("Account Settings") }}</span>
                </v-card-title>
                <v-row class="px-4">
                    <v-col cols="12" sm="auto" x-sm="6" class="p-2">
                        <upload-image
                            @imageReady="imageReady($event)"
                            :source="user.avatar || null"
                        />
                    </v-col>
                    <v-col>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        :label="$t('Name')"
                                        outlined
                                        v-model="user.name"
                                    ></v-text-field>
                                </v-col>
                                <!-- <v-col cols="12">
                                    <v-select
                                        outlined
                                        :label="$t('Language')"
                                        :items="supportedLanguages"
                                        item-text="name"
                                        item-value="locale"
                                        v-model="user.lang"
                                    >
                                        <template v-slot:item="{ item }">
                                            <div class="align-center">
                                                <div class="img px-2 py-1">
                                                    <v-img
                                                        max-width="25"
                                                        :src="
                                                            flagsPath +
                                                                item.flag +
                                                                '.svg'
                                                        "
                                                    ></v-img>
                                                </div>
                                                {{ item.name }}
                                            </div>
                                        </template>
                                        <template v-slot:selection="{ item }">
                                            <div class="align-center">
                                                <div class="img px-2 py-1">
                                                    <v-img
                                                        max-width="25"
                                                        :src="
                                                            flagsPath +
                                                                item.flag +
                                                                '.svg'
                                                        "
                                                    ></v-img>
                                                </div>
                                                {{ item.name }}
                                            </div>
                                        </template>
                                    </v-select>
                                </v-col> -->
                                <v-col cols="12" lg="6">
                                    <div
                                        class="card-title card-title-small py-1"
                                    >
                                        {{ $t("Update Password") }}
                                    </div>
                                    <v-divider></v-divider>
                                    <v-text-field
                                        :label="$t('Current password')"
                                        class="mt-2"
                                        type="password"
                                        v-model="user.currentPassword"
                                    ></v-text-field>
                                    <v-text-field
                                        :label="$t('New Password')"
                                        class="mt-2"
                                        type="password"
                                        v-model="user.newPassword"
                                    ></v-text-field>
                                    <v-text-field
                                        :label="$t('Confirm Password')"
                                        class="mt-2"
                                        type="password"
                                        v-model="user.confirmPassword"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    lg="6"
                                    v-if="
                                        $store.getters.getSettings
                                            .enableRealtime
                                    "
                                >
                                    <div
                                        class="card-title card-title-small  py-1"
                                    >
                                        {{ $t("Privacy") }}
                                    </div>
                                    <v-divider></v-divider>
                                    <v-switch
                                        v-if="
                                            $store.getters.getSettings
                                                .enableFriendshipSystemSystem ||
                                                true
                                        "
                                        v-model="user.hide_activity"
                                        :label="
                                            $t(
                                                'Do not show what I am currently playing to friends.'
                                            )
                                        "
                                    ></v-switch>
                                </v-col>
                            </v-row>
                        </v-container>
                        <template
                            v-if="
                                $store.getters.getSettings
                                    .enable_artist_account &&
                                    $store.getters.getSettings
                                        .allowArtistAccountRequests &&
                                    !$store.getters.getUser.roles.some(
                                        role => role.name == 'artist'
                                    ) &&
                                    !$store.getters.getUser.is_admin
                            "
                        >
                            <v-divider></v-divider>
                            <v-btn
                                outlined
                                dark
                                color="primary"
                                class="mt-4"
                                v-if="
                                    $store.getters.getUser
                                        .requested_artist_account
                                "
                                ><v-icon left
                                    >$vuetify.icons.account-music</v-icon
                                >{{ $t("Artist Account Request sent") }}</v-btn
                            >
                            <v-btn
                                dark
                                color="primary"
                                class="mt-4"
                                @click="requestArtistDialog = true"
                                v-else
                                ><v-icon left
                                    >$vuetify.icons.account-music</v-icon
                                >
                                {{ $t("Request Artist Account") }}
                            </v-btn>
                        </template>
                    </v-col>
                    <v-col cols="12">
                        <v-card v-if="userPlan" class="user-plan plan" outlined>
                            <div class="user-plan__header">
                                <v-container>
                                    <v-row justify="space-between">
                                        <div class="g-infos">
                                            <div class="sub">
                                                {{ $t("Subscribed to") }}
                                            </div>
                                            <div class="title d-flex">
                                                {{ userPlan.name }}
                                                {{ $t("Plan") }}
                                                <div
                                                    class=" ml-2 badge"
                                                    v-if="userPlan.badge"
                                                    :title="
                                                        userPlan.name + ' plan'
                                                    "
                                                >
                                                    <v-icon color="primary"
                                                        >$veutify.icons.{{
                                                            userPlan.badge
                                                        }}</v-icon
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="align-center"
                                            v-if="userPlan.stripe_plan"
                                        >
                                            <div class="plan__price">
                                                <div
                                                    class="plan__price__currency"
                                                >
                                                    {{
                                                        planCurrencySymbol(
                                                            userPlan
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="plan__price__amount"
                                                >
                                                    {{ price(userPlan.amount) }}
                                                </div>
                                                <div
                                                    class="plan__price__interval"
                                                >
                                                    / {{ userPlan.interval }}
                                                </div>
                                            </div>
                                        </div>
                                    </v-row>
                                </v-container>
                            </div>

                            <div class="user-plan__features">
                                <div class="user-plan__features__title py-2">
                                    <v-divider></v-divider>
                                    {{ $t("Features") }}
                                    <v-divider></v-divider>
                                </div>
                                <ul>
                                    <li
                                        v-for="(feature,
                                        i) in userPlan.displayed_features"
                                        :key="i"
                                    >
                                        <v-icon
                                            color="success"
                                            class="mr-1"
                                            small
                                            >$vuetify.icons.check</v-icon
                                        >{{ $t(feature) }}
                                    </li>
                                </ul>
                            </div>
                            <v-card-actions class=" py-2 justify-center">
                                <v-btn
                                    small
                                    color="#FF8F00"
                                    v-if="isUpgradable"
                                    dark
                                    @click="
                                        $router.push({ name: 'subscription' })
                                    "
                                >
                                    <v-icon left
                                        >$vuetify.icons.star-circle</v-icon
                                    >
                                    {{ $t("Upgrade") }}
                                </v-btn>
                                <v-btn
                                    small
                                    color="primary"
                                    v-if="
                                        $store.getters.getPlans &&
                                            $store.getters.getPlans.length >
                                                1 &&
                                            !isUpgradable
                                    "
                                    dark
                                    @click="
                                        $router.push({ name: 'subscription' })
                                    "
                                >
                                    <v-icon left>$vuetify.icons.refresh</v-icon>
                                    {{ $t("Change") }}
                                </v-btn>
                                <v-btn
                                    color="error"
                                    small
                                    outlined
                                    @click="cancelSubscription"
                                >
                                    <v-icon left
                                        >$vuetify.icons.window-close</v-icon
                                    >
                                    {{ $t("Cancel") }}</v-btn
                                >
                            </v-card-actions>
                        </v-card>
                    </v-col>
                    <v-col cols="12">
                        <v-btn
                            class="success ml-auto d-flex"
                            @click="saveChanges"
                            :disabled="isLoading"
                            >{{ $t("Save Changes") }}</v-btn
                        >
                    </v-col>
                </v-row>
            </v-card>
            <v-dialog v-model="requestArtistDialog" max-width="800">
                <v-card class="p-3  mx-auto">
                    <v-card-title class="d-block">
                        <div class="text-align-center">
                            <div class="header">
                                {{ $t("Fill your information") }}
                            </div>
                            <div class="sub">
                                {{
                                    $t(
                                        "This request can only be submitted ones."
                                    )
                                }}
                            </div>
                        </div>
                    </v-card-title>
                    <v-row class="px-4">
                        <v-col lg="3" md="4" x-sm="6" class="p-2">
                            <upload-image
                                @imageReady="imageReady($event, 'artist')"
                                :id="'artist-image'"
                                :source="
                                    artistAccount.avatar ||
                                        '/storage/defaults/images/artist_avatar.png'
                                "
                            />
                        </v-col>
                        <v-col lg="9" sm="6">
                            <v-text-field
                                :label="$t('Firstname')"
                                v-model="artistAccount.firstname"
                            ></v-text-field>
                            <v-text-field
                                :label="$t('Lastname')"
                                v-model="artistAccount.lastname"
                            ></v-text-field>
                            <v-text-field
                                :label="$t('Displayname')"
                                message="This name will be displayed on your profile."
                                v-model="artistAccount.displayname"
                            ></v-text-field>
                            <v-select
                                :label="$t('Country')"
                                :items="countriesList"
                                v-model="artistAccount.country"
                            ></v-select>
                            <v-text-field
                                :label="$t('Address')"
                                v-model="artistAccount.address"
                            ></v-text-field>
                            <v-text-field
                                :label="$t('Email')"
                                v-model="artistAccount.email"
                            ></v-text-field>
                            <v-text-field
                                :label="$t('Phone Number')"
                                v-model="artistAccount.phone"
                                hint="+xxxxxxxxxx"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <edit-external-links
                                :item="artistAccount"
                                @spotify_link="
                                    artistAccount.spotify_link = $event
                                "
                                @youtube_link="
                                    artistAccount.youtube_link = $event
                                "
                                @soundcloud_link="
                                    artistAccount.soundcloud_link = $event
                                "
                                @itunes_link="
                                    artistAccount.itunes_link = $event
                                "
                                @deezer_link="
                                    artistAccount.deezer_link = $event
                                "
                                :expanded="true"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-btn
                                class="ml-auto d-flex"
                                color="success"
                                @click="submitArtistAccount"
                                :disabled="status === 'requested' || isLoading"
                                >{{ $t("Submit") }}</v-btn
                            >
                        </v-col>
                    </v-row>
                </v-card>
            </v-dialog>
        </v-container>
    </div>
</template>

<script>
import countriesList from "../../../data/coutries";
export default {
    created() {
        // this.fetchLanguages();
    },
    data() {
        return {
            user: JSON.parse(JSON.stringify(this.$store.getters.getUser)),
            requestArtistDialog: false,
            status: "Submit",
            isLoading: false,
            artistAccount: {
                email: this.$store.getters.getUser.email
            },
            flagsPath: "/storage/defaults/icons/flags/",
            supportedLanguages: [],
            countriesList
        };
    },
    methods: {
        fetchLanguages() {
            axios.get("/api/languages").then(res => {
                this.supportedLanguages = res.data;
            });
        },
        imageReady(e, who) {
            if (who === "artist") {
                this.artistAccount.avatar = e;
            } else {
                this.user.avatar = e;
            }
        },
        cancelSubscription() {
            this.$confirm({
                auth: true,
                message: `${this.$t(
                    "Are you sure you wanna cancel your subscription?"
                )}`,
                button: {
                    no: this.$t("No"),
                    yes: this.$t("Yes")
                },
                /**
                 * Callback Function
                 * @param {Boolean} confirm
                 */
                callback: (confirm, password) => {
                    if (confirm) {
                        axios
                            .post("/api/user/cancel-subscritpion", {
                                subscription_id: this.$store.getters.getUser
                                    .subscription.id,
                                password
                            })
                            .then(() => {
                                this.$notify({
                                    group: "foo",
                                    type: "success",
                                    title: this.$t("Deleted"),
                                    text:
                                        this.$t("Subscription") +
                                        " " +
                                        this.$t("Deleted") +
                                        "."
                                });
                                setTimeout(() => {
                                    location.reload();
                                }, 800);
                            })
                            .catch(e => {
                                if (e.response.data.message) {
                                    this.$notify({
                                        group: "foo",
                                        type: "error",
                                        title: this.$t("Error"),
                                        text: e.response.data.message
                                    });
                                }
                            });
                    }
                }
            });
        },
        saveChanges() {
            this.isLoading = true;
            if (
                (this.confirmPassword || this.newPassword) &&
                this.confirmPassword !== this.newPassword
            ) {
                this.$notify({
                    group: "foo",
                    type: "error",
                    title: this.$t("Error"),
                    text: this.$t("Confirm password does not match.")
                });
                this.isLoading = false;
                return;
            }
            var formData = new FormData();
            formData.append("id", this.user.id);
            formData.append("name", this.user.name);
            formData.append("lang", this.user.lang);
            formData.append("currentPassword", this.user.currentPassword || 0);
            formData.append("newPassword", this.user.newPassword || 0);
            formData.append("hide_activity", this.user.hide_activity || 0);
            formData.append("avatar", this.user.avatar);
            if (this.user.avatar && this.user.avatar.data) {
                formData.append(
                    "avatar",
                    this.user.avatar.data,
                    this.user.avatar.title
                );
            }
            axios
                .post("/api/user/save-account-settings", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(() => {
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Success"),
                        text:
                            this.$t("Account Settings") +
                            " " +
                            this.$t("updated successfully.")
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                })
                .catch(e => {
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: Object.values(e.response.data.errors).join(
                            "<br />"
                        )
                    });
                })
                .finally(() => (this.isLoading = false));
        },
        submitArtistAccount() {
            var formData = new FormData();
            this.isLoading = true;
            formData.append("firstname", this.artistAccount.firstname || "");
            formData.append("lastname", this.artistAccount.lastname || "");
            formData.append("country", this.artistAccount.country || "");
            formData.append("phone", this.artistAccount.phone || "");
            formData.append("email", this.artistAccount.email || "");
            formData.append("address", this.artistAccount.address || "");
            formData.append(
                "spotify_link",
                this.artistAccount.spotify_link || ""
            );
            formData.append(
                "youtube_link",
                this.artistAccount.youtube_link || ""
            );
            formData.append(
                "soundcloud_link",
                this.artistAccount.soundcloud_link || ""
            );
            formData.append(
                "itunes_link",
                this.artistAccount.itunes_link || ""
            );
            formData.append(
                "displayname",
                this.artistAccount.displayname || ""
            );
            if (this.artistAccount.avatar && this.artistAccount.avatar.data) {
                formData.append(
                    "avatar",
                    this.artistAccount.avatar.data,
                    this.artistAccount.avatar.title
                );
            } else {
                formData.append(
                    "avatar",
                    "/storage/defaults/images/artist_avatar.png"
                );
            }
            axios
                .post("/api/user/request-artist-account", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(() => {
                    this.status = "requested";
                    this.isLoading = false;
                    this.requestArtistDialog = false;
                    this.$store.getters.getUser.requested_artist_account = 1;
                    this.$notify({
                        group: "foo",
                        type: "success",
                        title: this.$t("Requested"),
                        text: this.$t(
                            "Request submitted to admins successfully."
                        )
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                })
                .catch(e => {
                    this.isLoading = false;
                    this.$notify({
                        group: "foo",
                        type: "error",
                        title: this.$t("Error"),
                        text: Object.values(e.response.data.errors).join(
                            "<br />"
                        )
                    });
                });
        }
    }
};
</script>
<style lang="scss" scoped>
.account-settings-container {
    max-width: 1000px;
}
.user-plan {
    padding: 0.5em;
    &__features {
        ul {
            list-style: none;
            padding: 1em;
            padding-left: 0.3em;
            li {
                padding: 0.1em 0.2em;
                display: flex;
            }
        }
        &__title {
            font-size: 1.1em;
            font-weight: 600;
        }
    }
    &__header {
        padding: 1em 0.4em;
    }
}
</style>
