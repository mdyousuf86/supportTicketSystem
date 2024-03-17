@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group ">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" type="text" name="site_name" required
                                        value="{{ $general->site_name }}">
                                </div>
                            </div>

                            <div class="form-group col-md-3 col-sm-6">
                                <label> @lang('Timezone')</label>
                                <select class="select2-basic" name="timezone">
                                    @foreach ($timezones as $key => $timezone)
                                        <option value="{{ @$key }}">{{ __($timezone) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3 col-sm-6">
                                <label> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ $general->base_color }}" />
                                    </span>
                                    <input type="text" class="form-control colorCode" name="base_color"
                                        value="{{ $general->base_color }}" />
                                </div>
                            </div>

                            <div class="form-group col-md-3 col-sm-6">
                                <label> @lang('Site Secondary Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ $general->secondary_color }}" />
                                    </span>
                                    <input type="text" class="form-control colorCode" name="secondary_color"
                                        value="{{ $general->secondary_color }}" />
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Attachment file types')</label>
                                    <select class="form--control form-select select2-basic" name="attachment_file_type[]"
                                        multiple id="attachmentFileType" required>
                                        <option value="select">@lang('--Select--')</option>
                                        @foreach (fileSupportedExtensions() as $extension)
                                            <option value="{{ $extension }}" @selected(in_array($extension, $general->attachment_file_type ?? []))>
                                                {{ __($extension) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <div class="form-group ">
                                    <label for="file_size_limit">@lang('Attachment File Size Limit (in MB):')</label>
                                    <input class="form-control" type="number" name="file_size_limit" required
                                        value="{{ $general->file_size_limit }}">
                                </div>
                            </div>

                        </div>

                        @can('admin.setting.update')
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                            </div>
                        @endcan

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .select2-container {
            z-index: 0 !important;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });

            $('select[name=timezone]').val("{{ $currentTimezone }}").select2();
            $('.select2-basic').select2({
                dropdownParent: $('.card-body')
            });
        })(jQuery);
    </script>
@endpush
