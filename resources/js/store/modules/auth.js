import router from "../../router";
import Vue from 'vue'

const state = {};

const mutation = {}

const actions = {
    /**
     * login the user.
     * @param {*} context
     * @param {*} param1
     */
        async login(context, { email, password, driver, profile }) {
        try {
            if (driver === "google") {
                var res = await axios.post("/api/login/google/callback", {
                    profile
                });
            } else if (driver === "facebook") {
                var res = await axios.post("/api/login/facebook/callback", {
                    profile
                });
                if (res.data.message === "email required") {
                    // if email was not provide by the OAuth provider
                    router.push({
                        name: "complete_signup",
                        params: { profile: profile, driver: driver }
                    });
                    return;
                }
            } else {
                var res = await axios.post("/api/login", {
                    email: email,
                    password: password
                });
            }
            if (res) {
                let token = res.data["access_token"];
                if (token) {
                    context.commit("setToken", token);
                    // save the token at local storage if it exists
                    localStorage.setItem("token", token);
                    axios.defaults.headers.common["Authorization"] =
                        "Bearer " + token;
                    await context.dispatch("fetchUser");

                    const user = context.getters.getUser;
                    if (user) {
                       var isArtist = user.roles
                            ? user.roles.some(role => role.name === "artist") ||
                                  user.is_admin
                            : false;
                        var isAdmin = user.roles
                            ? user.roles.some(role => role.name === "admin")
                            : false;
                    }
                    if( isAdmin ) {
                        router.push({
                            name: "admin.analytics"
                        });
                        
                    } else if ( isArtist ) {
                        router.push({
                            name: "artist.analytics"
                        });
                    } else {
                        router.push({
                            path: "home"
                        });
                    }

                    if( context.getters.getSettings.ga4 && context.getters.getSettings.analytics_login_event ) {
                        emitAnalyticsEvent({
                            action: 'login',
                            category: 'auth',
                            label: context.getters.getUser.email
                        })
                    }
                    return Promise.resolve();
                } else {
                    Vue.notify({
                        type: "error",
                        group: "foo",
                        message: "Authentification failed try again!"
                    });
                }
            }
        } catch (e) {
            throw e;
        }
    },
    /**
     * Logout the user.
     * @param {*} context
     */
    async logout(context) {
        let res = await axios.post("/api/logout");
        if (context.state.fbLogoutFunction) {
            context.state.fbLogoutFunction();
        }
        if (res.status == 200) {
            if( context.getters.getSettings.ga4 && context.getters.getSettings.analytics_logout_event ) {
                emitAnalyticsEvent({
                    action: 'logout',
                    category: 'auth',
                    label: context.getters.getUser.email
                })
            }
            if( typeof gapi !== 'undefined' && gapi.auth2 && gapi.auth2.getAuthInstance()) {
                signOut()
            }
            context.commit("setUser", null);
            localStorage.removeItem("token");
        }
        window.open('/login', '_self');
    }
}


export default {
    state,
    mutation,
    actions
}