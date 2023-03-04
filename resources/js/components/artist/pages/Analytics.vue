<template>
    <div class="analytics-wrapper" v-if="stats">
        <v-container fluid>
            <v-row>
                <v-col
                    cols="12"
                    sm="6"
                    lg="3"
                    v-for="(card, t) in analyticsCards"
                    :key="t"
                >
                    <v-card rounded outlined class="analytic-info-card">
                        <v-card-title>
                            <v-row justify="space-between">
                                <v-col cols="8">
                                    <h5
                                        class="analytic-info-card__headline max-1-lines"
                                    >
                                        {{ $t(card.title) }}
                                    </h5>
                                    <div class="analytic-info-card__subline">
                                        {{ card.number }}
                                    </div>
                                </v-col>
                                <v-col cols="4" class="flex-end">
                                    <v-avatar>
                                        <v-img
                                            :gradient="card.gradient"
                                            class="d-flex justify-align-center analytics-card-icon"
                                        >
                                            <v-icon color="white" large
                                                >$vuetify.icons.{{
                                                    card.icon
                                                }}</v-icon
                                            >
                                        </v-img>
                                    </v-avatar>
                                </v-col>
                            </v-row>
                        </v-card-title>
                        <div class="percetage px-3 pb-2">
                            <strong
                                :class="
                                    (card.this_month === 0
                                        ? 'secondary'
                                        : 'success') + '--text'
                                "
                                >{{
                                    (card.this_month > 0 ? "+" : "") +
                                        card.this_month
                                }}</strong
                            >
                            <span class="muted">{{
                                $t("New this month")
                            }}</span>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
        <v-container fluid>
            <v-row class="py-5">
                <v-col cols="12" :sm="$store.getters.getSettings.enable_selling ? 6 : 12">
                    <v-card>
                        <v-card-title>
                            <v-row>
                                <v-col class="flex-grow-0">
                                    <v-icon class="ml-3" color="primary" x-large
                                        >$vuetify.icons.arrow-up</v-icon
                                    >
                                </v-col>
                                <v-col>
                                    <h5 class="analytic-info-card__headline">
                                        {{ $t("Popular Songs") }}
                                    </h5>
                                </v-col>
                            </v-row>
                        </v-card-title>

                        <v-data-table
                            :no-data-text="$t('No data available')"
                            :loading-text="$t('Fetching data') + '...'"
                            :headers="songsTableHeaders"
                            :items="popularSongs"
                            hide-default-footer
                        >
                            <template v-slot:item.rank="{ item }">
                                <span
                                    v-if="popularSongs.indexOf(item) == 0"
                                    class="rank rank-one"
                                    >1.</span
                                >
                                <span
                                    v-else-if="popularSongs.indexOf(item) == 1"
                                    class="rank rank-two"
                                    >2.</span
                                >
                                <span
                                    v-else-if="popularSongs.indexOf(item) == 2"
                                    class="rank rank-three"
                                    >3.</span
                                >
                                <span v-else
                                    >{{ popularSongs.indexOf(item) }}.</span
                                >
                            </template>
                            <template v-slot:item.cover="{ item }">
                                <div class="img-container py-2">
                                    <v-img
                                        :src="item.cover"
                                        :alt="item.title"
                                        class="user-avatar song-cover"
                                        width="50"
                                        height="50"
                                    >
                                    </v-img>
                                </div>
                            </template>
                            <template v-slot:item.title="{ item }">
                                <router-link
                                    class="router-link"
                                    :to="{
                                        name: 'Song',
                                        params: { id: item.id }
                                    }"
                                    target="_blank"
                                >
                                    {{ item.title }}
                                </router-link>
                            </template>
                            <template v-slot:item.artists="{ item }">
                                {{ getArtists(item.artists) }}
                            </template>
                            <template v-slot:item.created_at="{ item }">
                                {{ moment(item.created_at).format("ll") }}
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" v-if="$store.getters.getSettings.enable_selling">
                    <v-card>
                        <v-card-title>
                            <v-row>
                                <v-col class="flex-grow-0">
                                    <v-icon class="ml-3" color="primary" x-large
                                        >$vuetify.icons.shopping</v-icon
                                    >
                                </v-col>
                                <v-col>
                                    <h5 class="analytic-info-card__headline">
                                        {{ $t("New Sales") }}
                                    </h5>
                                </v-col>
                            </v-row>
                        </v-card-title>

                        <v-data-table
                            :no-data-text="$t('No data available')"
                            :loading-text="$t('Fetching data') + '...'"
                            :headers="salesTableHeaders"
                            :items="sales"
                            hide-default-footer
                        >
                            <template v-slot:item.rank="{ item }">
                                <span
                                    v-if="sales.indexOf(item) == 0"
                                    class="rank rank-one"
                                    >1.</span
                                >
                                <span
                                    v-else-if="sales.indexOf(item) == 1"
                                    class="rank rank-two"
                                    >2.</span
                                >
                                <span
                                    v-else-if="sales.indexOf(item) == 2"
                                    class="rank rank-three"
                                    >3.</span
                                >
                                <span v-else>{{ sales.indexOf(item) }}.</span>
                            </template>
                            <template v-slot:item.cover="{ item }">
                                <div class="img-container py-2">
                                    <v-img
                                        :src="item.cover"
                                        :alt="item.itemTitle"
                                        class="user-avatar song-cover"
                                        width="50"
                                        height="50"
                                    >
                                    </v-img>
                                </div>
                            </template>
                            <template v-slot:item.itemTitle="{ item }">
                                {{ item.itemTitle }}
                            </template>
                            <template v-slot:item.price="{ item }">
                                <div class="price success--text bold">
                                    {{
                                        price(
                                            (item.amount * item.artist_cut) /
                                                100
                                        ) + item.priceSymbol
                                    }}
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card height="550px" class="chart" outlined rounded>
                        <v-card-title>
                            <div
                                class="flex flex-grow-1 align-center plays-chart-card-title"
                            >
                                <v-icon class="ml-3" color="primary" x-large
                                    >$vuetify.icons.music-note</v-icon
                                >
                                <h5 class="analytic-info-card__headline">
                                    {{ $t("Plays Chart") }}
                                </h5>
                                <div class="card-subline ml-2">
                                    {{ $t("Number of plays per interval") }}
                                </div>
                            </div>
                            <div class="flex-grow-0">
                                <veutify-select
                                    class="ml-auto plays-chart-card-title__select"
                                    :items="timeOptions"
                                    item-text="name"
                                    return-object
                                    v-model="timeOption"
                                    @change="updatePlaysChart"
                                    :label="$t('Period')"
                                    dense
                                ></veutify-select>
                            </div>
                        </v-card-title>
                        <div class="">
                            <area-chart
                                v-if="chartPlaysData.datasets[0].data.length"
                                :chartdata="chartPlaysData"
                                :options="chartPlaysData.options"
                                :reset="playsChartReset"
                                @resetOver="playsChartReset = false"
                            />
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
    <page-loading v-else />
</template>

<script>
import AreaChart from "../../charts/AreaChart";
import PieChart from "../../charts/PieChart";
import { VSelect } from "vuetify/lib";
export default {
    components: {
        AreaChart,
        PieChart,
        veutifySelect: VSelect
    },
    data() {
        return {
            stats: null,
            playsChartReset: false,
            popularSongs: [],
            timeOptions: [
                {
                    name: this.$t("Last 7 days"),
                    param: "lw"
                },
                {
                    name: this.$t("Last Month"),
                    param: "lm"
                },
                {
                    name: this.$t("Last Year"),
                    param: "ly"
                }
            ],
            songsTableHeaders: [
                {
                    text: this.$t("#"),
                    value: "rank"
                },
                {
                    text: this.$t("Cover"),
                    align: "start",
                    sortable: false,
                    value: "cover"
                },
                { text: this.$t("Title"), value: "title" },
                { text: this.$t("Artists"), value: "artists" },
                { text: this.$t("Plays"), value: "nb_plays" },
                { text: this.$t("Likes"), value: "nb_likes" },
                { text: this.$t("Created At"), value: "created_at" }
            ],
            salesTableHeaders: [
                {
                    text: this.$t("#"),
                    value: "rank"
                },
                {
                    text: this.$t("Cover"),
                    align: "start",
                    sortable: false,
                    value: "cover"
                },
                { text: this.$t("Title"), value: "itemTitle" },
                { text: this.$t("Earned"), value: "price" }
            ],
            timeOption: {
                name: this.$t("Last 7 days"),
                param: "lw"
            },
            analyticsCards: [
                 {
                    title: this.$store.getters.getSettings.enable_selling ? "Sales" : "Songs",
                    icon: this.$store.getters.getSettings.enable_selling ? "currency-usd" :  'music-note-eighth',
                    number: 0,
                    gradient: "45deg,#00cdac,#02aab0",
                    this_month: "0"
                },
                {
                    icon: "account-group",
                    title: "Followers",
                    number: 0,
                    gradient: "45deg,#8C366C, #6E64E7",
                    this_month: "0"
                },
                {
                    icon: "music-note",
                    title: "Songs",
                    number: 0,
                    gradient: "45deg,#00cdac,#02aab0",
                    this_month: "0"
                },
                {
                    icon: "play-speed",
                    title: "Total Plays",
                    number: 0,
                    gradient: "45deg,#00cdac,#02aab0",
                    this_month: "0"
                }
                // upcoming feature
                // {
                //     icon: "account",
                //     title: this.$t("profile visits"),
                //     number: 0,
                //     gradient: "45deg,#f09819,#edde5d",
                //     this_month: "0",
                // }
            ],
            plays: [],
            chartPlaysData: {
                labels: [],
                datasets: [
                    {
                        data: [],
                        fill: true,
                        borderColor: "#2196f3",
                        backgroundColor: "#4245a8",
                        borderWidth: 1
                    }
                ],
                options: {
                    legend: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    precision: 0,
                                    beginAtZero: true
                                }
                            }
                        ]
                    }
                }
            }
        };
    },
    created() {
        // fetching the stats and updating the page components
        axios.get("/api/artist/analytics").then(res => {
            this.stats = res.data;
            this.updatePlaysChart();
            // upcoming feature
            // this.analyticsCards[
            //   this.analyticsCards.findIndex((card) => card.title.match(/profile/i))
            // ].number = this.stats.nb_profile_visits;
            // this.analyticsCards[
            //   this.analyticsCards.findIndex((card) => card.title.match(/profile/i))
            // ].this_month = this.stats.nb_profile_visits_this_month;

            // updating the analytics cards data
            this.analyticsCards[
                this.analyticsCards.findIndex(card => card.title.match(/play/i))
            ].number = this.stats.total_plays;
            this.analyticsCards[
                this.analyticsCards.findIndex(card => card.title.match(/play/i))
            ].this_month = this.stats.total_plays_this_month;
            this.analyticsCards[
                this.analyticsCards.findIndex(card => card.title.match(/song/i))
            ].number = this.stats.nb_songs;
            this.analyticsCards[
                this.analyticsCards.findIndex(card => card.title.match(/song/i))
            ].this_month = this.stats.nb_songs_this_month;

            // updating the songs card
            if(this.$store.getters.getSettings.enable_selling) {
                this.analyticsCards[
                    this.analyticsCards.findIndex(card => card.title.match(/sale/i))
                ].number = this.stats.nb_sales;
                this.analyticsCards[
                    this.analyticsCards.findIndex(card => card.title.match(/sale/i))
                ].this_month = this.stats.nb_sales_this_month;
            } else {
                this.analyticsCards[
                    this.analyticsCards.findIndex(card => card.title.match(/song/i))
                ].number = this.stats.nb_songs;
                this.analyticsCards[
                    this.analyticsCards.findIndex(card => card.title.match(/song/i))
                ].this_month = this.stats.nb_songs_this_month;
            }

            this.analyticsCards[
                this.analyticsCards.findIndex(card =>
                    card.title.match(/follower/i)
                )
            ].number = this.stats.nb_followers;
            this.analyticsCards[
                this.analyticsCards.findIndex(card =>
                    card.title.match(/follower/i)
                )
            ].this_month = this.stats.nb_followers_this_month;

            this.popularSongs = this.stats.popular_songs;

            this.sales = this.stats.sales;
        });
    },
    methods: {
        updatePlaysChart() {
            var playsCount;
            axios
                .get("/api/artist/plays/" + this.timeOption.param)
                .then(res => {
                    playsCount = res.data;
                })
                .then(() => {
                    // lw = last week
                    // After getting the plays data from the API
                    // this script reform the data so it is displayable
                    // by the ChartJs script
                    if (this.timeOption.param === "lw") {
                        let days = [
                            "Sun",
                            "Mon",
                            "Tue",
                            "Wed",
                            "Thu",
                            "Fri",
                            "Sat"
                        ];
                        let last7Days = [];
                        for (let i = 1; i <= 7; i++) {
                            let moment = this.moment()
                                .subtract(1, "weeks")
                                .add(i, "d");
                            last7Days.push({
                                day: days[moment.day()],
                                date: moment.format("YYYY-MM-DD")
                            });
                        }
                        let plays = last7Days.map(day => {
                            if (
                                playsCount.some(play => play.date === day.date)
                            ) {
                                day.plays = playsCount.find(
                                    play => play.date === day.date
                                ).plays;
                            } else {
                                day.plays = 0;
                            }
                            return day;
                        });
                        this.chartPlaysData.labels = plays.map(
                            play => play.day
                        );
                        this.chartPlaysData.datasets[0].data = plays.map(
                            play => play.plays
                        );
                        this.playsChartReset = true;
                    } else if (this.timeOption.param === "lm") {
                        // lm = last month
                        let last30Days = [];
                        for (let i = 1; i <= 30; i++) {
                            let moment = this.moment()
                                .subtract(30, "days")
                                .add(i, "d");
                            last30Days.push({
                                day: moment.format("ll"),
                                date: moment.format("YYYY-MM-DD")
                            });
                        }
                        let plays = last30Days.map(day => {
                            if (
                                playsCount.some(play => play.date === day.date)
                            ) {
                                day.plays = playsCount.find(
                                    play => play.date === day.date
                                ).plays;
                            } else {
                                day.plays = 0;
                            }
                            return day;
                        });
                        this.chartPlaysData.labels = plays.map(
                            play => play.day
                        );
                        this.chartPlaysData.datasets[0].data = plays.map(
                            play => play.plays
                        );
                        this.playsChartReset = true;
                    } else if (this.timeOption.param === "ly") {
                        // ly = last year
                        let months = [
                            "Jan",
                            "Feb",
                            "Mar",
                            "Apr",
                            "May",
                            "Jun",
                            "Jul",
                            "Aug",
                            "Sep",
                            "Oct",
                            "Nov",
                            "Dec"
                        ];
                        let last12Month = [];
                        for (let i = 1; i <= 12; i++) {
                            let moment = this.moment()
                                .subtract(12, "months")
                                .add(i, "M");
                            last12Month.push({
                                month: months[moment.month()],
                                date: moment.format("M")
                            });
                        }
                        let plays = last12Month.map(month => {
                            if (
                                playsCount.some(
                                    play => play.date === parseInt(month.date)
                                )
                            ) {
                                month.plays = playsCount.find(
                                    play => play.date === parseInt(month.date)
                                ).plays;
                            } else {
                                month.plays = 0;
                            }
                            return month;
                        });
                        this.chartPlaysData.labels = plays.map(
                            play => play.month
                        );
                        this.chartPlaysData.datasets[0].data = plays.map(
                            play => play.plays
                        );
                        this.playsChartReset = true;
                    }
                });
            setTimeout(() => {
                this.playsChartReset = false;
            }, 300);
        }
    }
};
</script>

<style lang="scss" scoped>
.v-image__image--preload {
    filter: blur(0) !important ;
}
.subline {
    font-size: 0.7em;
    margin-top: -1em;
    color: grey;
    font-weight: 600;
}
.flex-end {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}
.analytics-card-icon {
    .v-responsive__content {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
}
</style>
