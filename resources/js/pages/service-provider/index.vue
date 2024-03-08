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
                    label="New"
                    icon="pi pi-plus"
                    @click="create"
                  />
                </div>
                <div class="col-12 col-md-3 ms-auto">
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

                    <p-column>
                      <template #body="{data}">
                        <p-button
                          class="btn btn-sm btn-warning mr-1"
                          icon="pi pi-pencil"
                          @click="viewData(data)"
                        />
                        <p-button
                          class="btn btn-sm btn-danger"
                          icon="pi pi-times"
                          @click="deleteData(data)"
                        />
                      </template>
                    </p-column>
                    <p-column field="id" header="No."></p-column>
                    <p-column field="reference_number" header="Reference Number"></p-column>
                    <p-column header="Email">
                      <template #body="{data}">
                        {{ data.email.toLowerCase() }}
                      </template>
                    </p-column>
                    <p-column field="company_name" header="Company name"></p-column>
                    <p-column field="category" header="Category"></p-column>
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
                    <p-column field="created_at" header="Date Created"></p-column>
                </p-table>
                <p-paginator :rows="10" :totalRecords="table.total" @page="onPage($event)"></p-paginator>
              </div>
            </template>
          </p-card> <!-- end card -->
        </div> <!-- end col -->
      </div>
    </app-layout>

    <p-dialog
      class="provider-modal modal"
      v-model:visible="dialog.show"
      :style="{
        width: '100%',
        'max-width': '775px'
      }"
      modal
    >
      <template #header>
        <h3 v-text="dialog.title"></h3>
      </template>
      
      <div>
        <ProviderForm @hideModal="successEvent" :item="dialog.form"/>
      </div>
    
      <template #footer></template>
    </p-dialog>

    <p-toast />
    <p-confirm></p-confirm>
  </div>
</template>
  
<script>
  import ProviderForm from './form';
  import DSPService  from '../../services/service_provider';

  export default {

    components: { ProviderForm },

    data() {
      return {
        page: {
          title: "Service Provider",
          route: "service-provider",
          interval: null
        },
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
        breadcrumbs: [
          { current: false, title: 'Home', url: 'dashboard' }
        ],
        dialog: {
          title: "Service provider",
          isUpdate: !1,
          show: false,
          // form: new Form({
          //   id: null,
          //   code: null,
          //   secret: null,
          //   token: null,
          //   prefix: null,
          //   email: null,
          //   company_name: null,
          //   category: null,
          //   logo: null,
          //   new_image: null,
          //   fees: [],
          //   status: "ACTIVE"
          // }),
          form: new Form({
            id: null,
            code: null,
            secret: null,
            token: null,
            prefix: null,
            email: null,
            company_code:null,
            company_name: null,
            category: null,
            logo: null,
            new_image: null,
            fees: [],
            status: "ACTIVE"
          })
        },
      }
    },

    mounted(){
      this.breadcrumbs.push({
        current: true,
        title: this.page.title,
        url: `${this.page.route}`
      });

      this.updateTable();
    },

    methods: {
      onPage(val){
        this.table.params.page = val.page+1;
        this.updateTable();
      },
      updateTable() {
        if (this.table.loading) return;
        this.table.loading = true;

        DSPService.list(this.table.params)
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

      create() {
        this.dialog.show = !0;
        this.dialog.isUpdate = !1;

        this.dialog.form.reset();
        this.dialog.form.clearErrors();
      },

      viewData(item){
        this.dialog.title = "Update Service Provider";
        this.dialog.form.reset();
        console.log(item)
        // this.dialog.data.clearErrors();

        DSPService.get(item.reference_number)
        .then((response) => {
          let data = response.data.data;
          this.dialog.form.id = data.reference_number;
          this.dialog.form.code = data.code;
          this.dialog.form.email = data.email;
          this.dialog.form.secret = data.secret;
          this.dialog.form.token = data.token;
          this.dialog.form.prefix = data.prefix;
          this.dialog.form.company_code = data.company_code;
          this.dialog.form.company_name = data.company_name;
          this.dialog.form.category = data.category;
          this.dialog.form.logo = data.logo;
          this.dialog.form.fees = data.fees;
          this.dialog.form.status = data.status;
          this.dialog.isUpdate = true;
          this.dialog.show = true;
        })
        .catch((errors) => {
            try { 
              this.getError(errors);
            }
            catch(ex){ console.log(ex)}
        })
        .finally(() => {});
      },
      
      deleteData(item){
      this.$confirm.require({
        message: 'Are you sure to delete this record?',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        acceptClass: 'p-button-danger btn btn-light',
        rejectClass: 'p-button-danger btn btn-danger',
        accept: () => {
          DSPService.remove(item.reference_number)
          .then((response) => {
            this.swalMessage("success",response.data.message,"Okay",false,false,false);
            this.updateTable();
          })
          .catch((errors) => {
              try { 
                this.getError(errors);
              }
              catch(ex){ console.log(ex)}
          })
          .finally(() => {});
        }
      });
    },

      successEvent() {
        this.dialog.show = !this.dialog.show;
        this.updateTable();
      }
    },

    computed: {
      formTitle() {
        return this.dialog.isUpdate ? "Update service provider" : "Create service provider";
      }
    },

    watch: {
      'table.params.search': function (val) {
        this.updateTable();
      }
    }    
  };
</script>