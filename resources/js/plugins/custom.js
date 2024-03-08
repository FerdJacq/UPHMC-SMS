/* Custom Components */
import Breadcrumbs from '@/components/Breadcrumbs';
import ApplicationLogo from '@/components/ApplicationLogo';
import LoaderModal from '@/components/LoaderModal';
import FormWizard from '@/components/WizardForm';

/* Custom Layouts */
import GuestLayout from '@/layouts/GuestLayout';
import AdminLayout from '@/layouts/AdminLayout';

/*other packages*/
import { CountTo }  from 'vue3-count-to';
// import Datepicker from 'vue3-datepicker'

import $ from "jquery";


window.$ = $;

const item = {
    components: [
        { name: "app-logo", val: ApplicationLogo },
        { name: "app-breadcrumb", val: Breadcrumbs },
        { name: "app-loader-modal", val: LoaderModal },
        { name: "app-wizard", val: FormWizard },

        { name: "app-guest", val: GuestLayout },
        { name: "app-layout", val: AdminLayout },

        { name: "count-to", val: CountTo },
        // { name: "p-datepicker", val: Datepicker },
    ]
};

export default item;


