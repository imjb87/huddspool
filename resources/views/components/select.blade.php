<div x-data="" x-init="$($refs.{{ $name }}).select2();">
    <select x-ref="{{ $name }}" name="{{ $name }}" id="{{ $id }}" data-placeholder="{{ $placeholder }}" class="block w-full">
        <option></option>
        {{ $slot }}
    </select>
</div>