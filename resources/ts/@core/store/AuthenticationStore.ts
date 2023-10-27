import axios from "axios";
import { defineStore } from "pinia";
import type {
    AuthenticationData,
    ChangePasswordData,
    ChangeUserData,
} from "@core/types/Authentication.type";
let token;
if (localStorage.getItem("authUser")) {
    token = JSON.parse(localStorage.getItem("authUser") ?? "").token;
} else {
    token = "";
}

const headers = {
    Authorization: `Bearer ${token}`,
};

export const useAuthenticationStore = defineStore("AuthenticationStore", {
    actions: {
        login(data: AuthenticationData) {
            return axios.post("/api/auth/login", data);
        },

        logout() {
            return axios.post("/api/auth/logout", { headers });
        },

        changePassword(data: ChangePasswordData) {
            const formData = new FormData();
            formData.append("current_password", data.current_password);
            formData.append("password", data.password);
            formData.append(
                "password_confirmation",
                data.password_confirmation
            );
            return axios.post("/api/auth/changePassword", formData, {
                headers,
            });
        },

        changeUserData(data: ChangeUserData) {
            const formData = new FormData();
            formData.append("firstname", data.firstname);
            formData.append("lastname", data.lastname);
            formData.append("email", data.email);

            return axios.post("/api/auth/changeUserData", formData, {
                headers,
            });
        },

        getCurrentUser() {
            return axios.get("/api/auth/currentUser", { headers });
        },
    },
});
