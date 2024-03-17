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
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Content')</th>
                                    @if (can('admin.spam.filter.save') || can('admin.spam.filter.delete'))
                                        <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($spamFilters as $spamFilter)
                                    <tr>
                                        <td> {{ ($spamFilter->currentPage - 1) * $spamFilter->perPage + $loop->iteration }}
                                        </td>
                                        <td> {{ __(@$spamFilter->filter_type) }} </td>
                                        <td> {{ __(@$spamFilter->content) }} </td>
                                        @if (can('admin.spam.filter.save') || can('admin.spam.filter.delete'))
                                            <td>
                                                @can('admin.spam.filter.save')
                                                    <button type="button" data-id="{{ $spamFilter->id }}"
                                                        data-name="{{ $spamFilter->title }}"
                                                        class="btn btn-sm btn-outline--primary editBtn"
                                                        data-spam_filter="{{ $spamFilter }}"> <i class="las la-pen"></i>
                                                        @lang('Edit') </button>
                                                @endcan
                                                @can('admin.spam.filter.delete')
                                                    <button type="button"
                                                        data-action="{{ route('admin.spam.filter.delete', $spamFilter->id) }}"
                                                        data-question="@lang('Are you sure you want to delete this spam filter?')"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"> <i
                                                            class="las la-trash"></i> @lang('Delete')</button>
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
                @if ($spamFilters->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($spamFilters) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="modal" class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Spam Filtes')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        <span aria-hidden="true"><i class="las la-times"></i></span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Filtes Type')</label>
                            <select name="filter_type" class="form-control" required>
                                <option value="" selected disabled>@lang('Select One') </option>
                                <option value="email">@lang('Email')</option>
                                <option value="ip">@lang('Ip')</option>
                                <option value="phrase">@lang('Phrase')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('Content')</label>
                            <textarea name="content" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                    @can('admin.spam.filter.save')
                        <div class="modal-footer">
                            <button type="submit" class="btn w-100 btn--primary h-45">@lang('Submit')</button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    @can('admin.spam.filter.save')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i> @lang('Add New')
        </button>
    @endcan
@endpush


@push('script')
    <script>
        (function($) {
            'use strict';
            $('.editBtn').on('click', function() {
                var modal = $('#modal');
                var form = modal.find('form');
                var spamFilter = $(this).data('spam_filter');
                var action = "{{ route('admin.spam.filter.save', ':id') }}";

                modal.find('select[name=filter_type]').val(spamFilter.filter_type);
                modal.find('textarea[name=content]').val(spamFilter.content);
                form.attr('action', action.replace(":id", spamFilter.id));
                modal.find(`.modal-title`).text(`@lang('Edit Spam Filter')`);
                modal.modal('show');
            });

            $('.addBtn').on('click', function() {
                let modal = $('#modal');
                let form = modal.find(`form`);
                modal.find(`.modal-title`).text(`@lang('Add Spam Filter')`);
                form.attr('action', "{{ route('admin.spam.filter.save') }}");
                form.trigger('reset');
                modal.modal('show');
            });

        })(jQuery)
    </script>
@endpush
