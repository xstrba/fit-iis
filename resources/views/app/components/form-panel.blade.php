@php
/**
 * @var string $id
 * @var bool $active
 * @var string $parent
 */
$active = $active ?? false;
@endphp

<div class="form-panel @if(!$active) d-none @endif py-4 px-2 border-info border-top" id="{{ $id }}" data-parent="{{ $parent }}">
    {!! $slot !!}
</div>
