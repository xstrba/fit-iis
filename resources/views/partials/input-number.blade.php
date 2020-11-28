@php
/**
 * @var string|null $classes
 * @var string $name
 * @var string $id
 * @var string|null $description
 * @var string|null $placeholder
 * @var string $label
 * @var string|null $value
 * @var int $min
 * @var int $max
 * @var true|null $enableEvent
 * @var true|null $float
 */
$enableEvent = $enableEvent ?? false;
@endphp

<div class="form-group @isset($classes) {{ $classes }} @endisset">
    <label for="{{ $id ?? $name }}" @error($name) class="error" @enderror>
        {{ $label }}
        @isset($required) <span class="required">*</span> @endisset
    </label>
    <input type="number"
           class="form-control"
           id="{{ $id ?? $name }}"
           name="{{ $name }}"
           aria-describedby="{{ ($id ?? $name) . 'Help' }}"
           @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
           @isset($value) value="{{ $value }}" @endisset
           @isset($required) required @endisset
           @isset($min) min="{{ $min }}" @endisset
           @isset($max) max="{{ $max }}" @endisset
           @isset($float) step="0.01" @else step="any" @endisset
           @if (!$enableEvent) onkeydown="return event.key != 'Enter';" @endif>
    @isset($description)
        <small id="{{ ($id ?? $name) . 'Help' }}" class="form-text text-muted">{{ $description }}</small>
    @endisset
    @error($name)
        <small class="form-text error">{{ $message }}</small>
    @enderror
</div>
