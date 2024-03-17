@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two sortable-table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Status')</th>
                                    @if (can('admin.department.details') || can('admin.department.status'))
                                     <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="sort">
                                @forelse($ticketDepartments as $ticketDepartment)
                                    <tr class="sortable-table-row" data-id="{{ $ticketDepartment->id }}">
                                        <td><i class="fa fa-arrows-alt me-2"></i>{{ __(@$ticketDepartment->name) }}</td>
                                        <td>{{ __(@$ticketDepartment->email) }}</td>
                                        <td>{{ __(@$ticketDepartment->description) }}</td>
                                        <td>
                                            @php
                                                echo @$ticketDepartment->statusBadge;
                                            @endphp
                                        </td>
                                        @if (can('admin.department.details') || can('admin.department.status'))
                                            <td>
                                                <div class="button--group">
                                                    @can('admin.admin.department.details')
                                                        <a href="{{ route('admin.department.details', ['departmentId' => @$ticketDepartment->id]) }}"
                                                            class="btn btn-sm btn-outline--primary editGatewayBtn">
                                                            <i class="la la-pencil"></i> @lang('Edit')
                                                        </a>
                                                    @endcan

                                                    @can('admin.department.status')
                                                        @if ($ticketDepartment->status == Status::DISABLE)
                                                            <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                                data-question="@lang('Are you sure to enable this department?')"
                                                                data-action="{{ route('admin.department.status', $ticketDepartment->id) }}">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                                data-question="@lang('Are you sure to disable this department?')"
                                                                data-action="{{ route('admin.department.status', $ticketDepartment->id) }}">
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
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
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@can('admin.department.details')
    @push('breadcrumb-plugins')
        <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.department.details') }}"><i
                class="las la-plus"></i>@lang('Add New')</a>
    @endpush
@endcan


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/table-sortable.css') }}">
@endpush


    @push('script-lib')
        <script src="{{ asset('assets/admin/js/jquery-ui.js') }}"></script>
        <script src="{{ asset('assets/admin/js/table-sortable.js') }}"></script>
    @endpush

    @push('script')
        <script>
            function updateSortOrder(sorting) {
                var action = "{{ route('admin.department.sort') }}";
                var csrf = "{{ csrf_token() }}";
                sortOrderAction(sorting, action, csrf);
            }
        </script>
    @endpush

