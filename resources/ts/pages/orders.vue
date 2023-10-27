 <script>
import { VDataTableServer } from "vuetify/labs/VDataTable";
import axios from "axios";

export default {
  components: {
    VDataTableServer,
  },
  data() {
    return {
      itemsPerPage: 10,
      page: 1,
      total: 0,
      searchQuery: "",
      orders: [],
      loading: true,
      headers: [
        { title: "", key: "data-table-expand", sortable: false },
        { title: "Address", key: "address", align: "start", sortable: false },
        { title: "Wilaya", key: "wilaya", align: "start", sortable: false },
        { title: "Total", key: "total", align: "start", sortable: false },
        { title: "Actions", key: "actions", align: "start", sortable: false },
      ],
      snackbar: {
        isVisible: false,
        message: "",
        color: "",
      },
    };
  },

  methods: {
    async fetchOrders() {
      try {
        const { data } = await axios.get("/api/orders/all", {
          params: {
            q: this.searchQuery,
            page: this.page,
            perPage: this.itemsPerPage,
          },
        });
        this.orders = data.orders;
        this.total = data.meta.total;
        this.loading = false;
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
        this.loading = false;
      }
    },

    async statusToPending(id) {
      try {
        await axios.patch("/api/orders/pend/" + id);
        this.fetchOrders();
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
      }
    },

    async statusToFulfilled(id) {
      try {
        await axios.patch("/api/orders/fulfill/" + id);
        this.fetchOrders();
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
      }
    },

    async statusToCanceled(id) {
      try {
        await axios.patch("/api/orders/cancel/" + id);
        this.fetchOrders();
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
      }
    },

    calculateColorVariant(status) {
      switch (status) {
        case "pending":
          return "warning";
        case "fulfilled":
          return "success";
        case "canceled":
          return "error";
      }
    },
  },

  async mounted() {
    await this.fetchOrders();
  },
};
</script>
<template>
  <div class="container">
    <v-snackbar
      v-model="snackbar.isVisible"
      :color="snackbar.color"
      :timeout="2000"
    >
      {{ snackbar.message }}
    </v-snackbar>

    <v-row>
      <v-col>
        <h2>Orders</h2>
      </v-col>
    </v-row>
    <v-row>
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :headers="headers"
        :items-length="total"
        :items="orders"
        :loading="loading"
        :search="searchQuery"
        class="elevation-1"
        @update:options="fetchOrders"
        expand-on-click
      >
        <template v-slot:top>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="searchQuery"
                variant="filled"
                hide-details
                placeholder="Search "
                class="ma-2"
                density="compact"
              ></v-text-field>
            </v-col>
          </v-row>
        </template>
        <!--Expandable Row-->
        <template #expanded-row="{ item }">
          <tr class="v-data-table__tr">
            <td :colspan="headers.length">
              <p class="my-1">
                Name: {{ item.raw.firstname + " " + item.raw.lastname }}
              </p>
              <p class="my-1">City: {{ item.raw.city }}</p>
              <p v-if="item.raw.notes" class="my-1">
                Notes: {{ item.raw.notes }}
              </p>
              <p class="my-1">Products :</p>
              <p v-for="(product, index) in item.raw.products" :key="index">
                Name:{{ product.name }} <br />
                Quantity:{{ product.pivot.quantity }} <br />
              </p>
            </td>
          </tr>
        </template>

        <!-- Total-->
        <template #item.total="{ item }">
          <span>{{ item.raw.total }} DZD</span>
        </template>

        <!-- Actions-->
        <template #item.actions="{ item }">
          <v-menu transition="slide-y-transition">
            <template v-slot:activator="{ props }">
              <v-btn
                :color="calculateColorVariant(item.raw.status)"
                v-bind="props"
                variant="tonal"
              >
                {{ item.raw.status }}
              </v-btn>
            </template>
            <v-list>
              <v-list-item>
                <v-btn
                  :disabled="item.raw.status == 'fulfilled'"
                  :variant="item.raw.status == 'fulfilled' ? 'text' : 'tonal'"
                  width="100%"
                  small
                  color="success"
                  @click="statusToFulfilled(item.raw.id)"
                >
                  Fulfiled
                </v-btn>
              </v-list-item>
              <v-list-item>
                <v-btn
                  :disabled="item.raw.status == 'pending'"
                  :variant="item.raw.status == 'pending' ? 'text' : 'tonal'"
                  width="100%"
                  small
                  color="warning"
                  @click="statusToPending(item.raw.id)"
                >
                  Pending
                </v-btn>
              </v-list-item>
              <v-list-item>
                <v-btn
                  :disabled="item.raw.status == 'canceled'"
                  :variant="item.raw.status == 'canceled' ? 'text' : 'tonal'"
                  width="100%"
                  small
                  color="error"
                  @click="statusToCanceled(item.raw.id)"
                >
                  Canceled
                </v-btn>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table-server>
    </v-row>
  </div>
</template>

 <style>
.container {
  padding: 20px;
}
.btn-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding-left: 0;
}
</style>

