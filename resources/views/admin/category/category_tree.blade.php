@foreach ($categories as $category)
    @include('admin.category.subcategories', ['subcategory' => $category])
@endforeach
