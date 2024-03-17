@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('status')</th>
                                    @if (can('admin.reply.save') || can('admin.reply.status'))
                                        <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($predefinedReplies as $predefinedReply)
                                    <tr>
                                        <td class="fw-bold">{{ __($predefinedReply->name) }}</td>
                                        <td>
                                            @php
                                                echo $predefinedReply->statusBadge;
                                            @endphp
                                        </td>
                                        @if (can('admin.reply.save') || can('admin.reply.status'))
                                            <td>
                                                @can('admin.reply.save')
                                                    <button type="button" data-id="{{ $predefinedReply->id }}"
                                                        data-name="{{ $predefinedReply->title }}"
                                                        class="btn btn-sm btn-outline--primary editBtn"
                                                        data-reply="{{ $predefinedReply }}"> <i class="las la-pen"></i>
                                                        @lang('Edit')
                                                    </button>
                                                @endcan

                                                @can('admin.reply.status')
                                                    @if ($predefinedReply->status == Status::DISABLE)
                                                        <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                            data-question="@lang('Are you sure to enable this staff?')"
                                                            data-action="{{ route('admin.reply.status', $predefinedReply->id) }}">
                                                            <i class="la la-eye"></i> @lang('Enable')
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                            data-question="@lang('Are you sure to disable this staff?')"
                                                            data-action="{{ route('admin.reply.status', $predefinedReply->id) }}">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                        </button>
                                                    @endif
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($predefinedReplies->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($predefinedReplies) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />

    <div id="modal" class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Ticket Status')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        <span aria-hidden="true"><i class="las la-times"></i></span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category">@lang('Category')</label>
                            <select name="category_id" class="form-control" required>
                                <option value="" selected disabled>@lang('Select One or More')</option>
                                <x-category-options isAdmin=true :allCategories="$categories" />
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('Name')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="text-end">
                            <button type="button" id="previewButton"
                                class="btn btn-sm btn-outline--dark markdown-preview-btn ">
                                <i class="la la-eye"></i> @lang('Preview')
                            </button>
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('Reply Message')</label>
                            <textarea name="reply" class="form-control reply" rows="5" required>{{ old('reply') }}</textarea>

                            <iframe class="preview markdown-body border d-none w-100">
                            </iframe>
                        </div>
                    </div>
                    @can('admin.reply.save')
                        <div class="modal-footer">
                            <button type="submit" class="btn w-100 btn--primary h-45">@lang('Submit')</button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @can('admin.reply.save')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i> @lang('Add New')
        </button>
    @endcan
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/marked.min.js') }}"></script>
@endpush

@push('style')
    <style>
        .markdown-body {
            min-height: 119px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            $('.editBtn').on('click', function() {
                var modal = $('#modal');
                var form = modal.find('form');
                var reply = $(this).data('reply');
                var action = "{{ route('admin.reply.save', ':id') }}";
                modal.find('select[name=category_id]').val(reply.category_id);
                modal.find('input[name=name]').val(reply.name);
                modal.find('textarea[name=reply]').val(reply.reply);
                form.attr('action', action.replace(":id", reply.id));
                modal.find(`.modal-title`).text(`@lang('Edit Predefined Reply')`);
                modal.modal('show');
            });

            $('.addBtn').on('click', function() {
                let modal = $('#modal');
                let form = modal.find(`form`);
                modal.find(`.modal-title`).text(`@lang('Add New Reply Message')`);
                form.attr('action', "{{ route('admin.reply.save') }}");
                form.trigger('reset');
                modal.modal('show');
            });

            $('#previewButton').click(function() {
                var markdownText = $('.reply').val();
                var htmlContent = marked(markdownText);
                // $('.preview').html(htmlText);
                // var htmlContent = document.getElementById('htmlInput').value;
                $('.preview').attr('src', 'data:text/html;charset=utf-8,' + encodeURIComponent(htmlContent));
                // iframe.src = 'data:text/html;charset=utf-8,' + encodeURIComponent(htmlContent);
            });

            $(".markdown-preview-btn").on('click', function() {
                $(".preview").toggleClass(`d-none`);
                $(".reply").toggleClass(`d-none`);

            });
        })(jQuery)
    </script>
@endpush
