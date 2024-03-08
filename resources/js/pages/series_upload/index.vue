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
                    label="Upload"
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
                    <p-column field="original_filename" header="File"></p-column>
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
                        {{ formatDateTime(data.updated_at) }}
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
      <v-form ref="form" tag="form">

        <v-field as="div" class="field" slim name="Service Provider" vid="service_provider_id" v-slot="{ errors }">

          <div class="p-float-label">
            <p-dropdown
              appendTo="body"
              v-model="dialog.data.service_provider_id"
              :options="dsp_list"
              optionLabel="company_name"
              optionValue="id"
              class="w-full"
              :class="{ 'p-invalid': errors[0] }"
            />
            <label>Service Provider</label>
          </div>
          <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field" slim name="file" vid="file" v-slot="{ errors }">
          <ImageUpload @setFile="setFile" :error="errors[0]"></ImageUpload>
          <small class="p-error">{{ errors[0] }}</small>
        </v-field>
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

import SeriesUploadService  from '../../services/series_upload';
import ImageUpload from '../../components/FileUpload';
import DSPService  from '../../services/service_provider';
export default {
  components: {ImageUpload},
  data() {
    return {
      page: {
        title: "Upload Series",
        route: "series_upload",
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
          file: null,
          service_provider_id:null,
        })
      },
      dsp_list:[],
      isLoading: false,
    }
  },

  computed: {
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

      SeriesUploadService.list(this.table.params)
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

    createItem() {
      this.dialog.data.reset();
      this.dialog.data.service_provider_id = (this.dsp_list.length>=2) ? this.dialog.data.service_provider_id : this.dsp_list.map(e=>{return e.id})[0];
      this.dialog.title = "Upload";
      this.dialog.show = true;
    },

    
    async submit() {
        this.createData(this.dialog.data);
    },

    createData(data) {
        if (this.dialog.loading) return;
        this.dialog.loading = true;
        let headers =  {
          headers: {
              'Content-Type': 'multipart/form-data'
          }
        }
        let formData = new FormData();
        if(data.service_provider_id) formData.append("service_provider_id",data.service_provider_id);
        formData.append('file', data.file);
        SeriesUploadService.create(formData,headers)
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

    updateData(data, id) {
      if (this.dialog.loading) return;
      this.dialog.loading = true;
      SeriesUploadService.update(data,id)
      .then((response) => {
        this.swalMessage("success",response.data.message,"Okay",false,false,false);
        this.dialog.show = false;
        this.updateTable();
      })
      .catch((errors) => {
          try { 
              this.$refs.form.setErrors(this.getError(errors));
          }
          catch(ex){ console.log(ex)}
      })
      .finally(() => {
          this.dialog.loading = false;
      });
    }
  },

  beforeUnmount() {
    clearInterval(this.page.interval);
  }
};
</script>