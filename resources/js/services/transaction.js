import * as API from "./API";

var route = 'transaction';

export default {
    async list(payload) {
        return API.apiClient.get(`${route}/all`, {
            params: payload
        });
    },

    async get(id) {
        return API.apiClient.get(`${route}/data/${id}`);
    },

    async update(id,payload) {
        return API.apiClient.post(`${route}/data/${id}`, payload);
    },

    async seen_logs(id) {
        return API.apiClient.get(`${route}/logs/${id}`);
    },

    async generate(payload) {
        return API.apiClient.post(`${route}/generate`, payload);
    },
};