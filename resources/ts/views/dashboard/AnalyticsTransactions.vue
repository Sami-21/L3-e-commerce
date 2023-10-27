<script setup lang="ts">
import axios from "axios";

const { token } = JSON.parse(localStorage.getItem("authUser") ?? "");

const snackbar = ref({
  isVisible: false,
  message: "",
  color: "",
});

const statistics = ref<any[]>([]);

const fetchStats = async () => {
  const headers = {
    Authorization: `Bearer ${token}`,
  };
  try {
    const { data } = await axios.get("/api/dashboard", { headers });
    statistics.value = [
      {
        title: "Visitors",
        stats: data.visitors,
        icon: "mdi-trending-up",
        color: "primary",
      },
      {
        title: "Clients",
        stats: data.clients,
        icon: "mdi-account-outline",
        color: "success",
      },
      {
        title: "Products",
        stats: data.products,
        icon: "mdi-cellphone-link",
        color: "warning",
      },

      {
        title: "Orders",
        stats: data.orders,
        icon: "mdi-package",
        color: "secondary",
      },
      {
        title: "Revenue",
        stats: data.revenue + " DZD",
        icon: "mdi-currency-usd",
        color: "info",
      },
    ];
  } catch (error: any) {
    const { message } = error.response.data;

    snackbar.value.isVisible = true;
    snackbar.value.message = message;
    snackbar.value.color = "error";
  }
};

onMounted(fetchStats);
</script>

<template>
  <VCard>
    <v-snackbar
      v-model="snackbar.isVisible"
      :color="snackbar.color"
      :timeout="2000"
    >
      {{ snackbar.message }}
    </v-snackbar>
    <VCardItem>
      <VCardTitle>Statistics</VCardTitle>
    </VCardItem>

    <VCardText>
      <VRow>
        <VCol v-for="item in statistics" :key="item.title" cols="6" sm="3">
          <div class="d-flex align-center">
            <div class="me-3">
              <VAvatar
                :color="item.color"
                rounded
                size="42"
                class="elevation-1"
              >
                <VIcon size="24" :icon="item.icon" />
              </VAvatar>
            </div>

            <div class="d-flex flex-column">
              <span class="text-caption">
                {{ item.title }}
              </span>
              <span class="text-h6">{{ item.stats }}</span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>
