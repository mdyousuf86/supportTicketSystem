<li class="folder-root {{ $subcategory->allSubcategories->count() > 0 ? 'open' : '' }}">
    @if ($subcategory->allSubcategories->count() > 0)
        <i class="las la-minus-circle file-opener-i"></i>
    @endif
    <span class="parent cursor-pointer me-1" category="{{ $subcategory->id }}" data-category="{{ $subcategory }}">
        {{ __($subcategory->name) }}
    </span>

    <button type="button" class="addChildBtn btn-sm btn--success d-none" title="@lang('Add child')"><i class="las la-plus-circle"></i></button>

    @can('admin.category.delete')
    <button type="button" class="deleteBtn btn-sm btn--danger d-none confirmationBtn" data-action="{{ route('admin.category.delete', $subcategory->id) }}" data-question="@lang('Are you sure to delete this category?')" title="@lang('Delete')"><i class="las la-trash"></i></button>
    @endcan
    @if ($subcategory->allSubcategories->count() > 0)
        <ul class="childs">
            @foreach ($subcategory->allSubcategories as $childCategory)
                @include('admin.category.subcategories', ['subcategory' => $childCategory])
            @endforeach
        </ul>
    @endif
</li>
