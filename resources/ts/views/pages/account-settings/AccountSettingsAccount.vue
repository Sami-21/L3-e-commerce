<script lang="ts" setup>
import { useAuthenticationStore } from "@/@core/store/AuthenticationStore";
const AuthenticationStore = useAuthenticationStore();

const authUserData = JSON.parse(localStorage.getItem("authUser") ?? "");
const accountData = ref<any>({
  firstname: authUserData.user.firstname,
  lastname: authUserData.user.lastname,
  email: authUserData.user.email,
});

const formRef = ref("");

const snackbar = ref({
  isVisible: false,
  message: "",
  color: "",
});

const requiredRule = (value: string) => !!value || "Field is required";
const emailRule = (v: string) =>
  !v ||
  /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
  "E-mail must be valid";

const changeUserData = () => {
  formRef.value?.validate().then(({ valid }) => {
    if (valid) {
      AuthenticationStore.changeUserData(accountData.value)
        .then((response) => {
          const { message } = response.data;
          const newUserdata = JSON.stringify({
            token: authUserData.token,
            user: response.data.user,
          });
          localStorage.setItem("authUser", newUserdata);
          snackbar.value.isVisible = true;
          snackbar.value.message = message;
          snackbar.value.color = "success";
        })
        .catch((error: any) => {
          console.log(error);

          snackbar.value.isVisible = true;
          snackbar.value.message = error.message;
          snackbar.value.color = "error";
        });
    }
  });
};
</script>

<template>
  <VRow>
    <v-snackbar
      v-model="snackbar.isVisible"
      :color="snackbar.color"
      :timeout="2000"
    >
      {{ snackbar.message }}
    </v-snackbar>
    <VCol cols="12">
      <VCard title="Account Details">
        <VCardText>
          <!-- 👉 Form -->
          <VForm ref="formRef" class="mt-6" @submit.prevent="changeUserData">
            <VRow>
              <!-- 👉 First Name -->
              <VCol md="6" cols="12">
                <VTextField
                  v-model="accountData.firstname"
                  :rules="[requiredRule]"
                  label="First Name"
                />
              </VCol>

              <!-- 👉 Last Name -->
              <VCol md="6" cols="12">
                <VTextField
                  v-model="accountData.lastname"
                  :rules="[requiredRule]"
                  label="Last Name"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12" md="6">
                <VTextField
                  v-model="accountData.email"
                  :rules="[requiredRule, emailRule]"
                  label="E-mail"
                  type="email"
                />
              </VCol>

              <!-- 👉 Form Actions -->
              <VCol cols="12" class="d-flex flex-wrap gap-4">
                <VBtn type="submit">Save changes</VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
