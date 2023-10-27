 <script>
import { VDataTableServer } from "vuetify/labs/VDataTable";
import AddProductDialog from "@/views/products/AddProductDialog.vue";
import EditProductDialog from "@/views/products/EditProductDialog.vue";
import axios from "axios";

export default {
  components: {
    VDataTableServer,
    AddProductDialog,
    EditProductDialog,
  },
  data() {
    return {
      itemsPerPage: 10,
      page: 1,
      total: 0,
      searchQuery: "",
      products: [],
      categories: [],
      selectedCategory: "",
      loading: true,
      isEditProductDialogVisible: false,
      selectedProduct: null,
      headers: [
        { title: "Name", key: "name", align: "start", sortable: false },
        { title: "Price", key: "price", align: "start", sortable: false },
        {
          title: "category",
          key: "category.name",
          align: "start",
          sortable: false,
        },
        { title: "", key: "actions", align: "start", sortable: false },
      ],
      snackbar: {
        isVisible: false,
        message: "",
        color: "",
      },
    };
  },

  watch: {
    selectedCategory() {
      this.fetchCategories();
    },
  },

  methods: {
    async fetchCategories() {
      try {
        const { data } = await axios.get("/api/categories/all");
        this.categories = data.categories;
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
      }
    },

    async fetchProducts() {
      try {
        const { data } = await axios.get("/api/products/all", {
          params: {
            q: this.searchQuery,
            page: this.page,
            perPage: this.itemsPerPage,
            category: this.selectedCategory,
          },
        });
        this.products = data.products;
        this.total = data.meta.total;
        this.loading = false;
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
        this.loading = false;
      }
    },

    addProduct({ message, product }) {
      this.products.push(product);
      this.snackbar.isVisible = true;
      this.snackbar.message = message;
      this.snackbar.color = "success";
    },

    openEditDialog(product) {
      this.selectedProduct = product;
      this.isEditProductDialogVisible = true;
    },

    closeEditDialog() {
      this.isEditProductDialogVisible = false;
    },

    async deleteProduct(id) {
      try {
        const { data } = await axios.delete("/api/products/" + id);
        const index = this.products.findIndex((product) => {
          return product.id == id;
        });
        this.products.splice(index, 1);
        this.snackbar.isVisible = true;
        this.snackbar.message = data.message;
        this.snackbar.color = "success";
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
      }
    },

    async changeProductStatus(id) {
      try {
        await axios.patch("/api/products/changeStatus/" + id);
        this.fetchProducts();
      } catch (error) {
        this.snackbar.isVisible = true;
        this.snackbar.message = error.message;
        this.snackbar.color = "error";
      }
    },
  },

  async mounted() {
    await this.fetchCategories();
    await this.fetchProducts();
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

    <edit-product-dialog
      v-if="isEditProductDialogVisible && selectedProduct"
      v-model:isOpen="isEditProductDialogVisible"
      :categories="categories"
      :product="selectedProduct"
    />

    <v-row>
      <v-col>
        <h2>Products</h2>
      </v-col>
    </v-row>
    <v-row>
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :headers="headers"
        :items-length="total"
        :items="products"
        :loading="loading"
        :search="searchQuery"
        class="elevation-1"
        @update:options="fetchProducts"
      >
        <template v-slot:top>
          <v-row>
            <v-col cols="11">
              <v-text-field
                v-model="searchQuery"
                variant="filled"
                hide-details
                placeholder="Search "
                class="ma-2"
                density="compact"
              ></v-text-field>
            </v-col>
            <v-col class="btn-container" cols="1">
              <add-product-dialog
                :categories="categories"
                @add-product="addProduct"
              />
            </v-col>
          </v-row>
        </template>
        <template #item.price="{ item }">
          <span>{{ item.raw.price }} DZD</span>
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
                @click="changeProductStatus(item.raw.id)"
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
                @click="changeProductStatus(item.raw.id)"
              >
              </v-btn
            ></template>
            <span v-if="item.raw.status"> Ban</span>
            <span v-else> Unban</span>
          </v-tooltip>
          <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <v-btn
                v-bind="props"
                text
                small
                variant="text"
                color="primary"
                icon="mdi-edit"
                @click="openEditDialog(item.raw)"
              >
              </v-btn
            ></template>
            <span> Edit</span>
          </v-tooltip>
          <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <v-btn
                v-bind="props"
                text
                small
                variant="text"
                color="error"
                icon="mdi-delete"
                @click="deleteProduct(item.raw.id)"
              >
              </v-btn
            ></template>
            <span> Delete</span>
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
.btn-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding-left: 0;
}
</style>

