<template>
  <div>
    <div class="account-pages my-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-4">
            <slot />
          </div> <!-- end col -->
        </div>
        <!-- end row -->
      </div>
      <!-- end container -->
    </div>
    <p-toast position="top-center"/>
    
    <!-- end page -->
    <footer class="footer footer-alt">
      2025 &copy; Wise App by&nbsp;<span href="#!" class="text-dark text-decoration-underline">Sixpent Inc.</span>
    </footer>
  </div>
</template>

<script>
export default {
  watch: {
    $page: {
      handler() {
        const message = this.$page.props.flash.message;
        if (message != null) {
          switch (message.type) {
            case "success":
              this.$toast.success(message.text);
              break;
            case "error":
              this.$toast.error(message.text);
              break;
          }
        }
      },
    },
  },
};
</script>
