@props(['name'=>'','label'=>''])
<label for="{{ $name }}" ><h3 class="pb-2">{{ $label }}</h3>
<select name="{{ $name }}" id="{{ $name }}" 
class="w-full   rounded border dark:border-gray-500 h-auto dark:text-red-500'])" $attributes>
    {{ $slot }}

</select>
</label>