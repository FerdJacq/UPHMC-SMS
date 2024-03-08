<template>
  <div>
    <app-layout>
      <app-breadcrumb
        :title="page.title"
        :links="breadcrumbs"
      ></app-breadcrumb>

      <div class="row my-3 dashboard align-items-end">
        <div class="col-xl-8" v-if="region_list.length>1 && !['SELLER'].includes(_role)">
          <span class="p-float-label">
            <p-multiselect
              class="w-100 mw-100"
              inputId="region_list"
              :options="region_list"
              optionLabel="label"
              optionValue="region_code"
              display="chip"
              v-model="params.selected_regions"
              inputClass="form-control shadow-none"
              appendTo="body"
            />
            <label for="region_list">Region</label>
          </span>
        </div>
        <div class="col-xl-8" v-else-if="_auth_region.length>0 && !['SELLER'].includes(_role)">
          <h2 class="my-0 ms-1 text-primary fw-semibold">{{ _auth_region[0].label }}</h2>
        </div>
        
        <div class="col-xl-4 d-flex justify-content-end ms-auto">
          <div class="group-input d-inline-flex flex-nowrap flex-xs-wrap justify-content-end">
            <div class="p-float-label p-input-icon-right">
              <p-datepicker
                inputId="start_date"
                inputClass="form-control shadow-none"
                v-model="params.start_date"
                :maxDate="_max_date"
              />
              
              <i class="pi pi-calendar"></i>
              <label>Start date</label>
            </div>
            <div class="p-float-label p-input-icon-right">
              <p-datepicker
                inputId="end_date"
                inputClass="form-control shadow-none"
                v-model="params.end_date"
                :maxDate="_max_date"
              />
              <i class="pi pi-calendar"></i>
              <label> End date</label>
            </div>
            <button class="btn bg-primary-subtle text-primary input-group-append" @click="dialog.show=!0">
              <i class="fe-filter"></i>
            </button>
            <!-- <button class="btn bg-primary text-white input-group-append">
              <i class="fe-search"></i>
            </button> -->
          </div>
        </div>
      </div>

      <div class="row dashboard">
        <div class="col-xl-8">
          <div class="card h-100 overflow-hidden">
            <div class="card-body p-0 d-flex flex-column">
              <div class="flex-1 row g-0" v-if="!['SELLER'].includes(_role)">
                <div class="col-md-4">
                  <div class="stats-item height-100 card shadow-none border-end-md border-bottom rounded-0 mb-0">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="icon position-relative text-primary rounded-1 text-center">
                          <i class="fe-user-check"></i>
                        </div>
                        <div class="title text-end">
                          <span class="d-block fw-bold text-dark-emphasis mb-5x"> VAT Sellers </span>
                          <div class="d-flex align-items-center justify-content-end">
                            <h2 class="fw-black mb-0 me-10x text-dark">₱<count-to :startVal='0' :endVal='Number(data.sellers_baseprice.active_witheld)' :duration='1500' :decimals="2"></count-to></h2>
                          </div>
                          <span class="d-block fw-medium text-dark-emphasis mt-5x">Count: <span class="fw-bold text-dark"><count-to :startVal='0' :endVal='Number(data.sellers.active_witheld)' :duration='1500'></count-to></span></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="stats-item height-100 card shadow-none border-end-md border-bottom rounded-0 mb-0">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="icon position-relative text-success rounded-1 text-center">
                          <i class="fe-user-plus"></i>
                        </div>
                        <div class="title text-end">
                          <span class="d-block fw-bold text-dark-emphasis mb-5x">
                             Above 500K / Annum <span class="badge bg-success"> New</span>
                            </span> 
                          <div class="d-flex align-items-center justify-content-end">
                            <h2 class="fw-black mb-0 me-10x text-dark">₱<count-to :startVal='0' :endVal='Number(data.sellers_baseprice.eligible_witheld)' :duration='1500' :decimals="2"></count-to></h2>
                          </div>
                          <span class="d-block fw-medium text-dark-emphasis mt-5x">Count: <span class="fw-bold text-dark"><count-to :startVal='0' :endVal='Number(data.sellers.eligible_witheld)' :duration='1500'></count-to></span></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="stats-item height-100 card shadow-none border-end-md border-bottom rounded-0 mb-0">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="icon position-relative text-danger rounded-1 text-center">
                          <i class="fe-user"></i>
                        </div>
                        <div class="title text-end">
                          <span class="d-block fw-bold text-dark-emphasis mb-5x"> Below 500K / Annum </span>
                          <div class="d-flex align-items-center justify-content-end">
                            <h2 class="fw-black mb-0 me-10x text-dark">₱<count-to :startVal='0' :endVal='Number(data.sellers_baseprice.non_witheld)' :duration='1500' :decimals="2"></count-to></h2>
                          </div>
                          <span class="d-block fw-medium text-dark-emphasis mt-5x">Count: <span class="fw-bold text-dark"><count-to :startVal='0' :endVal='Number(data.sellers.non_witheld)' :duration='1500'></count-to></span></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex-1 row g-0">
                <div class="col-md-4">
                  <div class="stats-item height-100 card shadow-none border-end-md border-bottom rounded-0 mb-0">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="icon position-relative text-info rounded-1 text-center">
                          <i class="fe-shopping-cart"></i>
                        </div>
                        <div class="title text-end">
                          <span class="d-block fw-bold text-dark-emphasis mb-5x"> Online Platform VAT </span>
                          <div class="d-flex align-items-center justify-content-end">
                            <h2 class="fw-black mb-0 me-10x text-dark"><count-to :startVal='0' :endVal='Number(online_platform_vat)' :duration='1500' ::decimals="2"></count-to></h2>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="stats-item height-100 card shadow-none border-end-md border-bottom rounded-0 mb-0" v-tooltip.top="page.tooltip ? shipping_computation() : ''">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="icon position-relative text-purple rounded-1 text-center">
                          <i class="fe-truck"></i>
                        </div>
                        <div class="title text-end">
                          <span class="d-block fw-bold text-dark-emphasis mb-5x">
                            Shipping VAT 
                          </span>
                          <div class="d-flex align-items-center justify-content-end">
                            <h2 class="fw-black mb-0 me-10x text-dark"><count-to :startVal='0' :endVal='Number(shipping_vat)' :duration='1500' ::decimals="2"></count-to></h2>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="stats-item height-100 card shadow-none border-end-md border-bottom rounded-0 mb-0" v-tooltip.top="page.tooltip ? `Item VAT: ${formatNumber(item_vat)}` : ''">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="icon position-relative text-warning rounded-1 text-center">
                          <i class="fe-package"></i>
                        </div>
                        <div class="title text-end">
                          <span class="d-block fw-bold text-dark-emphasis mb-5x"> Item VAT </span>
                          <div class="d-flex align-items-center justify-content-end">
                            <h2 class="fw-black mb-0 me-10x text-dark"><count-to :startVal='0' :endVal='item_vat_compute()' :duration='1500' ::decimals="2"></count-to></h2>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-2">
          <div class="widget-rounded-circle card bg-purple shadow-none overflow-hidden" v-tooltip.top="page.tooltip ? `Total Witholding Tax: ${formatNumber(withholding_tax)}` : ''">
            <div class="card-body">
              <div class="row">
                <div class="position-absolute avatar-lg rounded-circle bg-soft-light background-icon">
                  <i class="fe-percent font-28 avatar-title text-white"></i>
                </div>
                <div class="col-12">
                  <div class="text-end">
                    <h2 class="text-white mt-2">
                      <!-- <span data-plugin="counterup">{{ formatNumber(transactions) }}</span> -->
                      <count-to :startVal='0' :endVal='Number(withholdingtax())' :duration='1500' ::decimals="2"></count-to>
                    </h2>
                    <p class="text-white mb-0 text-truncate">Total Witholding Tax (1%)</p>
                  </div>
                </div>
              </div> <!-- end row-->
            </div>
          </div>
          <div class="widget-rounded-circle card bg-info shadow-none overflow-hidden" v-tooltip.top="page.tooltip ? `Total Taxes Due: ${formatNumber(tax)}` : ''">
            <div class="card-body">
              <div class="row">
                <div class="position-absolute avatar-lg rounded-circle bg-soft-light background-icon">
                  <i class="fe-percent font-28 avatar-title text-white"></i>
                </div>
                <div class="col-12">
                  <div class="text-end">
                    <h2 class="text-white mt-2">
                      <!-- <span data-plugin="counterup">{{ formatNumber(transactions) }}</span> -->
                      <count-to :startVal='0' :endVal='Number(tax_compute())' :duration='1500' ::decimals="2"></count-to>
                    </h2>
                    <p class="text-white mb-0 text-truncate">Total Taxes Due</p>
                  </div>
                </div>
              </div> <!-- end row-->
            </div>
          </div>
        </div>
        <div class="col-xl-2">
          <div class="card h-100">
              <div class="card-body d-flex flex-column justify-content-center">
                <div class="avatar-md">
                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-2">
                        <i class="fe-bar-chart-line-"></i>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="d-block fw-bold text-dark-emphasis mb-1 fs-16x">Transactions</p>
                    <h1 class="fw-black mb-0 text-dark fs-32x"><span class="counter-value"><count-to :startVal='0' :endVal='Number(transactions)' :duration='1500'></count-to></span></h1>
                </div>
              </div><!-- end card body -->
            </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <div class="dropdown float-end">
              </div>
              <h4 class="header-title mb-0">Statistics</h4>

              <div dir="ltr">
                <div id="apex-area-charts" class="mt-0"></div>
                <apexchart
                  ref="statistics"
                  type="area"
                  class="mt-0"
                  height="285"
                  :options="statistics_graph.options"
                  :series="statistics_graph.series"
                ></apexchart>
              </div>
            </div>
          </div> <!-- end card -->
        </div> 

        <div class="col-lg-4 d-flex flex-column">
          <div class="card flex-1" v-if="!auth_role_dsp()">
              <div class="card-body d-flex align-items-center">
                  <div class="d-flex justify-content-between w-100">
                      <div class="bg-icon avatar-lg text-center bg-light rounded-circle">
                          <img class="avatar-title" src="images/icons/organization.png" alt="">
                      </div>
                      <div class="text-end">
                          <p class="text-uppercase">Active Digital Service Provider</p>
                          <h1 class="text-dark fw-bold mb-0"><span data-plugin="counterup">{{active_dsp}}</span></h1>
                      </div>
                  </div>
              </div>
          </div>

          <div class="card flex-1">
            <div class="card-body">
              <h4 class="header-title mb-3">Service Charge</h4>
              <div class="position-relative">
                <div class="row align-items-center">
                  <div class="col-xl-6">
                    <span>Transaction Fee <b>-</b> <span class="">{{formatNumber(getServiceCharge("transaction"),false)}}%</span></span>
                  </div>
                  <div class="col-xl-6">
                    <p-progress-bar class="fill-info" :value="getServiceCharge('transaction')" :showValue="false" style="height: .5em" />
                    <!-- <div class="progress" style="height: 6px;">
                      <div class="progress-bar bg-info" role="progressbar" :style="{width:getServiceCharge('transaction')+'%'}" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div> -->
                  </div>
                </div>

                <div class="row align-items-center mt-2">
                  <div class="col-xl-6">
                    <span>Service Fee <b>-</b> <span class="">{{formatNumber(getServiceCharge("service"),false)}}%</span></span>
                  </div>
                  <div class="col-xl-6">
                    <p-progress-bar class="fill-warning" :value="getServiceCharge('service')" :showValue="false" style="height: .5em" />
                    <!-- <div class="progress" style="height: 6px;">
                      <div class="progress-bar bg-warning" role="progressbar" :style="{width:getServiceCharge('service')+'%'}" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div> -->
                  </div>
                </div>

                <div class="row align-items-center mt-2">
                  <div class="col-xl-6">
                    <span>Commission Fee <b>-</b> <span class="">{{formatNumber(getServiceCharge("commission"),false)}}%</span></span>
                  </div>
                  <div class="col-xl-6">
                    <p-progress-bar class="fill-danger" :value="getServiceCharge('commission')" :showValue="false" style="height: .5em" />
                    <!-- <div class="progress" style="height: 6px;">
                      <div class="progress-bar bg-danger" role="progressbar" :style="{width:getServiceCharge('commission')+'%'}" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row pb-5">
        <div class="col-xl-6" :class="{'col-xl-9':auth_role_dsp()}">
          <div class="card h-100">
            <div class="card-body">
              <div class="dropdown float-end">
              </div>
              
              <h4 class="header-title mb-3">Top Sellers</h4>

              <div class="table-responsive">
                <table class="normal-border table table-hover table-centered mb-0 p-datatable-table w-100 table-sm">
                  <thead>
                    <tr>
                      <th>Seller</th>
                      <!-- <th>Tin</th> -->
                      <th>Type</th>
                      <th>Seller Type</th>
                      <th>COR</th>
                      <th>Gross Sales</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(data, index) of data.top_sellers" :key="index">
                      <td>
                        <div class="d-flex align-items-center">
                          <p class="mb-0">{{ data.registered_name }}</p>
                        </div>
                      </td>
                      <!-- <td>{{ data.tin }}</td> -->
                      <td>
                        <span class="badge bg-light text-dark" v-if="data.eligible_witheld_seller=='NONE'">
                          Below 500k/Annum
                        </span>
                        <span class="badge bg-success" v-else-if="data.eligible_witheld_seller=='ACTIVE'">
                          VAT Seller
                        </span> 
                        <span class="badge bg-warning" v-else-if="['ELIGIBLE'].includes(data.eligible_witheld_seller)">
                         Above 500k/Annum
                        </span>
                      </td>
                      <td><span class="badge bg-info">{{data.type=='CORP' ? 'Non' : ''}} Individual</span></td>
                      <td>
                        <span class="badge bg-primary" v-if="data.has_cor">Submitted</span>
                      </td>
                      <td class="text-right">{{ formatNumber(data.base_price,2) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- <div class="card h-100">
            <div class="card-body">
              <div class="dropdown float-end">
              </div>
              
              <h4 class="header-title mb-3">Recent Transaction</h4>

              <div class="table-responsive">
                <table class="normal-border table table-hover table-centered mb-0 p-datatable-table w-100 table-sm">
                  <thead>
                    <tr>
                      <th>Service Provider</th>
                      <th>Transaction ID</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Tax</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(data, index) of data.transactions" :key="index">
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar-sm me-2">
                            <img :src="data.logo" class="img-fluid rounded-circle"/>
                          </div>
                          <p class="mb-0">{{ data.company_name }}</p>
                        </div>
                      </td>
                      <td>{{ data.trans_id }}</td>
                      <td class="text-right">{{ formatNumber(data.total_amount) }}</td>
                      <td>
                        <span class="badge" :class="statusClass(data.status, 'badge-outline')">
                            {{ data.status }}
                          </span>
                        </td>
                      <td class="text-right">{{ formatNumber(data.tax) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div> -->
        </div>

        <div class="col-xl-3 col-md-6" v-if="!auth_role_dsp()">
          <div class="card h-100">
            <div class="card-body">
              <div class="dropdown float-end">
              </div>
              <h4 class="header-title mb-3">Top Service Provider</h4>

              <div class="align-items-center border-bottom border-light pb-2 mb-1" v-for="(item, index) in top_providers" :key="index">
                <h3 class="float-end my-2 py-1">{{ abbreviateNumber(parseInt(item.total_tax))}}</h3>
                <div class="d-flex align-items-center">
                  <div class="avatar-md rounded-circle bg-soft-info">
                    <img class="avatar-title" :src="item.logo" alt=""/>
                  </div>

                  <div class="ms-2">
                    <h5 class="mb-1 mt-0 fw-bold">{{item.company_name}}</h5>
                    <p class=" mb-0">Records: {{abbreviateNumber(parseInt(item.total_transaction))}}</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="dropdown float-end">
              </div>

              <h4 class="header-title mb-3">Transactions counts per status</h4>
              <div class="text-center mb-4">
                <apexchart
                  ref="status_graph"
                  type="pie"
                  class="apex-charts"
                  height="205"
                  width="205"
                  :options="status_graph.options"
                  :series="status_graph.series"
                ></apexchart>
              </div>
              <div
                class="row align-items-center justify-content-center mb-1"
                v-for="(item, index) in status_graph.gen_data.labels"
                :key="index"
              >
                <div class="col-6 font-10">
                    <span
                      class="badge d-iflex align-items-center justify-content-center line-height-1 minw-20 me-1"
                      :class="statusClass(item.toUpperCase())"
                    >
                      {{ formatNumber(status_graph.series[index]) }}
                    </span>
                    <span class="text-capitalize ">{{ item }}</span>
                </div>
                <div class="col-6">
                    <p-progress-bar :class="'fill-'+statusColor(item.toUpperCase())" :value="getStatusPercentage(item)" :showValue="false" style="height: .7em" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </app-layout>

    <p-dialog
      class="filter-modal"
      v-model:visible="dialog.show"
      :style="{
        'width': '100%',
        'max-width': '600px'
      }"
      modal
    >
      <template #header>
        <h3 v-text="dialog.title"></h3>
      </template>

      <div>
        <!-- <div class="field" v-if="region_list.length>1 && !['SELLER'].includes(_role)">
          <span class="p-float-label">
            <p-multiselect
              class="w-100 mw-100"
              inputId="region_list"
              :options="region_list"
              optionLabel="label"
              optionValue="region_code"
              display="chip"
              v-model="params.selected_regions"
              inputClass="form-control shadow-none"
              appendTo="body"
            />
            <label for="region_list">Region</label>
          </span>
        </div> -->

        <div class="field">
          <span class="p-float-label flex-grow-1">
            <p-dropdown
                v-model="params.type"
                :options="types"
                optionLabel="name"
                optionValue="val"
                class="w-full"
            />
            <label>Type</label>
          </span>
        </div>

        <div v-if="!['SELLER'].includes(_role)">
          <div class="field">
            <span class="p-float-label">
              <p-dropdown
                v-model="params.seller_type"
                :options="seller_type"
                optionLabel="name"
                optionValue="val"
                class="w-full"
              />
              <label>Seller Type</label>
            </span>
          </div>

          <div class="field">
            <span class="p-float-label pl-0">
              <p-multiselect
                class="w-100 mw-100"
                inputId="company_name"
                :options="dsp_list"
                optionLabel="company_name"
                optionValue="id"
                display="chip"
                v-model="params.selected_dsp"
                inputClass="form-control shadow-none"
                appendTo="body"
              />
              <label for="company_name">Service Provider</label>
            </span>
          </div>

          <div class="field">
            <span class="p-float-label pl-0">
              <p-multiselect
                class="w-100 mw-100"
                inputId="name"
                :options="eligible_witheld_seller"
                optionLabel="name"
                optionValue="val"
                display="chip"
                v-model="params.eligible_witheld_seller"
                inputClass="form-control shadow-none"
                appendTo="body"
              />
              <label for="name">Eligible Withholding Tax</label>
            </span>
          </div>

          <div class="field">
            <span class="p-float-label pl-0">
              <p-autocomplete
                class="w-100 mw-100"
                inputId="seller"
                v-model="params.seller"
                inputClass="form-control"
                :suggestions="filtered_seller"
                @complete="searchSeller($event)"
                value="id"
                field="business_name"
                :multiple="true"
                forceSelection
                appendTo="body"
              />
              <label for="seller">Seller</label>
            </span>
          </div>
        </div>
      </div>
    
      <template #footer>
        <p-button
          class="btn btn-outline-light min-width-100"
          label="Close"
          @click="dialog.show=!1"
        />
        <p-button
          class="btn btn-primary mx-0 min-width-100"
          label="Filter"
          @click="filterData"
        />
      </template>
    </p-dialog>
  </div>
</template>

<script>
import Graph from "../components/Graph.vue";
// import CalendarService  from '../services/calendar'
import DashboardService  from '../services/dashboard'
import DSPService  from '../services/service_provider';

export default {
  components: { Graph },
  computed: {
    active_dsp(){
      return this.getDSPStatusCount("ACTIVE");
    },
    inactive_dsp(){ 
      return this.getDSPStatusCount("INACTIVE");
    },
    transactions()
    {
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.total);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    transaction_fee()
    {
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.transaction_fee);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    service_fee()
    {
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.service_fee);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    commission_fee()
    {
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.commission_fee);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    online_platform_vat()
    {
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.online_platform_vat);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    shipping_vat()
    {
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.shipping_vat);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    item_vat()
    {
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.item_vat);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    tax()
    { 
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.tax);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    withholding_tax()
    { 
      if (this.data.service_providers.length>0)
      {
        return this.data.service_providers.map(e=>{
          return Number(e.withholding_tax);
        }).reduce((a, b) => a + b, 0);
      }
      return 0;
    },
    top_providers()
    {
      const item = this.data.service_providers.reduce((parent, item) => {
        const group = item.company_name;
        parent[group] = parent[group] || { total: 0, tax: 0 };
        parent[group].logo=item.logo;
        parent[group].total+=Number(item.total);
        parent[group].tax+=Number(item.tax);
        return parent;
      }, {});
      
      return Object.entries(item)
      .map((e,index)=>{ 
        return {
          company_name:e[0],
          logo:e[1].logo,
          total_transaction:e[1].total,
          total_tax:e[1].tax,
        }
      })
      .sort((a, b) => parseFloat(b.total_tax) - parseFloat(a.total_tax)).slice(0,5); 
    },
    paramDateDiff()
    {
      return this.dateDiff(this.params.end_date,this.params.start_date);
    },
    daterange() {
      return this.params.start_date && this.params.end_date;
    }
  },
  data() {
    return {
      dialog: {
        show: !1,
        title: "Advanced filter"
      },

      page: {
        title:"Dashboard",
        route:"dashboard",
        interval:null,
        tooltip:false,
      },
      breadcrumbs: [
        { current: false, title: 'Home', url: 'dashboard' }
      ],
      data:{
        dsp_count: [],
        service_providers: [],
        transactions:[],
        statistics:[],
        sellers:{active_witheld:0,eligible_witheld:0,non_witheld:0},
        sellers_baseprice:{active_witheld:0,eligible_witheld:0,non_witheld:0},
      },

      /* Apex charts */
      statistics_graph: {
        series: [],
          options: {
            chart: {
              type: 'area',
              height: 350,
              zoom: {
                enabled: false
              }
            },
            tooltip: {
            y: {
              formatter: function(val) {
                return Number(val).toLocaleString()
              },
              title: {
                formatter: function (seriesName) {
                  return `${seriesName.replace(/(^\w|\s\w)/g, m => m.toUpperCase())}:`
                }
              }
            }
          },
            dataLabels: {
              enabled: false
            },
            stroke: {
              curve: 'smooth'
            },
            
            title: {
              text: '',
              align: 'left'
            },
            // subtitle: {
            //   text: 'Price Movements',
            //   align: 'left'
            // },
            yaxis: {
              // opposite: true
            },
            legend: {
              horizontalAlign: 'left'
            }
          },
      },
      region_list:[],
      dsp_list:[],
      seller_type:[
        {name:"All",val:"all"},
        {name:"Non Individual",val:"CORP"},
        {name:"Individual",val:"INDIVIDUAL"}
      ],
      types:[
        {name:"All",val:"all"},
        {name:"Service",val:"Service"},
        {name:"Goods",val:"Product"}
      ],
      vat_types:[
        {name:"All",val:"all"},
        {name:"Non-VAT",val:"v"},
        {name:"VAT",val:"nv"}
      ],
      eligible_witheld_seller:[
        {name:"Below 500k / Annum",val:"NONE"},
        {name:"Above 500k / Annum",val:"ELIGIBLE"},
        {name:"VAT Sellers",val:"ACTIVE"}
      ],
      params:{
        selected_regions:[],
        selected_dsp:[],
        eligible_witheld_seller:["NONE","ELIGIBLE","ACTIVE"],
        seller_type:"all",
        type:"all",
        vat_type:"all",
        seller: null,
        start_date:new Date(),
        end_date:new Date()
      },
      status_graph: {
        series: [],
        gen_data:{colors:[],labels:[]},
        options: {
          labels:[],
          chart: {
            type: 'donut',
            sparkline: {
              enabled: true
            },
          },
          tooltip: {
            y: {
              formatter: function(val) {
                return Number(val).toLocaleString()
              },
              title: {
                formatter: function (seriesName) {
                  return `${seriesName.replace(/(^\w|\s\w)/g, m => m.toUpperCase())}:`
                }
              }
            }
          },
          dataLabels: {
            enabled: true,
          },
          legend: {
              position: 'bottom'
          },
          
        }
      },

      seller:[],
      filtered_seller: []
    }
  },
  
  methods:{
    regionList() {
      DashboardService.regions()
      .then((response) => {
        // this.region_list = [{id:"",name:"NA",region_code:"",region_desc:""},...response.data.data];
        this.region_list = [...response.data.data];
        this.params.selected_regions = [...response.data.data.map(e=>{return e.region_code})];
      })
      .catch((errors) => {
          try { 
            this.getError(errors);
          }
          catch(ex){ console.log(ex)}
      })
      .finally(() => {});
    },
    dspList() {
      DSPService.list( { page_size:-1 })
      .then((response) => {
        this.dsp_list = response.data.data;
        this.params.selected_dsp = response.data.data.map(e=>{return e.id});
      })
      .catch((errors) => {
          try { 
            this.getError(errors);
          }
          catch(ex){ console.log(ex)}
      })
      .finally(() => {});
    },
    updateData() {
      let payload = {...this.params}
      // console.log(payload)
      payload.start_date =  this.formatDate(payload.start_date,3);
      payload.end_date =  this.formatDate(payload.end_date,3);
      DashboardService.data(payload).then((res) => {
        this.data = res.data.data;
        this.genGraph();
      });
    },
    getServiceCharge(type){
      let sum = this.transaction_fee + this.service_fee + this.commission_fee;
      if (type=="transaction") return (this.transaction_fee/sum) * 100;
      else if (type=="service") return (this.service_fee/sum) * 100;
      else return (this.commission_fee/sum) * 100;
    },

    async genGraph() {

      const regions = [...new Set(this.data.statistics.map(e=>{return (e.region_name) }))]
      .map(e=>{
        let data = this.data.statistics.filter(f=>f.region_name==e).map(item=>{
          return {x:this.formatDate(item.date,1),y:item.total,date:item.date}
        })
        let region_code = this.data.statistics.filter(r=>r.region_name==e)[0].region_code;
        let days = (this.paramDateDiff) ? this.paramDateDiff*-1 : -6;
        const startDate = this.addDay(this.params.end_date,days);
        const endDate = this.params.end_date;

        for (let currentDate = new Date(startDate); currentDate <= endDate; currentDate.setDate(currentDate.getDate() + 1)) {
          let curDate = this.formatDate(currentDate,3)
          if(!data.some((d) => d.date === curDate)) data.push({x:this.formatDate(currentDate,1),y:0,date:curDate})
        }
        return {name:(e) ? e : "NA",data:data.sort((a, b) => new Date(a.date) - new Date(b.date)),region_code:region_code}
      })
      .sort(function (a, b) {
          return a.region_code - b.region_code;
      });

      // console.log(regions)

      this.statistics_graph.series = regions;

      let statuses = [
        {name:"pending",color:"#ff9800"},
        {name:"ongoing",color:"#348cd4"},
        {name:"delivered",color:"#45bbe0"},
        {name:"cancelled",color:"#f7531f"},
        {name:"refunded",color:"#6c757d"},
        {name:"completed",color:"#78c350"},
      ];


      let status_graph_data = [];
      statuses.forEach(item => {
        status_graph_data.push({
            name:item.name,
            color:item.color,
            data:this.getStatusCount(item.name)
          });
      });
      this.status_graph.series = status_graph_data.map((e)=>{return e.data});
      this.status_graph.gen_data = {
        colors:status_graph_data.map((e)=>{return e.color}),
        labels:status_graph_data.map((e)=>{return e.name}),
      }
      this.status_graph.options.labels = status_graph_data.map((e)=>{return e.name})
      this.$refs.status_graph.updateOptions(this.status_graph.gen_data); // manual update chart
      // console.log(this.status_graph)
    },

    getStatusCount(status) {
      return this.data.service_providers.map(e=>{
        return e[status];
      }).reduce((a, b) => a + b, 0);
    },

    getStatusPercentage(status) {
      let total = this.data.service_providers.map(e=>{
        return e.pending+e.ongoing+e.delivered+e.completed+e.cancelled+e.refunded;
      }).reduce((a, b) => a + b, 0);

      let data = this.data.service_providers.map(e=>{
        return e[status];
      }).reduce((a, b) => a + b, 0);

      return ((data/total)* 100)
    },

    getDSPStatusCount(status) {
      let dsp = this.data.dsp_count.filter(e=>e.status==status);
      return (dsp.length>0) ? dsp[0].total : 0;
    },
    
    busListener(){
			// this.emitter.on("new_transaction", val=>{
			// 	this.updateData();
      // });
		},

    getSellers(){
      let param = {}
      DashboardService.seller(param).then((res) => {
        this.seller = res.data.data;
      });
    },

    searchSeller(event) {
      // this.filtered_seller = [{business_name:"Urban",id:1},{business_name:"Test",id:2}];
      // this.filtered_seller = [...this.seller];
      setTimeout(() => {
        if (!event.query.trim().length) {
          this.filtered_seller = [...this.seller];
        }
        else {
          this.filtered_seller = this.seller.filter((item) => {
              return item.business_name.toLowerCase().startsWith(event.query.toLowerCase());
          });
        }
      }, 1000);
    },

    filterData() {
      this.dialog.show = !1;
      this.updateData();
    },

    withholdingtax(){
      return (Number(this.data.sellers_baseprice.active_witheld) + Number(this.data.sellers_baseprice.eligible_witheld)) * 0.005;
    },

    tax_compute(){
      // return (Number(this.data.sellers_baseprice.active_witheld) + Number(this.data.sellers_baseprice.eligible_witheld)) * 0.005;
      // let num = (
      //     Number(this.data.sellers_baseprice.active_witheld) 
      //   + Number(this.data.sellers_baseprice.eligible_witheld) 
      //   + Number(this.data.sellers_baseprice.non_witheld)
      // );
      // let div = num/1.12;
      // return div * 0.12;

      // return this.item_vat_compute() + this.online_platform_vat + this.shipping_vat + this.withholdingtax();
      return this.item_vat_compute() + this.shipping_vat;
    },

    item_vat_compute(){
      let total = (
          Number(this.data.sellers_baseprice.active_witheld) 
        + Number(this.data.sellers_baseprice.eligible_witheld) 
        + Number(this.data.sellers_baseprice.non_witheld)
      );
      let div = total/1.12;
      return div * 0.12;

      // console.log(`num${num},div:${div}`)
      return total * 0.12;
    },

    shipping_computation(){

      let net = this.shipping_vat / 0.12;
      let gross = net + this.shipping_vat;
      // Net Shipping Cost = Tax Amount / 0.12
      // P10,303.58 = P1,236.43 / 0.12

      // Gross Shipping Cost = Net + Tax Amount
      // P11,540.01 = P10,303.58 + P1,236.43
      return `Net Shipping: ${this.formatNumber(net)}\nGross Shipping: ${this.formatNumber(gross)}`;
      return {net:net,gross:gross};
    }
  },

  watch: {
    // params: {
    //     handler(val){
    //         this.updateData();
    //     },
    //     deep: true,
    //     immediate: true
    // }

    daterange() {
      this.updateData();
    },

    "params.selected_regions": function() {
      this.updateData();
    }
  },

  mounted(){
    if(this._is_auth_bir) {
      this.page.title = "BIR Dashboard";
    }
    
    this.breadcrumbs.push({
      current: true,
      title: this.page.title,
      url: `${this.page.route}`
    });

    
    this.regionList();
    this.dspList();
    this.getSellers();

    this.updateData();

    // this.page.interval = setInterval(() => {
    //   this.updateData();
    // }, 5000);

    this.busListener();
  },

  beforeDestroy() {
    this.emitter.off("new_transaction");
  },

  beforeUnmount() {
    clearInterval(this.page.interval);
  }
};
</script>
<style scoped>
  .dashboard {
    --tb-primary-rgb: 55,98,234;
    --tb-secondary-rgb: 30,26,34;
    --tb-success-rgb: 45,203,115;
    --tb-info-rgb: 74,176,193;
    --tb-warning-rgb: 246,183,73;
    --tb-danger-rgb: 255,108,108;
    --tb-light-rgb: 238,240,247;
    --tb-dark-rgb: 15,17,20;
    --tb-primary-bg-subtle: #e1e7fc;
    --tb-secondary-bg-subtle: #ddddde;
    --tb-success-bg-subtle: #e0f7ea;
    --tb-info-bg-subtle: #e4f3f6;
    --tb-warning-bg-subtle: #fef4e4;
    --tb-danger-bg-subtle: #ffe9e9;
    --tb-light-bg-subtle: #f9fbfc;
    --tb-dark-bg-subtle: #e9ebec;
    --splash-dark-emphasis-color: #9c9ab6;
  }

  .mb-5 {
    margin-bottom: 5px!important;
  }

  .fw-black {
    font-weight: 900;
  }

  .me-10x {
    margin-right: 10px!important;
  }

  .mt-5x {
    margin-top: 5px!important;
  }

  .flex-1 {
    flex: 1;
  }
  .fs-32x {
    font-size: 25px;
  }

  .fs-25x {
    font-size: 25px;
  }

  .fs-16x {
    font-size: 16px;
  }

  .text-dark-emphasis {
    color: var(--splash-dark-emphasis-color)!important;
  }

  .bg-primary-subtle {
    background-color: var(--tb-primary-bg-subtle)!important;
  }

  .bg-secondary-subtle {
    background-color: var(--tb-secondary-bg-subtle)!important;
  }

  .bg-info-subtle {
    background-color: var(--tb-info-bg-subtle)!important;
  }

  .bg-success-subtle {
    background-color: var(--tb-success-bg-subtle)!important;
  }

  .bg-warning-subtle {
    background-color: var(--tb-warning-bg-subtle)!important;
  }

  .bg-danger-subtle {
    background-color: var(--tb-danger-bg-subtle)!important;
  }

  .bg-dark-subtle {
    background-color: var(--tb-dark-bg-subtle)!important;
  }

  .bg-light {
    --tb-bg-opacity: 1;
    /* background-color: rgba(var(--tb-light-rgb),var(--tb-bg-opacity))!important; */
  }

  .stats-item .icon {
    width: 78px;
    height: 78px;
    font-size: 32px;
    background: #f8f8fb;
  }

  .stats-item .icon i {
    left: 0;
    right: 0;
    top: 50%;
    line-height: 1;
    margin-top: 1.5px;
    position: absolute;
    transform: translateY(-50%);
  }

  .background-icon {
    opacity: 0.2;
    transform: scale(2.5);
  }

  .stats-item.height-100 {
    height: 100%;
  }

  .min-width-100 {
    min-width: 100px;
  }

  @media (min-width: 768px){
    .border-end-md {
        border-right: 1px solid var(--ct-border-color)!important;
    }
  }

.p-dialog .p-dialog-header {
    border-bottom: 0 none;
    background: #ffffff;
    color: #495057;
    padding: 1.5rem;
    border-top-right-radius: 3px;
    border-top-left-radius: 3px;
}
</style>