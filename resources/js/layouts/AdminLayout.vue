<template>
    <div>
        <!-- Begin page -->
        <div id="wrapper">
            <!-- ========== Topbar Start ========== -->
            <div class="navbar-custom">
                <div class="topbar container-fluid">
                    <div class="d-flex align-items-center">
                        <!-- Brand Logo Light -->
                        <a href="javascript:void(0);" @click="goToPage('/')" class="logo logo-light">
                            <span class="logo-lg">
                                <img src="/codefox/img/logo.png" alt="small logo">
                                <span class="logo-text">{{_app_name}}</span>
                            </span>
                            <span class="logo-sm">
                                <img src="/codefox/img/logo.png" alt="small logo">
                            </span>
                        </a>

                        <!-- Brand Logo Dark -->
                        <a href="javascript:void(0);" @click="goToPage('/')" class="logo logo-dark">
                            <span class="logo-lg">
                                <img src="/codefox/img/logo.png" alt="small logo">
                                <span class="logo-text">{{_app_name}}</span>
                            </span>
                            <span class="logo-sm">
                                <img src="/codefox/img/logo.png" alt="small logo">
                            </span>
                        </a>

                        <ul class="topbar-menu d-flex align-items-center gap-1 p-0">
                            <li class="waves-effect waves-light">
                                <button class="button-toggle-menu" @click="toggleMenu">
                                    <i class="fe-menu"></i>
                                </button>
                            </li>
                            <li class="waves-effect waves-light">
                                <!-- <span class="logo-text" v-text="_app_name"></span> -->
                            </li>
                        </ul>
                    </div>

                    <ul class="topbar-menu d-flex align-items-center gap-1">
                        <li class="d-none d-sm-inline-block">
                            <div class="nav-link waves-effect waves-light" @click="toggleNightMode()" id="light-dark-mode">
                                <i class="ri-moon-line font-22"></i>
                            </div>
                        </li>
                        <li class="d-none d-md-inline-block">
                            <a class="nav-link waves-effect waves-light" href="javascript:void(0);" @click="toggleFullScreen">
                                <i class="fe-maximize font-22"></i>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link dropdown-toggle arrow-none nav-user waves-effect waves-light" :class="{'show': profileDropdownShow}" href="javascript:void(0);" role="button" @click="toggleProfileDropdown">
                                <span class="account-user-avatar">
                                    <img :src="`${_account.avatar}#?${randString(10)}`" width="32" height="32" class="rounded-circle"/>
                                </span>
                                <span class="d-lg-flex flex-column gap-1 d-none">
                                    <span class="my-0">{{ auth_fullname() }}
                                        <span class="mdi mdi-chevron-down"></span>
                                    </span>
                                </span>
                            </a>
                            <div id="profile-dropdown" class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown" :class="{'show': profileDropdownShow}">
                                <div class=" dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>
                                <a href="javascript:void(0);" @click="goToPage('/profile')" class="dropdown-item">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    <span>My Account</span>
                                </a>
                                <a href="javascript:void(0);" @click="logout" class="dropdown-item">
                                    <i class="mdi mdi-logout me-1"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- ========== Topbar End ========== -->

            <!-- ========== Left Sidebar Start ========== -->
            <div class="leftside-menu">
                <!-- Brand Logo Light -->
                <a href="javascript:void(0);" @click="goToPage('/')" class="logo logo-light">
                    <span class="logo-lg">
                        <img src="/codefox/img/logo-sm.png" alt="small logo">
                        <span class="logo-text">asdz    </span>
                    </span>
                    <span class="logo-sm">
                        <img src="/codefox/img/logo-sm.png" alt="small logo">
                    </span>
                </a>

                <!-- Brand Logo Dark -->
                <a href="javascript:void(0);" @click="goToPage('/')" class="logo logo-dark">
                    <span class="logo-lg">
                        <img src="/codefox/img/logo-sm.png" alt="small logo">
                        <span class="logo-text">asda</span>
                    </span>
                    <span class="logo-sm">
                        <img src="/codefox/img/logo-sm.png" alt="small logo">
                    </span>
                </a>

                <!-- Full Sidebar Menu Close Button -->
                <div class="button-close-fullsidebar" @click="toggleMenu">
                    <i class="ri-close-fill align-middle"></i>
                </div>

                <!-- Sidebar -left -->
                <div class="h-100" id="leftside-menu-container" data-simplebar style="overflow-y:auto">
                    <!--- Sidemenu -->
                    <ul class="side-nav">
                        <li
                            v-for="(item, index) in items"
                            :key="index"
                            :class="{
                                'side-nav-title': !item.to.length,
                                'side-nav-item': item.to.length,
                                'menuitem-active':isActive(item.to)
                                // 'menuitem-active' : index === indexMenu
                            }"
                        >
                            <span v-if="!item.to.length" v-text="item.title"></span>
                            <a @click="goToPage(item.to)" href="javascript:void(0);" class="side-nav-link" v-else>
                                <i :class="item.icon"></i>
                                <span v-text="item.title"></span>
                                <span v-if="item.count && counts[item.count]>0" class="badge bg-danger">{{ formatNumber(counts[item.count])}}</span>
                            </a>
                        </li>
                    </ul>
                    <!--- End Sidemenu -->

                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- ========== Left Sidebar End ========== -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <div class="content">
                    <!-- Start Content-->
                    <div class="container-fluid">
                        <slot />
                    </div> <!-- container -->
                </div> <!-- content -->
                <p-toast position="top-right"/>
                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                2025 &copy; {{_app_name}} - Sixpent Inc
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-end footer-links d-none d-md-block"></div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!--End Footer -->
            </div>
            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
        </div>
        <!-- END wrapper -->
        <Sound></Sound>
    </div>
</template>
<script>
import Sound from '../components/Sound.vue'

export default {
    components: { Sound },

    data() {
        return {
            html: document.getElementsByTagName("html")[0],
            width: window.innerWidth,
            height: window.innerHeight,
            profileDropdownShow: !1,
            items: [],
            interval:null,
            counts:{pending:0,ongoing:0,delivered:0,completed:0,cancelled:0,refunded:0,remitted:0,unremitted:0,total:0,editted:0},
            menus: [
                { icon: "", title: "Navigation", to: "", role:["ADMIN","BIR","DSP","SELLER","RDO"], count:"" },
                { icon: "fe-bar-chart-line-", title: "Dashboard", to: "/", role:["ADMIN","BIR","DSP","SELLER","RDO"], count:"" },

                { icon: "", title: "Members", to: "", role:["ADMIN","BIR"], count:"" },
                { icon: "fe-users", title: "Account", to: "/account", role:["ADMIN"], count:"" },
                { icon: "fe-shopping-bag", title: "Service Provider", to: "/service-provider", role:["ADMIN","BIR"], count:"" },

                { icon: "", title: "Data", to: "", role:["ADMIN","BIR","DSP","SELLER",,"RDO"], count:"" },
                { icon: "fe-calendar", title: "Calendar", to: "/calendar", role:["ADMIN","BIR","DSP"], count:"" },
                { icon: "fe-folder", title: "Online Sellers", to: "/seller", role:["ADMIN","BIR", "DSP","RDO"], count:"seller2" },
                { icon: "fe-folder", title: "Transactions", to: "/transaction", role:["ADMIN"], count:"total" },
                { icon: "fe-shopping-cart", title: "Pending", to: "/transaction?status=pending", role:["BIR","DSP","SELLER","RDO"], count:"pending" },
                { icon: "ri-luggage-cart-line", title: "Ongoing", to: "/transaction?status=ongoing", role:["BIR","DSP","SELLER","RDO"], count:"ongoing" },
                { icon: "ri-truck-line", title: "Delivered", to: "/transaction?status=delivered", role:["BIR","DSP","SELLER","RDO"], count:"delivered" },
                { icon: "fe-check-circle", title: "Completed", to: "/transaction?status=completed", role:["BIR","DSP","SELLER","RDO"], count:"completed" },
                { icon: "fe-x-circle", title: "Cancelled", to: "/transaction?status=cancelled", role:["BIR","DSP","SELLER","RDO"], count:"cancelled" },
                { icon: "fe-refresh-cw", title: "Refunded", to: "/transaction?status=refunded", role:["BIR","DSP","SELLER","RDO"], count:"refunded" },
                { icon: "ri-secure-payment-line", title: "Remitted", to: "/transaction?status=remitted", role:["BIR","DSP","SELLER","RDO"], count:"remitted2" },
                { icon: "ri-ticket-line", title: "Unremitted", to: "/transaction?status=unremitted", role:["BIR","DSP","SELLER","RDO"], count:"unremitted" },

                { icon: "", title: "Settings", to: "", role:["ADMIN","BIR","DSP","SELLER"], count:"" },
                { icon: "fe-wifi", title: "Whitelist", to: "/whitelist", role:["ADMIN"], count:"" },
                { icon: "fe-folder", title: "Series", to: "/series", role:["ADMIN","BIR","DSP"], count:"" },
                { icon: "fe-folder", title: "Generate Series", to: "/series_collection", role:["ADMIN","BIR","DSP"], count:"" },
                { icon: "fe-folder", title: "Upload Series", to: "/series_upload", role:["ADMIN","BIR","DSP"], count:"" },
                { icon: "fe-folder", title: "Seller", to: "/seller_settings", role:["SELLER"], count:"" },
            ]
        };
    },
    
    computed: {
    },

    watch: {
        $page: {
            handler() {
                const message = this.$page.props.flash.message;
                
                if (message != null) {
                    switch (message.type) {
                        case "success":
                            this.swalMessage("success", message.text, "Okay", "Cancel", "", false, false, "", false);
                            // this.$toast.success(message.text);
                            break;
                        case "error":
                            this.swalMessage("error", message.text, "Okay", "Cancel", "", false, false, "", false);
                            // this.$toast.error(message.text);
                        break;
                    }
                }
            }
        },

        width() {
            this.adjustSidebar();
        }
    },

    methods: {
        toggleMenu() { 
            let sidebarNavSize = "default";

            if(this.width <= 1140) {
                sidebarNavSize = "full";

                if(this.html.classList.contains("sidebar-enable")) {
                    this.hideBackdrop();
                }
                else {
                    this.showBackdrop();
                }
            }
            else {
                if(!this.html.classList.contains("sidebar-enable")) {
                    sidebarNavSize = "condensed";
                }
            }
            
            this.html.classList.toggle("sidebar-enable");
            this.html.setAttribute("data-sidenav-size", sidebarNavSize);
        },

        toggleProfileDropdown() {
            let e = document.getElementById('profile-dropdown');
            this.profileDropdownShow = !this.profileDropdownShow;

            if(this.profileDropdownShow) {
                e.style.cssText = "position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 72px);";
            }
            else {
                e.style.cssText = null;
            }
        },

        logout() {
            this.$inertia.post("/logout");
        },
        
        goToPage(page) {
            // this.$inertia.visit(this.route(page));
            this.$inertia.visit(page);

            if(this.width <= 1140) {
                this.toggleMenu();
            }
        },

        getDimensions() {
            this.width = window.innerWidth;
            this.height = window.innerHeight;
        },

        adjustSidebar() {
            let sidebarNavSize = "default";

            if(this.width <= 1140) {
                sidebarNavSize = "full";

                if(!this.html.classList.contains("sidebar-enable")) {
                    this.hideBackdrop();
                }
                else {
                    this.showBackdrop();
                }
            }
            else {
                this.hideBackdrop();

                if(this.html.classList.contains("sidebar-enable")) {
                    sidebarNavSize = "condensed";
                }
            }

            this.html.setAttribute("data-sidenav-size", sidebarNavSize);
        },

        showBackdrop() {
            if(document.getElementById("custom-backdrop")) return;

            var e = document.createElement("div"),
                t = (e.id = "custom-backdrop", e.classList = "offcanvas-backdrop fade show", document.body.appendChild(e), document.body.style.overflow = "hidden", 1140 < window.innerWidth && (document.body.style.paddingRight = "15px"), this);
                
                e.addEventListener("click", function(e) {
                    t.html.classList.remove("sidebar-enable"), t.hideBackdrop()
                });
        },

        hideBackdrop() {
            var e = document.getElementById("custom-backdrop");
                e && (document.body.removeChild(e), document.body.style.overflow = null, document.body.style.paddingRight = null)
        },

        toggleFullScreen() {
            document.body.classList.toggle("fullscreen-enable"),
            document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen() : document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
        },

        toggleNightMode(val="") {
            let e = (val) ? val : "light";

            if(this.html.getAttribute("data-bs-theme") == "light" && val=="") e = "dark";

            localStorage.setItem("night_mode", e);

            this.html.setAttribute("data-bs-theme", e);
        },

        isActive(route){
            let curr = window.location.pathname + window.location.search
            return (route == curr) ? true : false;
        }
    },
    
    created() {
        let self = this;
        
        self.items = this.menus.filter(e=>{
            return e.role.includes(self.$page.props.auth.user.role_name);
        });
    },

    mounted() {
        this.adjustSidebar();
        window.addEventListener('resize', this.getDimensions);
        this.toggleNightMode(localStorage.getItem("night_mode"));

        // this.interval = setInterval(() => {
        //     // this.emitter.emit('stop_sound_all');
        // }, 5000);
        // this.emitter.emit('play_sound',{name:"ting",loop:true});
    },

    unmounted() {
        window.removeEventListener('resize', this.getDimensions);
    },

    beforeDestroy() {
        clearInterval(this.interval);
    },

    beforeUnmount() {
        clearInterval(this.interval);
    } 
};
</script>
