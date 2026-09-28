import { autocomplete } from './components/autocomplete';

window.loadAutoComplete =  (id, data) => {
    const element = document.getElementById(id)
    autocomplete(element, data);
}

