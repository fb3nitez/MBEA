import { autocomplete } from './components/autocomplete';
import '../css/autocomplete.css';

window.loadAutoComplete = (id, data) => {
    const element = document.getElementById(id)
    if (element) autocomplete(element, data);
}

function initializeAutocompletes() {
    document.querySelectorAll('[data-autocomplete-options]').forEach((element) => {
        const options = JSON.parse(element.dataset.autocompleteOptions || '[]');
        if (options.length) autocomplete(element, options);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAutocompletes);
} else {
    initializeAutocompletes();
}

