<template>
    <div>
        <div class="col-12">
            <h3 class="mb-4">Basic information</h3>
        </div>

        <div class="field d-flex justify-content-center mb-3">
            <ImageUpload
                :url="data.avatar"
                @setImage="setImage"
                :containerWidth=200
                :containerHeight=150
            />
        </div>

        <!-- User info -->
        <v-field as="div" class="field" slim name="first_name" vid="first_name" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    id="first_name"
                    type="text"
                    v-model="data.first_name"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="first_name">First name</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field" slim name="middle_name" vid="middle_name" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    id="middle_name"
                    type="text"
                    v-model="data.middle_name"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="middle_name">Middle name</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>

        <v-field as="div" class="field" slim name="last_name" vid="last_name" v-slot="{ errors }">
            <span class="p-float-label">
                <p-input-text
                    id="last_name"
                    type="text"
                    v-model="data.last_name"
                    class="form-control shadow-none"
                    maxlength="50"
                    :class="{ 'p-invalid': errors[0] }"
                />
                <label for="last_name">Last name</label>
            </span>
            <small class="p-error">{{ errors[0] }}</small>
        </v-field>
        
    </div>
</template>
<script>
    import ImageUpload from '../../../components/image-upload';

    export default {
        components : { ImageUpload },
        props: ['service'],

        data() {
            return {
                data:{
                    new_avatar: null,
                    avatar: null,
                    first_name: null,
                    middle_name: null,
                    last_name: null
                }
            }
        },

        methods: {
            fillInfo() {
                Object.entries(this.data).forEach((item) => {
                    let label = item[0];
                    this.data[label] = (label.includes('date') && this.service.data[label]) ? this.formatDate(this.parseDate(this.service.data[label])) : this.service.data[label];
                    
                });
            },

            setImage(base64) {
                this.data.image = base64;
                this.data.new_avatar = base64;
            }
        },

        mounted() {
            this.fillInfo();
        },

        watch: {
            data: {
                handler: function () {
                    Object.entries(this.data).forEach((item) => {
                        let label = item[0];
                        this.service.data[label] = label.includes('date') ? this.formatDate(this.parseDate(this.service.data[label])) : this.data[label];
                    });
                },
                deep: true,
                immediate: false
            }
        }
    }
</script>