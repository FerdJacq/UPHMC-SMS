import * as API from "./API";

var route = 'series_collection';

export default {
    async list(payload) {
        return API.apiClient.get(`${route}/all`, {
            params: payload
        });
    },

    async get(id) {
        return API.apiClient.get(`${route}/data/${id}`);
    },

    async create (payload) {
        return API.apiClient.post(`${route}/create`, payload);
    },
};