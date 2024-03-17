@props(['isAdmin' => false, 'isDigital' => null, 'allCategories' => null])

@php
    $categories = $allCategories ?? $categories;
@endphp

@foreach ($categories as $category)
    <option value="{{ $isAdmin ? $category->id : $category->slug() }}" data-title="{{ __($category->name) }}"
        @selected($category->id == slugToId(request()->category))>
        {{ __($category->name) }}
    </option>

    @php $prefix = '&nbsp; &nbsp;'; @endphp

    @foreach ($category->allSubcategories as $subcategory)
        <option value="{{ $isAdmin ? $subcategory->id : $subcategory->slug() }}" data-title="{{ __($subcategory->name) }}"
            @selected($subcategory->id == slugToId(request()->category))>
            @php
                echo $prefix;
            @endphp
            {{ __($subcategory->name) }}
        </option>

        <x-subcategory-options :subcategory=$subcategory :prefix=$prefix :isAdmin=$isAdmin />
    @endforeach
@endforeach
