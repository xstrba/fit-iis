@php
/**
 * @var bool $active
 * @var string $target
 * @var string $color
 * @var string $parent
 */
$active = $active ?? false;
$color = $color ?? 'info';
@endphp

<a class="form-panel-label btn @if($active) btn-{{ $color }} @else btn-outline-{{ $color }} @endif" data-target="{{ $target }}"
   data-color="{{ $color }}" data-parent="{{ $parent }}">
    {!! $label !!}
</a>
