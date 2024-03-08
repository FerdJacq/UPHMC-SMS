/* Styles */
import "primevue/resources/themes/saga-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeflex/primeflex.css";

/* Icons */
import 'primeicons/primeicons.css';


import PrimeVue from 'primevue/config';

// PrimeVue Service
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

/* PrimeVue Components */
import Card from 'primevue/card';
import Panel from 'primevue/panel';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

import Message from 'primevue/message';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import Paginator from 'primevue/paginator';

import ProgressSpinner from 'primevue/progressspinner';
import ProgressBar from 'primevue/progressbar';

import Password from 'primevue/password';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import InputSwitch from 'primevue/inputswitch';
import Button from 'primevue/button';
import Skeleton from 'primevue/skeleton';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import MultiSelect from 'primevue/multiselect';
import AutoComplete from 'primevue/autocomplete';
import Timeline from 'primevue/timeline';

import Ripple from 'primevue/ripple';
import Tooltip from 'primevue/tooltip';


const primevue = {
    components: [
        { name: "p-card", val: Card },
        { name: "p-panel", val: Panel },
        { name: "p-dialog", val: Dialog },
        { name: "p-confirm", val: ConfirmDialog },
        { name: "p-toast", val: Toast },
        { name: "p-table", val: DataTable },
        { name: "p-column", val: Column },
        { name: "p-column-group", val: ColumnGroup },
        { name: "p-row", val: Row },
        { name: "p-paginator", val: Paginator },
        { name: "p-input-password", val: Password },
        { name: "p-input-text", val: InputText },
        { name: "p-input-number", val: InputNumber },
        { name: "p-textarea", val: Textarea },
        { name: "p-dropdown", val: Dropdown },
        { name: "p-datepicker", val: Calendar },
        { name: "p-switch", val: InputSwitch },
        { name: "p-button", val: Button },
        { name: "p-skeleton", val: Skeleton },
        { name: "p-message", val: Message },
        { name: "p-field-icon", val: IconField },
        { name: "p-input-icon", val: InputIcon },
        { name: "p-multiselect", val: MultiSelect },
        { name: "p-progress-bar", val: ProgressBar },
        { name: "p-progress-spinner", val: ProgressSpinner },
        { name: "p-autocomplete", val: AutoComplete },
        { name: "p-timeline", val: Timeline },
    ],
    directive:[
        {name:"ripple",val:Ripple},
        {name:"tooltip",val:Tooltip}
    ],
    service:[ToastService,ConfirmationService],
    config:PrimeVue
};


  
export default primevue;