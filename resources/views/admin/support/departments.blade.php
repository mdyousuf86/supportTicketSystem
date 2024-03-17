@extends('admin.layouts.app')
@section('panel')
    <div class="container">
        <div class="row gy-4">
            @foreach ($departments as $department)
                <div class="col-lg-4">
                    @can('admin.ticket.open')
                        <a href="{{ route('admin.ticket.open', $department->id) }}" class="card card--zoom">
                            <div class="card-body">
                                <h5 class="card-title">{{ __(@$department->name) }}</h5>
                                <p class="card-text">{{ __(@$department->description) }}</p>
                            </div>
                        </a>
                    @endcan
                </div>
            @endforeach
        </div>
    </div>
@endsection
@push('style')
    <style>
        .card--zoom {
            transition: all linear 0.3s;
            text-decoration: none;
            color: black;
            transform: scale(1);
            display: block
        }

        .card--zoom:hover {
            transform: scale(1.04)
        }
    </style>
@endpush
