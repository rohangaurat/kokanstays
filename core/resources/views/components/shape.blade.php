@props([
    'shapeClass' => '',
    'fileName' => '',
])
<div class="{{ $shapeClass }}">
    <img src="{{ asset(activeTemplate(true) . 'images/shapes/' . $fileName . '.png') }}" alt="shape-image">
</div>
