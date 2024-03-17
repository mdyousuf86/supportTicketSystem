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
                                <th>@lang('Date')</th>
                                <th>@lang('Requested Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($TicketLogs as $log)
                                <tr>
                                    <td>
                                        {{ showDateTime($log->created_at) }}
                                    </td>
                                    <td>
                                        {{ $log->action }}
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
                @if ($TicketLogs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($TicketLogs) }}
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
