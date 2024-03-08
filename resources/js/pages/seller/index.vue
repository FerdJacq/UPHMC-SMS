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
              <div class="row">
                <div class="col-12 col-md-3">
                  <div class="card text-white bg-opacity-100 shadow-none selectible-card bg-purple" @click="setWitheldSeller('all')" :class="[{'active':!table.params.eligible_witheld_seller.length}]">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                          <div class="avatar-lg rounded-circle bg-soft-light">
                            <i class="fe-users font-28 avatar-title text-white d-flex"></i>
                          </div>
                          <div class="text-end">
                              <p class="text-uppercase">All</p>
                              <h2 class="mb-0">
                                <!-- <span data-plugin="counterup">{{ table.sellers[item.val] }}</span> -->
                                <count-to :startVal='0' :endVal='Number(table.sellers.NONE) + Number(table.sellers.ACTIVE)+ Number(table.sellers.ELIGIBLE)' :duration='1500'></count-to>
                              </h2>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-3" v-for="(item, index) in eligible_witheld_seller" :key="index">
                  <div @click="setWitheldSeller(item.val)" class="card text-white bg-opacity-100 shadow-none selectible-card" :class="[item.bg,{'active':table.params.eligible_witheld_seller.includes(item.val)}]">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                          <div class="avatar-lg rounded-circle bg-soft-light">
                            <i class="fe-user-check font-28 avatar-title text-white d-flex" :class="[item.icon]"></i>
                          </div>
                          <div class="text-end">
                              <p class="text-uppercase">{{ item.name }}</p>
                              <h2 class="mb-0">
                                <!-- <span data-plugin="counterup">{{ table.sellers[item.val] }}</span> -->
                                <count-to :startVal='0' :endVal='Number(table.sellers[item.val])' :duration='1500'></count-to>
                              </h2>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">

                <div class="col-12 col-md-6" v-for="(item, index) in upload_cor">
                  <div @click="setCORType(item.val)" class="card text-white bg-opacity-100 shadow-none selectible-card" :class="[item.bg,{'active':table.params.has_cor == item.val}]">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                          <div class="avatar-lg rounded-circle bg-soft-light">
                            <i class="fe-file-text font-28 avatar-title text-white d-flex" :class="[item.icon]"></i>
                          </div>
                          <div class="text-end">
                              <p class="text-uppercase">{{ item.name }}</p>
                              <h2 class="mb-0">
                                <count-to :startVal='0' :endVal='Number(table.sellers[item.val])' :duration='1500'></count-to>
                              </h2>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-flex flex-wrap align-items-end mt-3 mb-2">
                <div class="col-12 col-md-5 px-0">
                  <p-button
                    class="btn btn-success"
                    label="Export"
                    icon="pi pi-file-excel"
                    @click="generate_report"
                     v-if="!dialog.generate.processing"
                  />
                  <button v-else  class="btn btn-success" disabled>
                      <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> 
                      Loading...
                  </button>

                  <div class="mt-4">
                      <div class="input-group flex-nowrap flex-xs-wrap">
                          <!-- <span class="p-float-label">
                                <p-dropdown
                                  v-model="table.params.selected_type"
                                  inputId="date_type"
                                  :options="table.type_list"
                                  optionLabel="text"
                                  optionValue="value"
                                  class="w-full"
                                />
                                <label for="status">Type</label>
                          </span> -->
                          <div class="p-float-label p-input-icon-right">
                              <p-datepicker
                              inputId="start_date"
                              class="h-100"
                              inputClass="form-control shadow-none"
                              v-model="table.params.start_date"
                              :maxDate="_max_date"
                              />
                              <i class="pi pi-calendar"></i>
                              <label for="start_date">{{(table.params.selected_type==1 ? "Remitted" : "Completed")}} Start date</label>
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
                              <label for="end_date">{{(table.params.selected_type==1 ? "Remitted" : "Completed")}} End date</label>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="col-12 col-md-4 ms-auto px-0">
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
                  </div>
                </div>
              </div>

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
                    
                    <p-column header="Business Name">
                      <template #body="{data}">
                        {{ data.registered_name }}
                      </template>
                    </p-column>
                    <!-- <p-column header="Business Name">
                      <template #body="{data}">
                        {{ data.business_name }}
                      </template>
                    </p-column> -->
                    <p-column header="TIN">
                      <template #body="{data}">
                        {{ data.tin }}
                      </template>
                    </p-column>
                    <p-column header="Type">
                      <template #body="{data}">
                        <!-- <span class="badge bg-dark" v-if="data.eligible_witheld_seller=='NONE'">
                          Non Withholding
                        </span>
                        <span class="badge bg-warning" v-else-if="data.eligible_witheld_seller=='ELIGIBLE'">
                          Eligible Withholding
                        </span>
                        <span class="badge bg-success" v-else-if="data.eligible_witheld_seller=='ACTIVE'">
                          Active Withholding
                        </span> -->

                        <span class="badge bg-light text-dark" v-if="data.eligible_witheld_seller=='NONE'">
                          Below 500k/Annum
                        </span>
                        <span class="badge bg-success" v-else-if="data.eligible_witheld_seller=='ACTIVE'">
                          VAT Seller
                        </span> 
                        <span class="badge bg-warning" v-else-if="['ELIGIBLE'].includes(data.eligible_witheld_seller)">
                         Above 500k/Annum
                        </span>
                      </template>
                    </p-column>
                    <p-column header="Seller Type">
                      <template #body="{data}">
                        <!-- <span class="badge bg-info" v-if="data.type=='CORP'">Non Individual</span>
                        <span class="badge bg-primary" v-else>Individual</span> -->
                        <span class="badge bg-info">{{data.type=='CORP' ? 'Non' : ''}} Individual</span>
                      </template>
                    </p-column>
                    <p-column header="COR">
                      <template #body="{data}">
                        <span class="badge bg-primary pointer" @click="viewCOR()" v-if="data.has_cor">Submitted</span>
                      </template>
                    </p-column>
                    <!-- <p-column header="Annual Sales">
                      <template #body="{data}">
                        {{ formatNumber(Number(data.sales_per_anum)) }}
                      </template>
                    </p-column> -->
                    <!-- <p-column header="Online Platform VAT">
                      <template #body="{data}">
                        {{ formatNumber(Number(data.online_platform_vat),0,1) }}
                      </template>
                    </p-column>
                    <p-column header="Shipping VAT">
                      <template #body="{data}">
                        {{ formatNumber(Number(data.shipping_vat),0,1) }}
                      </template>
                    </p-column>
                    <p-column header="Item VAT">
                      <template #body="{data}">
                        {{ formatNumber(Number(data.item_vat),0,1) }}
                      </template>
                    </p-column>
                    <p-column header="Total VAT">
                      <template #body="{data}">
                        {{ formatNumber(Number(data.item_vat)+Number(data.online_platform_vat) + Number(data.shipping_vat),0,1) }}
                      </template>
                    </p-column>
                    <p-column header="Withholding Tax(1%)">
                      <template #body="{data}">
                        {{ formatNumber(data.withholding_tax,0,1) }}
                      </template>
                    </p-column>
                    <p-column header="Total Taxes Due">
                      <template #body="{data}">
                        {{ formatNumber(data.tax,0,1) }}
                      </template>
                    </p-column> -->
                    <p-column header="Created date" >
                      <template #body="{data}">
                        {{ formatDateTime(data.created_at) }}
                      </template>
                    </p-column>
                    <p-column header="Updated date">
                      <template #body="{data}">
                        {{ formatDateTime(data.updated_at) }}
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

    <!-- View details modal-->
    <p-dialog
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
        >
        {{ dialog.details.data.registered_name}}
      
        <!-- <span class="badge bg-dark" v-if="dialog.details.data.eligible_witheld_seller=='NONE'">
          Non Withholding
        </span>
        <span class="badge bg-warning" v-else-if="dialog.details.data.eligible_witheld_seller=='ELIGIBLE'">
          Eligible Withholding
        </span>
        <span class="badge bg-success" v-else-if="dialog.details.data.eligible_witheld_seller=='ACTIVE'">
          Active Withholding
        </span> -->

        <span class="badge bg-light text-dark" v-if="dialog.details.data.eligible_witheld_seller=='NONE'">
          Below 500k/anum
        </span>
        <span class="badge bg-success" v-else-if="dialog.details.data.eligible_witheld_seller=='ACTIVE'">
          VAT Seller
        </span>
        <span class="badge bg-warning" v-else-if="[,'ELIGIBLE'].includes(dialog.details.data.eligible_witheld_seller)">
          Above 500k/anum
        </span>
      </h4>
      </div>
    </template>
      
      <div>
        <div class="d-flex align-items-start">
          <div class="col col-xs-12 pe-3 w-100">
            <div class="col">
             
              <div class="row">
                <!-- <div class="col-xs-12 col-6">
                  <p class="text-muted mb-0" :class="{'placeholder placeholder-md w-100': !dialog.details.data.business_name}">
                    Business Name: {{ dialog.details.data.business_name }}
                  </p>
                </div> -->
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
                <div class="col-xs-12 col-6">
                  <p class="text-muted mb-0">
                    Seller Type: <span class="badge bg-info">{{ (dialog.details.data.type=='CORP') ? 'Non Individual' : 'Individual' }}</span>
                  </p>
                </div>
                <div class="col-xs-12 col-6" v-if="dialog.details.data.region">
                  <p class="text-muted mb-0">
                    Region: {{ dialog.details.data.region.label }}
                  </p>
                </div>
                <div class="col-xs-12 col-6">
                  <p class="text-muted mb-0">
                    Annual Sales: {{ formatNumber(dialog.details.data.sales_per_anum) }}
                    <!-- <span class="badge bg-dark">{{ formatNumber(dialog.details.data.sales_per_anum) }}</span> -->
                  </p>
                </div>
                
                <div class="col-xs-12 col-12">
                  <p class="text-muted mb-0" :class="{'placeholder w-100': !dialog.details.data.registered_address}">
                    Address: {{ dialog.details.data.registered_address }}
                  </p>
                </div>
              </div>

              
                
              <div class="col-xs-12 col-12" v-if="dialog.details.data.has_cor">
                <a  @click="viewCOR()" class="text-info text-decoration-underline ms-1 pointer" target="_blank"><b>View Uploaded COR</b></a>
              </div>

            </div>
            <StepForm v-if="dialog.details.data" @hideModal="successEvent" :item="dialog.details.data"/>
          </div>
        </div>
      </div>
    
      <template #footer>
       
      </template>
    </p-dialog>

    <p-toast />
    <p-confirm></p-confirm>
  </div>
</template>
    
<script>
import Loader from "../../components/Loader.vue";
import ImageContainer from "../../components/ImageContainer.vue";
import SellerService  from '../../services/seller';
import StepForm from './form';

export default {
  props: ["items"],

  components: { ImageContainer, Loader, StepForm},
  data() {
    return {
      page: {
        title: "Online Sellers",
        route: "seller",
        interval: null,
      },
      breadcrumbs: [
        { current: false, title: 'Home', url: 'dashboard' }
      ],
      eligible_witheld_seller:[
        {name:"VAT Sellers",val:"ACTIVE", icon:"fe-user-check", bg:"bg-success"},
        {name:"Above 500k / Annum",val:"ELIGIBLE", icon:"fe-user-plus", bg:"bg-warning"},
        {name:"Below 500k / Annum",val:"NONE", icon:"fe-user", bg:"bg-dark"}
      ],
      upload_cor:[
        {name:"With Certificate of Registration(COR)",val:"WCOR", icon:"fe-file-text", bg:"bg-info"},
        {name:"Without Certificate of Registration(COR)",val:"WOCOR", icon:"fe-file", bg:"bg-primary"},
      ],
      table:{
        type_list:[
          {text:"All",value:3},
          {text:"Remitted Only",value:1},
          {text:"Unremitted Only",value:2}
        ],
        params: {
          search:null,
          eligible_witheld_seller:[],
          has_cor:"",
          page:null,
          selected_type:3,
          status:"",
          start_date:new Date(),
          end_date:new Date()
        },
        loading: false,
        data: [],
        sellers:{NONE:0,ACTIVE:0,ELIGIBLE:0,WCOR:0, WOCOR:0},
        total: 0,
        total_pages: 0,
      },
      dialog: {
        generate: {
            title: "Generate Excel Report",
            show: !1,
            processing: !1,
            type:[
              {text:"All",value:3},
              {text:"Remitted Only",value:1},
              {text:"Not Remitted Only",value:2},
            ],
            data: new Form({
                start_date:new Date(),
                end_date:new Date(),
                type:1
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

  computed: {
  },

  watch: {

    "table.params": {
        handler(val){
          this.updateTable();
        },
        deep: true,
        immediate: true
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
    this.table.params.status =(params.get("status")) ? params.get("status").toUpperCase() : "";
    if (this.table.params.status)  this.table.params.selected_date_type = this.table.params.status;

    this.busListener();
    this.updateTable();
  },

  methods: {
    onPage(val){
      this.table.params.page = val.page+1;
      this.updateTable();
    },
    viewCOR()
    {
      window.open('/images/cor.png', '_blank');
    },
    updateTable(Loading=true) {
      if (this.table.loading) return;
      this.table.loading = Loading;

      SellerService.list(this.table.params)
      .then((response) => {
        this.table.data = response.data.data.data;
        this.table.total = response.data.data.total;
        this.table.total_pages = response.data.data.last_page;
        this.table.sellers = response.data.sellers;
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
    setCORType(item)
    {
      if(item==this.table.params.has_cor)
        this.table.params.has_cor = "";
      else
        this.table.params.has_cor = item;
    },
    setWitheldSeller(item)
    {
      if(item=="all"){
        this.table.params.eligible_witheld_seller = [];
      }else{
        this.table.params.eligible_witheld_seller = [item];
      }

      // if (this.table.params.eligible_witheld_seller.some(e=>e==item)){
      //   this.table.params.eligible_witheld_seller = this.table.params.eligible_witheld_seller.filter(e=>e!=item);
      // }
      // else{
      //   this.table.params.eligible_witheld_seller.push(item);
      // }
     
    },
    viewData(item){
      // this.dialog.details.data = item;
      // this.dialog.details.show = true;
      SellerService.get(item.id)
      .then((res) => {
        let data = res.data.data;
        this.dialog.details.data = data;
        // console.log(this.data)
        this.dialog.details.data.start_date= this.table.params.start_date;
        this.dialog.details.data.end_date = this.table.params.end_date;
        this.dialog.details.data.selected_date_type = this.table.params.selected_type;
        this.dialog.details.show = true;
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

    successEvent() {
      this.dialog.details.show = !1;
    },

    busListener(){
		
		},
    generate(){
      this.dialog.generate.show = true;
      this.dialog.generate.data.type = this.table.params.selected_type;
      this.dialog.generate.data.start_date = this.table.params.start_date;
      this.dialog.generate.data.end_date = this.table.params.end_date;
    },
    generate_report(){
      // let params = {
      //     start_date: this.formatDate(this.table.params.start_date),
      //     end_date: this.formatDate(this.table.params.end_date),
      //     type:this.table.params.selected_type
      // };

      if (this.dialog.generate.processing) return;
      this.dialog.generate.processing = true;

      SellerService.generate_summary_report(this.table.params)
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
    }
  },

  beforeDestroy() {
    
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

  .seller-modal{
    /* min-height: calc(90vh - 150px);
    width:100%;
    max-width:1125px; */
  }

  .selectible-card{
    cursor: pointer;
  }

  .selectible-card:hover{
    scale: 1.05;
  }

  .selectible-card.active{
    /* scale: 1.05; */
    box-shadow: 0 3px 10px 2px #444 !important;
  }
</style>