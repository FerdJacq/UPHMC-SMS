<template>
  <div>
    <app-layout>
      <app-breadcrumb
        :title="page.title"
        :links="breadcrumbs"
      ></app-breadcrumb>

      <div class="row">
        <div class="col-lg-12">
          <p-card class="shadow-none card">
            <template #content>
              <div class="d-flex flex-wrap align-items-end mt-3 mb-2">
                <div class="col-12 col-md-6 px-0">
                  <p-button
                    class="btn btn-success"
                    label="Generate report"
                    icon="pi pi-file-excel"
                    @click="generate"
                  />

                  <div class="mt-4">
                    <div class="input-group flex-nowrap flex-xs-wrap">
                        <span class="p-float-label" v-if="!table.filtered_status">
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
                            inputClass="form-control shadow-none"
                            v-model="table.params.start_date"
                            :maxDate="_max_date"
                          />
                          <i class="pi pi-calendar"></i>
                          <label for="start_date">{{date_label}} Start date</label>
                        </div>
                        <span class="input-group-text">to</span>
                        <div class="p-float-label p-input-icon-right">
                          <p-datepicker
                            inputId="end_date"
                            inputClass="form-control shadow-none"
                            v-model="table.params.end_date"
                            :maxDate="_max_date"
                          />
                          <i class="pi pi-calendar"></i>
                          <label for="end_date">{{date_label}} End date</label>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-4 ms-auto px-0">
                  
                  <span class="p-float-label w-100" v-if="!['SELLER','RDO'].includes(_role)">
                    <p-multiselect
                      class="h-100 w-100 mw-100"
                      :options="region_list"
                      optionLabel="label"
                      optionValue="region_code"
                      display="chip"
                      v-model="table.params.selected_regions"
                      inputClass="form-control shadow-none"
                    />
                    <label for="region_list">Region</label>
                  </span>

                  <div class="mt-4">
                    <div class="input-group flex-nowrap w-full flex-xs-wrap">
                      <span class="p-float-label p-input-icon-right flex-grow-1">
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
              </div>

              <div class="table-responsive mb-3">
                <p-table
                  tableClass="table"
                  :rowHover="true"
                  :loading="table.loading"
                  :value="table.data"
                  :rowClass="rowClass"
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
                    <p-column header="Type">
                      <template #body="{data}">
                        {{ (data.type=="PRODUCT") ? "GOODS" : data.type }}
                      </template>
                    </p-column>
                    <p-column field="trans_id" header="Transaction ID"></p-column>
                    <p-column header="Total amount">
                      <template #body="{data}">
                        <span v-if="data.log && !_is_auth_seller">
                          <span class="text-line-through">{{ formatNumber(data.total_amount) }}</span>
                          <span class="text-warning text-updated">{{ formatNumber(data.log.total_amount) }}</span>
                        </span>
                        <span v-else-if="data.log">{{ formatNumber(data.log.total_amount) }}
                        </span>
                        <span v-else>{{ formatNumber(data.total_amount) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Gross Sales">
                      <template #body="{data}">
                        <span v-if="data.log && !_is_auth_seller">
                          <span class="text-line-through">{{ formatNumber(data.base_price) }}</span>
                          <span class="text-warning text-updated">{{ formatNumber(data.log.base_price) }}</span>
                        </span>
                        <span v-else-if="data.log">{{ formatNumber(data.log.base_price) }}</span>
                        <span v-else>{{ formatNumber(data.base_price) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Online Platform VAT">
                      <template #body="{data}">
                        <span v-if="data.log && !_is_auth_seller">
                          <span class="text-line-through">{{ formatNumber(data.online_platform_vat) }}</span>
                          <span class="text-warning text-updated">{{ formatNumber(data.log.online_platform_vat) }}</span>
                        </span>
                        <span v-else-if="data.log">{{ formatNumber(data.log.online_platform_vat) }}</span>
                        <span v-else>{{ formatNumber(data.online_platform_vat) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Shipping VAT">
                      <template #body="{data}">
                        <span v-if="data.log && !_is_auth_seller">
                          <span class="text-line-through">{{ formatNumber(data.shipping_vat) }}</span>
                          <span class="text-warning text-updated">{{ formatNumber(data.log.shipping_vat) }}</span>
                        </span>
                        <span v-else-if="data.log">{{ formatNumber(data.log.shipping_vat) }}</span>
                        <span v-else>{{ formatNumber(data.shipping_vat) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Item VAT">
                      <template #body="{data}">
                        <span v-if="data.log && !_is_auth_seller">
                          <span class="text-line-through">{{ formatNumber(data.item_vat) }}</span>
                          <span class="text-warning text-updated">{{ formatNumber(data.log.item_vat) }}</span>
                        </span>
                        <span v-else-if="data.log">{{ formatNumber(data.log.item_vat) }}</span>
                        <span v-else>{{ formatNumber(data.item_vat) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Withholding Tax(1%)">
                      <template #body="{data}">
                        <span v-if="data.log && !_is_auth_seller">
                          <span class="text-line-through">{{ formatNumber(data.withholding_tax) }}</span>
                          <span class="text-warning text-updated">{{ formatNumber(data.log.withholding_tax) }}</span>
                        </span>
                        <span v-else-if="data.log">{{ formatNumber(data.log.withholding_tax) }}</span>
                        <span v-else>{{ formatNumber(data.withholding_tax) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Total Taxes Due" >
                      <template #body="{data}">
                        <span v-if="data.log && !_is_auth_seller">
                          <span class="text-line-through">{{ formatNumber(data.tax) }}</span>
                          <span class="text-warning text-updated">{{ formatNumber(data.log.tax) }}</span>
                        </span>
                        <span v-else-if="data.log">{{ formatNumber(data.log.tax) }}</span>
                        <span v-else>{{ formatNumber(data.tax) }}</span>
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
                    <p-column header="Pending date" v-if="['','PENDING'].includes(table.filtered_status)">
                      <template #body="{data}">
                        {{ formatDateTime(data.pending_date) }}
                      </template>
                    </p-column>
                    <p-column header="Ongoing date" v-if="['','ONGOING'].includes(table.filtered_status)">
                      <template #body="{data}">
                        {{ formatDateTime(data.ongoing_date) }}
                      </template>
                    </p-column>
                    <p-column header="Delivery date" v-if="['','DELIVERED'].includes(table.filtered_status)">
                      <template #body="{data}">
                        {{ formatDateTime(data.delivered_date) }}
                      </template>
                    </p-column>
                    <p-column header="Completed date" v-if="['','COMPLETED'].includes(table.filtered_status)">
                      <template #body="{data}">
                        {{ formatDateTime(data.completed_date) }}
                      </template>
                    </p-column>
                    <p-column header="Cancel date" v-if="['','CANCELLED'].includes(table.filtered_status)">
                      <template #body="{data}">
                        {{ formatDateTime(data.cancelled_date) }}
                      </template>
                    </p-column>
                    <p-column header="Refund date" v-if="['','REFUNDED'].includes(table.filtered_status)">
                      <template #body="{data}">
                        {{ formatDateTime(data.refunded_date) }}
                      </template>
                    </p-column>
                    <p-column header="Remitted date" v-if="['','COMPLETED','REFUNDED','REMITTED'].includes(table.filtered_status)">
                      <template #body="{data}">
                        {{ formatDateTime(data.remitted_date) }}
                      </template>
                    </p-column>
                </p-table>
                <p-paginator :rows="10" :totalRecords="table.total" @page="onPage($event)"></p-paginator>
              </div>

              
            </template>
          </p-card> <!-- end card -->
        </div> <!-- end col -->
      </div>
    </app-layout>

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
        <v-field v-if="!table.filtered_status" tag="div" class="field" slim name="selected_date_type" vid="selected_date_type" v-slot="{ errors }">
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
            <label for="generate_start_date">Start date</label>
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
            <label for="generate_end_date">End date</label>
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
      :closeOnEscape="false"
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
        <!-- <span class="text-right text-block">Remitted Date: {{ formatDateTime(dialog.details.data.remitted_date) }}</span> -->
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

    <p-toast />
    <p-confirm></p-confirm>
  </div>
</template>
    
<script>
import Loader from "../../components/Loader.vue";
import ImageContainer from "../../components/ImageContainer.vue";
import TransactionService  from '../../services/transaction';
import DSPService  from '../../services/service_provider';
import StepForm from './form';
import DashboardService  from '../../services/dashboard'

export default {
  props: ["items"],

  components: { ImageContainer, Loader,StepForm},
  data() {
    return {
      page: {
        title: "Transactions",
        route: "transaction",
        interval: null,
      },
      breadcrumbs: [
        { current: false, title: 'Home', url: 'dashboard' }
      ],
      region_list:[],
      table:{
        selected_date_type:[],
        params: {
          search:null,
          page:null,
          selected_dsp:[],
          selected_type:"",
          status:[],
          selected_regions:[],
          start_date:new Date(),
          end_date:new Date()
        },
        loading: false,
        data: [],
        total: 0,
        total_pages: 0,
        selected_dsp: [],
        dsp_list: [],
        selected_date_type:"ALL",
        date_types: [
          { text:"ALL", date_type:"created_at", status:[], date_label:"Created", selected_type:3},
          { text:"Pending", date_type:"pending_date", status:["PENDING"], date_label:"Pending", selected_type:3},
          { text:"Ongoing", date_type:"ongoing_date", status:["ONGOING"], date_label:"Ongoing", selected_type:3},
          { text:"Delivered", date_type:"delivered_date", status:["DELIVERED"], date_label:"Delivered", selected_type:3},
          { text:"Completed", date_type:"completed_date", status:["COMPLETED"], date_label:"Completed", selected_type:3},
          { text:"Cancelled", date_type:"cancelled_date", status:["CANCELLED"], date_label:"Cancelled", selected_type:3},
          { text:"Refunded", date_type:"refunded_date", status:["REFUNDED"], date_label:"Refunded", selected_type:3},
          { text:"Remitted", date_type:"remitted_date", status:["COMPLETED"], date_label:"Remitted", selected_type:1},
          { text:"Unremitted", date_type:"completed_date", status:["COMPLETED"], date_label:"Completed", selected_type:2}
        ]
      },
      dialog: {
        generate: {
          title: "Generate Excel Report",
          show: !1,
          processing: !1,
          data: new Form({
            start_date:new Date(),
            end_date:new Date(),
            selected_dsp: [],
            selected_date_type: [],
            selected_status: ["PENDING","ONGOING","DELIVERED","COMPLETED","CANCELLED","REFUNDED"],
            status: ["PENDING","ONGOING","DELIVERED","COMPLETED","CANCELLED","REFUNDED"]
          }),
          selected_date_type:"ALL"
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

    "table.params.selected_regions": function(val) {
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
  },

  mounted(){
    this.breadcrumbs.push({
      current: true,
      title: this.page.title,
      url: `${this.page.route}`
    });

    this.page.interval = setInterval(() => {
      this.updateTable(false);
    }, 5000);
    let params = (new URL(document.location)).searchParams;
    this.table.filtered_status =(params.get("status")) ? params.get("status").toUpperCase() : "";
    if (this.table.filtered_status){
      this.table.selected_date_type = this.date_type(this.table.filtered_status).text;
    }

    this.busListener();
    this.dspList();
    this.updateTable();
    this.regionList();
  },

  methods: {
    regionList() {
      DashboardService.regions()
      .then((response) => {
        // this.region_list = [{id:"",name:"NA",region_code:"",region_desc:""},...response.data.data];
        this.region_list = [...response.data.data];
        this.table.params.selected_regions = [...response.data.data.map(e=>{return e.region_code})];
      })
      .catch((errors) => {
          try { 
            this.getError(errors);
          }
          catch(ex){ console.log(ex)}
      })
      .finally(() => {});
    },
    date_type(text){
        return this.table.date_types.filter(e=>e.text.toLowerCase()==text.toLowerCase())[0];
    },
    rowClass(data,index){
      console.log(data)
      if(data.log && !this._is_auth_seller){
        if (data.log.is_seen==0) return "blinking-row";
        else return "row-updated";
      }
    },
    onPage(val){
      this.table.params.page = val.page+1;
      this.updateTable();
    },
    async updateTable(Loading=true) {
      if (this.table.loading) return;
      this.table.loading = Loading;
      let type = await this.date_type(this.table.selected_date_type);
      
      if (this.table.filtered_status && !["REMITTED","UNREMITTED"].includes(this.table.filtered_status.toUpperCase())){
        this.table.params.status = [this.table.filtered_status];
      }
      else if(this.table.filtered_status.toUpperCase()=="REMITTED") {
        type = await this.date_type("REMITTED");
        this.table.params.status = ["COMPLETED"];
      }
      else if(this.table.filtered_status.toUpperCase()=="UNREMITTED"){
        type = await this.date_type("UNREMITTED");
        this.table.params.status = ["COMPLETED"];
      }
      else{
        this.table.params.status = type.status;
      }
      
      this.table.params.selected_date_type = type.date_type; 
      this.table.params.selected_type = type.selected_type;
      
      TransactionService.list(this.table.params)
      .then((response) => {
        this.table.data = response.data.data.data;
        this.table.total = response.data.data.total;
        this.table.total_pages = response.data.data.last_page;
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

    viewData(item){
      axios.get(route(`${this.page.route}.get`, item.id)).then((res) => {
        let data = res.data.data;
        this.dialog.details.data = data;
        if(data.log && this._is_auth_bir){
          if (data.log.is_seen==0)
          {
            TransactionService.seen_logs(data.id);
            this.updateTable();
            this.emitter.emit('stop_sound_all');
          }
        }
        this.dialog.details.show = true;
      })
    },

    generate() {
      this.dialog.generate.processing = false;
      this.dialog.generate.data.start_date = this.table.params.start_date;
      this.dialog.generate.data.end_date = this.table.params.end_date;
      this.dialog.generate.data.selected_dsp = this.table.params.selected_dsp;
      this.dialog.generate.selected_date_type = this.table.selected_date_type;
      this.dialog.generate.show = true;
    },

    async generate_report() {
        let type = this.date_type(this.dialog.generate.selected_date_type);
        console.log(type);
        let params = {
            start_date: this.formatDate(this.dialog.generate.data.start_date),
            end_date: this.formatDate(this.dialog.generate.data.end_date),
            selected_dsp: this.dialog.generate.data.selected_dsp,
            seller_id:this.table.params.seller_id,
            selected_date_type: type.date_type,
            selected_type: type.selected_type,
            transaction_status: type.status,
            selected_regions:this.table.params.selected_regions
        };
        
        console.log(params)

        if (this.dialog.generate.processing) return;
        this.dialog.generate.processing = true;

        TransactionService.generate(params)
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

    dspList() {
      DSPService.list( { page_size:-1 })
      .then((response) => {
        this.table.dsp_list = response.data.data;
        this.table.params.selected_dsp = response.data.data.map(e=>{return e.id});
      })
      .catch((errors) => {
          try { 
            this.getError(errors);
          }
          catch(ex){ console.log(ex)}
      })
      .finally(() => {});
    },

    successEvent() {
      this.dialog.details.show = !this.dialog.details.show;
    },

    busListener(){
			this.emitter.on("new_transaction", val=>{
				this.updateTable();
      });
		}
  },

  beforeDestroy() {
    this.emitter.off("new_transaction");
  },

  beforeUnmount() {
    clearInterval(this.page.interval);
  }
};
</script>
<style scope>
  .mn-175 {
    min-width: 175px;
  }

  .mx-175 {
    max-width: 175px;
  }

  .table-item {
    padding: 0.275rem 0.725rem;
    border: 0 solid #dee2e6;
    border-width: 0 0 1px;
  }

  .table-item[class*="badge-"] {
    border-radius: 3px;
  }

  .border-none {
    border: none;
  }
</style>