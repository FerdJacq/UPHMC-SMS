<template>
    <v-form ref="form" as="form">
        <alert-errors :form="context" message="There were some problems with your input." />
        <app-wizard
            :states="states"
            :context="context"
            @onComplete="onComplete"
            completeText="CLOSE"
            @onNext="onNext"
            @onPrev="onPrev"
            @onMove="onMove"
            :clickable_step="true"
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
    
    import List from './steps/list';
    import Calendar from './steps/calendar';


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
                    data: !this.item ? "" : this.item
                }),
                states: [
                    // {
                    //     title : 'List',
                    //     view: markRaw(List)
                    //     // guard: !0 /* Uncomment this if step has validation [for next button to be disabled]*/
                    // },
                    {
                        title : 'List',
                        view: markRaw(List)
                        // guard: !0 /* Uncomment this if step has validation [for next button to be disabled]*/
                    },
                    {
                        title : 'Calendar',
                        view: markRaw(Calendar)
                        // guard: !0 /* Uncomment this if step has validation [for next button to be disabled]*/
                    },
                ]
            }
        },

        mounted() {

        },

        methods: {
            /* Sample code for custom next/prev */
            onNext() {
                this.context.step_no++;
            },

            onPrev() {
                this.context.step_no--;
            },

            onMove(index) {
                this.context.step_no = index;
            },

            onComplete(ctx) {
                this.$emit('hideModal');
            },
        },

        computed: {
            
        },

        watch: {
            item: {
                handler(val){
                    this.data = val;
                    console.log(val)
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>