export function autocomplete(element, items) {
    if (!element || element.dataset.autocompleteInitialized === 'true') return;

    const input = element.querySelector('[data-autocomplete-input]');
    const list = element.querySelector('[data-autocomplete-list]');
    const multiple = element.dataset.autocompleteMultiple === 'true';

    element.dataset.autocompleteInitialized = 'true';

    let matches = [];
    let activeIndex = -1;

    function search(value = '') {
        const segments = multiple ? value.split(';') : [value];
        const lastSegment = segments.pop().trim();
        const lastSegmentIsSelected = multiple && items.some(item =>
            item.toLowerCase() === lastSegment.toLowerCase()
        );
        const query = lastSegmentIsSelected ? '' : lastSegment.toLowerCase();
        const selected = segments.map(item => item.trim()).filter(Boolean);
        if (lastSegmentIsSelected) selected.push(lastSegment);
        matches = items.filter(item =>
            !selected.some(selectedItem => selectedItem.toLowerCase() === item.toLowerCase()) &&
            item.toLowerCase().includes(query)
        );

        activeIndex = -1;
        render();
    }

    function render() {
        list.innerHTML = '';

        if (!matches.length) {
            close();
            return;
        }

        matches.forEach((item, index) => {
            const option = document.createElement('li');

            option.textContent = item;
            option.classList.add('autocomplete-option');

            if (index === activeIndex) {
                option.classList.add('active');
            }

            option.addEventListener('mousedown', () => {
                select(index);
            });

            list.appendChild(option);
        });

        list.hidden = false;
    }

    function select(index) {
        if (multiple) {
            const selected = input.value.split(';').map(item => item.trim()).filter(Boolean);
            if (selected.length && !items.some(item =>
                item.toLowerCase() === selected[selected.length - 1].toLowerCase()
            )) selected.pop();
            if (!selected.some(item => item.toLowerCase() === matches[index].toLowerCase())) {
                selected.push(matches[index]);
            }
            input.value = selected.join('; ');
        } else {
            input.value = matches[index];
        }
        close();
    }

    function close() {
        list.hidden = true;
        activeIndex = -1;
    }

    function keyboard(event) {
        if (!matches.length) return;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();

                activeIndex =
                    (activeIndex + 1) % matches.length;

                render();
                break;

            case 'ArrowUp':
                event.preventDefault();

                activeIndex =
                    (activeIndex - 1 + matches.length) %
                    matches.length;

                render();
                break;

            case 'Enter':
                if (activeIndex >= 0) {
                    event.preventDefault();
                    select(activeIndex);
                }
                break;

            case 'Escape':
                close();
                break;
        }
    }

    // Show suggestions when clicked/focused
    input.addEventListener('focus', () => {
        search(input.value);
    });

    // Filter while typing
    input.addEventListener('input', () => {
        search(input.value);
    });

    input.addEventListener('keydown', keyboard);

    // Close when clicking outside
    document.addEventListener('mousedown', event => {
        if (!element.contains(event.target)) {
            close();
        }
    });
}
