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
                    <p-column field="id" header="No."></p-column>
                    <p-column header="Name">
                      <template #body="{data}">
                        {{ `${ data.first_name ? data.first_name : "" } ${ data.last_name ? data.last_name : "" }` }}
                      </template>
                    </p-column>
                    <p-column header="Username">
                      <template #body="{data}">
                        {{ data.user.username }}
                      </template>
                    </p-column>
                    <p-column header="Email">
                      <template #body="{data}">
                        {{ data.user.email.toLowerCase() }}
                      </template>
                    </p-column>
                    <p-column header="Position">
                      <template #body="{data}">
                        {{ data.user.role_name }}
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
      class="account-modal modal"
      v-model:visible="dialog.show"
      :style="{
        width: '100%',
        'max-width': '700px'
      }"
      modal
    >
      <template #header>
        <h3 v-text="dialog.title"></h3>
      </template>
      
      <div>
        <AccountForm @hideModal="successEvent" :item="dialog.data"/>
      </div>
    
      <template #footer></template>
    </p-dialog>

    <p-toast />
    <p-confirm></p-confirm>
  </div>
</template>
  
<script>
import AccountForm from './form';
import AccountService  from '../../services/account';

export default {
  // props: ["items"],
  components: { AccountForm },

  data() {
    return {
      page: {
        title: "Accounts",
        route: "account",
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
      dialog: {
        title: "",
        show:false,
        processing:false,
        data: new Form({
          id: null,
          uid: null,
          first_name: null,
          middle_name: null,
          last_name: null,
          role: "DSP",
          username: null,
          password: null,
          verify_password: null,
          email: null,
          avatar: null,
          new_image: null,
          service_provider: [],
          region:[],
        })
      },
      breadcrumbs: [
        { current: false, title: 'Home', url: 'dashboard' }
      ],
      isLoading: false
    }
  },

  computed: {

    dialogSave() {
      return (this.dialog.data.role=='DSP' && this.dialog.data.stepper==2) || (this.dialog.data.role!='DSP' && this.dialog.data.stepper==1) ? true : false;
    },

    dialogNext() {
      return (this.dialog.data.role=='DSP' && this.dialog.data.stepper!=2) ? true : false;
    },

    dialogInformationError() {
      return (this.dialog.data.errors.password || 
        this.dialog.data.errors.email || 
        this.dialog.data.errors.username
      ) ? false : true;
    },

    dialogServiceProviderError() {
      return this.dialog.data.errors.service_provider ? false : true;
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

      AccountService.list(this.table.params)
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
      this.dialog.title = "Create account";
      this.dialog.data.stepper = 1;
      this.dialog.data.reset();
      // this.dialog.data.clearErrors();
      this.dialog.show = true;
    },

    deleteData(item){
      
      this.$confirm.require({
        message: 'Are you sure to delete this record?',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        acceptClass: 'p-button-danger btn btn-light',
        rejectClass: 'p-button-danger btn btn-danger',
        accept: () => {
          AccountService.remove(item.account_number)
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

    viewData(item){
      this.dialog.title = "Update account";
      this.dialog.data.reset();
      // this.dialog.data.clearErrors();

      AccountService.get(item.account_number)
      .then((response) => {
        let data = response.data.data;
        this.dialog.data.stepper = 1;
        this.dialog.data.id = data.account_number; 
        this.dialog.data.uid = data.user.id;
        this.dialog.data.first_name = data.first_name;
        this.dialog.data.middle_name = data.middle_name;
        this.dialog.data.last_name = data.last_name;
        this.dialog.data.role = data.user.role_name;
        this.dialog.data.username = data.user.username;
        this.dialog.data.email = data.user.email;
        this.dialog.data.avatar = data.avatar;
        this.dialog.data.password = null;
        this.dialog.data.region = data.region.map(e=> e.region_code);
        this.dialog.data.service_provider = (data.service_provider) ? data.service_provider : [];
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

    setSelectedImage(val) {
      this.dialog.data.new_image = val;
    },

    deleteDialogDSP(item) {
      this.dialog.data.service_provider = this.dialog.data.service_provider.filter(e=> e.service_provider_id!=item.service_provider_id);
    },

    nextDialogStepper() {
      this.dialog.data.stepper = Number(this.dialog.data.stepper) + 1;
    },

    backDialogStepper() {
      this.dialog.data.stepper = Number(this.dialog.data.stepper) - 1;
    },

    setSelectedProvider(item) {
      this.dialog_dsp.show = false;
      console.log(item);
      if(this.dialog.data.service_provider.some(e=> e.service_provider_id==item.id))
        this.$toast.error("Service Provider already exist");
      else
        this.dialog.data.service_provider.push({account_id:null, service_provider_id:item.id,data:item});
    },

    successEvent() {
      this.dialog.show = !this.dialog.show;
      this.updateTable();
      // this.$toast.add({ severity: 'success', summary: 'Success!', detail: 'Blotter report created successfully', life: 3000 });
    },
  },

  beforeUnmount() {
    clearInterval(this.page.interval);
  }
};
</script>
