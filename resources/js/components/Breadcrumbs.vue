<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li
                                v-for="(link, index) in links"
                                :key="index"
                                class="breadcrumb-item"
                                :class="{ active : link.current }"
                            >
                                <a href="javascript:void(0);" v-if="!link.current" v-text="link.title" @click="goToPage(link.url)"></a>
                                <span v-else v-text="link.title"></span>
                            </li>
                        </ol>
                    </div>
                    <h4 class="page-title" v-if="title.length">
                        <img src="/images/bir.png" width="50" v-if="title == 'BIR Dashboard'" />
                        {{ title }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : {
        title: { type: String },
        links: {
            type: Array,
            required: true
        }
    },

    methods : {
        goToPage(page) {
			this.$inertia.visit(this.route(page));
		}
    }
}
</script>
