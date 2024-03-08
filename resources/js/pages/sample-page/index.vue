<template>
    <admin-layout>
      <v-banner class="mb-4">
        <div class="d-flex flex-wrap justify-space-between">
          <h5 class="text-h5 font-weight-bold">{{page_setting.title}}</h5>
          <v-breadcrumbs :items="breadcrumbs" class="pa-0"></v-breadcrumbs>
        </div>
      </v-banner>
      <div class="d-flex flex-wrap align-center">
        <v-text-field
          v-model="table.search"
          prepend-inner-icon="mdi-magnify"
          label="search"
          single-line
          dense
          clearable
          hide-details
          class="py-4"
          solo
          style="max-width: 300px"
        />
        <v-spacer />
      </div>
      
      <v-data-table
        :items="items.data"
        :headers="table.headers"
        :options.sync="table.options"
        :server-items-length="items.total"
        :loading="table.isLoading"
        :sort-by.sync="table.sortBy"
        :sort-desc.sync="table.sortDesc"
        class="elevation-1"
      >
        <template #[`item.index`]="{ index }">
          {{ (table.options.page - 1) * table.options.itemsPerPage + index + 1 }}
        </template>
        <!-- <template #[`item.service_provider.company_name`]="{ item }">
          {{ item.service_provider.company_name }}
        </template> -->
        <template #[`item.status`]="{ item }">
          <span class="status" :class="statusColor(item.status)">{{item.status}}</span>
        </template>
        <template #[`item.action`]="{ item }">
          <v-btn x-small color="primary" @click="viewData(item)">
            <v-icon small> mdi-eye </v-icon>
          </v-btn>
        </template>
      </v-data-table>

    </admin-layout>
  </template>
    
<script>
import AdminLayout from "../../layouts/AdminLayout.vue";
import ImageContainer from "../../components/ImageContainer.vue";

export default {
  props: ["items"],
  components: { AdminLayout, ImageContainer },
  data() {
    return {
      page_setting:{title:"Transactions", route:"transaction",interval:null},
      table:{
        sortBy: 'created_at',
        sortDesc: true,
        headers: [
          { text: "No", value: "index", sortable: false },
          { text: "Company", value: "service_provider.company_name",sortable: false },
          { text: "TransactionID", value: "trans_id" },
          { text: "Total Amount", value: "total_amount" },
          { text: "Transaction Fee", value: "transaction_fee" },
          { text: "Service Fee", value: "service_fee" },
          { text: "Commission Fee", value: "commission_fee" },
          { text: "Tax", value: "tax" },
          { text: "Status", value: "status" },
          { text: "Created At", value: "created_at" },
          { text: "Actions", value: "action", sortable: false },
        ],
        options: {},
        search: null,
        params: {},
        isLoading: false,
      },
      breadcrumbs: [
        {
          text: "App",
          disabled: false,
          href: "/dashboard",
        }
      ],
      isLoading: false,
      form: this.$inertia.form({
        
      }),
    }
  },
  computed: {
    options()
    {
      return this.table.options;
    },
    search()
    {
      return this.table.search;
    }
  },
  watch: {
    options: function (val) {
      this.table.params.page = val.page;
      this.table.params.page_size = val.itemsPerPage;
      if (val.sortBy.length != 0) {
        this.table.params.sort_by = val.sortBy[0];
        this.table.params.order_by = val.sortDesc[0] ? "desc" : "asc";
      } else {
        this.table.params.sort_by = null;
        this.table.params.order_by = null;
      }
      this.updateData();
    },
    search: function (val) {
      this.table.params.search = val;
      this.updateData();
    },
  },
  mounted(){
    this.breadcrumbs.push({
      text: this.page_setting.title,
      disabled: true,
      href: `${this.page_setting.route}`,
    });

    this.page_setting.interval = setInterval(() => {
      this.updateData(false);
    }, 5000);
  },
  methods: {
    updateData(loadingTable=true) {
      this.table.isLoading = loadingTable
      this.$inertia.get(`/${this.page_setting.route}`, this.table.params, {
        preserveState: true,
        preverseScroll: true,
        onSuccess: () => {
          this.table.isLoading = false
        },
      });
    },
  },
  beforeUnmount() {
      clearInterval(this.page_setting.interval);
  },

};
</script>
