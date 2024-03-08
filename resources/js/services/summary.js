import * as API from "./API";

var route = 'summary';

export default {
    async counts() {
        return API.apiClient.get(`${route}/counts`);
    },

};