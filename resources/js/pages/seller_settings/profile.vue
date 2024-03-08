<template>
    <div>
        <app-layout>
            <app-breadcrumb
            :title="page.title"
            :links="breadcrumbs"
            ></app-breadcrumb>

            <div class="row">
            <div class="col-md-8 col-12 mx-auto">
                <div class="card h-100 p-3">
                    <div class="card-body">
                        <v-form ref="form" class="row">
                            
                            <div class="col-12 col-md-4">
                                <v-field as="div" class="field" slim rules="" name="registered_name" vid="registered_name" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-input-text
                                            type="text"
                                            v-model="form.registered_name"
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            readonly
                                            :class="{ 'p-invalid': errors[0] }"
                                        />
                                        <label>Seller Name</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div>
                            
                            <div class="col-12 col-md-4">
                                <v-field as="div" class="field" slim rules="" name="business_name" vid="business_name" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-input-text
                                            type="text"
                                            v-model="form.business_name"
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            readonly
                                            :class="{ 'p-invalid': errors[0] }"
                                        />
                                        <label>Business Name</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div>

                            <div class="col-12 col-md-4">
                                <v-field as="div" class="field" slim rules="required" name="email" vid="email" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-input-text
                                            type="email"
                                            v-model="form.email"
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            :class="{ 'p-invalid': errors[0] }"
                                        />
                                        <label>Email Address</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div>

                            <div class="col-12 col-md-4">
                                <v-field as="div" class="field" slim rules="" name="type" vid="type" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-input-text
                                            type="text"
                                            v-model="form.type"
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            readonly
                                            :class="{ 'p-invalid': errors[0] }"
                                            
                                        />
                                        <label>Seller Type</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div>

                            <!-- <div class="col-12 col-md-3">
                                <v-field as="div" class="field" slim rules="" name="vat_type" vid="vat_type" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-input-text
                                            type="text"
                                            v-model="form.vat_type"
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            readonly
                                            :class="{ 'p-invalid': errors[0] }"
                                        />
                                        <label>Tax Type</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div> -->

                            <div class="col-12 col-md-4">
                                <v-field as="div" class="field" slim rules="" name="tin" vid="tin" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-input-text
                                            type="text"
                                            v-model="form.tin"
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            readonly
                                            :class="{ 'p-invalid': errors[0] }"
                                        />
                                        <label>TIN Number</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div>

                            <div class="col-12 col-md-4">
                                <v-field as="div" class="field" slim rules="" name="contact_number" vid="contact_number" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-input-text
                                            type="number"
                                            v-model="form.contact_number"
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            :class="{ 'p-invalid': errors[0] }"
                                        />
                                        <label>Contact Number</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div>

                            <div class="col-12 col-md-12">
                                <v-field as="div" class="field" slim rules="" name="registered_address" vid="registered_address" v-slot="{ errors }">
                                    <span class="p-float-label">
                                        <p-textarea
                                            type="text"
                                            v-model="form.registered_address"
                                            rows="3"
                                            autoResize
                                            class="form-control shadow-none text-uppercase"
                                            maxlength="50"
                                            readonly
                                            :class="{ 'p-invalid': errors[0] }"
                                        />
                                        <label>Business Address</label>
                                    </span>
                                    <small class="p-error">{{ errors[0] }}</small>
                                </v-field>
                            </div>
                        </v-form>
                    </div> 

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <p-button
                                    class="btn btn-primary"
                                    label="View Uploaded Files"
                                    icon="pi pi-search"
                                    @click="viewFileModal"
                                />
                            </div>
                            <div class="col-md-6 text-right">
                                <p-button
                                    class="btn btn-primary"
                                    label="Upload"
                                    icon="pi pi-plus"
                                    @click="uploadFileModal"
                                />
                                <p-button
                                    class="btn btn-primary"
                                    label="Apply Changes"
                                    icon="fe-save"
                                    @click="update()"
                                />
                            </div>
                        </div>
                    </div>

                </div>
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
                <v-field as="div" class="field" slim name="file_type" vid="file_type" v-slot="{ errors }">
                    <div class="p-float-label">
                        <p-dropdown
                            appendTo="body"
                            v-model="dialog.data.file_type"
                            :options="file_options"
                            optionLabel="name"
                            optionValue="val"
                            class="custom-dropdown"
                            :class="{ 'p-invalid': errors[0] }"
                        />
                        <label>File Type</label>
                    </div>
                    <small class="p-error">{{ errors[0] }}</small>
                </v-field>

                <v-field as="div" class="field" slim name="file" vid="file" v-slot="{ errors }">
                    <FileUpload @setFile="setFile" :error="errors[0]"></FileUpload>
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

        <p-dialog
            class="whitelist-modal modal"
            v-model:visible="view_file_dialog.show"
            :style="{
            width: '100%',
            'max-width': this.view_file_dialog.max_width,
            }"
            modal
        >
        <template #header>
            <h3 v-text="view_file_dialog.title"></h3>
        </template>
        <div class="row content-height" >
            <p-card class="shadow-none card">
                <template #content>
                    <div class="d-flex">
                        <div class="col-12 d-flex w-50 px-0 pt-auto">
                            <div class="col-6 mb-0 p-0 pr-2">
                                <span class="p-float-label">
                                    <p-dropdown
                                        v-model="view_file_dialog.table.params.file_type"
                                        inputId="file_type"
                                        :options="file_options"
                                        optionLabel="name"
                                        optionValue="val"
                                        class="w-full"
                                    />
                                    <label for="file_type">File Type</label>
                                </span>
                            </div>
                            <div class ="col-4 mb-0 p-0">
                                <div class="p-float-label p-input-icon-right">
                                    <p-datepicker
                                        inputId="start_date"
                                        inputClass="form-control shadow-none"
                                        v-model="view_file_dialog.table.params.start_date"
                                        :maxDate="_max_date"
                                        showIcon
                                    />
                           
                                    <label for="start_date">Start date</label>
                                </div>
                            </div>
                            <span class="input-group-text">to</span>
                            <div class ="col-4 mb-0 p-0">
                                <div class="p-float-label p-input-icon-right">
                                    <p-datepicker
                                        inputId="end_date"
                                        inputClass="form-control shadow-none"
                                        v-model="view_file_dialog.table.params.end_date"
                                        :maxDate="_max_date"
                                        :minDate="view_file_dialog.table.params.start_date"
                                        showIcon
                                    />
                   
                                    <label for="end_date">End date</label>
                                </div>   
                            </div>
                        </div>

                        <div class="col-12 col-md-4 ms-auto">
                            <div class="mt-0">
                                <div class="input-group flex-nowrap w-full flex-xs-wrap">
                                    <span class="p-float-label p-input-icon-right">
                                        <i class="pi pi-search"></i> 
                                        <p-input-text
                                        id="search"
                                        type="text"
                                        v-model="view_file_dialog.table.params.search"
                                        class="form-control shadow-none"
                                        />
                                        <label for="search">Search</label>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-3 pt-3">
                        <p-table
                        tableClass="table"
                        :rowHover="true"
                        :loading="view_file_dialog.table.loading"
                        :value="view_file_dialog.table.data"
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
                                    icon="pi pi-download"
                                    v-tooltip.top="'Download File'"
                                    @click="downloadFile(data.id)"
                                    />

                                    <p-button
                                    class="btn btn-sm btn-danger"
                                    icon="pi pi-trash"
                                    v-tooltip.top="'Delete File'"
                                    @click="deleteFile(data.id)"
                                    />
                                </template>
                            </p-column>
                            <p-column header="No.">
                                <template #body="{data}">
                                    {{ data.id }}
                                </template>
                            </p-column>
                            <p-column header="File Name">
                                <template #body="{data}">
                                    {{ data.original_filename }}
                                </template>
                            </p-column>
                            <p-column header="File Type">
                                <template #body="{data}">
                                    {{ data.file_type }}
                                </template>
                            </p-column>
                            <p-column header="Date Uploaded">  
                                <template #body="{data}">
                                    {{ formatDateTime(data.created_at) }}
                                </template>
                            </p-column>
                        </p-table>
                        <div>
                            <h5>Total Record(s): {{ view_file_dialog.table.total }}</h5>
                        </div>
                        
                        <div>
                            <p-paginator
                                v-if="view_file_dialog.table.total > 5"
                                v-model="view_file_dialog.table.first"
                                @page="onPaginate"
                                :totalRecords="view_file_dialog.table.total"
                                :rows="5"
                            >
                            </p-paginator>
                        </div> 
                    </div>

                
                </template>
            </p-card> 
        </div>

            
        </p-dialog>

        <p-toast />
        <p-confirm></p-confirm>
    </div>
  </template>
  
  <script>
  import FileUpload from '../../components/FileUpload';
  import SellerService  from '../../services/seller';
  
  export default {

    components: { FileUpload },

    props: ["seller"],

    computed: {

    },

    data() {
      return {
        page: {
          title:"Profile",
          route:"profile",
          interval:null
        },
        breadcrumbs: [
          { current: false, title: 'Home', url: 'dashboard' }
        ],
        form: new Form({
            registered_name:"",
            registered_address:"",
            business_name:"",
            tin:"",
            vat_type:"",
            type:"",
            contact_number:"",
            email:""
        }),
        loading:false,

        dialog: {
            title: "",
            show:false,
            loading:false,
            data: new Form({
                file: null,
                file_type:"Certificates",
            })
        },

        view_file_dialog:{
            table:{
                first:null,
                loading: !1,
                data: [],
                total: 0,
                total_pages: 0,
                params: {
                    search:null,
                    start_date:new Date(),
                    end_date:new Date(),
                    file_type:"All",
                    page: 1,
                    limit: 5,
                },
            },
            title: "",
            show:false,
            loading:false,
            max_width:'1000px',
        },
        generate_loading:false,
        file_options: [
            { name: "All", val: "All"},
            { name: "BIR Forms", val: "BIR Forms"},
            { name: "Account Information Forms", val: "Account Information Forms"},
            { name: "Application Forms", val: "Application Forms"},
            { name: "Certificates", val: "Certificates"},
            { name: "Documentary Stamp Tax Return", val: "Documentary Stamp Tax Return"},
            { name: "Excise Tax Return", val: "Excise Tax Return"},
            { name: "Income Tax Return", val: "Income Tax Return"},
            { name: "Legal Forms", val: "Legal Forms"},
        ],
      }
    },
    
    methods:{

        onPaginate(val){
            this.viewFileModal(val.page + 1);
        },

        updateDateParams() {
            if(!this.view_file_dialog.table.params.end_date) this.view_file_dialog.table.params.end_date = this.view_file_dialog.table.params.start_date;
            else if(this.view_file_dialog.table.params.end_date < this.view_file_dialog.table.params.start_date) this.view_file_dialog.table.params.end_date = this.view_file_dialog.table.params.start_date;
            if(!this.view_file_dialog.table.params.start_date) this.view_file_dialog.table.params.start_date = this.view_file_dialog.table.params.end_date;
        },

        setFile(base64) {
            this.dialog.data.file = base64;
        },

        async submit() {
            this.uploadFile(this.dialog.data);
        },

        downloadFile(id) {
            SellerService.file_download(id)
                .then(response => {
                    if (response.headers['content-type'].startsWith('image/')) {
                        const base64String = response.data;
                        const mimeType = response.headers['content-type'];
                        const blob = this.base64ToBlob(base64String, mimeType);

                        const url = window.URL.createObjectURL(blob);

                        const a = document.createElement('a');
                        a.href = url;
                        a.download = response.headers['content-disposition'].split('filename=')[1];
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);

                        window.URL.revokeObjectURL(url);
                    } else {
                        const blob = new Blob([response.data], { type: response.headers['content-type'] });
                        const url = window.URL.createObjectURL(blob);

                        const a = document.createElement('a');
                        a.href = url;
                        a.download = response.headers['content-disposition'].split('filename=')[1];
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);

                        window.URL.revokeObjectURL(url);
                    }
                    
                })
                .catch(error => {
                    console.error('Error viewing file:', error);
                });
        },

        base64ToBlob(base64_string, mime_type) {
            const byteCharacters = atob(base64_string);
            const byteNumbers = new Array(byteCharacters.length);

            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }

            const byteArray = new Uint8Array(byteNumbers);
            return new Blob([byteArray], { type: mime_type });
        },

        deleteFile(id) {
            this.$confirm.require({
                message: 'Are you sure to delete this record?',
                header: 'Delete Confirmation',
                icon: 'pi pi-info-circle',
                acceptClass: 'p-button-danger btn btn-light',
                rejectClass: 'p-button-danger btn btn-danger',
                accept: () => {
                    SellerService.file_delete(id)
                        .then((response) => {
                            this.swalMessage("success",response.data.message,"Okay",false,false,false);
                            this.viewFileModal(1);
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

        uploadFile(data){
            if (this.dialog.loading) return;
            this.dialog.loading = true;
            let headers =  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            };
            let formData = new FormData();
            if(data.file)formData.append('file', data.file);
            if(data.file_type)formData.append('file_type', data.file_type);
            SellerService.upload(formData, headers)
                .then((response) => {
                    this.swalMessage("success",response.data.message,"Okay",false,false,false);
                    this.dialog.show = false;
                    this.viewFileModal(1);
                    this.view_file_dialog.show = false;
                })
                .catch((errors) => {
                    try { this.$refs.form.setErrors(this.getError(errors)); }
                    catch(ex){ console.log(ex) }
                })
                .finally(() => {
                    this.dialog.loading = false;
                });
        },

        viewFileModal(page, without_loading=false) {
            this.view_file_dialog.title = "Uploaded Files";
            this.view_file_dialog.show = true;

            this.view_file_dialog.table.loading = (without_loading) ? !1 : !0;
            this.view_file_dialog.table.params.page = page;


            let params = {...this.view_file_dialog.table.params};
            params.start_date =  this.formatDateTime(params.start_date,1);
            params.end_date =  this.formatDateTime(params.end_date,2);
            params.file_type = params.file_type === "All" ? "" : params.file_type

            SellerService.file_list(params)
                .then((response) => {
                    this.view_file_dialog.table.data = response.data.data.data;
                    this.view_file_dialog.table.total = response.data.data.total;
                    this.view_file_dialog.table.total_pages = response.data.data.last_page;
                    this.view_file_dialog.table.loading = false;
                })
                .catch((errors) => {})
                .finally(() => {});
        },

        uploadFileModal() {
            this.dialog.data.reset();
            this.dialog.title = "Upload File";
            this.dialog.show = true;
        },

        update(){
            SellerService.update_profile(this.form)
            .then((response) => {
                this.swalMessage("success",response.data.message,"Okay",false,false,false);
            })
            .catch((errors) => {
                try { 
                    this.$refs.form.setErrors(this.getError(errors));
                }
                catch(ex){ console.log(ex)}
            })
            .finally(() => {
                this.loading = false;
            });
        }
    },
  
    mounted(){
        this.form.registered_name = this.seller.registered_name;
        this.form.registered_address = this.seller.registered_address;
        this.form.business_name = this.seller.business_name;
        this.form.tin = this.seller.tin;
        this.form.vat_type = this.seller.vat_type === "V" ? "VAT" : "NON-VAT";
        this.form.type = this.seller.type;
        this.form.contact_number = this.seller.contact_number;
        this.form.email = this.seller.email;
    },
  
    beforeDestroy() {
    },
  
    beforeUnmount() {
      clearInterval(this.page.interval);
    },

    watch: {
        'view_file_dialog.table.params.search'(val) {
            this.viewFileModal(1);
        },

        'view_file_dialog.table.params.file_type'(val) {
            this.viewFileModal(1);
        },

        'view_file_dialog.table.params.start_date'(val) {
            this.updateDateParams();
            this.viewFileModal(1);
        },

        'view_file_dialog.table.params.end_date'(val) {
            this.updateDateParams();
            this.viewFileModal(1);
        },
    }
  };
</script>
<style scoped>
    .content-height{
        height: auto;
    }
</style>