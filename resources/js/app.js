import Vue from "vue";
import router from "./router.js";
import vuetify from "./plugins/vuetify.js";
import store from "./store/index.js";
import VueGtag from "vue-gtag";
import {i18n} from './i18n-setup.js';
import './googleOauth';
import './components-init';
import './plugins/axios';

import VueSocialSharing from 'vue-social-sharing'

Vue.use(VueSocialSharing);

Vue.use(VueGtag, {
    config: { id: Settings.find( s => s.key === 'ga4' ) && Settings.find( s => s.key === 'ga4MID' ) && Settings.find( s => s.key === 'ga4MID' ).value }
}, router);

window.emitAnalyticsEvent = function ({ action, category, label }) {
    Vue.$gtag.event(action, {
        'event_category': category,
        'event_label': label
    })
}

// helper methods
import helpers from './helpers';

// Vue mixin includes all the global methods that needs to be available among all the comps
import globalMixin from './globalMixin'; 


// initials settings on the store
helpers.updateSettings();

// disble debug tools & console message on production mode
if (process.env.MIX_ENV_MODE === 'production') {
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true; 
}



// get the user if he is logged before initializing the Vue instance
helpers.fetchUserIfExists().then(() => {
    helpers.fetchMessages();
    new Vue({
        el: "#app",
        mixins: [globalMixin],
        vuetify,
        router,
        store,
        i18n
    });
})


