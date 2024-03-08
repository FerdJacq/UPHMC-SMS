import axios from "axios";

export const apiClient = axios.create({
    baseURL: "/",
    // withCredentials: true,
});

// apiClient.interceptors.request.use(request => {
//     request.headers.common['Accept'] = 'application/json';
//     request.headers.common['Content-Type'] = 'application/json';
//     return request;
// });