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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStaff as $staff)
                                    <tr>
                                        <td class="fw-bold">{{ __($staff->name) }}</td>
                                        <td>{{ __($staff->username) }}</td>
                                        <td>
                                            {{ __($staff->email) }}
                                        </td>
                                        <td>
                                            @php
                                                echo $staff->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <button type="button" data-id="{{ $staff->id }}"
                                                data-name="{{ $staff->title }}"
                                                class="btn btn-sm btn-outline--primary editBtn"
                                                data-staff="{{ $staff }}"
                                                data-department-id='@json($staff->departments->pluck('id'))'> <i
                                                    class="las la-pen"></i>
                                                @lang('Edit')
                                            </button>
                                            @if ($staff->is_default == Status::NO)
                                                @if ($staff->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this staff?')"
                                                        data-action="{{ route('admin.staff.status', $staff->id) }}">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this staff?')"
                                                        data-action="{{ route('admin.staff.status', $staff->id) }}">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            @endif
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
                @if ($allStaff->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($allStaff) }}
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
                            <label for="name">@lang('Name')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('Username')</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('E-mail')</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('Password')</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="row">
                            @foreach ($ticketDepartments as $key => $ticketDepartment)
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input class="form-check-input" value="{{ $ticketDepartment->id }}" type="checkbox"
                                            name="department_id[]" id="ticket_department_{{ $key }}">
                                        <label
                                            for="ticket_department_{{ $key }}">{{ __($ticketDepartment->name) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn w-100 btn--primary h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addBtn">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            $('.editBtn').on('click', function() {
                var modal = $('#modal');
                var form = modal.find('form');
                var staff = $(this).data('staff');
                var departmentId = $(this).data('department-id')
                modal.find(`input[name="department_id[]"]`).val(departmentId || []);
                var action = "{{ route('admin.staff.save', ':id') }}";
                modal.find('input[name=name]').val(staff.name);
                modal.find('input[name=username]').val(staff.username);
                modal.find('input[name=email]').val(staff.email);
                modal.find('input[name=password]').removeAttr('required');
                modal.find('label[for=password]').removeClass('required');
                form.attr('action', action.replace(":id", staff.id));
                modal.find(`.modal-title`).text(`@lang('Edit Staff')`);
                modal.modal('show');
            });

            $('.addBtn').on('click', function() {
                let modal = $('#modal');
                let form = modal.find(`form`);
                modal.find(`.modal-title`).text(`@lang('Add New Staff')`);
                form.attr('action', "{{ route('admin.staff.save') }}");
                form.trigger('reset');
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush
