import * as API from "./API";

var route = 'whitelist';

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

    async update (payload, id) {
        return API.apiClient.post(`${route}/update/${id}`, payload);
    },

    async remove (id) {
        return API.apiClient.post(`${route}/delete/${id}`);
    }
};