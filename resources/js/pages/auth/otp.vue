<template>
    <div>
        <app-guest>
            <div class="card shadow-none">
                <div class="card-body p-3">
                    <div class="text-center w-75 m-auto">
                        <div class="auth-logo">
                            <Link href="/" class="logo logo-dark text-center">
                                <span class="logo-lg">
                                    <app-logo />
                                </span>
                            </Link>
                        </div>
                    </div>

                    <div class="mt-3 text-center">
                        <svg version="1.1" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 98 98" style="height: 120px;" xml:space="preserve">
                            <g i:extraneous="self">
                                <circle id="XMLID_50_" class="st0" cx="49" cy="49" r="49" />
                                <g id="XMLID_4_">
                                    <path id="XMLID_49_" class="st1" d="M77.3,42.7V77c0,0.6-0.4,1-1,1H21.7c-0.5,0-1-0.5-1-1V42.7c0-0.3,0.1-0.6,0.4-0.8l27.3-21.7
                                                c0.3-0.3,0.8-0.3,1.2,0l27.3,21.7C77.1,42.1,77.3,42.4,77.3,42.7z" />
                                    <path id="XMLID_48_" class="st2" d="M66.5,69.5h-35c-1.1,0-2-0.9-2-2V26.8c0-1.1,0.9-2,2-2h35c1.1,0,2,0.9,2,2v40.7
                                                C68.5,68.6,67.6,69.5,66.5,69.5z" />
                                    <path id="XMLID_47_" class="st1" d="M62.9,33.4H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                                c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,33,63.4,33.4,62.9,33.4z" />
                                    <path id="XMLID_46_" class="st1" d="M62.9,40.3H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                                c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,39.9,63.4,40.3,62.9,40.3z" />
                                    <path id="XMLID_45_" class="st1" d="M62.9,47.2H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                                c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,46.8,63.4,47.2,62.9,47.2z" />
                                    <path id="XMLID_44_" class="st1" d="M62.9,54.1H47.2c-0.5,0-0.9-0.4-0.9-0.9v-0.2c0-0.5,0.4-0.9,0.9-0.9h15.7
                                                c0.5,0,0.9,0.4,0.9,0.9v0.2C63.8,53.7,63.4,54.1,62.9,54.1z" />
                                    <path id="XMLID_43_" class="st2" d="M41.6,40.1h-5.8c-0.6,0-1-0.4-1-1v-6.7c0-0.6,0.4-1,1-1h5.8c0.6,0,1,0.4,1,1v6.7
                                                C42.6,39.7,42.2,40.1,41.6,40.1z" />
                                    <path id="XMLID_42_" class="st2" d="M41.6,54.2h-5.8c-0.6,0-1-0.4-1-1v-6.7c0-0.6,0.4-1,1-1h5.8c0.6,0,1,0.4,1,1v6.7
                                                C42.6,53.8,42.2,54.2,41.6,54.2z" />
                                    <path id="XMLID_41_" class="st1" d="M23.4,46.2l25,17.8c0.3,0.2,0.7,0.2,1.1,0l26.8-19.8l-3.3,30.9H27.7L23.4,46.2z" />
                                    <path id="XMLID_40_" class="st3" d="M74.9,45.2L49.5,63.5c-0.3,0.2-0.7,0.2-1.1,0L23.2,45.2" />
                                </g>
                            </g>
                        </svg>

                        <h3>Two-Factor Authentication</h3>
                        <p class="text-muted"> Please enter the 6-digit authentication code sent to <b>{{ _user.email.toLowerCase() }}</b>.</p>
                    </div>

                    <v-form ref="form" as="form">
                        <alert-errors :form="form" message="There were some problems with your input."></alert-errors>
                        <div class="mb-3">
                            <v-field slim rules="required" name="otp" vid="otp" v-slot="{ errors }">
                                <p-input-text
                                    type="text"
                                    v-model="form.otp"
                                    placeholder="xxxxxx"
                                    class="form-control text-center otp-text"
                                    maxlength="6"
                                    :readonly="loading"
                                />
                            </v-field>
                        </div>

                        <div class="row mt-3">
                            <p-progress-spinner v-if="loading2" style="width:50px;height:50px" strokeWidth="8" fill="#EEEEEE" animationDuration=".5s"/>
                            <div class="col-12 text-center" v-else>
                                <p class="text-success" v-if="resent_message">
                                    {{resent_message}}
                                </p>
                                <p class="text-muted" v-else>
                                    Can't received the OTP?
                                    <a href="javascript:void(0);" class="text-primary text-decoration-underline" @click="resend()">
                                        <b>RESEND OTP</b>
                                    </a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="text-center d-grid">
                            <p-button
                                label="Continue"
                                :disabled="disabled_button"
                                @click="validate"
                                class="btn btn-primary"
                            />
                        </div>
                    </v-form>

                </div> 
            </div>

            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p class="text-muted">
                        Back to 
                        <a href="javascript:void(0);" class="text-dark text-decoration-underline ms-1" @click="logout">
                            <b>Log in</b>
                        </a>
                    </p>
                </div> <!-- end col -->
            </div>
        </app-guest>
    </div>
</template>
  
<script>
    import AuthService  from '../../services/auth';
    
    export default {
        components: { },
        data() {
            return {
                form: new Form({
                    otp: ""
                }),
                loading:false,
                loading2:false,
                codes_length:6,
                resent_message:"",
            };
        },

        computed:{
            disabled_button() {
                if (this.loading) return true;
                return (this.form.otp.length == 6) ? false : true;
            }
        },
        
        methods: {
            goNext(elem) {
                let input = elem.target;
                
                console.log(elem);
                
                let keyCode = (elem.keyCode ? elem.keyCode : elem.which);
                const currentIndex = Array.from(input.form.elements).indexOf(input);

                console.log(currentIndex);

                // console.log(keyCode)
                // if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                if ([46].includes(keyCode)) { // 46 is dot
                    elem.preventDefault();
                }
                else if(keyCode==8){
                    if(currentIndex>0)
                        input.form.elements.item(currentIndex - 1).focus();
                }
                else {
                    if(currentIndex<5)
                        input.form.elements.item(currentIndex + 1).focus();
                }
            },

            resend(){
                if (!this.loading)
                {
                    this.loading2 = true;
                    AuthService.resend_otp(this.form)
                    .then((response) => {
                        this.resent_message = response.data.message;
                    })
                    .catch((errors) => {
                        try { 
                            this.$refs.form.setErrors(this.getError(errors));
                        }
                        catch(ex){ console.log(ex)}
                    })
                    .finally(() => {
                        this.loading2 = false;
                    });
                }
            },

            logout() {
                this.$inertia.post("/logout");
            },

            validate() { 
                this.loading = true;
                AuthService.validate(this.form)
                .then((response) => {
                    this.$inertia.visit(this.route("/"));
                    document.body.classList.remove("authentication-bg");
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
            },
        },

        mounted() {
            document.body.classList.add("authentication-bg");
        }
    };
</script>

<style type="text/css" scoped>
    .st0 {
        fill: #FFFFFF;
    }

    .st1 {
        fill: #1abc9c;
    }

    .st2 {
        fill: #FFFFFF;
        stroke: #1abc9c;
        stroke-width: 2;
        stroke-miterlimit: 10;
    }

    .st3 {
        fill: none;
        stroke: #FFFFFF;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-miterlimit: 10;
    }
</style>