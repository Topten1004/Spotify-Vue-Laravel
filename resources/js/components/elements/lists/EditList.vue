<template>
    <v-data-table
        :headers="headers"
        :items="content"
        hide-default-header
        hide-default-footer
        v-if="content.length"
        class="elevation-1"
    >
        <template v-slot:body="{ items }">
            <tbody>
                <draggable
                    class="list-group mt-5"
                    v-model="content"
                    @start="isDragging = true"
                    @end="isDragging = false"
                    v-bind="dragOptions"
                    handle=".handle"
                >

                <tr
                    v-for="item in items"
                    :key="item.id"
                    :search="search"
                    class="data-table-tr"
                    :class="{
                        'currently-playing': $store.getters.isCurrentlyPlaying(
                            item
                        )
                    }"
                >
                    <td width="60">
                        <div class="img-container py-2 relative">
                            <v-img
                                :src="item.cover"
                                :alt="item.title"
                                
                                class="table-cover"
                                width="50"
                                height="50"
                            >
                                <div class="play-icon">
                                    <v-icon dark class="i"
                                        >$veutify.icons.play-circle</v-icon
                                    >
                                </div>
                            </v-img>
                        </div>
                    </td>
                    <td width="30">
                        <div
                            class="menu-button"
                            @click="
                                $store.commit('setSongMenu', 'album-' + item.id)
                            "
                        >
                            <song-menu
                                :item="item"
                                @close="$store.commit('setSongMenu', null)"
                            ></song-menu>
                        </div>
                    </td>
                    <td>
                        {{ item.title }}
                    </td>
                    <td>
                        <div class="badges d-flex align-center">
                            <div
                                class="premium"
                                :title="$t('Premium')"
                                v-if="true"
                            >
                                <v-icon color="#FFA500" class="i"
                                    >$vuetify.icons.crown</v-icon
                                >
                            </div>
                            <div class="explicit mx-2" :title="$t('Explicit')">
                                <div class="explicit__sign">
                                    E
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <artists :artists="item.artists"></artists>
                    </td>
                    <td>
                        <!-- <router-link
                            v-if="item.album"
                            class="router-link"
                            :to="{
                                name: 'album',
                                params: { id: item.album.id }
                            }"
                        >
                            {{ item.album.title }}
                        </router-link> -->
                    </td>
                    <td>
                        <div class="like-button">
                            <div
                                class="button-svg-container"
                                v-if="$store.getters.isLiked(item.id)"
                                @click.stop="$store.commit('unlike', item.id)"
                            >
                                <v-icon color="primary" class="pointer" small
                                    >$vuetify.icons.heart</v-icon
                                >
                            </div>
                            <div
                                class="button-svg-container"
                                v-else
                                @click.stop="$store.commit('like', item.id)"
                            >
                                <v-icon class="pointer" small
                                    >$vuetify.icons.heart-outline</v-icon
                                >
                            </div>
                        </div>
                    </td>
                    <td>
                        {{ item.duration ? mmss(item.duration) : "" }}
                    </td>
                </tr>
                </draggable>
            </tbody>
        </template>
        <!-- <template v-slot:item.cover="{ item }">
                    <div class="img-container py-2">
                        <v-img
                            :src="
                                item.cover
                            "
                            :alt="item.title"
                            class="table-cover"
                            width="50"
                            height="50"
                        >
                        </v-img>
                    </div>
                    </template>
                    <template v-slot:item.title="{ item }">
                        <router-link
                            class="router-link"
                            :to="{ name: 'song', params: { id: item.id } }"
                            target="_blank"
                        >
                            {{ item.title }}
                        </router-link>
                    </template> -->
        <template v-slot:item.badges="{ item }">
            <div class="badges d-flex align-center">
                <div class="premium" :title="$t('Premium')" v-if="true">
                    <v-icon color="#FFA500" class="i"
                        >$vuetify.icons.crown</v-icon
                    >
                </div>
                <div class="explicit mx-2" :title="$t('Explicit')">
                    <div class="explicit__sign">
                        E
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:item.artists="{ item }">
            <artists :artists="item.artists"></artists>
        </template>
        <template v-slot:item.album="{ item }">
            <router-link
                v-if="item.album"
                class="router-link"
                :to="{
                    name: 'album',
                    params: { id: item.album.id }
                }"
            >
                {{ item.album.title }}
            </router-link>
        </template>
        <template v-slot:item.duration="{ item }">
            {{ item.duration ? mmss(item.duration) : "" }}
        </template>
    </v-data-table>
    <empty-page
        v-else-if="!content.length && !contentLoading"
        :headline="$t('No content!')"
        :sub="$t('There is no content available yet for this section.')"
    ></empty-page>
    <page-loading v-else />
</template>

<script>
import draggable from "vuedraggable";
export default {
    components: {
        draggable,
    },
    props: ["section", "contentLoading", "content"],
    data() {
        return {
            headers: [
                // {
                // text: '#',
                // align: 'center',
                // value: 'index',
                // },
                { text: "Cover", value: "cover" },
                { text: "Title", value: "title" },
                { text: "Badges", value: "badges" },
                { text: "Artists", value: "artists" },
                { text: "Album", value: "album" },
                { text: "Actions", value: "actions" },
                { text: "Duration", value: "duration" }
            ]
        };
    },
        computed: {
        dragOptions() {
            return {
                animation: 0,
                disabled: false,
                ghostClass: "ghost"
            };
        }
    },
};
</script>

<style></style>
