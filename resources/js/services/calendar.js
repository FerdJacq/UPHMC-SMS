import * as API from "./API";

var route = 'calendar';

export default {
    async data(payload) {
        return API.apiClient.get(`${route}/data`, {
            params: payload
        });
    },

    async get(id) {
        return API.apiClient.get(`${route}/data/${id}`);
    },


    async export(payload) {
        return API.apiClient.get(`${route}/export`,{
            params: payload
        });
    },

    async regions(payload="") {
        return API.apiClient.get(`${route}/regions`, {
            params: payload
        });
    },
};