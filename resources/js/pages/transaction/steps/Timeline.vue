<template>
    <div class="row">
        <p-timeline :value="timeline" >
            <template #opposite="slotProps">
                <small class="p-text-secondary">{{formatDateTime(slotProps.item.date)}}</small>
            </template>
            <template #content="slotProps">
                {{slotProps.item.status}}
            </template>
        </p-timeline>

       
    </div>
</template>
<script>

    export default {
        props: ['service'],

        data() {
            return {
                data: '',
                timeline:[
                    {icon:"",status:"PENDING",date:"","color":""},
                    {icon:"",status:"ONGOING",date:"","color":""},
                    {icon:"",status:"DELIVERED",date:"","color":""},
                    {icon:"",status:"COMPLETED",date:"","color":""},
                    {icon:"",status:"CANCELLED",date:"","color":""},
                    {icon:"",status:"REFUNDED",date:"","color":""},
                    {icon:"",status:"REMITTED",date:"","color":""},
                ]
            }
        },

        methods: {
            fillInfo() {
                this.data = this.service.data;
                let timeline = [];
                if(this.data){
                    timeline.push({status:"CREATED",date:this.data.created_at});

                    let statuses = ["PENDING","ONGOING","DELIVERED","COMPLETED","CANCELLED","REFUNDED","REMITTED"];
                    statuses.forEach(status => {
                        if (this.data[`${status.toLowerCase()}_date`])
                            timeline.push({status:status,date:this.data[`${status.toLowerCase()}_date`]});
                    });

                    this.timeline = timeline;
                }
            },
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

<style>
.breakdown-container{
    font-size:13px;
}

.tbl-item td{
    font-size:13px !important;
}

.custom-card{
    box-shadow: 0 2px 1px -1px rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.14), 0 1px 3px 0 rgba(0, 0, 0, 0.12) !important;
}
</style>