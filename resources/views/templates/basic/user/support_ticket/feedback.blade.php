@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="text-white">{{ __($pageTitle) }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('support.ticket.feedback.save',$ticket->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>@lang('Rating')</label>
                                <select name="rating" class="form-control" required>
                                    <option value="" selected disabled>@lang('Select One') </option>
                                    <option value="1">@lang('One Star')</option>
                                    <option value="2">@lang('Two Star')</option>
                                    <option value="3">@lang('Three Star')</option>
                                    <option value="4">@lang('Four Star')</option>
                                    <option value="5">@lang('Five Star')</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">@lang('Comment')</label>
                                <textarea name="comment" class="form-control" rows="3" required>{{ old('comment') }}</textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn w-100 btn--primary h-45">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush
