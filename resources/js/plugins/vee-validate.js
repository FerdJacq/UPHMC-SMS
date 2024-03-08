import { defineRule, configure, Form, Field, ErrorMessage } from 'vee-validate';
import { required } from '@vee-validate/rules';
import { localize } from '@vee-validate/i18n';
import en from '@vee-validate/i18n/dist/locale/en.json';

// Object.keys(AllRules).forEach(rule => {
//     defineRule(rule, AllRules[rule]);
// });

defineRule('required', required);

localize({ en })

const item = {
    components: [
        { name: "v-form", val: Form },
        { name: "v-field", val: Field },
        { name: "v-error", val: ErrorMessage },
    ],
    config:Form
};

export default item;