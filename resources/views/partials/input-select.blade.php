@php
/**
 * @var string|null $classes
 * @var string $name
 * @var string $id
 * @var string|null $description
 * @var string $label
 * @var mixed $value
 * @var string[] $options
 */
if (!isset($value)) {
    $value = null;
}
@endphp

<div class="form-group @isset($classes) {{ $classes }} @endisset">
    <label for="{{ $id ?? $name }}" @error($name) class="error" @enderror>
        {{ $label }}
        @isset($required) <span class="required">*</span> @endisset
    </label>
    <select class="form-control" id="{{ $id ?? $name }}" name="{{ $name }}"
            @isset($required) required @endisset>
        @foreach($options as $key => $label)
            <option value="{{ $key }}" @if($value === $key) selected @endif>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @isset($description)
        <small id="{{ ($id ?? $name) . 'Help' }}" class="form-text text-muted">{{ $description }}</small>
    @endisset
    @error($name)
        <small class="form-text error">{{ $message }}</small>
    @enderror
</div>
