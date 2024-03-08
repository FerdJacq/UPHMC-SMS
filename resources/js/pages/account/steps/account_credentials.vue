<template>
    <div>
        <div class="col-12">
            <h3 class="mb-4">Account Credential</h3>
        </div>

        <div class="field">
            <div class="p-float-label">
                <p-dropdown
                    v-model="role"
                    inputId="role"
                    :options='["ADMIN", "BIR", "DSP","SELLER", "RDO"]'
                    class="w-full"
                />
                <label for="role">Role</label>
            </div>
        </div>

        <v-field  v-if="role=='RDO'" tag="div" class="field" slim rules="required" name="region" vid="region" v-slot="{ errors }">
            <span class="p-float-label">
                <p-multiselect 
                    class="w-100"
                    :options="region_list"
                    optionLabel="name"
                    optionValue="region_code"
                    display="chip"
                    v-model="region"
                    inputClass="form-control shadow-none"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="role">Region</label>
                <small class="p-error">{{ errors[0] }}</small>
            </span>
        </v-field>

        <!-- User Credentials -->

        <v-field as="div" class="field" slim rules="required" name="email" vid="email" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    id="email"
                    type="text"
                    v-model="email"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="email">Email address</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field" slim rules="required" name="username" vid="username" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    id="username"
                    type="text"
                    v-model="username"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="username">Username</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>
        
        <v-field as="div" class="field" slim name="password" vid="password" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-password
                    id="password"
                    v-model="password"
                    toggleMask
                    :feedback=false
                    maxlength="50"
                    :inputClass="{
                        'form-control shadow-none': !0,
                        'p-invalid': errors[0]
                    }"
                />
                <label for="password">Password</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field v-if="password" tag="div" class="field" slim name="confirm_password" vid="confirm_password" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-password
                    id="confirm_password"
                    v-model="confirm_password"
                    toggleMask
                    :feedback=false
                    maxlength="50"
                    :inputClass="{
                        'form-control shadow-none': !0,
                        'p-invalid': errors[0]
                    }"
                />
                <label for="confirm_password">Confirm Password</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>
    </div>
</template>
<script>
    import ImageUpload from '../../../components/ImageUpload';

    export default {
        components : { ImageUpload },
        props: ['service'],

        data() {
            return {
                image: '',
                username: '',
                password: '',
                confirm_password: '',
                first_name: '',
                middle_name: '',
                last_name: '',
                email: '',
                role: 'DSP',
                region:[],
                region_list:[],
                selected_regions:[]
            }
        },

        methods: {
            fillInfo() {
                this.image = this.service.image;
                this.username = this.service.username;
                this.password = this.service.password;
                this.confirm_password = this.service.confirm_password;
                this.first_name = this.service.first_name;
                this.middle_name = this.service.middle_name;
                this.last_name = this.service.last_name;
                this.email = this.service.email;
                this.role = this.service.role;
                this.region = this.service.region;
            },

            setImage(base64) {
                this.image = base64;
            }
        },

        mounted() {
            this.fillInfo();

            axios.get(route("region.list"), {}).then(e=>{
                this.region_list =e.data.data;
            });
        },

        watch: {
            image() {
                this.service.image = this.image;
            },

            region(){
                this.service.region = this.region;
            },

            username() {
                this.service.username = this.username;
            },

            password() {
                this.service.password = this.password;
            },

            confirm_password() {
                this.service.confirm_password = this.confirm_password;
            },

            first_name() {
                this.service.first_name = this.first_name;
            },

            middle_name() {
                this.service.middle_name = this.middle_name;
            },

            last_name() {
                this.service.last_name = this.last_name;
            },

            email() {
                this.service.email = this.email;
            },

            role() {
                this.service.role = this.role;
            }
        }
    }
</script>