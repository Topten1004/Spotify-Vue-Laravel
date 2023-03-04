<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-alert type="error"  max-width="400" class="mx-auto" v-if="error">
                    {{ error }}
                </v-alert>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="auto">
                <div class="status justify-align-center">
                    <v-progress-circular
                        v-if="status[0] === 0"
                        indeterminate
                        size="16"
                        width="2"
                        color="grey"
                    ></v-progress-circular>
                    <v-icon v-else-if="status[0] === 1" color="success"
                        >$vuetify.icons.check-circle-outline</v-icon
                    >
                    <v-icon v-else-if="status[0] === -1" color="error"
                        >$vuetify.icons.close-circle-outline</v-icon
                    >
                </div>
            </v-col>
            <v-col>
                Creating .env file
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="auto">
                <div class="status justify-align-center">
                    <v-progress-circular
                        v-if="status[1] === 0"
                        indeterminate
                        size="16"
                        width="2"
                        color="grey"
                    ></v-progress-circular>
                    <v-icon v-else-if="status[1] === 1" color="success"
                        >$vuetify.icons.check-circle-outline</v-icon
                    >
                    <v-icon v-else-if="status[1] === -1" color="error"
                        >$vuetify.icons.close-circle-outline</v-icon
                    >
                </div>
            </v-col>
            <v-col>
                Migrating database tables
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="auto">
                <div class="status justify-align-center">
                    <v-progress-circular
                        v-if="status[2] === 0"
                        indeterminate
                        size="16"
                        width="2"
                        color="grey"
                    ></v-progress-circular>
                    <v-icon v-else-if="status[2] === 1" color="success"
                        >$vuetify.icons.check-circle-outline</v-icon
                    >
                    <v-icon v-else-if="status[2] === -1" color="error"
                        >$vuetify.icons.close-circle-outline</v-icon
                    >
                </div>
            </v-col>
            <v-col>
                Installing dependencies
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="auto">
                <div class="status justify-align-center">
                    <v-progress-circular
                        v-if="status[3] === 0"
                        indeterminate
                        size="16"
                        width="2"
                        color="grey"
                    ></v-progress-circular>
                    <v-icon v-else-if="status[3] === 1" color="success"
                        >$vuetify.icons.check-circle-outline</v-icon
                    >
                    <v-icon v-else-if="status[3] === -1" color="error"
                        >$vuetify.icons.close-circle-outline</v-icon
                    >
                </div>
            </v-col>
            <v-col>
                Importing translations
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="auto">
                <div class="status justify-align-center">
                    <v-progress-circular
                        v-if="status[4] === 0"
                        indeterminate
                        size="16"
                        width="2"
                        color="grey"
                    ></v-progress-circular>
                    <v-icon v-else-if="status[4] === 1" color="success"
                        >$vuetify.icons.check-circle-outline</v-icon
                    >
                    <v-icon v-else-if="status[4] === -1" color="error"
                        >$vuetify.icons.close-circle-outline</v-icon
                    >
                </div>
            </v-col>
            <v-col>
                Finishing
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import axios from "axios";
export default {
    props: ["config", "admin", "survey"],
    data() {
        return {
            error: '',
            status: [null, null, null, null, null]
        };
    },
    async created() {
        try {
            this.$set(this.status, 0, 0);
            await axios.post("/api/installer/env", {
                config: this.config
            });
            this.$set(this.status, 0, 1);
        } catch (e) {
            this.error = e.response.data.message
            this.$set(this.status, 0, -1);
        }

        try {
            this.$set(this.status, 1, 0);
            await axios.post("/api/installer/migrate");
            this.$set(this.status, 1, 1);
        } catch (e) {
            this.error = e.response.data.message
            this.$set(this.status, 1, -1);
        }

        try {
            this.$set(this.status, 2, 0);
            await axios.post("/api/installer/dependencies");
            this.$set(this.status, 2, 1);
        } catch (e) {
            this.error = e.response.data.message
            this.$set(this.status, 2, -1);
        }
        try {
            this.$set(this.status, 3, 0);
            await axios.post("/api/installer/translations");
            this.$set(this.status, 3, 1);
        } catch (e) {
            this.$set(this.status, 3, -1);
        }

        try {
            this.$set(this.status, 4, 0);
            await axios.post("/api/installer/finish", {
                survey: this.survey,
                admin: this.admin
            });
            this.$set(this.status, 4, 1);
            this.$emit("finished");
        } catch (e) {
            this.error = e.response.data.message
            this.$set(this.status, 4, -1);
        }
    }
};
</script>

<style>
.status {
    width: 30px;
    height: 100%;
}
</style>
