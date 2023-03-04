import Vue from "vue";

import Vuetify from 'vuetify'
import "vuetify/dist/vuetify.min.css";
import Vuelidate from 'vuelidate'

import { 
    mdiAccount,
    mdiCheckCircleOutline,
    mdiCloseCircleOutline
} from '@mdi/js'


Vue.use(Vuelidate)
Vue.use(Vuetify);


const defaultThemes = {
    light: {
        primary : '#4245a8',
        error: '#ff5252'
    }
} 

Vue.component('Master', require('./components/Master.vue').default);

const opts = {
    theme: {
        themes: defaultThemes
    },
    breakpoint: {
        thresholds: {
            xs: 500,
            sm: 540,
            md: 800,
            lg: 1280,
        },
        scrollBarWidth: 24,
    },
    icons: {
        iconfont: "mdiSvg", // default - only for display purposes,
        values: {
            'account': mdiAccount,
            'close-circle-outline': mdiCloseCircleOutline,
            'check-circle-outline': mdiCheckCircleOutline
        }
    }
};


const vuetify = new Vuetify(opts);

new Vue({
    el: "#app",
    vuetify 
});


