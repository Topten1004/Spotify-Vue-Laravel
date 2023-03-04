<template>
    <div class="artists d-flex">
        <template v-if="withAvatar">
            <div
                class="artist-container align-center"
                v-for="(artist, i) in artists"
                :key="artist.id"
            >
            <v-chip class="mr-1">
                <v-avatar left size="30" v-if="artist.avatar">
                    <v-img :src="artist.avatar"></v-img>
                </v-avatar>
                <router-link
                    @click.prevent
                    class="router-link"
                    :class="{ 'router-link__white': textColor }"
                    :to="{ name: 'artist', params: { id: artist.id } }"
                >
                    <div
                        class="artist-name"
                        :class="{ 'mr-1': i + 1 !== artists.length }"
                    >
                        {{
                            artist.displayname
                                ? artist.displayname
                                : artist.name
                                ? artist.name
                                : ""
                        }}
                        <v-icon x-small v-if="showLinker">$vuetify.icons.open-in-new</v-icon>
                    </div>
                </router-link>
            </v-chip>
            </div>
        </template>
        <template v-else>
            <div
                class="artist-container align-center"
                v-for="(artist, i) in artists"
                :key="artist.id"
            >
                <div class="artist-dsiplayname">
                    <router-link
                        @click.prevent
                        class="router-link"
                        :class="{ 'router-link__white': textColor }"
                        :to="{ name: 'artist', params: { id: artist.id } }"
                    >
                        <div
                            class="artist-name"
                            :class="{ 'mr-1': i + 1 !== artists.length }"
                        >
                            {{
                                artist.displayname
                                    ? artist.displayname
                                    : artist.name
                                    ? artist.name
                                    : ""
                            }}{{ i + 1 !== artists.length ? "," : "" }}
                        </div>
                    </router-link>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: ["artists", "withAvatar", "textColor", "showLinker"]
};
</script>

<style scoped>
.artists {
    white-space: nowrap;
    row-gap: 1em;
    flex-wrap: wrap;
}
</style>
