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
                                    <th>@lang('Is Active Tickets')</th>
                                    <th>@lang('Is Awaiting Tickets')</th>
                                    <th>@lang('Is Auto Close Tickets')</th>
                                    <th>@lang('Status')</th>
                                    @if (can('admin.status.save') || can('admin.status.status'))
                                        <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="sort">
                                @forelse($ticketStatuses as $ticketStatus)
                                    <tr class="sortable-table-row" data-id="{{ $ticketStatus->id }}">
                                        <td class="fw-bold">
                                            <i class="fa fa-arrows-alt me-2"></i>
                                            <span
                                                style="color: {{ @$ticketStatus->color }};">{{ __(@$ticketStatus->title) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div
                                                class="status-icon {{ @$ticketStatus->is_active ? 'bg--success' : 'bg--danger' }}">
                                                <i
                                                    class="{{ @$ticketStatus->is_active ? 'las la-check' : 'las la-times' }}"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="status-icon {{ @$ticketStatus->is_awaiting ? 'bg--success' : 'bg--danger' }}">
                                                <i
                                                    class="{{ @$ticketStatus->is_awaiting ? 'las la-check' : 'las la-times' }}"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="status-icon {{ @$ticketStatus->auto_close ? 'bg--success' : 'bg--danger' }}">
                                                <i
                                                    class="{{ @$ticketStatus->auto_close ? 'las la-check' : 'las la-times' }}"></i>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                echo @$ticketStatus->statusBadge;
                                            @endphp
                                        </td>
                                        @if (can('admin.status.save') || can('admin.status.status'))
                                            <td>
                                                @can('admin.status.save')
                                                    <button type="button" data-id="{{ $ticketStatus->id }}"
                                                        data-name="{{ $ticketStatus->title }}"
                                                        class="btn btn-sm btn-outline--primary editBtn"
                                                        data-ticket_status="{{ $ticketStatus }}"> <i class="las la-pen"></i>
                                                        @lang('Edit') </button>
                                                @endcan
                                                @can('admin.status.status')
                                                    @if ($ticketStatus->is_default == Status::NO)
                                                        @if ($ticketStatus->status == Status::DISABLE)
                                                            <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                                data-question="@lang('Are you sure to enable this ticket status?')"
                                                                data-action="{{ route('admin.status.status', $ticketStatus->id) }}">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                                data-question="@lang('Are you sure to disable this ticket status?')"
                                                                data-action="{{ route('admin.status.status', $ticketStatus->id) }}">
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
                @if ($ticketStatuses->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ticketStatuses) }}
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
                            <label for="name">@lang('Title')</label>
                            <input type="text" class="form-control" name="title">
                        </div>

                        <div class="form-group">
                            <label for="name">@lang('Color')</label>
                            <input type="color" class="form-control" name="color">
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="name">@lang('Is Active')</label>
                                    <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-height="35"
                                        data-on="@lang('Enable')" data-off="@lang('Disable')" name="is_active">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group ">
                                    <label for="name">@lang('Is Awaiting')</label>
                                    <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-height="35"
                                        data-on="@lang('Enable')" data-off="@lang('Disable')" name="is_awaiting">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group ">
                                    <label for="name">@lang('Auto Close')</label>
                                    <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-height="35"
                                        data-on="@lang('Enable')" data-off="@lang('Disable')" name="auto_close">
                                </div>
                            </div>
                        </div>
                    </div>
                    @can('admin.status.save')
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
    @can('admin.status.save')
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
        .status-icon {
            border-radius: 50%;
            color: white;
            font-size: 10px;
            width: 20px;
            height: 20px;
            line-height: 22px;
            margin: 0 auto
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
                var ticketStatus = $(this).data('ticket_status');
                var action = "{{ route('admin.status.save', ':id') }}";
                modal.find('input[name=title]').val(ticketStatus.title);
                modal.find('input[name=color]').val(ticketStatus.color);

                $('input[name=is_active]').bootstrapToggle(ticketStatus.is_active ? 'on' : "off");
                $('input[name=is_awaiting]').bootstrapToggle(ticketStatus.is_awaiting ? 'on' : "off");
                $('input[name=auto_close]').bootstrapToggle(ticketStatus.auto_close ? 'on' : "off");
                form.attr('action', action.replace(":id", ticketStatus.id));
                modal.find(`.modal-title`).text(`@lang('Edit Ticeket Status')`);
                modal.modal('show');
            });

            $('.addBtn').on('click', function() {
                let modal = $('#modal');
                let form = modal.find(`form`);
                modal.find(`.modal-title`).text(`@lang('Add Ticket Status')`);
                form.attr('action', "{{ route('admin.status.save') }}");
                form.trigger('reset');
                modal.modal('show');
            });
        })(jQuery)

        function updateSortOrder(sorting) {
            var action = "{{ route('admin.status.sort') }}";
            var csrf = "{{ csrf_token() }}";
            sortOrderAction(sorting, action, csrf);
        }
    </script>
@endpush
