import * as API from "./API";

var route = 'auth';

export default {
    async login(payload) {
        return API.apiClient.post(`${route}/login`,payload);
    },

    async validate(payload) {
        return API.apiClient.post(`${route}/validate`,payload);
    },

    async resend_otp(payload) {
        return API.apiClient.post(`${route}/otp/resend`,payload);
    },
};