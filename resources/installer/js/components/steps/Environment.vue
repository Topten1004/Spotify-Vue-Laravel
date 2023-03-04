<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-alert
                    v-if="error"
                    v-text="error"
                    class="error white--text mx-auto"
                    max-width="600px"
                ></v-alert>
            </v-col>
            <v-col cols="12">
                App
                <v-divider></v-divider>
            </v-col>
            <v-col cols="12">
                <v-text-field
                    outlined
                    dense
                    label="App Name"
                    :error-messages="appNameErrors"
                    v-model="config.app.name"
                    @input="$v.config.app.name.$touch()"
                    @blur="$v.config.app.name.$touch()"
                ></v-text-field>
                <v-text-field
                    outlined
                    dense
                    label="App URL"
                    :error-messages="appUrlErrors"
                    @input="$v.config.app.url.$touch()"
                    @blur="$v.config.app.url.$touch()"
                    v-model="config.app.url"
                ></v-text-field>
            </v-col>
            <v-col cols="12">
                Database
                <v-divider></v-divider>
            </v-col>
            <v-col cols="12">
                <v-select
                    outlined
                    dense
                    label="Database Connection"
                    :items="databaseConnections"
                    v-model="config.database.connection"
                ></v-select>
                <v-text-field
                    outlined
                    dense
                    label="Database Host"
                    v-model="config.database.host"
                    :error-messages="databaseHostErrors"
                    @input="$v.config.database.host.$touch()"
                    @blur="$v.config.database.host.$touch()"
                ></v-text-field>
                <v-text-field
                    outlined
                    dense
                    label="Database Port"
                    placeholder="3306"
                    v-model="config.database.port"
                    :error-messages="databasePortErrors"
                    @input="$v.config.database.port.$touch()"
                    @blur="$v.config.database.port.$touch()"
                ></v-text-field>
                <v-text-field
                    outlined
                    dense
                    label="Database Name"
                    v-model="config.database.name"
                    :error-messages="databaseNameErrors"
                    @input="$v.config.database.name.$touch()"
                    @blur="$v.config.database.name.$touch()"
                ></v-text-field>
                <v-text-field
                    outlined
                    dense
                    label="Username"
                    v-model="config.database.username"
                    :error-messages="databaseUsernameErrors"
                    @input="$v.config.database.username.$touch()"
                    @blur="$v.config.database.username.$touch()"
                ></v-text-field>
                <v-text-field
                    outlined
                    dense
                    label="Password"
                    v-model="config.database.password"
                    type="password"
                ></v-text-field>
            </v-col>
        </v-row>
        <NextButton text="Next" @clicked="submit" :loading="loading" />
    </v-container>
</template>

<script>
import axios from "axios";
import NextButton from "../NextButton.vue";
import {
    required,
    integer,
    url
} from "vuelidate/lib/validators";
export default {
    components: { NextButton },
    validations: {
        config: {
            app: {
                name: { required },
                url: { required, url }
            },
            database: {
                host: { required },
                username: { required },
                name: { required },
                port: { required, integer },
                host: { required }
            }
        }
    },
    computed: {
        appNameErrors() {
            const errors = [];
            if (!this.$v.config.app.name.$dirty) return errors;
            !this.$v.config.app.name.required &&
                errors.push("App name is required");
            return errors;
        },
        appUrlErrors() {
            const errors = [];
            if (!this.$v.config.app.url.$dirty) return errors;
            !this.$v.config.app.url.required &&
                errors.push("App URL is required");
            !this.$v.config.app.url.url &&
                errors.push("Please enter a valid URL");
            return errors;
        },
        databasePortErrors() {
            const errors = [];
            if (!this.$v.config.database.port.$dirty) return errors;
            !this.$v.config.database.port.required &&
                errors.push("Database port is required");
            !this.$v.config.database.port.integer &&
                errors.push("Please enter the correct port value (xxxx)");
            return errors;
        },
        databaseUsernameErrors() {
            const errors = [];
            if (!this.$v.config.database.username.$dirty) return errors;
            !this.$v.config.database.username.required &&
                errors.push("Database username is required");
            return errors;
        },
        databaseNameErrors() {
            const errors = [];
            if (!this.$v.config.database.name.$dirty) return errors;
            !this.$v.config.database.name.required &&
                errors.push("Database name is required");
            return errors;
        },
        databaseHostErrors() {
            const errors = [];
            if (!this.$v.config.database.host.$dirty) return errors;
            !this.$v.config.database.host.required &&
                errors.push("Database host is required");
            return errors;
        }
    },
    data: () => ({
        error: "",
        loading: false,
        config: {
            app: {
                name: "",
                url: window.location.href.replace("install", "")
            },
            database: {
                connection: "mysql",
                host: "localhost",
                port: "3306",
                name: "",
                username: "",
                password: ""
            }
        },
        databaseConnections: ["mysql", "sqlite", "pgsql", "sqlsrv"]
    }),
    methods: {
        submit() {
            this.loading = true;
            this.$v.$touch();
            if (!this.$v.$invalid) {
                this.testDBConnection();
            } else {
                this.loading = false;
            }
        },
        testDBConnection() {
            this.error = "";
            this.loading = true;
            axios
                .post("/api/installer/test-db-connection", {
                    ...this.config.database
                })
                .then(() => {
                    this.loading = false;
                    this.$emit("next", this.config);
                })
                .catch(e => {
                    this.loading = false;
                    this.error = e.response.data;
                });
        }
    }
};
</script>

<style></style>
