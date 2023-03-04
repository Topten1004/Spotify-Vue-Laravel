<template>
    <div class="section-container" v-if="!serverError">
          <div class="section-header" v-if="!isAlbum">
            <div class="section-header__title">
                {{ $t(section.title) }}
            </div>
          </div>
          <div class="list-container" v-if="!isContentLoading">
    <v-data-table
        :headers="headers"
        :items="mo7tawa"
        hide-default-header
        hide-default-footer
        v-if="mo7tawa.length"
    >
        <template v-slot:body="{ items }">
            <tbody>
                <tr v-if="isAlbum">
                    <div  class="align-center px-4 py-3" align="center">
                        <div class="hash pr-4">
                            #
                        </div>
                        <div class="text">
                            {{$t('Title')}}
                        </div>
                    </div>
                </tr>
                <template v-if="section">
                    <TROW  
                        v-for="(item, i) in mo7tawa"
                        :key="i"
                        :item="item"
                        :isOnSectionEdit="isOnSectionEdit"
                        :isEndpoint="section.endpoint"
                        :isOnEditPage="isOnEditPage"
                        @attachAsset="$emit('attachAsset', i)"
                    ></TROW>
                </template>
                <template v-else>
                    <TROW  
                        v-for="(item, n) in mo7tawa"
                        :key="item.id"
                        :rank="n + 1"
                        :item="item"
                        :playlist_id="playlist_id"
                        :isAlbum="true"
                        :isPlaylistOwner="isPlaylistOwner"
                        @deleted="$emit('deleted', $event)"
                    ></TROW>
                </template>
            </tbody>
        </template>
    </v-data-table>
          </div>
            <ul class="skeleton-list" v-else>
                <div class="skeleton" v-for="n in 7" :key="n">
                    <content-placeholders :rounded="true">
                        <content-placeholders-img />
                    </content-placeholders>
                    <content-placeholders :rounded="true">
                        <content-placeholders-img />
                    </content-placeholders>
                </div>
        </ul>
    </div>
    <empty-page
        v-else-if="!mo7tawa.length && !contentLoading"
        :headline="$t('No content!')"
        v-show="showEmpty"
        :sub="$t('There is no content available yet for this section.')"
    ></empty-page>
    <page-loading v-else />
</template>

<script>
import TROW from './EditTrList.vue';
export default {
    components: {
        TROW
    },
    props: {
        section : {
            type: Object,
        },
        contentLoading: {
            type: Boolean
        },
        content : {
            type: Array
        },
        isOnEditPage: {
            type: Boolean
        },
        isOnSectionEdit: {
            type: Boolean
        },
        isAlbum: {
            type: Boolean
        },
        showEmpty: {
            type: Boolean
        },
        isPlaylistOwner: {
            type: Boolean
        },
        playlist_id: {
            type: Number
        },
    },
    created() {
        if (!this.content) {
            this.getContent();
        } else {
            this.contentLength = this.content.length;
            this.$emit("contentLength", this.content.length);
        }
        if( this.isAlbum ) {
            this.headers.unshift(
                { text: "Rank", value: "rank" },
            )
        }
    },
    computed: {
        mo7tawa() {
            if( this.contents &&  this.contents.length ) {
                return this.contents
            } else if(  this.content && this.content.length) {
                return this.content
            } else {
                return []
            }
        }
    },
    data() {
        return {
            contents: [],
            contentLength: 0,
            isContentLoading: false,
            serverError: false,
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
    watch: {
        content(val) {
            this.contents = val;
        }
    },
    methods: {
        getContent() {
            this.serverError = false
            this.isContentLoading = true
            axios.get("/api/section/content/" + this.section.id)
            .then(res => {
                this.contents = res.data;
                this.contentLength = res.data.length;
                this.$emit("contentLength", res.data.length ? 0 : -1);
                this.$emit("content");
            })
            .catch(() => {
                this.contents = [];
                this.serverError = true
            })
            .finally(() => {
                this.isContentLoading = false
                this.$emit('content', this.contents)
            });
        }
}
};
</script>

<style lang="scss" scoped>
.empty-skeleton-tr {
    position: relative;
    height: 60px;
    margin: .3em 0;
    .child {
        background-color: #cccccc;
        border-radius: 4px;
        top: 10%;
        bottom: 10%;
    }
}

.v-data-table__wrapper {
  overflow-x: hidden !important;
}
</style>
