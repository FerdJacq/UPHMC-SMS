import * as API from "./API";

var route = 'dashboard';

export default {
    async data(payload) {
        return API.apiClient.post(`${route}/data`,payload);
    },

    async regions(payload="") {
        return API.apiClient.get(`${route}/regions`, {
            params: payload
        });
    },

    async seller(payload) {
        return API.apiClient.get(`${route}/seller`,payload);
    },
};