<template>
  <div class="subscriptions-wrapper">
    <v-card outlined>
      <v-card-title>
        <v-icon color="primary" x-large>$vuetify.icons.handshake-outline</v-icon>
        <v-btn
          class="mx-2"
          dark
          small
          color="primary"
          @click="createSubscription"
          v-if="hasPermission('CED subscriptions')"
        >
          <v-icon>$vuetify.icons.plus</v-icon> {{ $t('New') }}
        </v-btn>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <div class="admin-search-bar">
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
           :label="$t('Search')"
            single-line
            hide-details
          ></v-text-field>
        </div>
      </v-card-title>
      <v-data-table
        :no-data-text="$t('No data available')"
        :loading-text="$t('Fetching data') + '...'"
        :headers="headers"
        :items="subscriptions || []"
        :items-per-page="25"
        class="elevation-1"
        :loading="!subscriptions"
      >
              <template v-slot:item.owner="{ item }">
          <template v-if="item.owner">
            <router-link
              class="router-link"
              :to="{ name: 'profile', params: { id: item.id } }"
              target="_blank"
            >
              <div class="avatar">
                <v-avatar size="35">
                  <v-img :src="item.owner.avatar"></v-img>
                </v-avatar>
              </div>
              <div class="artist-name">
                {{ item.owner.email }}
              </div>
            </router-link>
          </template>
          <template v-else> - </template>
        </template>
        <template v-slot:item.plan="{ item }">
          {{ item.plan.name }} ({{ item.plan.free ? $t('Free') : planCurrencySymbol(item.plan) + price(item.plan.amount) + '/' + $t(item.plan.interval) }})
        </template>
        <template v-slot:item.gateway="{ item }">
          <template v-if="item.gateway == 'paypal'">
            <div class="justify-center">   <v-img  max-width="70" src="/storage/defaults/images/billing/paypal-logo.png"></v-img></div>
          </template>
          <template v-else-if="item.gateway == 'stripe'">
            <div class="justify-center">
                <v-img  max-width="70" src="/storage/defaults/images/billing/stripe.png"></v-img>
            </div>
          </template>
          <template v-else>
              -
          </template>
        </template>
        <template v-slot:item.status="{ item }">
          <v-btn x-small dense depressed v-if="item.status === 'active'" class="success">Active</v-btn>
          <v-btn x-small dense depressed v-else-if="item.status === 'canceled'" class="error">Canceled</v-btn>
          <v-btn x-small dense depressed v-else class="warning">{{ item.status }}</v-btn>
        </template>
         <template v-slot:item.renews_at="{ item }">
           <template v-if="item.renews_at">
              {{ moment(item.renews_at).format("ll") }}
           </template>
            <template v-else>
            -
           </template>
        </template>
        <template v-slot:item.operations="{ item }">
          <!-- <v-btn
            class="mx-2"
            @click="editSubscription(item)"
            v-if="ehasPermission('Edit subscriptions')"
            x-small
            fab
            dark
            color="info"
          >
            <v-icon>$vuetify.icons.pencil</v-icon>
          </v-btn> -->
          <v-btn
            class="mx-2"
            @click="cancelSubscription(item.id)"
            v-if="hasPermission('CED subscriptions') && item.status !=='canceled'"
            x-small
            outlined
            color="error"
          >
            <v-icon left>$vuetify.icons.close</v-icon> {{$t('Cancel')}}
          </v-btn> 
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="editDialog" max-width="500">
      <edit-subscription-dialog
        v-if="editDialog"
        @update="subscriptionUpdated"
        @close="closeSubsctiptionDialog"
        :subscription="editingSubscription"
      />
    </v-dialog>
  </div>
</template>
<script>
import editSubscriptionDialog from "../../dialogs/admin/edit/Subscription";
export default {
  components: {
    editSubscriptionDialog,
  },
  data() {
    return {
      subscriptions: null,
      search: "",
      headers: [
        { text: this.$t('Owner'), value: "owner", align: "center" },
        { text: this.$t('Status'), value: "status",align: "center"  },
        { text: this.$t('Gateway'), value: "gateway",align: "center"  },
        { text: this.$t('Plan'), value: "plan",align: "center"  },
        { text: this.$t('Renews At'), value: "renews_at", align: "center"},
        { text: this.$t('Operations'), value: "operations" },
      ],
      editDialog: false,
      editingSubscription: null,
    };
  },
  created() {
    this.fetchSubscriptions();
  },
  methods: {
    fetchSubscriptions() {
      axios.get("/api/admin/subscriptions").then((res) => {
        this.subscriptions = res.data;
      });
    },
    cancelSubscription(id) {
      this.$confirm({
        message: `${this.$t("Are you sure you wanna cancel this") + " " + this.$t('Subscription')}?`,
        button: {
          no: this.$t('No'),
          yes: this.$t('Yes'),
        },
        /**
         * Callback Function
         * @param {Boolean} confirm
         */
        callback: (confirm) => {
          if (confirm) {
            axios
              .delete("/api/admin/subscriptions/" + id)
              .then(() => {
                this.$notify({
                  group: "foo",
                  type: "success",
                  title: this.$t("Cancelled"),
                  text: this.$t('Subscription cancelled successfully.'),
                });
                this.fetchSubscriptions();
              })
              .catch();
          }
        },
      });
    },
    editSubscription(subscription) {
        this.editingSubscription = subscription;
        this.editDialog = true;
    },
    createSubscription() {
        this.editingSubscription = {
          new: true,
        };
        this.editDialog = true;
    },
    closeSubsctiptionDialog() {
      this.editDialog = false;
      this.editingSubscription = null;
    },
    subscriptionUpdated() {
      this.editDialog = false;
      this.$notify({
        group: "foo",
        type: "success",
        title: this.$t("Saved"),
        text: this.$t('User') + " " +  this.$t('Updated') + ".",
      });
      this.fetchSubscriptions();
    },
  },
};
</script>