export type AuthenticationData = {
    email: string;
    password: string;
};

export type ChangePasswordData = {
    current_password: string;
    password: string;
    password_confirmation: string;
};

export type ChangeUserData = {
    firstname: string;
    lastname: string;
    email: string;
};
