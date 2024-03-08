<template>
    <div class="row">
        <div class="col-xs-12 col-8">
          
          <button class="btn btn-primary" @click="show_transaction()" v-if="_is_auth_seller">Edit</button>
            <p-table
              tableClass="table table-fit-column-1 table-wrap mt-2 mb-0 tbl-item"
              :value="data.details"
              :scrollable="true"
              scrollHeight="400px"
              :loading="!data"
            >
              <p-column header="" class="text-center" :styles="{'min-width':'75px'}">
                <template #body="{data}">
                  <img :src="data.image" class="img-table m-auto"/>
                </template>
              </p-column>
              <p-column field="item" header="Product" :styles="{'min-width':'225px'}"></p-column>
              <p-column field="variant" header="Variant"></p-column>
              <p-column header="Qty" :styles="{'justify-content': 'flex-end'}">
                <template #body="{data}">
                  {{ formatNumber(data.qty) }}
                </template>
              </p-column>
              <p-column header="Unit price" :styles="{'justify-content': 'flex-end'}">
                <template #body="{data}">
                  <span class="text-right" v-if="data.log && !_is_auth_seller">
                    <span class="text-line-through">{{ formatNumber(data.unit_price) }}</span>
                    <span class="text-danger text-updated">{{ formatNumber(data.log.unit_price) }}</span>
                  </span>
                  <span class="text-right" v-else>{{ (data.log) ? formatNumber(data.log.unit_price) : formatNumber(data.unit_price) }}</span>
                </template>
              </p-column>
              <p-column header="Total price" :styles="{'justify-content': 'flex-end'}">
                <template #body="{data}">
                  <span class="text-right" v-if="data.log && !_is_auth_seller">
                    <span class="text-line-through">{{ formatNumber(data.total_price) }}</span>
                    <span class="text-danger text-updated">{{ formatNumber(data.log.total_price) }}</span>
                  </span>
                  <span class="text-right" v-else>{{ (data.log) ? formatNumber(data.log.total_price) : formatNumber(data.total_price) }}</span>
                </template>
              </p-column>
              <template #footer v-if="data">
                <div class="text-end fw-semibold text-dark">
                    <span v-if="data.log && !_is_auth_seller">
                      <span class="text-line-through">{{ formatNumber(data.subtotal_amount) }}</span>
                      <span class="text-danger text-updated">{{ formatNumber(data.log.subtotal_amount) }}</span>
                    </span>
                    <span v-else>{{ (data.log) ? formatNumber(data.log.subtotal_amount) : formatNumber(data.subtotal_amount) }}</span>
                </div>
              </template>
            </p-table>
            <p-button v-if="_is_auth_seller"
              class="btn btn-primary my-3 float-right"
              label="View Waybill"
              @click="print()"
            />
        </div>

        <div class="col-xs-12 col-4 breakdown-container">
            <div
            class="table-item d-flex align-items-start"
            :class="{
              'placeholder mb-1 w-100': !data.trans_id
            }"
          >
            <span>Subtotal Amount: </span>
            <span class="ms-auto">
              <!-- {{ formatNumber(data.subtotal_amount) }} -->
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.subtotal_amount) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.subtotal_amount) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.subtotal_amount) : formatNumber(data.subtotal_amount) }}</span>
            </span>
          </div>
          <div
            class="table-item d-flex align-items-start"
            :class="{
              'placeholder mb-1 w-100': !data.trans_id
            }"
          >
            <span>Shipping Fee: </span>
            <span class="ms-auto" v-if="data.log">
              {{ (data.log.shipping_fee > 0 ? `+${ formatNumber(data.log.shipping_fee) }` : "0") }}
            </span>
            <span class="ms-auto" v-else>{{ (data.shipping_fee > 0 ? `+${ formatNumber(data.shipping_fee) }` : "0") }}</span>
          </div>
          <div class="table-item d-flex align-items-start" v-if="data.voucher">
            <span>Voucher: </span>
            <span class="ms-auto" v-if="data.log">{{ (data.log.voucher > 0) ? `-${ formatNumber(data.log.voucher) }` : "0" }}</span>
            <span class="ms-auto" v-else>{{ (data.voucher > 0) ? `-${ formatNumber(data.voucher) }` : "0" }}</span>
          </div>
          <div class="table-item d-flex align-items-start" v-if="data.coins">
            <span>Coins: </span>
            <span class="ms-auto" v-if="data.log">{{ (data.log.coins > 0) ? `-${ formatNumber(data.log.coins) }` : "0" }}</span>
            <span class="ms-auto" v-else>{{ (data.coins > 0) ? `-${ formatNumber(data.coins) }` : "0" }}</span>
          </div>
          <div
            class="table-item d-flex align-items-start fw-bolder text-dark"
            :class="{
              'placeholder w-100 mb-1': !data.trans_id
            }"
          >
            <span>Total Amount: </span>
            <span class="ms-auto" v-if="data.log">
                <span v-if="data.log && !_is_auth_seller">
                  <span class="text-line-through">{{ formatNumber(data.total_amount) }}</span>
                  <span class="text-danger text-updated">{{ formatNumber(data.log.total_amount) }}</span>
                </span>
                <span v-else>{{ (data.log) ? formatNumber(data.log.total_amount) : formatNumber(data.total_amount) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.total_amount) }}</span>
          </div>
          <div
            class="table-item d-flex align-items-start fw-bolder text-dark"
            :class="{
              'placeholder w-100 mb-1': !data.trans_id
            }"
          >
            <!-- <span>{{ data.seller.vat_type=="V" ? "Sales(Before VAT)" : "Gross Sales" }}: </span> -->
            <span>Gross Sales:</span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.base_price) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.base_price) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.base_price) : formatNumber(data.base_price) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.base_price) }}</span>
          </div>
          <div class="table-item d-flex align-items-start" v-if="data.service_fee > 0">
            <span>Service Fee: </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.service_fee) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.service_fee) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.service_fee) : formatNumber(data.service_fee) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.service_fee) }}</span>
          </div>
          <div class="table-item d-flex align-items-start" v-if="data.transaction_fee > 0">
            <span>Transaction Fee: </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.transaction_fee) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.transaction_fee) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.transaction_fee) : formatNumber(data.transaction_fee) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.transaction_fee) }}</span>
          </div>
          <div class="table-item d-flex align-items-start" v-if="data.commission_fee > 0">
            <span>Commission Fee: </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.commission_fee) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.commission_fee) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.commission_fee) : formatNumber(data.commission_fee) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.commission_fee) }}</span>
          </div>
          <div class="table-item d-flex align-items-start" v-if="data.online_platform_vat > 0">
            <span>Online Platform VAT: </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.online_platform_vat) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.online_platform_vat) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.online_platform_vat) : formatNumber(data.online_platform_vat) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.online_platform_vat) }}</span>
          </div>
          <div class="table-item d-flex align-items-start" v-if="data.shipping_vat > 0">
            <span>Shipping VAT: </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.shipping_vat) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.shipping_vat) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.shipping_vat) : formatNumber(data.shipping_vat) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.shipping_vat) }}</span>
          </div>
          <div class="table-item d-flex align-items-start border-none">
            <span>Item VAT: </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.item_vat) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.item_vat) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.item_vat) : formatNumber(data.item_vat) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.item_vat) }}</span>
          </div>
          <div class="table-item d-flex align-items-start border-none">
            <span>Withholding Tax(1%): </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through">{{ formatNumber(data.withholding_tax) }}</span>
                <span class="text-danger text-updated">{{ formatNumber(data.log.withholding_tax) }}</span>
              </span>
              <span v-else>{{ (data.log) ? formatNumber(data.log.withholding_tax) : formatNumber(data.withholding_tax) }}</span>
            </span>
            <span class="ms-auto" v-else>{{ formatNumber(data.withholding_tax) }}</span>
          </div>
          <div class="table-item d-flex align-items-start badge-soft-warning text-dark fw-bold border border-warning" v-if="data.tax > 0">
            <span>Total Taxes Due: </span>
            <span class="ms-auto" v-if="data.log">
              <span v-if="data.log && !_is_auth_seller">
                <span class="text-line-through"> ₱{{ formatNumber(data.tax) }}</span>
                <span class="text-danger text-updated"> ₱{{ formatNumber(data.log.tax) }}</span>
              </span>
              <span v-else> ₱{{ formatNumber(data.log.tax) }}</span>
            </span>
            <span class="ms-auto" v-else>₱{{ formatNumber(data.tax) }}</span>
          </div>
          <div class="table-item d-flex align-items-start bg-warning text-danger placeholder mb-1 w-100" v-if="!data.trans_id">
            <span>Total Taxes Due: </span>
            <span class="ms-auto"></span>
          </div>
        </div>

        <p-dialog
          class="item-modal modal"
          v-model:visible="edit_transaction.show"
          :closeOnEscape="false"
          :style="{
            width: '100%',
            'max-width': '800px'
          }"
          modal
        >
          <template #header>
            <!-- <h3 v-text="dialog.title"></h3> -->
            &nbsp;
          </template>
            <table v-if="edit_transaction.data">
              <tr>
                <th>Item</th>
                <th>Unit Price</th>
              </tr>
              <tr v-for="(item,index) in edit_transaction.data.details">
                <td>{{item.item}}</td>
                <td>
                  <input type="text" class="form-control" v-model="item.unit_price" @input="updateUnitPrice(item, index)" />
                </td>
              </tr>
            </table>
         
          <template #footer>
            <p-button
              class="btn btn-outline-light"
              label="Close"
              :disabled="edit_transaction.loading"
              @click="edit_transaction.show=false"
            />

            <p-button
              class="btn btn-primary"
              label="Submit"
              @click="update()"
            />
           
          </template>
        </p-dialog>
    </div>
</template>
<script>
import TransactionService  from '../../../services/transaction';
    export default {
        props: ['service'],

        data() {
            return {
              data: '',
              dialog: {
                title: "",
                show:false,
                loading:false,
                data: []
              },

              edit_transaction: {
                title: "",
                show:false,
                loading:false,
                data:null,
              },
            }
        },

        methods: {
            fillInfo() {
                this.data = this.service.data;
                console.log(this.data)
            },

            show_transaction(){
              this.edit_transaction.title = "Edit";
              this.edit_transaction.data = JSON.parse(JSON.stringify(this.data));
              this.edit_transaction.data.details.map((obj)=>{
                obj.unit_price = (obj.log) ? obj.log.unit_price : obj.unit_price;
                return obj;
              })
              this.edit_transaction.show = true;
            },
            print(){
              window.open("/waybill", '_blank');
            },
            updateUnitPrice(item, index) {
              item.total_price = item.unit_price * item.qty;
              // Handle any additional logic here if needed
              console.log(`Updated unit price for ${item.item} to ${item.unit_price}`);
            },
            update(){
              
              let params = {
                items:this.edit_transaction.data.details
              }
              this.edit_transaction.loading = false;
              TransactionService.update(this.edit_transaction.data.id,params)
              .then((e) => {
                this.data = e.data.data;
                this.edit_transaction.show = false;
                this.swalMessage("success","Successfully updated","Okay",false,false,false);
              })
              .catch((errors) => {
                  try { 
                    
                  }
                  catch(ex){ console.log(ex)}
              })
              .finally(() => {
                this.edit_transaction.loading = false;
              });
            }
        },

        mounted() {
            this.fillInfo();
        },

        watch: {
            service: {
                handler(val){
                    this.data = val;
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>

<style>
.breakdown-container{
    font-size:13px;
}

.tbl-item td{
    font-size:13px !important;
}
</style>