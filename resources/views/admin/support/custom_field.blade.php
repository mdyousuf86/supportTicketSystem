@extends('admin.layouts.master')
@section('content')
    <div class="body-overlay"></div>
    @include('admin.support.header')
    <div class="ticket-reply">
        @include('admin.support.ticket_sidebar')
        <div class="ticket-reply__right">
            <div class="d-xl-none d-block">
                <p class="bar-icon">
                    <i class="fa fa-list"></i>
                </p>
            </div>
            <div class="ticket-reply__body">
                @include('admin.support.card_header')
                <div class="row">
                    <div class="col-md-6">
                        @if ($ticket->extra_fields)
                            <ul class="list--group">
                                @foreach ($ticket->extra_fields as $val)
                                    @continue(!$val->value)
                                    <li class="list--group__item">
                                        <span class="list--group__title">{{ __($val->name) }}</span>
                                        <span class="list--group__text">
                                            @if ($val->type == 'checkbox')
                                                {{ implode(',', $val->value) }}
                                            @elseif($val->type == 'file')
                                                @if ($val->value)
                                                    <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"
                                                        class="me-3"><i class="fa fa-file"></i>
                                                        @lang('Attachment') </a>
                                                @else
                                                    @lang('No File')
                                                @endif
                                            @else
                                                <p>{{ __($val->value) }}</p>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <h5 class="text-center">@lang('Custom Fields data not found!')</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Close Support Ticket!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you want to close this support ticket?')</p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="{{ route('admin.ticket.close', $ticket->ticket_number) }}">
                        @csrf
                        <input type="hidden" name="replayTicket" value="2">
                        @can('admin.ticket.close')
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal"> @lang('No')
                            </button>
                            <button type="submit" class="btn btn--primary"> @lang('Yes') </button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@can('admin.ticket.index')
    @push('breadcrumb-plugins')
        <x-back route="{{ route('admin.ticket.index') }}" />
    @endpush
@endcan
@push('style')
    <style>
        .custom--tab .nav-link {
            color: #333;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            var $ticket = @json($ticket);
            $('#departmentId').on('change', function() {
                var department_id = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.ticket.department.change') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'department_id': department_id,
                        'ticket_id': $ticket.id,
                    },
                });
            });
            $('#priorityId').on('change', function() {
                var ticket_priority_id = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.ticket.priority.change') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'ticket_id': $ticket.id,
                        'ticket_priority_id': ticket_priority_id,
                    },
                });
            });

        })(jQuery);
    </script>
@endpush
