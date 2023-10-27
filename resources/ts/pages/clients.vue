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
      clients: [],
      loading: true,
      headers: [
        { title: "Name", key: "name", align: "start", sortable: false },
        { title: "Email", key: "email", align: "start", sortable: false },
        { title: "", key: "actions", align: "start", sortable: false },
      ],
      snackbar: {
        isVisible: false,
        message: "",
        color: "",
      },
    };
  },

  methods: {
    async fetchClients() {
      try {
        const { data } = await axios.get("/api/clients/all", {
          params: {
            q: this.searchQuery,
            page: this.page,
            perPage: this.itemsPerPage,
          },
        });
        this.clients = data.clients;
        this.total = data.meta.total;
        this.loading = false;
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
        this.loading = false;
      }
    },

    async changeClientStatus(id) {
      try {
        await axios.patch("/api/clients/changeStatus/" + id);
        this.fetchClients();
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
      }
    },
  },

  mounted() {
    this.fetchClients();
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
      <v-col cols="8">
        <h2>Clients</h2>
      </v-col>
    </v-row>
    <v-row>
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :headers="headers"
        :items-length="total"
        :items="clients"
        :loading="loading"
        :search="searchQuery"
        class="elevation-1"
        @update:options="fetchClients"
      >
        <template v-slot:top>
          <v-row>
            <v-col>
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
        <template #item.name="{ item }">
          <span>{{ item.raw.firstname + " " + item.raw.lastname }}</span>
        </template>
        <template #item.actions="{ item }">
          <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <v-btn
                v-bind="props"
                v-if="item.raw.status"
                text
                small
                variant="text"
                color="success"
                icon="mdi-check"
                @click="changeClientStatus(item.raw.id)"
              >
              </v-btn>

              <v-btn
                v-bind="props"
                v-else
                text
                small
                variant="text"
                color="error"
                icon="mdi-cancel"
                @click="changeClientStatus(item.raw.id)"
              >
              </v-btn
            ></template>
            <span v-if="item.raw.status"> Ban</span>
            <span v-else> Unban</span>
          </v-tooltip>
        </template>
      </v-data-table-server>
    </v-row>
  </div>
</template>

 <style>
.container {
  padding: 20px;
}
</style>

