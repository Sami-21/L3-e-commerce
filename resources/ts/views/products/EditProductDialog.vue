<script>
import axios from "axios";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/pagination";
import { Pagination } from "swiper/modules";

export default {
  components: {
    Swiper,
    SwiperSlide,
  },

  props: {
    categories: {
      type: Array,
      required: true,
    },
    product: {
      type: Object,
      required: true,
    },
    isOpen: {
      type: Boolean,
      required: true,
    },
  },

  setup() {
    return {
      modules: [Pagination],
    };
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
        ...this.product,
        features: JSON.parse(this.product.features).map((feature) => {
          return { value: feature };
        }),
        deleted_images: [],
      },
      selectedImages: [],
      isDialogVisible: this.isOpen,
      promotionAvailable: this.product.promotion_price != 0,
    };
  },

  watch: {
    "newProduct.images": function (newVal, oldVal) {
      this.newProduct.deleted_images = this.product.images
        .filter((image) => {
          return !this.newProduct.images.includes(image);
        })
        .map((image) => {
          return image.id;
        });
    },
  },

  methods: {
    async updateProduct() {
      try {
        const formData = new FormData();
        formData.append("name", this.newProduct.name);
        formData.append("price", this.newProduct.price);
        if (this.promotionAvailable)
          formData.append("promotion_price", this.newProduct.promotion_price);
        else formData.append("promotion_price", 0);
        formData.append("category_id", this.newProduct.category.id);
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
        this.newProduct.deleted_images.forEach((image) => {
          formData.append("deleted_images[]", image);
        });

        const { data } = await axios.post(
          `/api/products/update/${this.product.id}`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
        this.$emit("edit-product", {
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
          await this.updateProduct();
          this.closeDialog();
        } else {
        }
      } catch (error) {
        console.log(error);
      }
    },

    closeDialog() {
      this.$emit("update:isOpen", false);
    },

    addFeature() {
      this.newProduct.features.push({
        value: null,
      });
    },

    removeFeature(index) {
      this.newProduct.features.splice(index, 1);
    },

    handleFileSelection(event) {
      this.selectedImages = [];
      const selectedFiles = event.target.files;

      for (let i = 0; i < selectedFiles.length; i++) {
        const file = selectedFiles[i];
        const imageURL = URL.createObjectURL(file);

        // Push an object representing the selected image to the array
        this.selectedImages.push({
          id: i,
          url: imageURL,
        });
      }
    },
  },
};
</script>
<template>
  <v-row justify="center">
    <v-dialog v-model="isDialogVisible" persistent width="1024">
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
                    v-model="newProduct.promotion_price"
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
                    v-model="newProduct.category.id"
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
                  chips
                  accept="image/png,jpg,gif,jpeg"
                  multiple
                  @input="($event) => handleFileSelection($event)"
                ></v-file-input>
              </v-row>
              <v-row class="images-slider mt-10">
                <swiper
                  v-if="
                    newProduct.images.length > 0 && selectedImages.length == 0
                  "
                  class="mySwiper"
                  :pagination="true"
                  :modules="modules"
                >
                  <swiper-slide
                    class="image-container"
                    v-for="(image, index) in newProduct.images"
                    :key="index"
                  >
                    <img :src="image.path" :alt="`Image ${index + 1}`" />
                  </swiper-slide>
                </swiper>
                <swiper
                  v-if="selectedImages.length > 0"
                  class="mySwiper"
                  :pagination="true"
                  :modules="modules"
                >
                  <swiper-slide
                    class="image-container"
                    v-for="(image, index) in selectedImages"
                    :key="index"
                  >
                    <img :src="image.url" :alt="`Image ${index + 1}`" />
                  </swiper-slide>
                </swiper>
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
.images-slider {
  display: flex;
  gap: 20px;
}
.images-slider .image-container {
  display: flex;
  align-items: center;
  justify-content: center;
}
.images-slider .image-container img {
  width: calc(50% - 10px);
  height: 250px;
  object-fit: contain;
}
</style>