@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        @php
            $hasAssignedItems = false;
            $assignedItemsCount = 0;
        @endphp
        @foreach ($items as $item)
            @if ($item->assigned_admin_id == auth()->guard('admin')->user()->id && $item->ticket_status_id != 4)
                @php
                    $hasAssignedItems = true;
                    $assignedItemsCount++;
                @endphp
            @endif
        @endforeach

        @if ($hasAssignedItems)
            <div class="col-lg-12 mb-3">
                <div class="m-2">
                    <h4>@lang('Your Assigned Ticket')</h4>
                    <p>@lang('You have') {{ $assignedItemsCount }} @lang('ticket(s) assigned to you and requiring attention')</p>
                </div>
                <div class="card b-radius--10 ">
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">
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
                                    @foreach ($items as $item)
                                        @if (
                                            $item->assigned_admin_id == auth()->guard('admin')->user()->id &&
                                                $item->assigned_admin_id != 0 &&
                                                $item->ticket_status_id != 4)
                                            <tr>
                                                <td>
                                                    @can('admin.ticket.view')
                                                        <a href="{{ route('admin.ticket.view', $item->ticket_number) }}"
                                                            class="fw-bold">
                                                            [@lang('Ticket')#{{ $item->ticket }}]
                                                            {{ strLimit($item->subject, 30) }}
                                                        </a>
                                                    @endcan
                                                </td>

                                                <td>
                                                    @if ($item->user_id)
                                                        @can('admin.users.detail')
                                                            <a href="{{ route('admin.users.detail', $item->user_id) }}">
                                                                {{ @$item->user->fullname }}</a>
                                                        @else
                                                            <span>@</span>{{ @$item->user_name }}
                                                        @endcan
                                                    @else
                                                        <p class="fw-bold"> {{ @$item->user_name }}</p>
                                                    @endif
                                                </td>

                                                <td class="fw-bold" style="color:{{ $item->status_color }}">
                                                    {{ $item->status }}
                                                </td>
                                                <td class="fw-bold" style="color: {{ $item->priority_color }}">
                                                    {{ $item->priority }}
                                                </td>

                                                <td>
                                                    {{ diffForHumans($item->last_reply) }}
                                                </td>

                                                <td>
                                                    @can('admin.ticket.view')
                                                        <a href="{{ route('admin.ticket.view', $item->ticket_number) }}"
                                                            class="btn btn-sm btn-outline--primary ms-1">
                                                            <i class="las la-desktop"></i> @lang('Details')
                                                        </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
        @endif
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
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
                                @forelse($items as $item)
                                    <tr>
                                        @php
                                            $staff = \App\Models\Admin::find($item->assigned_admin_id);
                                        @endphp
                                        <td>
                                            @can('admin.ticket.view')
                                                <a href="{{ route('admin.ticket.view', $item->ticket_number) }}"
                                                    class="fw-bold">
                                                    [@lang('Ticket')#{{ $item->ticket }}]
                                                    {{ strLimit($item->subject, 30) }}
                                                    @if ($staff)
                                                        <span class="badge badge--primary ms-2 fw-bold"> {{ $staff->name }}</span>
                                                    @endif
                                                </a>
                                            @endcan
                                        </td>

                                        <td>
                                            @if ($item->user_id)
                                                @can('admin.users.detail')
                                                    <a href="{{ route('admin.users.detail', $item->user_id) }}">
                                                        {{ @$item->user->fullname }}</a>
                                                @else
                                                    <span>@</span>{{ @$item->user_name }}
                                                @endcan
                                            @else
                                                <p class="fw-bold"> {{ @$item->user_name }}</p>
                                            @endif
                                        </td>


                                        <td class="fw-bold" style="color:{{ $item->status_color }}">
                                            {{ $item->status }}
                                        </td>
                                        <td class="fw-bold" style="color: {{ $item->priority_color }}">
                                            {{ $item->priority }}
                                        </td>

                                        <td>
                                            {{ diffForHumans($item->last_reply) }}
                                        </td>

                                        <td>
                                            @can('admin.ticket.view')
                                                <a href="{{ route('admin.ticket.view', $item->ticket_number) }}"
                                                    class="btn btn-sm btn-outline--primary ms-1">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                            @endcan
                                        </td>
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
                @if (request()->routeIs('admin.ticket.index'))
                    @if ($items->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($items) }}
                        </div>
                    @endif
                @endif

            </div><!-- card end -->
        </div>
    </div>
@endsection
