import store from "../store/index.js";
import axiosRetry from "axios-retry";
import { i18n } from '../i18n-setup.js'
import Vue from 'vue';

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// set the Auth token for every request
axios.defaults.headers.common["Authorization"] =
    "Bearer " + store.getters.getToken;

// show a toast for particular failed AJAX requests
axios.interceptors.response.use(
    response => response,
    error => {
        // for the demo
        if( error.response.data.type == 'demo' ) {
            Vue.notify({
                group: "foo",
                type: "info",
                title: "Sorry!",
                text: "You can't do this in preview mode"
            });
        }
        if( error.response.data.message == 'Invalid scope(s) provided.' || error.response.data.message == 'Unauthenticated.' ) {
            Vue.notify({
                group: "foo",
                type: "warning",
                title: i18n.t("Relogin"),
                text: i18n.t("Please relogin to your account")
            });
        }
        if( error.response.data.exception == 'FEEXCEPTION' ) {
            Vue.notify({
                group: "foo",
                type: "error",
                title: i18n.t("Error"),
                text: i18n.t(error.response.data.message)
                    // || Object.values(error.response.data.errors).join("<br />")
            });
        } else if( error.response.data.errors && error.response.data.message) {
            Vue.notify({
                group: "foo",
                type: "error",
                title: i18n.t("Error"),
                text: Object.values(error.response.data.errors).map(error => i18n.t(error)).join("<br />")
            });
        }
        throw error;
    }
);

// repeat a request 2 times if it failes
axiosRetry(axios, { retries: 2 });

export default axios;
