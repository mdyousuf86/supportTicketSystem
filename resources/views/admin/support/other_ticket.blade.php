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
                <div class="table-responsive--sm table-responsive mt-4">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>@lang('Subject')</th>
                                <th>@lang('Submitted By')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Priority')</th>
                                <th>@lang('Last Reply')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($otherTickets as $ticket)
                                <tr>
                                    <td>
                                        @can('admin.ticket.view')
                                            <a href="{{ route('admin.ticket.view', $ticket->ticket_number) }}" class="fw-bold">
                                                [@lang('Ticket')#{{ $ticket->ticket }}]
                                                {{ strLimit($ticket->subject, 30) }}
                                            </a>
                                        @endcan
                                    </td>

                                    <td>
                                        @if ($ticket->user_id)
                                            @can('admin.users.detail')
                                                <a href="{{ route('admin.users.detail', $ticket->user_id) }}">
                                                    {{ @$ticket->user->fullname }}</a>
                                            @else
                                                <span>@</span>{{ @$item->username }}
                                            @endcan
                                        @else
                                            <p class="fw-bold"> {{ $ticket->user_name }}</p>
                                        @endif
                                    </td>
                                    <td class="fw-bold" style="color:{{ $ticket->status_color }}">
                                        {{ $ticket->status }}
                                    </td>
                                    <td class="fw-bold" style="color: {{ $ticket->priority_color }}">
                                        {{ $ticket->priority }}
                                    </td>

                                    <td>
                                        {{ diffForHumans($ticket->last_reply) }}
                                    </td>

                                    <td>
                                        @can('admin.ticket.view')
                                            <a href="{{ route('admin.ticket.view', $ticket->ticket_number) }}"
                                                class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        {{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                @if ($otherTickets->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($otherTickets) }}
                    </div>
                @endif
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

@can('admin.branch.index')
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
