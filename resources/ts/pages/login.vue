<script setup lang="ts">
import { useTheme } from "vuetify";

import authV1MaskDark from "@images/pages/auth-v1-mask-dark.png";
import authV1MaskLight from "@images/pages/auth-v1-mask-light.png";
import authV1Tree2 from "@images/pages/auth-v1-tree-2.png";
import authV1Tree from "@images/pages/auth-v1-tree.png";
import { useAuthenticationStore } from "@core/store/AuthenticationStore";

const router = useRouter();
const AuthenticationStore = useAuthenticationStore();
const form = ref({
  email: "",
  password: "",
});
const formRef = ref();
const vuetifyTheme = useTheme();

const rules = {
  required: (value: any) => !!value || "Required.",
  email: (value: any) => {
    const pattern =
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return pattern.test(value) || "Invalid e-mail.";
  },
};

const isPasswordVisible = ref(false);
const snackbar = ref({
  isVisible: false,
  message: "",
  color: "",
});

const handleSubmit = () => {
  formRef.value?.validate().then(async ({ valid }) => {
    if (valid) {
      AuthenticationStore.login(form.value)
        .then((response) => {
          const { data } = response;
          localStorage.setItem("authUser", JSON.stringify(data));

          router.push("/dashboard");
        })
        .catch((error) => {
          const { message } = error.response.data;

          snackbar.value.isVisible = true;
          snackbar.value.message = message;
          snackbar.value.color = "error";
        });
    }
  });
};
</script>

<template>
  <div class="page-container">
    <v-snackbar
      v-model="snackbar.isVisible"
      :color="snackbar.color"
      :timeout="2000"
    >
      {{ snackbar.message }}
    </v-snackbar>
    <VCard class="pa-4 pt-7" max-width="450" max-height="450">
      <VCardItem class="justify-center">
        <VCardTitle class="font-weight-semibold text-2xl text-uppercase">
          Daradji Telecom
        </VCardTitle>
      </VCardItem>

      <VCardText class="pt-2">
        <h5 class="text-h5 font-weight-semibold mb-1">
          Welcome to Daradji Telecom!
        </h5>
      </VCardText>

      <VCardText>
        <VForm ref="formRef" @submit.prevent="handleSubmit">
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                v-model="form.email"
                label="Email"
                type="email"
                :rules="[rules.required, rules.email]"
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField
                v-model="form.password"
                label="Password"
                :rules="[rules.required]"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="
                  isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
                "
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />

              <div
                class="d-flex align-center justify-space-between flex-wrap mt-1 mb-4"
              ></div>

              <!-- login button -->
              <VBtn block type="submit"> Login </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss">
@use "@core-scss/pages/page-auth.scss";

.page-container {
  height: 100vh;
  max-width: 100%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
