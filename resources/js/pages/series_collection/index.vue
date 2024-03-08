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
              <div class="d-flex flex-wrap align-items-center mt-3 mb-3">
                <div class="col-12 col-md-6 mb-2">
                  <p-button
                    class="btn btn-primary"
                    label="Create"
                    icon="pi pi-plus"
                    @click="createItem"
                  />
                </div>
                <div class="col-12 col-md-3 ms-auto">
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
                </div>
              </div>

              <div class="table-responsive mb-3">
                <p-table
                  tableClass="table table-fit-column-1"
                  :rowHover="true"
                  :loading="table.loading"
                  :value="table.data"
                  showGridlines
                  responsiveLayout="scroll"
                >
                    <template #empty>No records found.</template>
                    <template #loading>Loading data. Please wait.</template>
                    <p-column header="Service Provider">
                      <template #body="{data}">
                        <span>{{ data.service_provider.company_name }}</span>
                      </template>
                    </p-column>
                    <p-column header="Range">
                      <template #body="{data}">
                        <span>{{ getSeriesFormat(data) }} - {{ getSeriesFormat(data,true) }}</span>
                      </template>
                    </p-column>
                    <p-column field="prefix" header="Prefix"></p-column>
                    <p-column field="suffix" header="Suffix"></p-column>
                    <p-column header="Starting No">
                      <template #body="{data}">
                        <span>{{ formatNumber(data.starting_no) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Ending No">
                      <template #body="{data}">
                        <span>{{ formatNumber(data.ending_no) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Length">
                      <template #body="{data}">
                        <span>{{ formatNumber(data.length) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Total Success">
                      <template #body="{data}">
                        <span>{{ formatNumber(data.total_success) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Total Failed">
                      <template #body="{data}">
                        <span>{{ formatNumber(data.total_failed) }}</span>
                      </template>
                    </p-column>
                    <p-column header="Total">
                      <template #body="{data}">
                        <span>{{ formatNumber(data.total) }}</span>
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
                    <p-column header="Date & Time">
                      <template #body="{data}">
                        {{ formatDateTime(data.created_at) }}
                      </template>
                    </p-column>
                    <p-column header="Processed At">
                      <template #body="{data}">
                        {{ formatDateTime(data.processed_at) }}
                      </template>
                    </p-column>
                    <p-column header="Completed At">
                      <template #body="{data}">
                        {{ formatDateTime(data.completed_at) }}
                      </template>
                    </p-column>
                </p-table>
                <p-paginator :rows="10" :totalRecords="table.total" @page="onPage($event)"></p-paginator>
              </div>
            </template>
          </p-card> 
        </div> 
      </div>
    </app-layout>

    <p-dialog
      class="whitelist-modal modal"
      v-model:visible="dialog.show"
      :style="{
        width: '100%',
        'max-width': '500px'
      }"
      modal
    >
      <template #header>
        <h3 v-text="dialog.title"></h3>
      </template>
      <v-form ref="form" tag="form" class="row rg-15">

        <v-field as="div" class="col-12" slim name="Service Provider" vid="service_provider_id" v-slot="{ errors }">
          <div class="p-float-label">
            <p-dropdown
              appendTo="body"
              v-model="dialog.data.service_provider_id"
              :options="dsp_list"
              optionLabel="company_name"
              optionValue="id"
              class="w-full"
              :class="{'p-invalid':errors[0]}"
            />
            <label>Service Provider</label>
          </div>
          <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="col-md-6 col-12" slim name="prefix" vid="prefix" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    type="text"
                    v-model="dialog.data.prefix"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{'p-invalid':errors[0]}"
                />
                <label>Prefix</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="col-md-6 col-12" slim name="suffix" vid="suffix" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    type="text"
                    v-model="dialog.data.suffix"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{'p-invalid':errors[0]}"
                />
                <label>Suffix</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="col-md-6 col-12" slim name="starting_no" vid="starting_no" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    type="number"
                    v-model="dialog.data.starting_no"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{'p-invalid':errors[0]}"
                />
                <label>Series From</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="col-md-6 col-12" slim name="ending_no" vid="ending_no" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    type="number"
                    v-model="dialog.data.ending_no"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{'p-invalid':errors[0]}"
                />
                <label>Series To</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <span class="label-title">For Leading Zeros</span>
        <v-field as="div" class="col-md-6 col-12" slim name="length" vid="length" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    type="number"
                    v-model="dialog.data.length"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{'p-invalid':errors[0]}"
                />
                <label class="">Series Length</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <div class="col-12 text-center">
            <span style="font-size:20px;font-weight: 600; display: block;">Sample Output</span>
            <span class="label-title">Company Code + Prefix + Series + Suffix</span>
            <h3>{{ sampleOutput }}</h3>
        </div>

      </v-form>
      <template #footer>
        <p-button
          class="btn btn-outline-light"
          label="Cancel"
          :disabled="dialog.loading"
          @click="dialog.show=false"
        />
        <p-button
          class="btn btn-primary mx-0"
          label="Upload"
          @click="submit"
           v-if="!dialog.loading"
        />
        <button v-else  class="btn btn-primary mx-0" disabled>
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> 
            Loading...
        </button>
      </template>
    </p-dialog>

    <p-toast />
    <p-confirm></p-confirm>
  </div>
</template>
<script>

import SeriesCollectionService  from '../../services/series_collection';
import ImageUpload from '../../components/FileUpload';
import DSPService  from '../../services/service_provider';
export default {
  components: {ImageUpload},
  data() {
    return {
      page: {
        title: "Series Collection",
        route: "series_collection",
        interval: null
      },
      breadcrumbs: [
        { current: false, title: 'Home', url: 'dashboard' }
      ],
      table: {
          params: {
              search: "",
              page: 1,
          },
          loading: false,
          data: [],
          total: 0,
          total_pages: 0,
      },
      dialog: {
        title: "",
        show:false,
        loading:false,
        data: new Form({
          prefix: "",
          suffix: "",
          starting_no:"",
          ending_no:"",
          length:"",
          service_provider_id:null,
        })
      },
      dsp_list:[],
      isLoading: false,
    }
  },

  computed: {
    sampleOutput() {
      if(this.dialog.data.service_provider_id)
      {
        let dsp_code = this.dsp_list.filter(e=>e.id==this.dialog.data.service_provider_id)[0].company_code;
        let length = this.dialog.data.length;
        let startingStr = this.padWithZeros(this.dialog.data.starting_no, length);
        let endingStr = this.padWithZeros(this.dialog.data.ending_no, length);
        let start = dsp_code + this.dialog.data.prefix + startingStr + this.dialog.data.suffix;
        let end = dsp_code + this.dialog.data.prefix + endingStr + this.dialog.data.suffix;
        return `${start.toUpperCase()} - ${end.toUpperCase()}`
      }
      else
      {
        return "---";
      }
    }
  },

  watch: {
    "table.params.search": function (val) {
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
    this.updateTable();
    this.dspList();
  },

  methods: {
    onPage(val){
      this.table.params.page = val.page+1;
      this.updateTable();
    },
    setFile(base64) {
        this.dialog.data.file = base64;
    },
    dspList() {
      DSPService.list( { page_size:-1 })
      .then((response) => {
        this.dsp_list = response.data.data;
        // this.dialog.data.service_provider_id = response.data.data.map(e=>{return e.id});
      })
      .catch((errors) => {
        console.log(errors)
          try { 
            this.getError(errors);
          }
          catch(ex){ console.log(ex)}
      })
      .finally(() => {});
    },
    updateTable(Loading=true) {
      if (this.table.loading) return;
      this.table.loading = Loading;

      SeriesCollectionService.list(this.table.params)
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
    getSeriesFormat(data,end=false){
      let dsp_code = data.company_code;
      let length = data.length;
      let startingStr = this.padWithZeros(data.starting_no, length);
      let endingStr = this.padWithZeros(data.ending_no, length);
      let start_series = dsp_code + data.prefix + startingStr + data.suffix;
      let end_series = dsp_code + data.prefix + endingStr + data.suffix;
      return (end) ? end_series : start_series;
    },
    createItem() {
      this.dialog.data.reset();
      this.dialog.data.service_provider_id = (this.dsp_list.length>=2) ? this.dialog.data.service_provider_id : this.dsp_list.map(e=>{return e.id})[0];
      this.dialog.title = "Create";
      this.dialog.show = true;
    },

    async submit() {
        this.createData(this.dialog.data);
    },

    createData(data) {
        if (this.dialog.loading) return;
        this.dialog.loading = true;
        SeriesCollectionService.create(data)
        .then((response) => {
          this.swalMessage("success",response.data.message,"Okay",false,false,false);
          this.dialog.show = false;
          this.updateTable();
        })
        .catch((errors) => {
            try { this.$refs.form.setErrors(this.getError(errors)); }
            catch(ex){ console.log(ex) }
        })
        .finally(() => {
            this.dialog.loading = false;
        });
    },

  },

  beforeUnmount() {
    clearInterval(this.page.interval);
  }
};
</script>