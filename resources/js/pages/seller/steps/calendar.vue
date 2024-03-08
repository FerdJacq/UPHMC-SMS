<template>
      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-xl-12">
                <span class="p-float-label pl-0">
                  <p-multiselect
                    class="w-100 mw-100"
                    inputId="company_name"
                    :options="dsp_list"
                    optionLabel="company_name"
                    optionValue="id"
                    display="chip"
                    v-model="selected_dsp"
                    inputClass="form-control shadow-none"
                  />
                  <label for="company_name">Service Provider</label>
                </span>
                <span class="p-float-label pl-0 mt-3">
                  <p-multiselect
                    class="w-100 mw-100"
                    inputId="region_list"
                    :options="region_list"
                    optionLabel="name"
                    optionValue="region_code"
                    display="chip"
                    v-model="selected_regions"
                    inputClass="form-control shadow-none"
                  />
                  <label for="region_list">Region</label>
                </span>

                <p-button
                  class="btn btn-success w-100 mt-3"
                  label="Export"
                  icon="pi pi-file-excel"
                  @click="exportReport()"
                  :disabled="!computeAmount('total_tax')"
                  v-if="!generate_loading"
                />
                <button v-else  class="btn btn-success w-100 mt-3" disabled>
                  <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> 
                  Loading...
              </button>
            </div>

            <div class="col-xl-12 mt-3">
                <ul class="list-group">
                    <li @click="viewCompanyRecord(item)" v-for="(item, index) in service_providers" :key="index" class="list-group-item d-flex justify-content-between align-items-center pointer">
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

            <div class="col-xl-12 mt-3">
              
              <ul class="list-group">
                  <li class="list-group-item d-flex align-items-center py-1">
                      <i class="fe-shopping-cart me-1"></i> Total Amount
                      <span class="text-bold ms-auto">{{ computeAmount("total_amount") }}</span>
                  </li>
                  <li class="list-group-item d-flex align-items-center py-1">
                      <i class="fe-activity me-1"></i> 
                      Gross Sales
                      <span class="text-bold ms-auto">{{ computeAmount("base_price") }}</span>
                  </li>
              </ul>

                <ul class="list-group mt-3">
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-shuffle me-1"></i> Transaction Fee
                        <span class="text-bold ms-auto">{{computeAmount("transaction_fee")}}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-percent me-1"></i> Commission Fee
                        <span class="text-bold ms-auto">{{computeAmount("commission_fee")}}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-globe me-1"></i> Service Fee
                        <span class="text-bold ms-auto">{{computeAmount("service_fee")}}</span>
                    </li>
                </ul>

                <ul class="list-group mt-3">
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-cloud me-1"></i> Online Platform VAT
                        <span class="text-bold ms-auto">{{computeAmount("online_platform_vat")}}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-truck me-1"></i> Shipping VAT
                        <span class="text-bold ms-auto">{{computeAmount("shipping_vat")}}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-package me-1"></i> Item VAT
                        <span class="text-bold ms-auto">{{computeAmount("item_vat")}}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="fe-package me-1"></i> Withholding Tax(1%)
                        <span class="text-bold ms-auto">{{computeAmount("withholding_tax")}}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-1">
                        <i class="ri-line-chart-line me-1"></i> Total Taxes Due
                        <span class="text-bold ms-auto">{{computeAmount("total_tax")}}</span>
                    </li>
                </ul>

            </div>

          </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100 p-3">
                <Loader
                  v-if="loading"
                  title="Fetching Data..."
                  message=""
                  customClass="loader-absolute"
                ></Loader>
                <FullCalendar ref="calendar" :options="calendarOptions">
                  <template v-slot:eventContent='arg'>
                    {{ abbreviateNumber(parseInt(arg.event.title)) }}
                  </template>
                </FullCalendar>
            </div>
        </div>

        <p-dialog
            class="calendar-modal modal" v-model:visible="dialog.show" modal
            :style="{
              width: '100%',
              'max-width': '1125px'
            }">
            <template #header>
             
              <div class="d-flex align-items-center">
                <div class="avatar-sm me-2" v-if="dialog.logo">
                  <img :src="dialog.logo" class="img-fluid rounded-circle"/>
                </div>
                <h3 v-text="dialog.title" class="my-0"></h3>
              </div>
            </template>
            <div class="table-responsive mb-3">
              <table class="table table-bordered table-fit-column-1 table-sm">
                <thead>
                  <tr>
                    <td v-if="dialog.type" class="w-fit"></td>
                    <td v-if="dialog.type">Service Provider</td>
                    <td v-if="!dialog.type" class="w-fit">Date</td>
                    <td>Region</td>
                    <td class="w-fit">Total Transactions</td>
                    <td class="w-fit">Gross Sales</td>
                    <td class="w-fit">Transaction Fee</td>
                    <td class="w-fit">Commission Fee</td>
                    <td class="w-fit">Service Fee</td>
                    <td class="w-fit">Online Platform VAT</td>
                    <td class="w-fit">Shipping VAT</td>
                    <td class="w-fit">Item VAT</td>
                    <td class="w-fit">Withholding Tax(1%)</td>
                    <td class="w-fit">Total Taxes Due</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in dialog.data" :key="index">
                    <td v-if="dialog.type">
                      <div class="avatar-sm">
                        <img :src="item.logo" class="img-fluid rounded-circle"/>
                      </div>
                    </td>
                    <td v-if="dialog.type">{{ item.company_name }}</td>
                    <td v-if="!dialog.type">{{ formatDate(item.assigned_date) }}</td>
                    <td><span v-if="item.region_name">{{ item.region_name }}({{ item.region_desc }})</span></td>
                    <td class="text-right">
                      <span class="">{{item.total_transaction}}</span>
                    </td>
                    <td class="text-right">{{ formatNumber(item.base_price) }}</td>
                    <td class="text-right">{{ formatNumber(item.transaction_fee) }}</td>
                    <td class="text-right">{{ formatNumber(item.commission_fee) }}</td>
                    <td class="text-right">{{ formatNumber(item.service_fee) }}</td>
                    <td class="text-right">{{ formatNumber(item.shipping_vat) }}</td>
                    <td class="text-right">{{ formatNumber(item.online_platform_vat) }}</td>
                    <td class="text-right">{{ formatNumber(item.item_vat) }}</td>
                    <td class="text-right">{{ formatNumber(item.withholding_tax) }}</td>
                    <td class="text-right"> 
                      <span class="">{{ formatNumber(item.total_tax,2) }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td v-if="dialog.type" colspan="3"></td>
                    <td v-if="!dialog.type" colspan="2"></td>
                    <td class="text-right text-bold">
                      <span class="">
                        {{ computeDialog("total_transaction",0)  }}
                      </span>
                    </td>
                    <td class="text-right text-bold">{{ computeDialog("base_price")  }}</td>
                    <td class="text-right text-bold">{{ computeDialog("transaction_fee")  }}</td>
                    <td class="text-right text-bold">{{ computeDialog("commission_fee")  }}</td>
                    <td class="text-right text-bold">{{ computeDialog("service_fee")  }}</td>
                    <td class="text-right text-bold">{{ computeDialog("online_platform_vat")  }}</td>
                    <td class="text-right text-bold">{{ computeDialog("shipping_vat")  }}</td>
                    <td class="text-right text-bold">{{ computeDialog("item_vat")  }}</td>
                    <td class="text-right text-bold">{{ computeDialog("withholding_tax")  }}</td>
                    <td class="text-right text-bold">
                      <span class="text-bold">{{ computeDialog("total_tax")  }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <template #footer>
              <p-button class="btn btn-outline-light" label="Close" @click="dialog.show=false"/>
            </template>
          </p-dialog>

      </div>
  </template>
  
  <script>
  import FullCalendar from '@fullcalendar/vue3'
  import dayGridPlugin from '@fullcalendar/daygrid'
  import interactionPlugin from '@fullcalendar/interaction'
  import CalendarService  from '../../../services/calendar'
  import DSPService  from '../../../services/service_provider';
  import Loader from "../../../components/Loader.vue";
  
  export default {
    components: { FullCalendar,Loader },
    props: ['service'],
    computed: {
      service_providers(){
        return this.getDataPerCompany().sort((a, b) => b.base_price - a.base_price);
      }
    },
    data() {
      return {
        page: {
          title:"Calendar",
          route:"calendar",
          interval:null
        },
        breadcrumbs: [
          { current: false, title: 'Home', url: 'dashboard' }
        ],
        calendarOptions: {
            plugins: [dayGridPlugin],
            initialView: 'dayGridMonth',
            editable: true,
            selectable: true, 
            events: [],
            validRange:{end:new Date()},
            height:"auto"
        },
       
        
        search:"",
        
        data:[],
        selected_dsp:[],
        dsp_list:[],
        region_list:[],
        selected_regions:[],
        loading:false,
        dialog: {
          title: "",
          show:false,
          processing:false,
          type:1,
          logo:"",
          data:[]
        },
        generate_loading:false
      }
    },
    
    methods:{
        updateData(loading=true) {
          const calendarApi = this.$refs.calendar.getApi()
          const date = calendarApi.view.currentStart

          let params = {
            start_date:this.getDateMonth(date),
            last_date:this.getDateMonth(date,1),
            selected_dsp:this.selected_dsp,
            selected_regions:this.selected_regions,
            seller_id:this.service.id,
            transaction_type:"all",
            seller_type:"all",
            eligible_witheld_seller:[]
          }
          this.loading = loading;
          CalendarService.data(params)
          .then((response) => {
            let data = response.data.data;
            this.data = data.summaries;
            this.renderData();
          })
          .catch((errors) => {
              try { 
                console.log(errors)
              }
              catch(ex){ console.log(ex)}
          })
          .finally(() => {
            this.loading = false;
          });
            
        },
        getDataPerCompany(filtered_date="",per_region=false,company_name=""){
          const item = this.data
          .filter((x) => (filtered_date) ? x.assigned_date==filtered_date : true)
          .filter((x) => (company_name) ? x.company_name==company_name : true)
          .reduce((parent, item) => {
            let group = item.company_name;
            if (company_name)
              group = item.assigned_date;
            
            if(per_region) group += `-${item.region_code}`

            parent[group] = parent[group] || {total:0,tax:0,total_amount:0,base_price:0,online_platform_vat:0,shipping_vat:0,item_vat:0,transaction_fee:0,service_fee:0,commission_fee:0,withholding_tax:0};
            parent[group].assigned_date=item.assigned_date;
            parent[group].logo=item.logo;
            parent[group].total+=this.parseNumber(item.total);
            parent[group].tax+=this.parseNumber(item.tax);
            parent[group].company_name=item.company_name;
            parent[group].region_code=item.region_code;
            parent[group].region_name=item.region_name;
            parent[group].region_desc=item.region_desc;
            parent[group].total_amount+=this.parseNumber(item.total_amount);
            parent[group].base_price+=this.parseNumber(item.base_price);
            parent[group].online_platform_vat+=this.parseNumber(item.online_platform_vat);
            parent[group].shipping_vat+=this.parseNumber(item.shipping_vat);
            parent[group].item_vat+=this.parseNumber(item.item_vat);
            parent[group].transaction_fee+=this.parseNumber(item.transaction_fee);
            parent[group].service_fee+=this.parseNumber(item.service_fee);
            parent[group].commission_fee+=this.parseNumber(item.commission_fee);
            parent[group].withholding_tax+=this.parseNumber(item.withholding_tax);
            return parent;
          }, {});

          let data = Object.entries(item).sort(([, a], [, b]) => b - a).map((e,index)=>{ 
            return {
              group:e[0],
              company_name:e[1].company_name,
              assigned_date:e[1].assigned_date,
              logo:e[1].logo,
              total_transaction:e[1].total,
              total_tax:e[1].tax,
              total_amount:e[1].total_amount,
              base_price:e[1].base_price,
              region_code:e[1].region_code,
              region_name:e[1].region_name,
              region_desc:e[1].region_desc,
              online_platform_vat:e[1].online_platform_vat,
              shipping_vat:e[1].shipping_vat,
              item_vat:e[1].item_vat,
              transaction_fee:e[1].transaction_fee,
              service_fee:e[1].service_fee,
              commission_fee:e[1].commission_fee,
              withholding_tax:e[1].withholding_tax
            }
          }); 
          return data.sort(function (a, b) {
              return  new Date(a.assigned_date) - new Date(b.assigned_date) ||
              (a.company_name > b.company_name) ? 1 : ((b.company_name > a.company_name)) || 
              a.region_code - b.region_code;
          });
          // return data.sort((a,b) => (a.company_name > b.company_name) ? 1 : ((b.company_name > a.company_name) ? -1 : 0))
        },
        renderData(){
          const item = this.data.filter((x) => x.assigned_date).reduce((parent, item) => {
            const group = item.assigned_date;
            parent[group] = parent[group] || { total: 0, tax: 0, base_price:0, online_platform_vat:0,total_amount:0 };
            parent[group].total+=this.parseNumber(item.total);
            parent[group].tax+=this.parseNumber(item.tax);
            parent[group].total_amount+=this.parseNumber(item.total_amount);
            parent[group].base_price+=this.parseNumber(item.base_price);
            parent[group].online_platform_vat+=this.parseNumber(item.online_platform_vat);
            parent[group].shipping_vat+=this.parseNumber(item.shipping_vat);
            parent[group].item_vat+=this.parseNumber(item.item_vat);
            parent[group].transaction_fee+=this.parseNumber(item.transaction_fee);
            parent[group].service_fee+=this.parseNumber(item.service_fee);
            parent[group].commission_fee+=this.parseNumber(item.commission_fee);
            parent[group].withholding_tax+=this.parseNumber(item.withholding_tax);
            return parent;
          }, {});

          let data = Object.entries(item).sort(([, a], [, b]) => b - a).map((e,index)=>{ 
            return {
              start:e[0],
              title:e[1].base_price,
              assigned_date:e[0],
              total_transaction:e[1].total,
              total_tax:e[1].tax,
              online_platform_vat:e[1].online_platform_vat,
              shipping_vat:e[1].shipping_vat,
              item_vat:e[1].item_vat,
              transaction_fee:e[1].transaction_fee,
              service_fee:e[1].service_fee,
              commission_fee:e[1].commission_fee,
              withholding_tax:e[1].withholding_tax
            }
          });
          this.calendarOptions.events = data;
        },
        dspList() {
          DSPService.list( { page_size:-1 })
          .then((response) => {
            // this.dsp_list = response.data.data;
            // this.selected_dsp = response.data.data.map(e=>{return e.id});
            let dsp = response.data.data.filter(e=>this.service.data.service_provider.some(sp=>sp.service_provider_id==e.id));
            this.dsp_list = dsp;
            this.selected_dsp = dsp.map(e=>{return e.id});
          })
          .catch((errors) => {
              try { 
                this.getError(errors);
              }
              catch(ex){ console.log(ex)}
          })
          .finally(() => {});
        },
        regionList() {
          CalendarService.regions()
          .then((response) => {
            this.region_list = [{id:"",name:"NA",region_code:"",region_desc:""},...response.data.data];
            this.selected_regions = ["",...response.data.data.map(e=>{return e.region_code})];
          })
          .catch((errors) => {
              try { 
                this.getError(errors);
              }
              catch(ex){ console.log(ex)}
          })
          .finally(() => {});
        },
        busListener(){
              // this.emitter.on("new_transaction", val=>{
              // 	this.updateData();
        // });
        },
        viewRecord(event){
          let calendar_data=event.event._def.extendedProps
          this.dialog.type = 1;
          this.dialog.logo = "";
          this.dialog.title = this.formatDate(calendar_data.assigned_date,2)
          this.dialog.data = this.getDataPerCompany(calendar_data.assigned_date,true,"")
          console.log(this.dialog.data)
          this.dialog.show = true;
        },
        viewCompanyRecord(item){
          this.dialog.type = 0;
          this.dialog.logo = item.logo;
          this.dialog.title = item.company_name;
          this.dialog.data = this.getDataPerCompany("",true,item.company_name)
          this.dialog.show = true;
        },
        computeAmount(type){
          return this.formatNumber(this.service_providers.map(e=>{return e[type] }).reduce((a, b) => a + b, 0),2);
        },
        computeDialog(type,decimal=2){
          return this.formatNumber(this.dialog.data.map(e=>{return e[type] }).reduce((a, b) => a + b, 0),decimal);
        },

        exportReport() {
          const calendarApi = this.$refs.calendar.getApi()
          const date = calendarApi.view.currentStart
          let params = {
            start_date:this.getDateMonth(date),
            last_date:this.getDateMonth(date,1),
            selected_dsp:this.selected_dsp,
            selected_regions:this.selected_regions,
            seller_id:this.service.id
          }
          // window.open(`/${this.page.route}/export`,'_blank');
          this.generate_loading = true;
          CalendarService.export(params)
            .then((e) => {
              window.open(this._hostname + '/' + e.data.file);
              this.dialog.generate.show = false;
          })
          .catch((errors) => {
                try { 
                  this.getError(errors);
                }
                catch(ex){ console.log(ex)}
          })
          .finally(() => { this.generate_loading = false; });
        }

    },
  
    mounted(){
      let self = this;
      this.breadcrumbs.push({
        current: true,
        title: this.page.title,
        url: `${this.page.route}`
      });
  
      // this.updateData();
      this.dspList();
      this.regionList();
  
      this.page.interval = setInterval(() => {
        // this.updateData(false);
      }, 5000);
  
      this.busListener();

      $('.fc-button').click(function () {
          self.updateData();
      });

      const calendar = this.$refs.calendar.getApi();
      calendar.on('eventClick', this.viewRecord);
    },
  
    beforeDestroy() {
    },
  
    beforeUnmount() {
      clearInterval(this.page.interval);
    },

    watch: {
      "selected_dsp": function (val) {
        this.updateData();
      },
      "selected_regions":function (val) {
        this.updateData();
      },
    }
  };
  </script>

<style>
.fc-event {
  background-color: transparent !important;
  color:#585454 !important;
}

.fc-event-main{
  color: #585454 !important;
  font-weight: 700 !important;
  font-size:20px !important;
}

.calendar-modal .p-dialog-content {
  /* min-height: calc(90vh - 150px); */
  max-height: calc(90vh - 150px);
}

</style>
<style scoped>
.fc-view-harness fc-view-harness-active{
  height:500px !important;
}
</style>