import axios from "axios";
import { createStore } from 'vuex';

export default new createStore({
    strict: !0,

    state () {
        return {
            version : "0.1.1",
            counts: [],
            confirm_dialog: {
                header: '',
                header_icon: '',
                close_button: '',
                close_icon: ''
            }
        }
    },

    getters: {
        GET_COUNTS(state) {
            return state.counts;
        },

        GET_CONFIRM_DIALOG_CLASS(state) {
            return state.confirm_dialog;
        }
    },

    mutations: {
        SET_COUNTS(state, data) {             
            state.counts = data;
        },

        SET_CONFIRM_DIALOG_CLASS(state,type) {
            let dialog_data = {};

            if(type == "warning") {
                dialog_data = {
                    header: 'top-danger text-danger',
                    header_icon: '',
                    close_button: '',
                    close_icon: ''
                };
            }
            else {
                dialog_data = {
                    header: 'top-secondary text-secondary',
                    header_icon: '',
                    close_button: '',
                    close_icon: ''
                };
            }

            state.confirm_dialog = dialog_data;
        }
    },
    
    actions: {
        async recomputeCounts({ commit }, params) {
            await axios.get('/dashboard/counts',{ params: params }).then((response) => {
                commit('SET_COUNTS', response.data.data.counts);
            });
        },
    },  
})