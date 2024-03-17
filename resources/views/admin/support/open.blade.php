@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center mt-4">
        <div class="col-xl-8">
            <div class="ticket-form">
                <form action="{{ route('admin.ticket.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="form-label">@lang('User')</label>
                            <input type="text" id="user" name="user" value="{{ old('user') }}"
                                class="form-control form--control" required>
                            <span id="username-feedback"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('Subject')</label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                class="form-control form--control" required>
                            <input type="hidden" name="department_id" value="{{ $department->id }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('Priority')</label>
                            <select name="priority" class="form-control form--control" required>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->id }}">{{ __(@$priority->title) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label">@lang('Message')</label>
                            <textarea name="message" id="inputMessage" rows="6" class="form-control form--control" required>{{ old('message') }}</textarea>
                        </div>
                        <div class="add-file">
                            <div class="form-group">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline--primary addFile add-file-btn">
                                        <i class="fa fa-plus"></i> @lang('Add New')
                                    </button>
                                </div>
                                <div class="file-upload">
                                    <label class="form-label">@lang('Attachments')</label> <small
                                        class="text-danger">@lang('Max 5 files can be uploaded'). @lang('Maximum upload size is')
                                        {{ ini_get('upload_max_filesize') }}</small>
                                    <input type="file" name="attachments[]" id="inputAttachments"
                                        class="form-control form--control mb-2" />
                                    <div id="fileUploadsContainer"></div>
                                    <p class="ticket-attachments-message text-muted">
                                        @lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'),
                                        .@lang('png'),
                                        .@lang('pdf'), .@lang('doc'), .@lang('docx')
                                    </p>
                                </div>
                            </div>
                        </div>

                        <x-viser-form identifier="id" identifierValue="{{ $department->form_id }}" />
                    </div>
                    @can('admin.ticket.store')
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }

        .ticket-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
        }

        .ticket-form__title {
            margin-bottom: 25px;
            text-align: center;
        }

        .add-file {
            margin-top: 25px;
        }

        .add-file-btn {
            margin-bottom: 10px;
        }

        @media (max-width:575px) {
            .add-file {
                margin-top: 10px;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addFile').on('click', function() {
                if (fileAdded >= 4) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(`
                    <div class="input-group my-3">
                        <input type="file" name="attachments[]" class="form-control form--control" required />
                        <button type="button" class="input-group-text btn-danger remove-btn"><i class="las la-times"></i></button>
                    </div>
                `)
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.input-group').remove();
            });

            $('#user').on('blur', function() {
                var username = $(this).val();
                if (username == '') {
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.ticket.user') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'username': username
                    },
                    success: function(data) {
                        if (!data.exists) {
                            $('#username-feedback').html(
                                '<span class="text-danger">User not found</span>');
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
