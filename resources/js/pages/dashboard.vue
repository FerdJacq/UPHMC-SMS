<template>
  <app-layout>
    <app-breadcrumb
      :title="page.title"
      :links="breadcrumbs"
    ></app-breadcrumb>

  </app-layout>
</template>

<script>
export default {
    data(){
        return {
            page: {
                title:"Dashboard",
                route:"dashboard",
                interval:null,
                tooltip:false,
            },
            breadcrumbs: [
                { current: false, title: 'Home', url: 'dashboard' }
            ],
          
        }
    },
    mounted() {
        this.breadcrumbs.push({
            current: true,
            title: this.page.title,
            url: `${this.page.route}`
        });
    },
    methods: {
        
    },
    computed: {
        
    },
    watch: {
        
    }
}
</script>