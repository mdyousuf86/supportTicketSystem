@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-header card-header-bg d-flex flex-wrap justify-content-between align-items-center">
                        <h5 class="text-white mt-0">
                            @php echo $myTicket->statusBadge; @endphp
                            [@lang('Ticket')#{{ $myTicket->ticket_number }}] {{ $myTicket->subject }}
                        </h5>
                        @if ($myTicket->ticket_status_id != 4)
                            <button class="btn btn-danger close-button btn-sm confirmationBtn" type="button"
                                data-question="@lang('Are you sure to close this ticket?')"
                                data-action="{{ route('support.ticket.close', $myTicket->id) }}"><i
                                    class="fa fa-lg fa-times-circle"></i>
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('support.ticket.reply', $myTicket->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea name="message" class="form-control form--control" rows="4">{{ old('message') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="javascript:void(0)" class="btn btn--base btn-sm addFile"><i class="fa fa-plus"></i>
                                    @lang('Add New')</a>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Attachments')</label> <small
                                    class="text-danger">@lang('Max 5 files can be uploaded'). @lang('Maximum upload size is')
                                    {{ ini_get('upload_max_filesize') }}</small>
                                <input type="file" name="attachments[]" class="form-control form--control" />
                                <div id="fileUploadsContainer"></div>
                                <p class="my-2 ticket-attachments-message text-muted">
                                    @lang('Allowed File Extensions'): {{ implode(', ', $general->attachment_file_type) }}
                                </p>
                            </div>

                            <button type="submit" class="btn btn--base w-100"> <i class="fa fa-reply"></i>
                                @lang('Reply')</button>
                        </form>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        @foreach ($messages as $message)
                            @if ($message->admin_id == 0)
                                <div class="row border border-primary border-radius-3 my-3 py-3 mx-2">
                                    <div class="col-md-3 border-end text-end">
                                        <h5 class="my-3">{{ $message->user_name }}</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted fw-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>
                                            {{-- @php
                                                echo $message->message;
                                            @endphp --}}
                                            <iframe
                                                src="data:text/html;charset=utf-8,{{ rawurlencode($message->message) }}"></iframe>
                                        </p>
                                        @if ($message->attachments->count() > 0)
                                            <p class="attachment-title">@lang('Attachments')</p>
                                            <div class="attechment-list">
                                                @foreach ($message->attachments as $k => $image)
                                                    <div class="attechment-items">
                                                        @php
                                                            $extension = pathinfo(
                                                                $image->attachment,
                                                                PATHINFO_EXTENSION,
                                                            );
                                                        @endphp
                                                        @if (in_array($extension, ['jpg', 'png', 'jpeg']))
                                                            <div class="ticket-attachments">
                                                                <img src="{{ getImage(getFilePath('ticket') . '/' . $image->attachment) }}"
                                                                    alt="@lang('Image')">
                                                            </div>
                                                            <p class="attachment__name"><i
                                                                    class="las la-file-image"></i>@lang('Image.'){{ __($extension) }}
                                                            </p>
                                                        @else
                                                            <div class="ticket-attachments">
                                                                <img src="{{ getImage('assets/images/default.php') }}"
                                                                    alt="@lang('Image')">
                                                            </div>
                                                            <p class="attachment__name"><i
                                                                    class="las la-file-image"></i>@lang('File'){{ __($extension) }}
                                                            </p>
                                                        @endif
                                                        <div class="d-flex justify-content-center">
                                                            <a href="{{ route('support.ticket.download', encrypt($image->id)) }}"
                                                                class="me-2 attachment__action">@lang('Download')
                                                            </a>
                                                            <a href="{{ route('support.ticket.attachment.delete', encrypt($image->id)) }}"
                                                                class="me-2 attachment__action delete">@lang('Delete')
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="row border border-warning border-radius-3 my-3 py-3 mx-2"
                                    style="background-color: #ffd96729">
                                    <div class="col-md-3 border-end text-end">
                                        <h5 class="my-3">{{ $message->admin_name }}</h5>
                                        <p class="lead text-muted">@lang('Staff')</p>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted fw-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>
                                            {{-- @php
                                                echo $message->message;
                                            @endphp --}}
                                            <iframe
                                                src="data:text/html;charset=utf-8,{{ rawurlencode($message->message) }}"></iframe>
                                        </p>
                                        @if ($message->attachments->count() > 0)
                                            <div class="my-3">
                                                @foreach ($message->attachments as $k => $image)
                                                    <div class="attechment-items">
                                                        @php
                                                            $extension = pathinfo($image->attachment,PATHINFO_EXTENSION,);
                                                        @endphp
                                                        @if (in_array($extension, ['jpg', 'png', 'jpeg']))
                                                            <div class="ticket-attachments">
                                                                <img src="{{ getImage(getFilePath('ticket') . '/' . $image->attachment) }}"
                                                                    alt="@lang('Image')">
                                                            </div>
                                                            <p class="attachment__name"><i
                                                                    class="las la-file-image"></i>@lang('Image.'){{ __($extension) }}
                                                            </p>
                                                        @else
                                                            <div class="ticket-attachments">
                                                                <img src="{{ getImage('assets/images/default.php') }}"
                                                                    alt="@lang('Image')">
                                                            </div>
                                                            <p class="attachment__name"><i
                                                                    class="las la-file-image"></i>@lang('File.'){{ __(@$extension) }}
                                                            </p>
                                                        @endif
                                                        <div class="d-flex justify-content-center">
                                                            <a href="{{ route('support.ticket.download', encrypt($image->id)) }}"
                                                                class="me-2 attachment__action">@lang('Download')
                                                            </a>
                                                            <a href="{{ route('support.ticket.attachment.delete', encrypt($image->id)) }}"
                                                                class="me-2 attachment__action delete">@lang('Delete')
                                                            </a>
                                                        </div>
                                                        </>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection
@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }

        .attechment-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-bottom: 45px;
            row-gap: clamp(0.625rem, 0.2268rem + 1.699vw, 1.5rem)
        }

        .ticket-attachments img {
            object-fit: cover;
            width: 100%;
            height: 100%;
            min-height: 220px;
            max-height: 220px
        }

        .attechment-items {
            max-width: 200px;
        }

        .attechment-items .ticket-attachments {
            border: 1px solid rgba(212, 212, 212, 1);
            border-radius: 5px;
        }

        .attachment-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 12px;
            color: rgba(16, 22, 58, 1)
        }

        .attachment__name {
            text-align: center;
            font-size: 12px;
            color: rgba(109, 126, 149, 1);
            margin-block: 4px 8px;
        }

        .attachment__action {
            color: rgba(16, 22, 58, 1);
            font-size: 14px;
            font-weight: 500;
            padding-right: 8px;
            margin-right: 8px;
            line-height: 1
        }

        .attachment__action:not(:last-child) {
            border-right: 1px solid rgba(16, 22, 58, 1);
        }

        .attachment__action.delete {
            color: rgba(234, 84, 85, 1);
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
                        <button type="submit" class="input-group-text btn-danger remove-btn"><i class="las la-times"></i></button>
                    </div>
                `)
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.input-group').remove();
            });


        })(jQuery);
    </script>
@endpush
