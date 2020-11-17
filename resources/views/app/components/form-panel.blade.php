@php
/**
 * @var string $id
 * @var bool $active
 * @var string $parent
 * @var string $color
 */
$active = $active ?? false;
$color = $color ?? 'info';
@endphp

<div class="form-panel @if(!$active) d-none @endif py-4 px-2 border-{{ $color }} border-top" id="{{ $id }}" data-parent="{{ $parent }}">
    {!! $slot !!}
</div>
