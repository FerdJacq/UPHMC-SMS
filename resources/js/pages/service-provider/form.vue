<template>
    <v-form ref="form" as="form">
        <alert-errors :form="context" message="There were some problems with your input." />
        <app-wizard
            :states="states"
            :context="context"
            @onComplete="onComplete"
            completeText="Save"
            @onNext="onNext"
            @onPrev="onPrev"
            :customStep="true"
        />
    </v-form>
    
    <!-- For controlling next/prev and step no -->
    <!-- 
        :customStep="!0"
        @onNext="onNext"
        @onPrev="onPrev"
    -->
</template>
<script>
    import { markRaw } from "vue";
    
    import BasicInformation from './steps/basic_information';
    import ServiceCharge from './steps/service_charge';
    import DSPService  from '../../services/service_provider';

    export default {
        emits: ['hideModal'],
        props: ['item'],

        data() {
            return {
                context: new Form({
                    loading:false,
                    id: !this.item ? 0 : this.item.id,
                    step_no: 0,
                    disable_btn: !1, /* Set to (true) For manual disable next/done button */
                    
                    /* Context */
                    image: !this.item ? '' : this.item.logo,
                    prefix: !this.item ? '' : this.item.prefix,
                    company_code: !this.item ? '' : this.item.company_code,
                    company_name: !this.item ? '' : this.item.company_name,
                    email: !this.item ? '' : this.item.email,
                    category: !this.item ? '' : this.item.category,
                    status: !this.item ? '' : this.item.status,
                    fees : !this.item ? [] : this.item.fees
                }),
                states: [
                    {
                        title : 'Information',
                        view: markRaw(BasicInformation)
                        // guard: !0 /* Uncomment this if step has validation [for next button to be disabled]*/
                    },
                    {
                        title : 'Charges',
                        view: markRaw(ServiceCharge)
                    }
                ]
            }
        },

        mounted() {

        },

        methods: {
            /* Sample code for custom next/prev */
            onNext() {
                if (this.context.loading) return;
                this.context.loading = true;
                DSPService.validate(this.context)
                .then((response) => {
                    this.context.step_no++;
                })
                .catch((errors) => {
                    try { 
                        this.$refs.form.setErrors(this.getError(errors));
                    }
                    catch(ex){ console.log(ex)}
                })
                .finally(() => {
                    this.context.loading = false;
                });
            },

            onPrev() {
                this.context.step_no--;
            },

            onComplete(ctx) {
                /* Validatioin before proceeding to final step */
                // if(!ctx.service_provider.length) {
                //     this.swal("warning", "Warning!", "Please select service provider", "Okay");
                //     return;
                // }
                
                (ctx.id) ? this.updateData(ctx, ctx.id) : this.createData(ctx);
            },

            createData(data) {
                if (this.context.loading) return;
                this.context.loading = true;
                DSPService.create(data)
                .then((response) => {
                    this.swalMessage("success",response.data.message,"Okay",false,false,false);
                    this.$emit('hideModal');
                })
                .catch((errors) => {
                    try { this.$refs.form.setErrors(this.getError(errors)); }
                    catch(ex){ console.log(ex) }
                })
                .finally(() => {
                    this.context.loading = false;
                });
            },

            updateData(data, id) {
                if (this.context.loading) return;
                this.context.loading = true;
                DSPService.update(data,id)
                .then((response) => {
                    this.swalMessage("success",response.data.message,"Okay",false,false,false);
                    this.$emit('hideModal');
                })
                .catch((errors) => {
                    try { 
                        this.$refs.form.setErrors(this.getError(errors));
                    }
                    catch(ex){ console.log(ex)}
                })
                .finally(() => {
                    this.context.loading = false;
                });
            }
        },

        computed: {
            /* Example for step validation guard */
            // basic_info() {
            //     return this.states[0].guard = !this.context.username || !this.context.password || !this.context.last_name || !this.context.email || !this.context.role;
            // }
        },

        watch: {
            /* Example for step validation guard */
            // basic_info(e) {
            //     this.states[0].guard = e;
            // }
        }
    }
</script>