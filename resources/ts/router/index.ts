import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        { path: "/", redirect: "/dashboard" },
        {
            path: "/",
            component: () => import("../layouts/default.vue"),
            children: [
                {
                    path: "dashboard",
                    component: () => import("../pages/dashboard.vue"),
                    meta: { requiredAuth: true },
                    beforeEnter: (to, from, next) => {
                        // Check if the user is authenticated
                        const isAuthenticated =
                            localStorage.getItem("authUser");

                        if (isAuthenticated) {
                            // If the user is authenticated, allow access to the route
                            next();
                        } else {
                            // If not authenticated, redirect to the login page
                            next("/login");
                        }
                    },
                },
                {
                    path: "account-settings",
                    component: () => import("../pages/account-settings.vue"),
                    meta: { requiredAuth: true },
                    beforeEnter: (to, from, next) => {
                        // Check if the user is authenticated
                        const isAuthenticated =
                            localStorage.getItem("authUser");

                        if (isAuthenticated) {
                            // If the user is authenticated, allow access to the route
                            next();
                        } else {
                            // If not authenticated, redirect to the login page
                            next("/login");
                        }
                    },
                },
                {
                    path: "clients",
                    component: () => import("../pages/clients.vue"),
                    meta: { requiredAuth: true },
                    beforeEnter: (to, from, next) => {
                        // Check if the user is authenticated
                        const isAuthenticated =
                            localStorage.getItem("authUser");

                        if (isAuthenticated) {
                            // If the user is authenticated, allow access to the route
                            next();
                        } else {
                            // If not authenticated, redirect to the login page
                            next("/login");
                        }
                    },
                },
                {
                    path: "products",
                    component: () => import("../pages/products.vue"),
                    meta: { requiredAuth: true },
                    beforeEnter: (to, from, next) => {
                        // Check if the user is authenticated
                        const isAuthenticated =
                            localStorage.getItem("authUser");

                        if (isAuthenticated) {
                            // If the user is authenticated, allow access to the route
                            next();
                        } else {
                            // If not authenticated, redirect to the login page
                            next("/login");
                        }
                    },
                },
                {
                    path: "orders",
                    component: () => import("../pages/orders.vue"),
                    meta: { requiredAuth: true },
                    beforeEnter: (to, from, next) => {
                        // Check if the user is authenticated
                        const isAuthenticated =
                            localStorage.getItem("authUser");

                        if (isAuthenticated) {
                            // If the user is authenticated, allow access to the route
                            next();
                        } else {
                            // If not authenticated, redirect to the login page
                            next("/login");
                        }
                    },
                },
            ],
        },
        {
            path: "/login", // Change the path to /login
            component: () => import("../layouts/blank.vue"),
            children: [
                {
                    path: "",
                    component: () => import("../pages/login.vue"),
                    meta: { requiredAuth: true },
                    beforeEnter: (to, from, next) => {
                        // Check if the user is authenticated
                        const isAuthenticated =
                            localStorage.getItem("authUser");

                        if (isAuthenticated) {
                            // If the user is authenticated, allow access to the route

                            next("/dashboard");
                        } else {
                            // If not authenticated, redirect to the login page
                            next();
                        }
                    },
                },
                {
                    path: "/:pathMatch(.*)*",
                    component: () => import("../pages/[...all].vue"),
                },
            ],
        },
    ],
});

export default router;
