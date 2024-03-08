<template>
    <div class="row">
        <div class="col-xs-12 col-6">
            <p-card class="shadow card custom-card" v-if="data.customer">
                <template #title>{{data.customer.first_name}} {{ data.customer.middle_name }} {{ data.customer.last_name }}</template>
                <template #subtitle>Customer</template>
                <template #content>
                    <div>Email: {{ data.customer.email }}</div>
                    <div>Mobile Number: {{ data.customer.mobile_number }}</div>
                    <div>Birthdate: {{ data.customer.birth_date }}</div>
                </template>
            </p-card>
            <p-card class="shadow card custom-card" v-else>
                <template #title>Not Available</template>
                <template #subtitle>Customer</template>
            </p-card>
        </div>

        <div class="col-xs-12 col-6">
            <p-card class="shadow card custom-card"  v-if="data.seller">
                <template #title>{{data.seller.registered_name}}</template>
                <template #subtitle>Seller</template>
                <template #content>
                    <div v-if="data.seller.region">Region: {{ data.seller.region.label }}</div>
                    <div>Business Name: {{ data.seller.business_name }}</div>
                    <div>Location: {{ data.seller.registered_address }}</div>
                    <div>Email: {{ data.seller.email }}</div>
                    <div>Contact Number: {{ data.seller.contact_number }}</div>
                    <div>TIN: {{ data.seller.tin }}</div>
                    <div> 
                    <!-- <p-button
                        class="btn btn-success w-100 mt-3"
                        label="View"
                        icon="pi pi-eye"
                        @click="viewSeller()"
                    /> -->
                    </div>
                </template>
            </p-card>
            <p-card class="shadow card custom-card" v-else>
                <template #title>Not Available</template>
                <template #subtitle>Seller</template>
            </p-card>
        </div>

        <!-- View details modal-->
    <!-- <p-dialog
      :closeOnEscape="false"
      class="seller-modal modal"
      v-model:visible="dialog.details.show"
      :style="{
        width: '100%',
        'max-width': '1125px'
      }"
      modal
    >
    <template #header>
      <div class="company-detail d-flex align-items-center placeholder-glow">
        <h4
          class="m-0"
          :class="{
            'placeholder placeholder-xl': !dialog.details.data.registered_name
          }"
        >{{ dialog.details.data.registered_name}}</h4>
      </div>
    </template>
      
      <div>
        <div class="d-flex align-items-start">
          <div class="col col-xs-12 pe-3 w-100">
            <div class="col">
             
              <div class="row">
                <div class="col-xs-12 col-6">
                  <p class="text-muted mb-0" :class="{'placeholder placeholder-md w-100': !dialog.details.data.business_name}">
                    Business Name: {{ dialog.details.data.business_name }}
                  </p>
                </div>
                <div class="col-xs-12 col-6">
                  <p class="text-muted mb-0" :class="{'placeholder w-100': !dialog.details.data.tin}">
                    TIN #: {{ dialog.details.data.tin }}
                  </p>
                </div>
                <div class="col-xs-12 col-6">
                  <p class="text-muted mb-0">
                    Email: {{ dialog.details.data.email }}
                  </p>
                </div>
                <div class="col-xs-12 col-6">
                  <p class="text-muted mb-0">
                    Contact Number: {{ dialog.details.data.contact_number }}
                  </p>
                </div>
                <div class="col-xs-12 col-12">
                  <p class="text-muted mb-0" :class="{'placeholder w-100': !dialog.details.data.registered_address}">
                    Registered Address: {{ dialog.details.data.registered_address }}
                  </p>
                </div>
              </div>

            </div>
            <StepForm v-if="dialog.details.data" @hideModal="successEvent" :item="dialog.details.data"/>
          </div>
        </div>
      </div>
    
      <template #footer>
       
      </template>
    </p-dialog> -->

       
    </div>
</template>
<script>
    // import SellerService  from '../../../services/seller';
    // import SellerForm from '../../transaction/form';
    export default {
        props: ['service'],
        // components: {SellerForm},
        data() {
            return {
                data: '',
                dialog: {
                    details: {
                        show: !1,
                        processing: !1,
                        data: {
                            service_provider: {
                            logo: '',
                            company_name: ''
                            },
                            trans_id: '',
                            reference_number: ''
                        }
                    }
                },
            }
        },

        methods: {
            fillInfo() {
                this.data = this.service.data;
                console.log(this.data)
            },
            viewSeller(){
                // SellerService.get(data.seller.id)
                // .then((res) => {
                //     let data = res.data.data;
                //     this.dialog.details.data = data;
                //     this.dialog.details.show = true;
                // })
                // .catch((errors) => {
                //     try { 
                //         this.getError(errors);
                //     }
                //     catch(ex){ console.log(ex)}
                // })
                // .finally(() => {
                //     this.table.loading = false;
                // });
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