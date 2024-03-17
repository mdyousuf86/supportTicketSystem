@props([
    'subcategory' => [],
    'prefix',
    'isAdmin',
])

@php $prefix .='&nbsp; &nbsp;'  @endphp

@foreach ($subcategory->allSubcategories ?? [] as $childCategory)
    <option value="{{ $isAdmin ? $childCategory->id : $childCategory->slug() }}"
        data-title="{{ __($childCategory->name) }}"@selected($childCategory->id == slugToId(request()->category))>
        @php
            echo $prefix;
        @endphp {{ __($childCategory->name) }}
    </option>
    <x-subcategory-options :subcategory=$childCategory :prefix=$prefix :isAdmin=$isAdmin />
@endforeach
