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
                                    <th>@lang('Title')</th>
                                    <th>@lang('Color Code')</th>
                                    <th>@lang('Status')</th>
                                    @if (can('admin.priority.save') || can('admin.priority.status'))
                                        <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="sort">
                                @forelse($ticketPriorities as $ticketPriority)
                                    <tr class="sortable-table-row" data-id="{{ $ticketPriority->id }}">
                                        <td class="fw-bold">
                                            <i class="fa fa-arrows-alt me-2"></i> <span
                                                style="color:{{ @$ticketPriority->color }};">{{ __(@$ticketPriority->title) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="color-plate"
                                                    style="background-color:{{ __(@$ticketPriority->color) }}"></span>
                                                <span>{{ __(@$ticketPriority->color) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                echo @$ticketPriority->statusBadge;
                                            @endphp
                                        </td>
                                        @if (can('admin.priority.save') || can('admin.priority.status'))
                                            <td>
                                                @can('admin.priority.save')
                                                    <button type="button" data-id="{{ $ticketPriority->id }}"
                                                        data-name="{{ $ticketPriority->title }}"
                                                        class="btn btn-sm btn-outline--primary editBtn"
                                                        data-ticket_priority="{{ $ticketPriority }}"> <i class="las la-pen"></i>
                                                        @lang('Edit') </button>
                                                @endcan

                                                @can('admin.priority.status')
                                                    @if ($ticketPriority->is_default == Status::NO)
                                                        @if ($ticketPriority->status == Status::DISABLE)
                                                            <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                                data-question="@lang('Are you sure to enable this ticket priority?')"
                                                                data-action="{{ route('admin.priority.status', $ticketPriority->id) }}">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                                data-question="@lang('Are you sure to disable this ticket priority?')"
                                                                data-action="{{ route('admin.priority.status', $ticketPriority->id) }}">
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </button>
                                                        @endif
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
                @if ($ticketPriorities->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ticketPriorities) }}
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
                    <h5 class="modal-title">@lang('Add Ticket Priority')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        <span aria-hidden="true"><i class="las la-times"></i></span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">@lang('Title')</label>
                            <input type="text" class="form-control" name="title">
                        </div>

                        <div class="form-group">
                            <label for="name">@lang('Color')</label>
                            <input type="color" class="form-control" name="color">
                        </div>
                    </div>
                    @can('admin.priority.save')
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
    @can('admin.priority.save')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i> @lang('Add New')
        </button>
    @endcan
@endpush
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/table-sortable.css') }}">
@endpush
@push('style')
    <style>
        .color-plate {
            height: 15px;
            width: 15px;
            display: inline-block;
            border-radius: 50px;
            margin-right: 5px;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/admin/js/table-sortable.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            'use strict';
            $('.editBtn').on('click', function() {
                var modal = $('#modal');
                var form = modal.find('form');
                var ticketPriority = $(this).data('ticket_priority');
                var action = "{{ route('admin.priority.save', ':id') }}";
                modal.find('input[name=title]').val(ticketPriority.title);
                modal.find('input[name=color]').val(ticketPriority.color);
                form.attr('action', action.replace(":id", ticketPriority.id));
                modal.find(`.modal-title`).text(`@lang('Edit Ticeket Priority')`);
                modal.modal('show');
            });

            $('.addBtn').on('click', function() {
                let modal = $('#modal');
                let form = modal.find(`form`);
                modal.find(`.modal-title`).text(`@lang('Add Ticket Priority')`);
                form.attr('action', "{{ route('admin.priority.save') }}");
                form.trigger('reset');
                modal.modal('show');
            });
        })(jQuery)

        function updateSortOrder(sorting) {
            var action = "{{ route('admin.priority.sort') }}";
            var csrf = "{{ csrf_token() }}";
            sortOrderAction(sorting, action, csrf);
        }
    </script>
@endpush
