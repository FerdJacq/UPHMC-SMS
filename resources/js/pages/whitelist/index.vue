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

                    <p-column>
                      <template #body="{data}">
                        <p-button
                          class="btn btn-sm btn-warning"
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
                    <p-column field="name" header="Name"></p-column>
                    <p-column field="ip_address" header="IP Address"></p-column>
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
        <v-field as="div" class="field" slim name="name" vid="name" v-slot="{ errors }">
          <span class="p-float-label">
            <p-input-text
              id="name"
              type="text"
              v-model="dialog.data.name"
              class="form-control shadow-none"
              maxlength="50"
              :class="{ 'p-invalid': errors[0] }"
            />
            <label for="name">Name</label>
          </span>
          <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field" slim name="ip_address" vid="ip_address" v-slot="{ errors }">
          <span class="p-float-label">
            <p-input-text
              id="ip_address"
              type="text"
              v-model="dialog.data.ip_address"
              class="form-control shadow-none"
              maxlength="50"
              :class="{ 'p-invalid': errors[0] }"
            />
            <label for="ip_address">IP Address</label>
          </span>
          <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <div class="field">
          <div class="p-float-label">
            <p-dropdown
              appendTo="body"
              v-model="dialog.data.status"
              inputId="status"
              :options="dialog.status"
              optionLabel="text"
              optionValue="value"
              class="w-full"
            />
            <label for="status">Status</label>
          </div>
        </div>
      </v-form>
    
      <template #footer>
        <p-button
          class="btn btn-outline-light"
          label="Cancel"
          :disabled="dialog.processing"
          @click="dialog.show=false"
        />
        <p-button
          class="btn btn-primary"
          label="Save"
          :disabled="dialog.processing"
          @click="submit"
        />
      </template>
    </p-dialog>

    <p-toast />
    <p-confirm></p-confirm>
  </div>
</template>
<script>

import WhitelistService  from '../../services/whitelist';
export default {
  components: {},
  data() {
    return {
      page: {
        title: "Whitelist",
        route: "whitelist",
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
        processing:false,
        status:[
          { text:"ACTIVE", value:"ACTIVE" },
          { text:"DEACTIVATED", value:"DEACTIVATED" },
        ],
        data: new Form({
          id: null,
          name: null,
          ip_address: null,
          status: "ACTIVE",
        })
      },
      
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

    // this.page.interval = setInterval(() => {
    //   this.updateTable();
    // }, 5000);
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

      WhitelistService.list(this.table.params)
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
      this.dialog.title = "New";
      this.dialog.show = true;
    },
    
    editItem(item) {
      this.dialog.data.id = item.id;
      this.dialog.data.name = item.name;
      this.dialog.data.ip_address = item.ip_address;
      this.dialog.data.status = item.status;

      this.dialog.title = "Update";
      this.dialog.show = true;
    },

    viewData(item){
      this.dialog.title = "Update whitelist";
      this.dialog.data.reset();
      // this.dialog.data.clearErrors();

      WhitelistService.get(item.id)
      .then((response) => {
        let data = response.data.data;
        this.dialog.data.id = data.id;
        this.dialog.data.name = data.name;
        this.dialog.data.ip_address = data.ip_address;
        this.dialog.data.status = data.status;
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
          WhitelistService.remove(item.id)
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

    async submit() {
     
        if(this.dialog.data.id > 0)
          this.updateData(this.dialog.data,this.dialog.data.id);
        else
          this.createData(this.dialog.data);
    },

    createData(data) {
        if (this.dialog.loading) return;
        this.dialog.loading = true;
        WhitelistService.create(data)
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
      WhitelistService.update(data,id)
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