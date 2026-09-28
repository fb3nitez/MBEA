<div
    class="autocomplete"
    data-autocomplete
    id="{{ $id }}"
>
    <input
        type="text"
        name="{{ $name }}"
        id="input_{{ $id }}"
        value="{{ $value ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        autocomplete="off"
        class="autocomplete-input"
        data-autocomplete-input
    >

    <ul
        class="autocomplete-list"
        data-autocomplete-list
    ></ul>
</div>
