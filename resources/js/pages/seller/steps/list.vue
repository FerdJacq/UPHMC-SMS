<template>
    <div class="row">
      <div class="col-lg-12">
          <div class="d-flex flex-wrap align-items-end mb-2">
            <div class="col-12 col-md-7 px-0">
                <p-button
                class="btn btn-success"
                label="Generate report"
                icon="pi pi-file-excel"
                @click="generate"
                />
                <div class="mt-4">
                    <div class="input-group flex-nowrap flex-xs-wrap">
                        <span class="p-float-label">
                              <p-dropdown
                                v-model="table.selected_date_type"
                                inputId="date_type"
                                :options="table.date_types"
                                optionLabel="text"
                                optionValue="text"
                                class="w-full"
                              />
                              <label for="status">Status</label>
                        </span>
                        <div class="p-float-label p-input-icon-right">
                            <p-datepicker
                            inputId="start_date"
                            class="h-100"
                            inputClass="form-control shadow-none"
                            v-model="table.params.start_date"
                            :maxDate="_max_date"
                            />
                            <i class="pi pi-calendar"></i>
                            <label for="start_date">{{ date_label }} Start date</label>
                        </div>
                        <span class="input-group-text">to</span>
                        <div class="p-float-label p-input-icon-right">
                            <p-datepicker
                            inputId="end_date"
                            class="h-100"
                            inputClass="form-control shadow-none"
                            v-model="table.params.end_date"
                            :maxDate="_max_date"
                            />
                            <i class="pi pi-calendar"></i>
                            <label for="end_date">{{ date_label }} End date</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 ms-auto px-0">
                <div class="input-group flex-nowrap w-full flex-xs-wrap">
                <span class="p-float-label p-input-icon-right">
                    <i class="pi pi-search" />
                    <p-input-text
                      id="search"
                      type="text"
                      v-model="table.params.search"
                      class="form-control shadow-none"
                    />
                    <label for="search">Search</label>
                </span>
                <span class="p-float-label">
                    <p-multiselect
                    class="mn-175 mx-175 h-100"
                    inputId="company_name"
                    :options="table.dsp_list"
                    optionLabel="company_name"
                    optionValue="id"
                    display="chip"
                    v-model="table.params.selected_dsp"
                    inputClass="form-control shadow-none"
                    />
                    <label for="company_name">Service Provider</label>
                </span>
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-4">
              <ul class="list-group">
                  <li v-for="(item, index) in table.summary" :key="index" class="list-group-item d-flex justify-content-between align-items-center pointer">
                    <div class="d-flex align-items-center">
                      <div class="avatar-sm me-2">
                        <img :src="item.logo" class="img-fluid rounded-circle"/>
                      </div>
                      {{item.company_name}}
                    </div>
                    <span class="text-bold" >
                      {{ formatNumber(item.base_price,2) }}
                    </span>
                  </li>
              </ul>
            </div>
            <!-- <div class="col-12 col-md-4">
              <ul class="list-group">
                  <li class="list-group-item d-flex align-items-center py-1">
                      <i class="fe-shopping-cart me-1"></i> Total Amount
                      <span class="text-bold ms-auto">{{ formatNumber(table.summary.total_amount) }}</span>
                  </li>
                  <li class="list-group-item d-flex align-items-center py-1">
                      <i class="fe-activity me-1"></i> 
                      Gross Sales
                      <span class="text-bold ms-auto">{{ formatNumber(table.summary.base_price) }}</span>
                  </li>
              </ul>
              <ul class="list-group mt-3">
                  <li class="list-group-item d-flex align-items-center py-1">
                      <i class="fe-shuffle me-1"></i> Transaction Fee
                      <span class="text-bold ms-auto">{{ formatNumber(table.summary.transaction_fee) }}</span>
                  </li>
                  <li class="list-group-item d-flex align-items-center py-1">
                      <i class="fe-percent me-1"></i> Commission Fee
                      <span class="text-bold ms-auto">{{ formatNumber(table.summary.commission_fee) }}</span>
                  </li>
                  <li class="list-group-item d-flex align-items-center py-1">
                      <i class="fe-globe me-1"></i> Service Fee
                      <span class="text-bold ms-auto">{{ formatNumber(table.summary.service_fee) }}</span>
                  </li>
              </ul>
              <ul class="list-group mt-3">
                <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-cloud me-1"></i> Online Platform VAT
                        <span class="text-bold ms-auto">{{ formatNumber(table.summary.online_platform_vat) }}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-truck me-1"></i> Shipping VAT
                        <span class="text-bold ms-auto">{{ formatNumber(table.summary.shipping_vat) }}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-package me-1"></i> Item VAT
                        <span class="text-bold ms-auto">{{ formatNumber(table.summary.item_vat) }}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-shuffle me-1"></i> Total VAT
                        <span class="text-bold ms-auto">{{ formatNumber(Number(table.summary.online_platform_vat) + Number(table.summary.shipping_vat) + Number(table.summary.item_vat)) }}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-percent me-1"></i> Withholding Tax(1%)
                        <span class="text-bold ms-auto">{{ formatNumber(table.summary.withholding_tax) }}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-globe me-1"></i> Total Taxes Due
                        <span class="text-bold ms-auto">{{ formatNumber(table.summary.tax) }}</span>
                    </li>
              </ul>
            </div> -->
            <div class="col-12 col-md-8">
              <div class="table-responsive mb-3">
                <p-table
                    tableClass="table"
                    :rowHover="true"
                    :loading="table.loading"
                    :value="table.data"
                    showGridlines
                    responsiveLayout="scroll"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
                >
                    <template #empty>No records found.</template>
                    <template #loading>Loading data. Please wait.</template>
                    <p-column>
                        <template #body="{data}">
                        <p-button
                            class="btn btn-sm btn-info"
                            icon="pi pi-folder-open"
                            @click="viewData(data)"
                        />
                        </template>
                    </p-column>
                    <p-column field="id" header="No."></p-column>
                    <p-column header="Service Provider">
                        <template #body="{data}">
                        {{ data.service_provider.company_name }}
                        </template>
                    </p-column>
                    <p-column field="trans_id" header="Transaction ID"></p-column>
                    <p-column header="Total amount">
                        <template #body="{data}">
                        {{ formatNumber(data.total_amount) }}
                        </template>
                    </p-column>
                    <!-- <p-column header="Sales(Before VAT)" v-if="data.type=='V'">
                        <template #body="{data}">
                        {{ formatNumber(data.base_price) }}
                        </template>
                    </p-column> -->
                    <p-column header="Gross Sales">
                        <template #body="{data}">
                        {{ formatNumber(data.base_price) }}
                        </template>
                    </p-column>
                    <p-column header="Online Platform VAT">
                        <template #body="{data}">
                        {{ formatNumber(data.online_platform_vat) }}
                        </template>
                    </p-column>
                    <p-column header="Shipping VAT">
                        <template #body="{data}">
                        {{ formatNumber(data.shipping_vat) }}
                        </template>
                    </p-column>
                    <p-column header="Item VAT">
                        <template #body="{data}">
                        {{ formatNumber(data.item_vat) }}
                        </template>
                    </p-column>
                    <p-column header="Withholding Tax(1%)">
                        <template #body="{data}">
                        {{ formatNumber(data.withholding_tax) }}
                        </template>
                    </p-column>
                    <p-column header="Total Taxes Due" >
                        <template #body="{data}">
                        {{ formatNumber(data.tax) }}
                        </template>
                    </p-column>
                    <p-column header="Status">
                        <template #body="{data}">
                        <span
                            class="badge"
                            :class="statusClass(data.status, 'badge-outline')"
                        >
                            {{ data.status }}
                        </span>
                        </template>
                    </p-column>
                    <p-column header="Created date" >
                        <template #body="{data}">
                        {{ formatDateTime(data.created_at) }}
                        </template>
                    </p-column>
                    <p-column header="Pending date" v-if="['','PENDING'].includes(table.params.status)">
                        <template #body="{data}">
                        {{ formatDateTime(data.pending_date) }}
                        </template>
                    </p-column>
                    <p-column header="Ongoing date" v-if="['','ONGOING'].includes(table.params.status)">
                        <template #body="{data}">
                        {{ formatDateTime(data.ongoing_date) }}
                        </template>
                    </p-column>
                    <p-column header="Delivery date" v-if="['','DELIVERED'].includes(table.params.status)">
                        <template #body="{data}">
                        {{ formatDateTime(data.delivered_date) }}
                        </template>
                    </p-column>
                    <p-column header="Completed date" v-if="['','COMPLETED'].includes(table.params.status)">
                        <template #body="{data}">
                        {{ formatDateTime(data.completed_date) }}
                        </template>
                    </p-column>
                    <p-column header="Cancel date" v-if="['','CANCELLED'].includes(table.params.status)">
                        <template #body="{data}">
                        {{ formatDateTime(data.cancelled_date) }}
                        </template>
                    </p-column>
                    <p-column header="Refund date" v-if="['','REFUNDED'].includes(table.params.status)">
                        <template #body="{data}">
                        {{ formatDateTime(data.refunded_date) }}
                        </template>
                    </p-column>
                    
                    <p-column header="Remitted date">
                        <template #body="{data}">
                        {{ (data.status=="COMPLETED") ? formatDateTime(data.remitted_date) : "" }}
                        </template>
                    </p-column>
                </p-table>
                <p-paginator :rows="table.params.page_size" :totalRecords="table.total" @page="onPage($event)"></p-paginator>
              </div>
            </div>
          </div>
      </div> <!-- end col -->

        <!-- Generate Excel Modal -->
      <p-dialog
        class="generate-excel-modal modal"
        v-model:visible="dialog.generate.show"
        :style="{
          width: '100%',
          'max-width': '500px'
        }"
        modal
      >
        <template #header>
          <h3 v-text="dialog.generate.title"></h3>
        </template>
        
        <v-form ref="form" tag="form"  v-show="!dialog.generate.processing">
          <v-field as="div" class="field" slim name="company_name" vid="company_name" v-slot="{ errors }">
            <span class="p-float-label">
              <p-multiselect
                appendTo="body"
                inputId="dsp"
                :options="table.dsp_list"
                optionLabel="company_name"
                optionValue="id"
                v-model="dialog.generate.data.selected_dsp"
                :class="{ 'p-invalid': errors[0] }"
                class="w-full"
              />
              <label for="dsp">Service Provider</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
          </v-field>
          <v-field as="div" class="field" slim name="selected_date_type" vid="selected_date_type" v-slot="{ errors }">
            <span class="p-float-label">
              <p-dropdown
                appendTo="body"
                v-model="dialog.generate.selected_date_type"
                inputId="date_status"
                :options="table.date_types"
                optionLabel="text"
                optionValue="text"
                class="w-full"
                :inputClass="{
                  'p-invalid': errors[0]
                }"
              />
              <label for="date_status">Date status</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
          </v-field>
          <v-field as="div" class="field" slim name="start_date" vid="start_date" v-slot="{ errors }">
            <div class="p-float-label p-input-icon-right">
              <p-datepicker
                appendTo="body"
                inputId="generate_start_date"
                v-model="dialog.generate.data.start_date"
                :maxDate="_max_date"
                class="w-100"
                :inputClass="{
                  'form-control shadow-none': !0,
                  'p-invalid': errors[0]
                }"
              />
              <i class="pi pi-calendar"></i>
              <label for="generate_start_date">{{ generate_date_label }} Start date</label>
            </div>
            <small class="p-error">{{ errors[0] }}</small>
          </v-field>
          <v-field as="div" class="field" slim name="end_date" vid="end_date" v-slot="{ errors }">
            <div class="p-float-label p-input-icon-right">
              <p-datepicker
                appendTo="body"
                inputId="generate_end_date"
                v-model="dialog.generate.data.end_date"
                :maxDate="_max_date"
                class="w-100"
                :inputClass="{
                  'form-control shadow-none': !0,
                  'p-invalid': errors[0]
                }"
              />
              <i class="pi pi-calendar"></i>
              <label for="generate_end_date">{{ generate_date_label }} End date</label>
            </div>
            <small class="p-error">{{ errors[0] }}</small>
          </v-field>
        </v-form>

        <Loader
          v-if="dialog.generate.processing"
          title="Hello"
          message="Getting your data and generating an excel file."
        >
        </Loader>
      
        <template #footer>
          <p-button
            class="btn btn-outline-light"
            label="Cancel"
            :disabled="dialog.generate.processing"
            @click="dialog.generate.show=false"
          />
          <p-button
            class="btn btn-primary mx-0"
            label="Generate"
            :disabled="dialog.generate.processing"
            @click="generate_report"
          />
        </template>
      </p-dialog>

      <!-- View details modal-->

      <p-dialog
        class="provider-modal modal"
        v-model:visible="dialog.details.show"
        :style="{
          width: '100%',
          'max-width': '1125px'
        }"
        modal
      >
      <template #header>
        <div class="company-detail d-flex align-items-center placeholder-glow">
          <div class="avatar-md me-2">
            <img :src="dialog.details.data.service_provider.logo" class="img-fluid rounded-circle" v-if="dialog.details.data.service_provider.logo" />
            <span class="placeholder avatar-title bg-secondary text-secondary font-20 rounded-circle" v-else></span>
          </div>
          <h4
            class="m-0"
            :class="{
              'placeholder placeholder-xl': !dialog.details.data.service_provider.company_name
            }"
          >{{ dialog.details.data.service_provider.company_name}}</h4>
        </div>
      </template>
        
        <div>
          <div class="d-flex align-items-start">
            <div class="col col-xs-12 pe-3">
              <div class="col">
              
                <div class="row">
                  <div class="col-xs-12 col-6">
                    <h2 class="m-0 mb-1" :class="{'placeholder placeholder-md w-100': !dialog.details.data.trans_id}">Trx #: {{ dialog.details.data.trans_id }}</h2>
                  </div>
                  <div class="col-xs-12 col-6">
                    <span
                      class="badge" :class="statusClass(dialog.details.data.status, 'badge-outline')">
                      {{ dialog.details.data.status }}
                    </span>
                  </div>
                  <div class="col-xs-12 col-6">
                    <p class="text-muted mb-0 d-flex align-items-center" :class="{'placeholder w-100': !dialog.details.data.trans_id}">
                      Ref. #: {{ dialog.details.data.reference_number }}
                      <span class="label-type">({{ (dialog.details.data.type=="PRODUCT") ? "GOODS" : dialog.details.data.type }})</span>
                    </p>
                  </div>
                  <div class="col-xs-12 col-6">
                    <p class="text-muted mb-0" :class="{'placeholder w-100': !dialog.details.data.or_number}">
                      OR#: {{ dialog.details.data.or_number }}
                    </p>
                  </div>
                </div>
              </div>
              <!-- {{ dialog.details.data }} -->
              <StepForm v-if="dialog.details.data" @hideModal="successEvent" :item="dialog.details.data"/>
            </div>
          </div>
        </div>
      
        <template #footer></template>
      </p-dialog>


  </div>
</template>
<script>
import StepForm from '../../transaction/form';
import SellerService  from '../../../services/seller';
import DSPService  from '../../../services/service_provider';
import TransactionService  from '../../../services/transaction';
export default {
    props: ['service'],
    components: {StepForm},
    data() {
        return {
            table:{
                params: {
                    page_size:6,
                    seller_id:null,
                    search:null,
                    page:null,
                    selected_date_type:"",
                    selected_type:0,
                    dsp_list:[],
                    selected_dsp:[],
                    start_date:new Date(),
                    end_date:new Date()
                },
                selected_date_type:"Completed",
                loading: false,
                data: [],
                date_types: [
                    { text:"ALL", date_type:"created_at", status:[], date_label:"Created", selected_type:3},
                    { text:"Pending", date_type:"pending_date", status:[], date_label:"Pending", selected_type:3},
                    { text:"Ongoing", date_type:"ongoing_date", status:[], date_label:"Ongoing", selected_type:3},
                    { text:"Delivered", date_type:"delivered_date", status:[], date_label:"Delivered", selected_type:3},
                    { text:"Completed", date_type:"completed_date", status:[], date_label:"Completed", selected_type:3},
                    { text:"Cancelled", date_type:"cancelled_date", status:[], date_label:"Cancelled", selected_type:3},
                    { text:"Refunded", date_type:"refunded_date", status:[], date_label:"Refunded", selected_type:3},
                    { text:"Remitted", date_type:"remitted_date", status:["COMPLETED"], date_label:"Remitted", selected_type:1},
                    { text:"Unremitted", date_type:"completed_date", status:["COMPLETED"], date_label:"Completed", selected_type:2}
                ],
                summary:{
                    "base_price": "0",
                    "transaction_fee": "0",
                    "service_fee": "0",
                    "commission_fee": "0",
                    "online_platform_vat": "0",
                    "shipping_vat": "0",
                    "item_vat": "0",
                    "withholding_tax": "0",
                    "total_amount": "0",
                    "tax": "0",
                    "pending": "0",
                    "ongoing": "0",
                    "delivered": "0",
                    "completed": "0",
                    "cancelled": "0",
                    "refunded": "0"
                },
                total: 0,
                total_pages: 0,
            },
            dialog: {
                generate: {
                    title: "Generate Excel Report",
                    show: !1,
                    processing: !1,
                    selected_date_type:"ALL",
                    data: new Form({
                        start_date:new Date(),
                        end_date:new Date(),
                        selected_dsp: [],
                        selected_date_type: [],
                        selected_status: ["PENDING","ONGOING","DELIVERED","COMPLETED","CANCELLED","REFUNDED"],
                        status: ["PENDING","ONGOING","DELIVERED","COMPLETED","CANCELLED","REFUNDED"]
                    })
                },

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

    mounted() {
        this.data = this.service.data;
        this.table.params.seller_id = this.service.data.id;
        // alert(this.data.selected_date_type);
        if (this.data.selected_date_type==1)
          this.table.selected_date_type="Remitted";
        else if(this.data.selected_date_type==2)
          this.table.selected_date_type="Unremitted";

        // this.table.params.start_date = this.data.start_date;
        // this.table.params.end_date = this.data.end_date;
        this.table.params.start_date = null;
        this.table.params.end_date = null;


        // this.updateTable();
        this.dspList();
    },

    methods: {
        onPage(val){
            this.table.params.page = val.page+1;
            this.updateTable();
        },
        generate() {
            this.dialog.generate.processing = false;
            this.dialog.generate.data.start_date = this.table.params.start_date;
            this.dialog.generate.data.end_date = this.table.params.end_date;
            this.dialog.generate.data.selected_dsp = this.table.params.selected_dsp;
            this.dialog.generate.selected_date_type = this.table.selected_date_type;
            this.dialog.generate.show = true;
        },

        date_type(text){
          return this.table.date_types.filter(e=>e.text==text)[0];
        },

        async generate_report() {
            let type = this.date_type(this.dialog.generate.selected_date_type);
            let params = {
                start_date: this.formatDate(this.dialog.generate.data.start_date),
                end_date: this.formatDate(this.dialog.generate.data.end_date),
                selected_dsp: this.dialog.generate.data.selected_dsp,
                seller_id:this.table.params.seller_id,
                selected_date_type: type.date_type,
                selected_type: type.selected_type,
                transaction_status: type.status,
            };

            if (this.dialog.generate.processing) return;
            this.dialog.generate.processing = true;

            SellerService.generate(params)
            .then((e) => {
            window.open(this._hostname + '/' + e.data.file);
            this.dialog.generate.show = false;
            })
            .catch((errors) => {
                try { 
                this.$refs.form.setErrors(this.getError(errors));
                }
                catch(ex){ console.log(ex)}
            })
            .finally(() => {
                this.dialog.generate.processing = false;
            });
        },
        async updateTable(Loading=true) {
            if (this.table.loading) return;
            this.table.loading = Loading;
            let type = await this.date_type(this.table.selected_date_type);
            let params = {...this.table.params}
            params.start_date = this.formatDate(params.start_date,3);
            params.end_date = this.formatDate(params.end_date,3);
            params.selected_date_type = type.date_type; 
            params.selected_type = type.selected_type;
            params.status = type.status;

            SellerService.transactions(params)
            .then((response) => {
                this.table.data = response.data.data.data;
                this.table.total = response.data.data.total;
                this.table.total_pages = response.data.data.last_page;
                this.table.summary = response.data.summary;
            })
            .catch((errors) => {
                try { 
                    this.getError(errors);
                }
                catch(ex){ console.log(ex)}
            })
            .finally(() => {
                this.table.loading = false;
            });
        },
        dspList() {
            DSPService.list( { page_size:-1 })
            .then((response) => {
              let dsp = response.data.data.filter(e=>this.data.service_provider.some(sp=>sp.service_provider_id==e.id));
              this.table.dsp_list = dsp;
              this.table.params.selected_dsp = dsp.map(e=>{return e.id});
            })
            .catch((errors) => {
                try { 
                    this.getError(errors);
                }
                catch(ex){ console.log(ex)}
            })
            .finally(() => {});
        },
        viewData(item){
            TransactionService.get(item.id)
            .then((res) => {
                let data = res.data.data;
                this.dialog.details.data = data;
                this.dialog.details.show = true;
            })
            .catch((errors) => {
                try { 
                    this.getError(errors);
                }
                catch(ex){ console.log(ex)}
            })
            .finally(() => {});
            // axios.get(route(`${this.page.route}.get`, item.id)).then((res) => {
            //     let data = res.data.data;
            //     this.dialog.details.data = data;
            //     this.dialog.details.show = true;
            // })
        },
        successEvent() {
            this.dialog.details.show = !this.dialog.details.show;
        },
    },

    computed: {
        date_label:function(){
          return this.table.date_types.filter(e=>e.text==this.table.selected_date_type)[0].date_label
        },
        generate_date_label:function(){
          return this.table.date_types.filter(e=>e.text==this.dialog.generate.selected_date_type)[0].date_label
        }
    },

    watch: {
        "table.params.search": function (val) {
            this.updateTable();
        },
        
        "table.params.selected_dsp": function(val) {
            this.updateTable();
        },

        "table.params.start_date":function(val) {
            this.updateTable();
        },

        "table.params.end_date":function(val) {
            this.updateTable();
        },

        "table.selected_date_type":function(val){
            this.updateTable();
        }
    }
}
</script>