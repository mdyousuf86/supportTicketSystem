@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center gy-4">

        <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-5">
            <div class="card b-radius--10">
                <div class="card-body">
                    <div class="card-title text-end">
                        @if ($categories->count())
                            <span class="close-tree cursor-pointer" data-state="collapsed">@lang('Collapse All')</span>
                        @else
                            <span class="text-muted">@lang('Categories will be displayed here')</span>
                        @endif
                    </div>

                    <div class="file-tree-wrapper">
                        <ul class="file-tree" id="fileTree">
                            @include('admin.category.category_tree')
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-8 col-xl-7 col-lg-6 col-md-7 col-sm-6">
            <div class="card right-sticky">
                <div class="card-body">
                    <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data"
                        id="addForm">
                        @csrf
                        <div class="form-group row">
                            <div class="col-xxl-2 col-xl-3">
                                <label>@lang('Parent Category')</label>
                            </div>
                            <div class="col-xxl-10 col-xl-9 select2-parent">
                                <select name="parent_id" class="form-control">
                                    <option value="" data-title="None" selected>@lang('None')</option>
                                    <x-category-options isAdmin=true :allCategories="$categories" />
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-xxl-2 col-xl-3">
                                <label>@lang('Name')</label>
                            </div>
                            <div class="col-xxl-10 col-xl-9">
                                <input type="text" class="form-control" value="{{ old('name') }}" name="name"
                                    required>
                            </div>
                        </div>

                        @can('admin.category.store')
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn--primary h-45 flex-grow-1"
                                    id="submitButton">@lang('Submit')</button>
                                <button type="reset" class="btn btn--dark clearFormBtn" title="@lang('Clear Form')"> <i
                                        class="las la-redo-alt"></i></button>
                            </div>
                        @endcan

                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            const form = $('#addForm');
            const clearFormButton = $('.clearFormBtn');
            const submitButton = $('#submitButton');
            const formAction = `{{ route('admin.category.store', '') }}`;
            const fileTree = $("#fileTree");
            const parentCategorySelect = $('[name=parent_id]');
            let isSubmitting = false;

            const initSelect2ForParentCategory = () => {
                $('[name=parent_id]').select2({
                    dropdownParent: $('[name=parent_id]').parent('.select2-parent'),
                    templateSelection: function(data, container) {
                        return $(data.element).data('title');
                    }
                });
            }

            const handleParentClick = ($this) => {
                const data = $this.data('category');
                console.log(data);
                const fieldMappings = ['parent_id', 'name'];

                $('.parent.active').removeClass('active');

                $this.addClass('active');

                form.attr('action', `${formAction}/${data.id}`);

                $('.folder-root button').addClass('d-none');

                $this.siblings('button').removeClass('d-none');

                form.find('.select2-auto-tokenize').empty();

                initSelect2ForParentCategory();
            }

            const clearFormButtonClickHandler = () => {
                const parent = $(document).find('.parent.active');
                if (parent.length > 0) {
                    parent.first().removeClass('active');
                    parent.parents('li').find('button').addClass('d-none');
                }
                clearFormFields();
                initSelect2ForParentCategory();
            }

            const clearFormFields = () => {
                form.attr('action', `${formAction}/0`);
                form.find("input[type=text], textarea, select").val("");
                form.find('input[type=checkbox]').prop('checked', false);
                form.find('.select2-auto-tokenize').empty();
            }


            const addChildBtnClickHandler = function() {
                const parentCategory = $(this).siblings('.parent').data('category');

                // Update the parent category value in the form
                clearFormFields();

                form.find("[name=parent_id]").val(parentCategory.id);
                initSelect2ForParentCategory();
            }

            $(document).on('click', '.close-tree', function() {
                $(this).toggleTreeState();
            });

            $(document).on('click', '.parent', function() {
                handleParentClick($(this));
            });

            clearFormButton.on('click', clearFormButtonClickHandler);

            form.on('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) {
                    return;
                }

                submitButton.prop('disabled', true);
                submitButton.html('<i class="fa fa-circle-notch fa-spin" aria-hidden="true"></i>');

                $.ajax({
                    url: this.action,
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: new FormData(this),
                    success: function(response) {
                        if (response.status == 'error') {
                            notify('error', response.message);
                        } else {
                            fileTree.html(response.tree);
                            fileTree.filetree();
                            handleParentClick(fileTree.find(`[category=${response.categoryId}]`));
                            notify('success', response.message);
                            form.find('[name=parent_id] option:not(:first)').remove();
                            form.find('[name=parent_id]').append(response.allCategories);
                            form.find('[name=parent_id]').val(response.parentId);
                            initSelect2ForParentCategory();
                        }
                    }
                }).always((response) => {
                    isSubmitting = false;
                    submitButton.prop('disabled', false);
                    submitButton.text(`@lang('Submit')`);
                });

            });

            $(document).on('click', '.addChildBtn', addChildBtnClickHandler);

            fileTree.filetree();
            initSelect2ForParentCategory();
        })(jQuery)
    </script>
@endpush

@push('breadcrumb-plugins')
    @can('admin.category.store')
        <button type="reset" class="btn btn-outline--primary clearFormBtn"> <i class="las la-plus"></i>
            @lang('Add Parent Category')</button>
    @endcan
    @can('admin.category.trashed')
        <a href="{{ route('admin.category.trashed') }}" class="btn btn-outline--danger"><i class="las la-trash-alt"></i>
            @lang('Trashed')</a>
    @endcan
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/file-explore.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/file-explore.css') }}">
@endpush

@push('style')
    <style>
        .select2-container {
            z-index: 0 !important;
        }

        .folder-root button {
            height: 26px;
            line-height: 13px;
        }

        .category-icon .image--uploader,
        .category-icon .image-upload-wrapper {
            height: 45px;
        }

        .category-icon .image-upload-wrapper {
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .category-icon .image--uploader .image-upload-preview {
            position: absolute;
            height: 43px;
            width: 43px;
            border-radius: 5px;
            border: none;
        }

        .card.right-sticky {
            position: sticky;
            top: 30px;
        }

        .cursor-pointer:hover {
            cursor: pointer;
        }
    </style>
@endpush
