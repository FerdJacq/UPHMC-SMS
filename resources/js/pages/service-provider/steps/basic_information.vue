<template>
    <div>
        <div class="col-12">
            <h3 class="mb-4">Basic information</h3>
        </div>

        <div class="field d-flex justify-content-center mb-3">
            <ImageUpload
                :url="image"
                @setImage="setImage"
                :containerWidth=200
                :containerHeight=150
            />
        </div>

        <v-field as="div" class="field" slim rules="required" name="Company Code" vid="company_code" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    type="text"
                    v-model="company_code"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label>Company Code</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field"  rules="required" slim name="company_name" vid="company_name" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    id="company_name"
                    type="text"
                    v-model="company_name"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="company_name">Company name</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>
        
        <v-field as="div" class="field"  rules="required" slim name="email" vid="email" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    id="email"
                    type="text"
                    v-model="email"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="email">Email</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field"  rules="required" slim name="category" vid="category" v-slot="{ errors }">
            <div class="p-float-label">
                <p-dropdown
                    v-model="category"
                    inputId="category"
                    :options='["E-COMMERCE", "E-WALLET", "SOCIAL PLATFORM"]'
                    class="w-full"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="category">Category</label>
            </div>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field" rules="required" slim name="status" vid="status" v-slot="{ errors }">
            <div class="p-float-label">
                <p-dropdown
                    v-model="status"
                    appendTo="body"
                    :options='["ACTIVE", "INACTIVE"]'
                    class="w-full"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="status">Status</label>
            </div>
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
                company_code: '',
                company_name: '',
                email: '',
                category: 'E-COMMERCE',
                status: 'ACTIVE'
            }
        },

        methods: {
            fillInfo() {
                this.image = this.service.image;
                this.company_code = this.service.company_code;
                this.company_name = this.service.company_name;
                this.email = this.service.email;
                this.category = this.service.category;
                this.status = this.service.status;
            },

            setImage(base64) {
                this.image = base64;
            }
        },

        mounted() {
            this.fillInfo();
        },

        watch: {
            image() {
                this.service.image = this.image;
            },

            company_code() {
                this.service.company_code = this.company_code;
            },

            company_name() {
                this.service.company_name = this.company_name;
            },

            email() {
                this.service.email = this.email;
            },

            category() {
                this.service.category = this.category;
            },

            status() {
                this.service.status = this.status;
            }
        }
    }
</script>