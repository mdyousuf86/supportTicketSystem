@php
    $ticketCreatedTime = \Carbon\Carbon::parse($ticket->last_reply);
    $currentTime = \Carbon\Carbon::now();
    $timeDifference = $ticketCreatedTime->diff($currentTime);
    $hours = $timeDifference->h;
    $minutes = $timeDifference->i;
    $seconds = $timeDifference->s;
@endphp

<div class="card-title  mb-4">
    <div class="row gy-2">
        <div class="col-sm-8">
            <span class="ticket-number">[@lang('Ticket#'){{ $ticket->ticket_number }}]</span> {{ $ticket->subject }}
            <p class="time"> <span>@lang('Last Reply: '){{ $hours }} @lang('hours') {{ $minutes }}
                    @lang('minutes') {{ $seconds }} @lang('seconds ago')</span></p>
        </div>
        <div class="col-sm-4">
            @if ($ticket->ticket_status_id != 4)
                <div class="d-flex justify-content-sm-end">
                    <button class="btn btn--danger btn-sm" type="button" data-bs-toggle="modal"
                        data-bs-target="#DelModal">
                        <span class="d-flex align-items-center close-btn">
                            <i class="las la-times-circle"></i> @lang('Close Ticket')
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="tab-wrapper">
    <div class="nav nav-tabs custom--tab" id="nav-tab" role="tablist">
        @can('admin.ticket.view')
            <a class="nav-link {{ menuActive('admin.ticket.view') }}"
                href="{{ route('admin.ticket.view', @$ticket->ticket_number) }}">@lang('Add Reply')
            </a>
        @endcan
        @can('admin.ticket.note')
            <a class="nav-link {{ menuActive('admin.ticket.note') }}"
                href="{{ route('admin.ticket.note', @$ticket->ticket_number) }}">@lang('Add Note')
            </a>
        @endcan
        @can('admin.ticket.custom.fields')
            <a class="nav-link {{ menuActive('admin.ticket.custom.fields') }}"
                href="{{ route('admin.ticket.custom.fields', @$ticket->ticket_number) }}">@lang('Custom Fields')
            </a>
        @endcan
        @can('admin.ticket.other.tickets')
            <a class="nav-link {{ menuActive('admin.ticket.other.tickets') }}"
                href="{{ route('admin.ticket.other.tickets', @$ticket->ticket_number) }}">@lang('Other Tickets')
            </a>
        @endcan
        @can('admin.ticket.log')
            <a class="nav-link {{ menuActive('admin.ticket.log') }}"
                href="{{ route('admin.ticket.log', @$ticket->ticket_number) }}">@lang('Log')
            </a>
        @endcan
    </div>
</div>
