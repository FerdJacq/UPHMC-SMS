<template>
    <div>
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
    </div>
    
    
    <!-- For controlling next/prev and step no -->
    <!-- 
        :customStep="!0"
        @onNext="onNext"
        @onPrev="onPrev"
    -->
</template>
<script>
    import { markRaw } from "vue";
    
    import AccountCredentials from './steps/account_credentials';
    import BasicInformation from './steps/basic_information';
    import ServiceProvider from './steps/service_provider';
    import AccountService  from '../../services/account';

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
                    uid: !this.item ? '' : this.item.uid,
                    image: !this.item ? '' : this.item.avatar,
                    username: !this.item ? '' : this.item.username,
                    password: !this.item ? '' : this.item.password,
                    first_name: !this.item ? '' : this.item.first_name,
                    middle_name: !this.item ? '' : this.item.middle_name,
                    last_name: !this.item ? '' : this.item.last_name,
                    email: !this.item ? '' : this.item.email,
                    role: !this.item ? '' : this.item.role,
                    service_provider : !this.item ? [] : this.item.service_provider,
                    region:  !this.item ? [] : this.item.region,
                }),
                states: [
                    {
                        title : 'Account',
                        view: markRaw(AccountCredentials),
                    },
                    {
                        title : 'Information',
                        view: markRaw(BasicInformation),
                    }
                ]
            }
        },

        mounted() {
            this.changeTab();
        },

        methods: {
            /* Sample code for custom next/prev */
            onNext() {
                if (this.context.loading) return;
                this.context.loading = true;
                AccountService.validate(this.context)
                .then((response) => {
                    this.context.step_no++;
                })
                .catch((errors) => {
                    try { 
                        this.$refs.form.setErrors(this.getError(errors));
                        // this.context.errors.set(this.getError(errors));
                    }
                    catch(ex){ console.log(ex)}
                })
                .finally(() => {
                    this.context.loading = false;
                });
                
            },

            onPrev() {
                if (this.context.loading) return;
                this.context.step_no--;
            },

            onComplete(ctx) {
                /* Validatioin before proceeding to final step */
                // if(!ctx.service_provider.length) {
                //     this.swal("warning", "Warning!", "Please select service provider", "Okay");
                //     return;
                // }
                
                (ctx.id) ? this.updateData(ctx, ctx.id) : this.createData(ctx);
                console.log(ctx);
            },

            createData(data) {
                /* API for create */
                if (this.context.loading) return;
                this.context.loading = true;
                AccountService.create(data)
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
                /* Success hit - pass to parent to update list & hide modal */
            },

            updateData(data, id) {
                if (this.context.loading) return;
                this.context.loading = true;
                AccountService.update(data,id)
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
            },

            changeTab(){
                if (this.context.role=="DSP"){
                    this.states.push(
                        {
                            title : 'Provider',
                            view: markRaw(ServiceProvider)
                        }
                    );
                }
               
                else if(this.states.length>=3)
                {
                    this.states.splice(-1);
                }       
            }
        },

        computed: {
            /* Example for step validation guard */
            // basic_info() {
            //     return this.states[0].guard = !this.context.username || !this.context.password || !this.context.last_name || !this.context.email || !this.context.role;
            // }


        },

        watch: {
            'context.role':function(val){
                this.changeTab();
            }
           
        }
    }
</script>