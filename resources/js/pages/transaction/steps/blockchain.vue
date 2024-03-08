<template>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <p-card class="shadow card custom-card blockchain-container" v-if="data">
                <template #title>Blockchain</template>
                <template #content>
                    <table class="normal-border table table-hover table-centered mb-0 p-datatable-table w-100 table-sm">
                        <tr>
                            <td>
                                Transaction ID:
                                <div>{{ data.blockchain_trx_id }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Blockchain ID:
                                <div>{{ data.blockchain_block_number }}</div>
                            </td>
                        </tr>
                    </table>
                </template>
            </p-card>
        </div>
        <div class="col-xs-12 col-md-6">
            <p-card class="shadow card custom-card blockchain-container" v-if="data.blockchain">
                <template #title>Credentials</template>
                <template #content>
                    <table class="normal-border table table-hover table-centered mb-0 p-datatable-table w-100 table-sm">
                        <tr>
                            <td>
                                Username:
                                <div>{{ data.blockchain.username }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Password
                                <div class="text-wrap">{{ data.blockchain.password }}</div>
                            </td>
                        </tr>
                    </table>
                    <button class="btn btn-outline-primary w-100 explorer-url mt-2" type="button" @click="viewExplorer()" v-if="!data.blockchain.url">VIEW IN EXPLORER</button>
                    <div class="blockchain-notes">Note: Explorer is only available in desktop.</div>
                </template>
            </p-card>
        </div>
    </div>
</template>
<script>

    export default {
        props: ['service'],

        data() {
            return {
                data: '',
            }
        },

        methods: {
            fillInfo() {
                this.data = this.service.data;
            },
            viewExplorer(){
                window.open(this.data.blockchain.blockchain_url, '_blank');
            }
        },

        mounted() {
            this.fillInfo();
        },

        watch: {
            service: {
                handler(val){
                    this.data = val;
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>

<style scoped>
@media (min-width:1025px) {
    .blockchain-notes{
        display:none !important;
    }
    .explorer-url{
        display:revert !important;
    }
}

@media (min-width:320px)  {
    .blockchain-notes{
        display:block;
    }
    .explorer-url{
        display:none;
    }
}

.blockchain-container table div{
    display:block;
    font-weight: 800;
}
.blockchain-notes{
    color:red;
    font-size: 13px;
    font-style: italic;
    font-weight: 800;
}
</style>