@props([
    'type' => '', // danger, success, info, warning, error
    'message' => '',
    'class' => '',
])

@php
    $color = 'gray';
    $icon = 'info';
    $borderColor = 'gray';
    $bgColor = 'gray';

    if ($type == 'danger') {
        $color = 'red';
        $icon = 'x-circle';
        $borderColor = 'red';
        $bgColor = 'red';
    } elseif ($type == 'success') {
        $color = 'green';
        $icon = 'check-circle';
        $borderColor = 'green';
        $bgColor = 'green';
    } elseif ($type == 'info') {
        $color = 'blue';
        $icon = 'info';
        $borderColor = 'blue';
        $bgColor = 'blue';
    } elseif ($type == 'warning') {
        $color = 'yellow';
        $icon = 'warning';
        $borderColor = 'yellow';
        $bgColor = 'yellow';
    }else{
        $color = 'gray';
        $icon = 'info';
        $borderColor = 'gray'; 
        $bgColor = 'gray';
    }
@endphp

@if($message)
<div class="flex items-center p-4 mb-4 text-sm text-{{ $color }}-700 border border-{{ $borderColor }}-500 bg-{{ $bgColor }}-100 rounded {{ $class }}" role="alert">
  <i class="ph ph-{{ $icon }} text-{{ $color }}-700 text-lg mr-2"></i>
  <span class="font-medium">
    {!! $message !!}
  </span>
</div>
@endif
