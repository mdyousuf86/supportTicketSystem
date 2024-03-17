@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <div class="text-end">
                    <a href="{{route('support.ticket.department') }}" class="btn btn-sm btn--base mb-2"> <i class="fa fa-plus"></i> @lang('New Ticket')</a>
                </div>
                <div class="table-responsive">
                    <table class="table custom--table">
                        <thead>
                        <tr>
                            <th>@lang('Subject')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Priority')</th>
                            <th>@lang('Last Reply')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($supports as $support)
                                <tr>
                                    <td> <a href="{{ route('support.ticket.view', $support->ticket_number) }}" class="fw-bold"> [@lang('Ticket')#{{ $support->ticket_number }}] {{ __($support->subject) }} </a></td>
                                    <td>
                                        {{ __($support->status) }}
                                    </td>
                                    <td>
                                         {{ __($support->priority) }}
                                    </td>
                                    <td>{{ diffForHumans($support->last_reply) }} </td>
                                    <td>
                                        <a href="{{ route('support.ticket.view', $support->ticket_number) }}" class="btn btn--base btn-sm">
                                            <i class="fa fa-desktop"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{$supports->links()}}

            </div>
        </div>
    </div>
@endsection
