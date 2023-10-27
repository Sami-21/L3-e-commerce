<script lang="ts" setup>
import { useAuthenticationStore } from "@/@core/store/AuthenticationStore";
const AuthenticationStore = useAuthenticationStore();

const isCurrentPasswordVisible = ref(false);
const isNewPasswordVisible = ref(false);
const isConfirmPasswordVisible = ref(false);
const currentPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");

const requiredRule = (value: string) => !!value || "Field is required";

const formRef = ref("");

const snackbar = ref({
  isVisible: false,
  message: "",
  color: "",
});

// 👉 Change password
const changePassword = async () => {
  formRef.value?.validate().then(async ({ valid }) => {
    if (valid) {
      AuthenticationStore.changePassword({
        current_password: currentPassword.value,
        password: newPassword.value,
        password_confirmation: confirmPassword.value,
      })
        .then((response) => {
          const { message } = response.data;
          snackbar.value.isVisible = true;
          snackbar.value.message = message;
          snackbar.value.color = "success";
          nextTick(() => {
            formRef.value?.reset();
            formRef.value?.resetValidation();
          });
        })
        .catch((error: any) => {
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
    <!-- SECTION: Change Password -->
    <VCol cols="12">
      <VCard title="Change Password">
        <VForm ref="formRef" @submit.prevent="changePassword">
          <VCardText>
            <!-- 👉 Current Password -->
            <VRow class="mb-3">
              <VCol cols="12" md="6">
                <!-- 👉 current password -->
                <VTextField
                  v-model="currentPassword"
                  :type="isCurrentPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="
                    isCurrentPasswordVisible
                      ? 'mdi-eye-off-outline'
                      : 'mdi-eye-outline'
                  "
                  :rules="[requiredRule]"
                  label="Current Password"
                  @click:append-inner="
                    isCurrentPasswordVisible = !isCurrentPasswordVisible
                  "
                />
              </VCol>
            </VRow>

            <!-- 👉 New Password -->
            <VRow>
              <VCol cols="12" md="6">
                <!-- 👉 new password -->
                <VTextField
                  v-model="newPassword"
                  :type="isNewPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="
                    isNewPasswordVisible
                      ? 'mdi-eye-off-outline'
                      : 'mdi-eye-outline'
                  "
                  :rules="[requiredRule]"
                  label="New Password"
                  @click:append-inner="
                    isNewPasswordVisible = !isNewPasswordVisible
                  "
                />
              </VCol>

              <VCol cols="12" md="6">
                <!-- 👉 confirm password -->
                <VTextField
                  v-model="confirmPassword"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="
                    isConfirmPasswordVisible
                      ? 'mdi-eye-off-outline'
                      : 'mdi-eye-outline'
                  "
                  :rules="[requiredRule]"
                  label="Confirm New Password"
                  @click:append-inner="
                    isConfirmPasswordVisible = !isConfirmPasswordVisible
                  "
                />
              </VCol>
            </VRow>
          </VCardText>

          <!-- 👉 Action Buttons -->
          <VCardText class="d-flex flex-wrap gap-4">
            <VBtn type="submit">Save changes</VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VCol>
  </VRow>
</template>
