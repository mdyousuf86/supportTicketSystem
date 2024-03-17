@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <nav>
                    <div class="nav nav-tabs custom--tab" id="nav-tab" role="tablist">
                        @can('admin.department.details')
                            <a class="nav-link {{ menuActive('admin.department.details') }}"
                                href="{{ route('admin.department.details', @$ticketDepartment->id) }}">@lang('Details')
                            </a>
                        @endcan

                        @can('admin.department.custom.field')
                            <a href="{{ route('admin.department.custom.field', @$ticketDepartment->id) }}"
                                class="nav-link {{ menuActive('admin.department.custom.field') }}">@lang('Custom Fields')
                            </a>
                        @endcan
                    </div>
                </nav>
                @include('admin.department.' . $fileName)
            </div>
        </div>
    </div>
@endsection

@can('admin.department.index')
    @push('breadcrumb-plugins')
        <x-back route="{{ route('admin.department.index') }}" />
    @endpush
@endcan

@push('style')
    <style>
        .custom--tab .nav-link {
            color: #333;
        }
    </style>
@endpush
