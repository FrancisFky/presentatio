@props(['name', 'options' => [], 'placeholder' => 'Tous'])
<select name="{{ $name }}" onchange="this.form.submit()"
    class="h-10 rounded-lg border-0 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand-500">
    <option value="">{{ $placeholder }}</option>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) request($name) === (string) $value)>{{ $label }}</option>
    @endforeach
</select>
