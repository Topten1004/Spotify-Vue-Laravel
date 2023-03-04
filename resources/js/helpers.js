// file includes helper methods used by the Vue instance
import Vue from 'vue'
import store from "./store/index";
import router from "./router";

function parseJSON(string) {
    try {
        return JSON.parse(string);
    } catch (e) {
        return string;
    }
}

export default {
    /**
     * Get the user data if he is logged ( checking the token on the local storage )
     */
    fetchUserIfExists: () => {
        return new Promise(resolve => {
            if (store.getters.getToken) {
                store
                    .dispatch("fetchUser")
                    .catch(() => {
                        // remove the token if it is expired
                        localStorage.removeItem("token");
                        // store.dispatch('fetchMessages', document.documentElement.lang)
                        // .then(() => {
                            resolve();  
                        // })
                    })
                    .then(() => {
                        // store.dispatch('fetchMessages', store.getters.getUser.lang)
                        // .then(() => {
                            resolve();  
                        // })
                    });
            } else {
                // set the locale
                store.commit("setUser", null);
                resolve();
            }
        });
    },
    /**
     * Fetch the app messages based on the locale
     */
    fetchMessages() {
        if (store.getters.getSettings.enableLangSwitcher) {
            // fetch the available languages
            axios
              .get("/api/languages")
              .then((res) => {
                store.commit("setAvailableLanguages", res.data);
              })
              .then(() => {
                // fetch messsages
                if (localStorage.getItem("pref-locale")) {
                  store.dispatch(
                    "fetchMessages",
                    localStorage.getItem("pref-locale")
                  );
                } else {
                  store.dispatch("fetchMessages", document.documentElement.lang);
                }
              });
          } else {
            // fetch messsages
            if (localStorage.getItem("pref-locale")) {
              store.dispatch("fetchMessages", localStorage.getItem("pref-locale"));
            } else {
              store.dispatch("fetchMessages", document.documentElement.lang);
            }
          }
    },
    /**
     * Set the setting values on the app.
     */
    updateSettings: () => {
        if (window.Settings) {
            let settings = {};
            window.Settings.forEach(element => {
                settings[element.key] =
                    element.value == "0"
                        ? false
                        : element.value == "1"
                        ? true
                        : parseJSON(element.value)
            });
            store.commit("setSettings", settings);
        }
    },
    /**
     * Confirm dialog: if the user wants to proceed to login or not
     */
    loginOrCancel: () => {
        return new Promise((res, rej) => {
            Vue.$confirm({
                message: `You need to login to your account`,
                button: {
                    no: "Cancel",
                    yes: "Login"
                },
                callback: confirm => {
                    if (confirm) {
                        res(router.push({ name: "login" }));
                    } else {
                        rej();
                    }
                }
            });
        });
    }
};
