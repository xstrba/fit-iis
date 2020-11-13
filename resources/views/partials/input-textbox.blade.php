@php
/**
 * @var string|null $classes
 * @var string $name
 * @var string $id
 * @var string|null $description
 * @var string|null $placeholder
 * @var string $label
 * @var string|null $value
 * @var string $pattern
 */
@endphp

<div class="form-group @isset($classes) {{ $classes }} @endisset">
    <label for="{{ $id ?? $name }}" @error($name) class="error" @enderror>
        {{ $label }}
        @isset($required) <span class="required">*</span> @endisset
    </label>
    <textarea class="form-control"
           id="{{ $id ?? $name }}"
           name="{{ $name }}"
           aria-describedby="{{ ($id ?? $name) . 'Help' }}"
           @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
           @isset($required) required @endisset
           @isset($pattern) pattern="{{ $pattern }}" @endisset>{{ $value ?? '' }}</textarea>
    @isset($description)
        <small id="{{ ($id ?? $name) . 'Help' }}" class="form-text text-muted">{{ $description }}</small>
    @endisset
    @error($name)
        <small class="form-text error">{{ $message }}</small>
    @enderror
</div>