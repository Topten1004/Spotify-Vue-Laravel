<template>
    <div class="grid-layout">
        <div class="item" v-for="item in items" :key="item.id">
            <template v-if="item.type === 'song'">
                <song-expo :song="item" />
            </template>
            <template v-else-if="item.type === 'podcast'">
                <podcast-box :podcast="item" />
            </template>
            <template v-else-if="item.type === 'album'">
                <album-expo :album="item" />
            </template>
        </div>
    </div>
</template>
<script>
export default {
    props: ["items"]
};
</script>
<style lang="scss">
.grid-layout, .items-grid {
    display: flex;
    flex-wrap: wrap;
    .item {
        min-width: 150px;
        max-width: 240px;
        padding: 0.4em;
        flex-basis: 20%;
        @media screen and (max-width: 1550px) {
            flex-basis: 25%;
            font-size: 95%;
        }
        @media screen and (max-width: 1400px) {
            font-size: 90%;
        }
        @media screen and (max-width: 1300px) {
            flex-basis: 33.33%;
            font-size: 100%;
        }
        @media screen and (max-width: 1200px) {
            font-size: 80%;
        }
        @media screen and (max-width: 1100px) {
            font-size: 100%;
        }
        @media screen and (max-width: 1000px) {
            font-size: 85%;
        }
    }
}
</style>
