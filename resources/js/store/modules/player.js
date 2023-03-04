import Vue from "vue"
import {i18n} from "../../i18n-setup.js";

async function isPlayable(audio, index) {
    // if( !audio.source )
    // {   
    //     const title = audio.title;
    //     const artist = audio.artists.length  ? audio.artists[0].name ? audio.artists[0].name : audio.artists[0].displayname : '';
    //     await getYoutubeVideoIfExists(title, artist, index);
    // }
    return (
        (audio.youtube_id && store.getters.getSettings.allowVideos) ||
        audio.source ||
        audio.streamEndpoint
    );
}


const state = {
    currentAudio: null
};

const getters = {
    isCurrentlyPlaying(state)
    {
        return function(audio)
        {
            return state.currentAudio && state.currentAudio.id === audio.id && state.currentAudio.type === audio.type
        }
    },
    getCurrentAudio(state) {
        return state.currentAudio;
    },
}

const mutations = {
    setCurrentAudio(state, song) {
        state.currentAudio = song;
    }
}

const actions = {
    /**
     * Update the queue ( playlist ).
     * @param {*} songs
     * @param {*} reset
     */
     async updateQueue(context, { content, reset }) {
        var newQueue = Array.isArray(content) ? content : [content];

        // newQueue = (Array.isArray(content) ? content : [content]).filter(async (c, i) => {
        //     let _isPlayable = await isPlayable(content[i]);
        //     return _isPlayable;
        // });
        if (!newQueue.filter(valid => valid).length) {
            return Vue.notify({
                group: "foo",
                type: "warning",
                title: i18n.t("Could not be played!"),
                text: i18n.t("No streaming source found.")
            });
        }
        context.commit(reset ? "updateQueue" : "pushIntoQueue", newQueue);
    }
}


export default {
    state,
    getters,
    mutations,
    actions
}