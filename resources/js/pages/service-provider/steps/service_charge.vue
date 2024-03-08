<template>
    <div>
        <div class="col-12">
            <h3 class="mb-3">Service Charge</h3>
        </div>

        <div class="col-12 mb-2">
            <p-button
                class="btn btn-soft-primary"
                label="Add"
                icon="pi pi-plus"
                @click="create"
            />
        </div>

        <div class="table-responsive field">
            <v-field as="div" class="field" slim name="fees" vid="fees" v-slot="{ errors }">
                <p-table
                    tableClass="table"
                    :rowHover="true"
                    :loading="table.loading"
                    :value="table.data"
                    showGridlines
                    responsiveLayout="scroll"

                    :paginator="true"
                    :rows="10"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
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
                                @click="removeData(data)"
                            />
                        </template>
                    </p-column>

                    <p-column field="type" header="Service type"></p-column>
                    <p-column field="amount" header="Amount">
                        <template #body="{data}">
                            {{ (data.amount_type == "PERCENTAGE") ? `${ formatNumber(data.amount) }%` : `₱ ${ formatNumber(data.amount) }` }}
                        </template>
                    </p-column>
                    <p-column field="amount_type" header="Type (Percentage/Fixed)"></p-column>
                    <p-column header="Range">
                        <template #body="{data}">
                            {{ (parseFloat(data.max) > 0) ? `${ formatNumber(data.min) } - ${ formatNumber(data.max) }` : `-` }}
                        </template>
                    </p-column>
                    <p-column header="Status">
                        <template #body="{data}">
                            <span class="badge border"
                                :class="{
                                'text-success border-success': (data.status == 'ACTIVE'),
                                'text-danger border-danger': (data.status != 'ACTIVE')
                                }">
                                {{ data.status }}
                            </span>
                        </template>
                    </p-column>
                </p-table>
                <small class="p-error">{{ errors[0] }}</small>
            </v-field>
        </div>

        <p-dialog
            class="charge-modal modal"
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

            <div class="field">
                <div class="p-float-label">
                    <p-dropdown
                        v-model="dialog.form.type"
                        inputId="type"
                        :options='["TRANSACTION", "SERVICE", "COMMISSION"]'
                        class="w-full"
                        appendTo="body"
                    />
                    <label for="type">Service type</label>
                </div>
            </div>
            
            <div class="field">
                <span class="p-float-label">
                    <p-input-number
                        inputId="amount"
                        inputClass="form-control shadow-none"
                        v-model="dialog.form.amount"
                    />
                    <label for="amount">Amount</label>
                </span>
                <small class="p-error"></small>
            </div>

            <div class="field">
                <div class="p-float-label">
                    <p-dropdown
                        v-model="dialog.form.amount_type"
                        inputId="amount_type"
                        :options='["PERCENTAGE", "FIXED"]'
                        class="w-full"
                        appendTo="body"
                    />
                    <label for="amount_type">Type</label>
                </div>
            </div>

            <div class="field">
                <h5 class="m-0">Range (min-max)</h5>
            </div>

            <div class="field">
                <div class="row g-2">
                    <div class="col-md">
                        <span class="p-float-label">
                            <p-input-number
                                inputId="min"
                                inputClass="form-control shadow-none"
                                v-model="dialog.form.min"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                            />
                            <label for="min">Min.</label>
                        </span>
                        <small class="p-error"></small>
                    </div>
                    <div class="col-md">
                        <span class="p-float-label">
                            <p-input-number
                                inputId="max"
                                inputClass="form-control shadow-none"
                                v-model="dialog.form.max"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                            />
                            <label for="max">Max</label>
                        </span>
                        <small class="p-error"></small>
                    </div>
                </div>
            </div>

            <div class="field">
                <div class="p-float-label">
                    <p-dropdown
                        appendTo="body"
                        v-model="dialog.form.status"
                        inputId="type"
                        :options='["ACTIVE", "INACTIVE"]'
                        class="w-full"
                    />
                    <label for="type">Status</label>
                </div>
            </div>
            
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
                    :disabled="dialog.processing || !dialog.form.amount"
                    @click="save"
                />
            </template>
        </p-dialog>
    </div>
</template>
<script>
    import ImageUpload from '../../../components/ImageUpload';

    export default {
        components : { ImageUpload },
        props: ['service'],

        data() {
            return {
                table : {
                    loading: false,
                    data: [],
                },
                dialog: {
                    title: "Fees",
                    show: false,
                    processing:false,

                    table: {
                        data: [],
                        loading: !1
                    },

                    form: new Form({
                        id: null,
                        type: "TRANSACTION",
                        amount: null,
                        amount_type: "PERCENTAGE",
                        min: null,
                        max: null,
                        status: "ACTIVE"
                    })
                }
            }
        },

        methods: {
            create() {
                this.dialog.show = true;
                this.dialog.table.loading = !0;
                this.dialog.form.reset();
            },

            viewData(item) {
                console.log(item)
                
                this.dialog.form.reset();

                this.dialog.form.id = item.id;
                this.dialog.form.type = item.type;
                this.dialog.form.amount = item.amount;
                this.dialog.form.amount_type = item.amount_type;
                this.dialog.form.status = item.status;
                this.dialog.form.min = item.min;
                this.dialog.form.max = item.max;
                
                this.dialog.show = true;
            },

            async save() {
                (this.dialog.form.id) ? this.updateData() : this.createData();
                this.dialog.show = false;
            },

            createData() {
                this.table.data.push({
                    id: (this.table.data.length + 1),
                    type: this.dialog.form.type,
                    status: this.dialog.form.status,
                    amount: this.dialog.form.amount,
                    amount_type: this.dialog.form.amount_type,
                    min: this.dialog.form.min,
                    max: this.dialog.form.max
                });
                this.updatePropData();
            },

            updateData() {
                let form = this.dialog.form,
                    index = this.table.data.findIndex(e => e.id == form.id);
                
                if(index > -1) {
                    this.table.data[index].type = form.type;
                    this.table.data[index].status = form.status;
                    this.table.data[index].amount = form.amount;
                    this.table.data[index].amount_type = form.amount_type;
                    this.table.data[index].min = form.min;
                    this.table.data[index].max = form.max;
                    this.updatePropData();
                }
            },

            removeData(item) {
                let index = this.table.data.findIndex(e => e.id == item.id);

                this.$confirm.require({
                    message: 'Do you want to delete this record?',
                    header: 'Delete Confirmation',
                    icon: 'pi pi-info-circle',
                    acceptClass: 'p-button-danger btn btn-danger',
                    accept: () => {
                        this.table.data.splice(index, 1);
                    },
                    reject: () => {
                        
                    }
                });
            },

            updatePropData() {
                this.service.fees = this.table.data;
            }
        },

        mounted() {
            this.table.data = this.service.fees;
        },

        watch: {
            
        }
    }
</script>