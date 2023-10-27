<script>
import axios from "axios";
export default {
  props: {
    categories: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      nameRules: [
        (value) => {
          if (value?.length > 3) return true;

          return "Product name must be at least 3 characters.";
        },
        (value) => {
          if (value) return true;

          return "Product name is required";
        },
      ],
      priceRules: [
        (value) => {
          if (value > 0) return true;

          return "Invalid price.";
        },
        (value) => {
          if (value) return true;

          return "Price is required.";
        },
      ],
      promotionPriceRules: [
        (value) => {
          if (this.promotionAvailable) {
            if (value > 0) return true;
            return "Invalid promotion price.";
          }
          return true;
        },
        (value) => {
          if (this.promotionAvailable) {
            if (value) return true;
            return "Promotion price is required.";
          }
          return true;
        },
      ],
      categoryRules: [
        (value) => {
          if (value) return true;
          return "Category is required.";
        },
      ],
      featureRules: [
        (value) => {
          if (value) return true;
          return "feature is required.";
        },
      ],
      imagesRules: [
        (value) => {
          if (value) return true;
          return "feature is required.";
        },
      ],
      newProduct: {
        name: null,
        price: null,
        promotionPrice: null,
        categoryId: null,
        features: [
          {
            value: null,
          },
        ],
        images: null,
      },
      isDialogVisible: false,
      promotionAvailable: false,
    };
  },

  methods: {
    async addProduct() {
      try {
        const formData = new FormData();
        formData.append("name", this.newProduct.name);
        formData.append("price", this.newProduct.price);
        if (this.promotionAvailable)
          formData.append("promotion_price", this.newProduct.promotionPrice);
        else formData.append("promotion_price", 0);
        formData.append("category_id", this.newProduct.categoryId);
        formData.append(
          "features",
          JSON.stringify(
            this.newProduct.features.map((feature) => {
              return feature.value;
            })
          )
        );
        this.newProduct.images.forEach((image) => {
          formData.append("images[]", image);
        });
        const { data } = await axios.post("/api/products/add", formData);
        this.$emit("add-product", {
          message: data.message,
          product: data.product,
        });
      } catch (error) {
        console.log(error);
      }
    },

    async handleSubmit() {
      try {
        const { valid } = await this.$refs.myForm.validate();
        if (valid) {
          await this.addProduct();
          this.closeDialog();
        } else {
        }
      } catch (error) {
        console.log(error);
      }
    },

    closeDialog() {
      this.isDialogVisible = false;
      nextTick(() => {
        this.$refs.myForm.reset();
      });
    },

    openDialog() {
      this.isDialogVisible = true;
    },

    addFeature() {
      this.newProduct.features.push({
        value: null,
      });
    },
    removeFeature(index) {
      this.newProduct.features.splice(index, 1);
    },
  },
};
</script>
<template>
  <v-row justify="center">
    <v-dialog v-model="isDialogVisible" persistent width="1024">
      <template v-slot:activator="{ props }">
        <v-btn @click="openDialog">
          <v-icon icon="mdi-plus" size="32"></v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-form ref="myForm" @submit.prevent="handleSubmit">
          <v-card-title>
            <span class="text-h5">New Product</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="newProduct.name"
                    label="Name"
                    name="name"
                    :rules="nameRules"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="newProduct.price"
                    label="Price"
                    name="price"
                    :rules="priceRules"
                  ></v-text-field>
                </v-col>
                <v-col cols="10">
                  <v-text-field
                    :disabled="!promotionAvailable"
                    v-model="newProduct.promotionPrice"
                    label="Promotion price"
                    name="promotion_price"
                    :rules="promotionPriceRules"
                  ></v-text-field>
                </v-col>
                <v-col cols="2">
                  <v-switch
                    v-model="promotionAvailable"
                    label="Promotion"
                  ></v-switch>
                </v-col>
                <v-col cols="12">
                  <v-select
                    item-title="name"
                    item-value="id"
                    v-model="newProduct.categoryId"
                    :items="categories"
                    label="Category"
                    name="category_id"
                    :rules="categoryRules"
                  ></v-select>
                </v-col>
              </v-row>
              <v-row
                v-for="(feature, index) in newProduct.features"
                :key="index"
              >
                <v-col cols="10">
                  <v-text-field
                    :label="'Feature ' + (index + 1)"
                    type="text"
                    name="features"
                    v-model="feature.value"
                    :rules="featureRules"
                  ></v-text-field>
                </v-col>
                <v-col
                  v-if="newProduct.features.length - 1 == index"
                  class="btn-container"
                  cols="1"
                >
                  <v-btn color="success" @click="addFeature">
                    <v-icon icon="mdi-plus"></v-icon>
                  </v-btn>
                </v-col>
                <v-col
                  v-if="newProduct.features.length > 1 || index !== 0"
                  class="btn-container"
                  cols="1"
                >
                  <v-btn color="error" @click="removeFeature(index)">
                    <v-icon icon="mdi-minus"></v-icon>
                  </v-btn>
                </v-col>
              </v-row>
              <v-row>
                <v-file-input
                  label="Select images"
                  v-model="newProduct.images"
                  :rules="imagesRules"
                  name="images"
                  multiple
                ></v-file-input>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue-darken-1" variant="text" @click="closeDialog">
              Close
            </v-btn>
            <v-btn color="blue-darken-1" variant="text" type="submit">
              Save
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </v-row>
</template>


<style>
.btn-container {
  align-items: center;
  justify-content: center;
}
</style>