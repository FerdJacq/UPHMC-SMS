import * as API from "./API";

var route = 'seller';

export default {
    async list(payload) {
        return API.apiClient.get(`${route}/all`, {
            params: payload
        });
    },

    async transactions(payload) {
        return API.apiClient.get(`${route}/transactions`, {
            params: payload
        });
    },

    async get(id) {
        return API.apiClient.get(`${route}/data/${id}`);
    },

    async generate(payload) {
        return API.apiClient.post(`${route}/generate`, payload);
    },

    async generate_summary_report(payload) {
        return API.apiClient.post(`${route}/generate/summary`, payload);
    },

    async update_profile(payload){
        return API.apiClient.post(`${route}_settings/update`, payload);
    },

    async upload (payload,headers="") { //upload file
        return (headers) ? API.apiClient.post(`${route}_settings/file/upload`, payload,headers) : API.apiClient.post(`${route}_settings/file/upload`, payload);
    },

    async file_list(payload) {
        return API.apiClient.get(`${route}_settings/file/all`, {
            params: payload
        });
    },

    async file_download(id) {
        return API.apiClient.get(`${route}_settings/file/${id}`);
    },

    async file_delete(id) {
        return API.apiClient.post(`${route}_settings/file/delete/${id}`);
    },
};