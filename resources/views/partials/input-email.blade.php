@php
/**
 * @var string|null $classes
 * @var string $name
 * @var string $id
 * @var string|null $description
 * @var string|null $placeholder
 * @var string $label
 * @var string|null $value
 * @var true|null $required
 * @var true|null $enableEvent
 */
$enableEvent = $enableEvent ?? false;
@endphp

<div class="form-group @isset($classes) {{ $classes }} @endisset">
    <label for="{{ $id ?? $name }}" @error($name) class="error" @enderror>
        {{ $label }}
        @isset($required) <span class="required">*</span> @endisset
    </label>
    <input type="email"
           class="form-control"
           id="{{ $id ?? $name }}"
           name="{{ $name }}"
           aria-describedby="{{ ($id ?? $name) . 'Help' }}"
           @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
           @isset($value) value="{{ $value }}" @endisset
           @isset($required) required @endisset
           @if (!$enableEvent) onkeydown="return event.key != 'Enter';" @endif>
    @isset($description)
        <small id="{{ ($id ?? $name) . 'Help' }}" class="form-text text-muted">{{ $description }}</small>
    @endisset
    @error($name)
        <small class="form-text error">{{ $message }}</small>
    @enderror
</div>
