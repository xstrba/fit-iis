@php
/**
 * @var bool $active
 * @var string $target
 */
$active = $active ?? false;
@endphp

<a class="form-panel-label btn @if($active) btn-info @else btn-outline-info @endif" data-target="{{ $target }}">
    {!! $label !!}
</a>
