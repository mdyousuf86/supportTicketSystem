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
                                    <th>@lang('Ticket Number')</th>
                                    <th>@lang('Comment')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Ip Address')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($ticketFeedbacks as $ticketFeedback)
                                    <tr>
                                        <td> {{ ($ticketFeedback->currentPage - 1) * $ticketFeedback->perPage + $loop->iteration }}
                                        </td>
                                        <td><a href="{{ route('admin.ticket.view', $ticketFeedback->tickets->ticket_number) }}"
                                                class="fw-bold">
                                                {{ $ticketFeedback->tickets->ticket_number }}
                                            </a>
                                        </td>
                                        <td> {{ __(@$ticketFeedback->comment) }} </td>
                                        <td class="text--warning">
                                            @for ($i = 0; $i < $ticketFeedback->rating; $i++)
                                                <i class="las la-star"></i>
                                            @endfor
                                        </td>
                                        <td> {{ $ticketFeedback->ip_address }} </td>
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
                @if ($ticketFeedbacks->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ticketFeedbacks) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Ticket Number" />
@endpush
