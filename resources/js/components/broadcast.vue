<template>
  
</template>

<script>
export default {
    filters: {
    },

    computed: {
       
    },
    data() {
        return {
            
        }
    },
    methods: {
        socket_listener()
        {
            this.$socket.client.off('connect');
            this.$socket.client.on("connect", data => {
                this.$socket.client.off('new_transaction');
                this.$socket.client.on('new_transaction', data => {
                    this.emitter.emit("new_transaction",data);
                    let message = `New transaction for ${data.dsp.company_name.toLowerCase()}`;
                    this.toast("success",message);
                    // this.$toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record approved', life: 3000 });
                });
            });
        },      
    },
    mounted(){
        this.$socket.client.close();
        this.$socket.client.open();
        this.socket_listener();
    },
    beforeDestroy()
    {
        this.$socket.client.close();
    },
}
</script>