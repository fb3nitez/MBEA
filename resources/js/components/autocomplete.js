export function autocomplete(element, items) {
    const input = element.querySelector('[data-autocomplete-input]');
    const list = element.querySelector('[data-autocomplete-list]');

    let matches = [];
    let activeIndex = -1;

    function search(value = '') {
        matches = items.filter(item =>
            item.toLowerCase().includes(value.toLowerCase())
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
        input.value = matches[index];
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
