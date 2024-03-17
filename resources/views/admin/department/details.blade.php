<form action="{{ route('admin.department.details.save', @$ticketDepartment->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control " name="name"
                        value="{{ old('name', @$ticketDepartment->name) }}" required />
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label>@lang('Email')</label>
                    <input type="text" name="email" class="form-control border-radius-5" required
                        value="{{ old('email', @$ticketDepartment->email) }}" />
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label>@lang('Description')</label>
                    <div class="form-group">
                        <textarea name="description" class="form-control" rows="2" required>{{ old('description', @$ticketDepartment->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-2">
            <div class="col-xl-12">
                <div class="configuration">
                    <span class="name">@lang('Clients Only')</span>
                    <div class="form-check form--check">
                        <input class="form-check-input" type="checkbox" name="clients_only" id="clientsOnlyCheck"
                            @checked(@$ticketDepartment->clients_only)>
                        <label class="form-check-label mb-0" for="clientsOnlyCheck">
                            @lang('Ticket creation or replies are only permitted when the sender\'s address is associated with a registered client, user, or contact with updated ticket permissions.')
                        </label>
                    </div>
                </div>
                <div class="configuration">
                    <span class="name">@lang('Dashboard Only')</span>
                    <div class="form-check form--check">
                        <input class="form-check-input" type="checkbox" name="pipe_only" id="pipeOnlyCheck"
                            @checked(@$ticketDepartment->pipe_only)>
                        <label class="form-check-label mb-0" for="pipeOnlyCheck">
                            @lang('Please ensure that all tickets are initiated by the client')
                        </label>
                    </div>
                </div>
                <div class="configuration">
                    <span class="name">@lang('No Autoresponder')</span>
                    <div class="form-check form--check">
                        <input class="form-check-input" type="checkbox" name="auto_respond" id="autoResponderCheck"
                            @checked(@$ticketDepartment->auto_respond)>
                        <label class="form-check-label mb-0" for="autoResponderCheck">
                            @lang('Exclude the automated response for new tickets')
                        </label>
                    </div>
                </div>

                <div class="configuration">
                    <span class="name">@lang('Hidden?')</span>
                    <div class="form-check form--check">
                        <input class="form-check-input" type="checkbox" name="is_hidden" id="hiddenCheck"
                            @checked(@$ticketDepartment->is_hidden)>
                        <label class="form-check-label mb-0" for="hiddenCheck">
                            @lang('Hide from clients')
                        </label>
                    </div>
                </div>

                <div class="configuration">
                    <span class="name">@lang('Feedback Request')</span>
                    <div class="form-check form--check">
                        <input class="form-check-input" type="checkbox" name="feedback_request" id="feedbackCheck"
                            @checked(@$ticketDepartment->feedback_request)>
                        <label class="form-check-label mb-0" for="feedbackCheck">
                            @lang('Send a feedback rating/review request when closing the ticket')
                        </label>
                    </div>
                </div>

                <div class="configuration">
                    <span class="name">@lang('Auto Import')</span>
                    <div class="form-check form--check">
                        <input class="form-check-input autoImport" type="checkbox" name="auto_import"
                            id="autoImportCheck" @checked(@$ticketDepartment->auto_import)>
                        <label class="form-check-label mb-0" for="autoImportCheck">
                            @lang('Auto import')
                        </label>
                    </div>
                </div>
                <div class="configuration">
                    <span class="name"></span>
                    <div class="right-width">
                        <div class="row mt-3 pip-port">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>@lang('Hostname')</label>
                                    <input type="text" class="form-control " name="host"
                                        value="{{ old('host', @$ticketDepartment->host) }}" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>@lang('Mail Server Port')</label>
                                    <input type="text" class="form-control " name="port"
                                        value="{{ old('port', @$ticketDepartment->port) }}" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>@lang('Email Address')</label>
                                    <input type="email" class="form-control " name="email_username"
                                        value="{{ old('host', @$ticketDepartment->email_username) }}" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>@lang('Email Password')</label>
                                    <input type="text" class="form-control " name="email_password"
                                        value="{{ old('email_password', @$ticketDepartment->email_password) }}" />
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn--primary"><i class="las la-cog"></i>
                                    @lang('Check Configuration')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @can('admin.department.details.save')
        <div class="card-footer">
            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
        </div>
    @endcan
</form>

@push('style')
    <style>
        .form--check.form-check .form-check-input {
            float: unset;
            margin-left: 0;
        }

        .form--check.form-check {
            padding-left: 0;
            display: flex;
            align-items: center;
            margin-bottom: 0;
            background-color: #8d868636;
            padding: 10px;
            border-radius: 4px;
            width: calc(100% - 180px);
        }

        .right-width {
            width: calc(100% - 180px);
        }

        .form--check .form-check-input[type=checkbox] {
            border-radius: 2px;
            margin-top: 0;
        }

        .form--check .form-check-input {
            box-shadow: none;
            background-color: transparent;
            box-shadow: none !important;
            border: 0;
            position: relative;
            border-radius: 2px;
            width: 16px;
            height: 16px;
            border: 1px solid #777;
            cursor: pointer;
        }

        .configuration {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 3px;
        }

        .configuration:last-child {
            margin-bottom: 0px;
        }

        .form--check .form-check-input:checked {
            background-color: #4634ff !important;
            border-color: #4634ff !important;
            box-shadow: none;
        }

        .form--check .form-check-input:checked[type=checkbox] {
            background-image: none;
        }

        .form--check .form-check-input:checked::before {
            position: absolute;
            content: "\f00c";
            font-family: "Line Awesome Free";
            font-weight: 700;
            color: #fff;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .form--check .form-check-label {
            font-weight: 400;
            width: calc(100% - 16px);
            padding-left: 12px;
            cursor: pointer;
            color: #342525;
            font-size: 16px;
        }

        .name {
            text-align: right;
            width: 160px;
        }

        @media (max-width: 767px) {
            .configuration {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 25px !important;
                gap: 10px
            }

            .form--check.form-check {
                width: 100%;
            }

            .name {
                width: unset;
                text-align: left
            }
        }

        @media (max-width: 424px) {
            .form--check label {
                font-size: 14px !important;
            }
        }

        /* custom tab css start here  */

        .custom--tab .nav-link {
            color: #333;
        }

        /* custom tab css end here  */
    </style>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.autoImport').on('change', function() {
                pipShowHide();
            });

            function pipShowHide() {
                const isChecked = $('.autoImport').prop('checked');
                if (isChecked) {
                    $(".pip-port").removeClass('d-none');
                } else {
                    $(".pip-port").addClass('d-none');
                }
            }
            pipShowHide();

        })(jQuery);
    </script>
@endpush
