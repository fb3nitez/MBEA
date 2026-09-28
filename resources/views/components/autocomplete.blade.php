@props([
'id',
'name' => null,
'value' => '',
'placeholder' => 'Search or select...',
'options' => [],
'multiple' => false,
])

<div
    class="autocomplete"
    data-autocomplete
    data-autocomplete-options="{{ json_encode($options, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
    data-autocomplete-multiple="{{ $multiple ? 'true' : 'false' }}"
    id="{{ $id }}">
    <input
        type="text"
        name="{{ $name ?? $id }}"
        id="input_{{ $id }}"
        value="{{ $value ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        autocomplete="off"
        class="autocomplete-input"
        data-autocomplete-input>

    <ul
        class="autocomplete-list"
        data-autocomplete-list></ul>
</div>