@extends('admin.layouts.master')
@section('content')
    <div class="body-overlay"></div>
    @include('admin.support.header')
    <div class="ticket-reply">
        @include('admin.support.ticket_sidebar')
        <div class="ticket-reply__right">
            <div class="d-xl-none d-block">
                <p class="bar-icon">
                    <i class="fa fa-list"></i>
                </p>
            </div>
            <div class="ticket-reply__body">
                @include('admin.support.card_header')
                <form action="{{ route('admin.ticket.reply', $ticket->ticket_number) }}" enctype="multipart/form-data"
                    method="post" class="form-horizontal">
                    @csrf
                    <div class="ticket-reply-area">
                        <div class="btn--group">
                            <button type="button" class="btn btn-sm btn-outline--dark btn-markdown-bold fw-bold">
                                <span class="icon"><i class="fa fa-bold"></i></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline--dark  btn-markdown-italic fst-italic">
                                <span class="icon"><i class="fa fa-italic"></i></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline--dark  btn-markdown-heading">
                                <span class="icon"><i class="fa fa-heading"></i></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline--dark  btn-markdown-link">
                                <span class="icon"><i class="fa fa-link"></i></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline--dark  unorder-list">
                                <span class="icon"><i class="fa fa-list"></i></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline--dark  order-list">
                                <span class="icon"><i class="fa fa-list-ol"></i></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline--dark  markdown-blockquote">
                                <span class="icon"><i class="fa fa-greater-than-equal"></i></span>
                            </button>
                            <button type="button" id="previewButton"
                                class="btn btn-sm btn-outline--dark markdown-preview-btn ">
                                <span class="icon"><i class="fa fa-eye"></i></span> @lang('preview')
                            </button>
                        </div>
                        <iframe class="preview markdown-body border d-none w-100"></iframe>
                        <div class="form-group ticket-reply__box">
                            <textarea class="form--control" name="message" rows="10" required id="inputMessage">{{ old('message') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <select class="form--control form-select" name="department_id">
                                        <option value="">@lang('-Set Department-')</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">
                                                {{ __($department->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <select class="form--control form-select" name="assigned_admin_id">
                                        <option value="">@lang('-Set Assingment-')</option>
                                        @foreach ($ticketDepartment->staffs as $staff)
                                            <option value="{{ $staff->id }}"> {{ __($staff->name) }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <select class="form--control form-select" name="priority_id">
                                        <option value="">@lang('-Set Priority-')</option>
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority->id }}">
                                                {{ __($priority->title) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <select class="form--control form-select" name="ticket_status_id">
                                        <option value="">@lang('-Set Status-')</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}">
                                                {{ __($status->title) }}
                                            </option>
                                        @endforeach
                                        <input class="form-check-input d-none" type="checkbox" name="is_private"
                                            id="is_privat" checked>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-attachment">
                            <div class="ticket-attachment__btn d-md-none d-block">
                                <div class="d-flex gap-2 flex-wrap">
                                    @can('admin.ticket.predefined.messages')
                                        <button type="button" class="btn btn-outline--base predefineReply">
                                            <i class="la la-pencil"></i> @lang('Insert Predefined Replay')
                                        </button>
                                    @endcan
                                    @can('admin.ticket.reply')
                                        <button class="btn btn--base" type="submit" name="replayTicket" value="1"><i
                                                class="la la-fw la-lg la-reply"></i> @lang('Add Note')
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            <div class="ticket-attachment__left">
                                <div class="image-upload">
                                    <div class="form-group mb-0">
                                        <label for="inputAttachments">@lang('Attachments')</label> <span
                                            class="image-text text--danger">@lang('Max 5 files can be uploaded. Maximum upload size is')
                                            {{ ini_get('upload_max_filesize') }}</span>
                                        <div class="file-upload-wrapper" data-text="@lang('Select your file!')">
                                            <input type="file" name="attachments[]" id="inputAttachments"
                                                class="file-upload-field" />
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn--dark add-btn extraTicketAttachment ms-0"><i
                                            class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="ticket-attachment__btn  d-md-block d-none">
                                <div class="d-flex gap-2 flex-wrap">
                                    @can('admin.ticket.predefined.messages')
                                        <button type="button" class="btn btn-outline--base predefineReply">
                                            <i class="la la-pencil"></i> @lang('Insert Predefined Replay')
                                        </button>
                                    @endcan
                                    @can('admin.ticket.reply')
                                        <button class="btn btn--base" type="submit" name="replayTicket" value="1"><i
                                                class="la la-fw la-lg la-reply"></i> @lang('Add Note')
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <div id="fileUploadsContainer"></div>

                        <div class="check-list">
                            <div class="form-check">
                                <input class="form-check-input" value="return_to_ticket_list" type="checkbox"
                                    name="return" id="return_to" checked>
                                <label for="return_to">@lang('Return To Ticket List ')</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="predefinedReplies"></div>
            @foreach ($messages as $message)
                @php
                    $planText = strip_tags($message->message);
                @endphp
                @if ($message->admin_id == 0 && $message->is_private == 0)
                    <div class="message-box">
                        <div class="message-box__left">
                            <h5 class="name">{{ $ticket->user_name }}</h5>
                            <span class="badge bg--success">@lang('Owner')</span>
                            @if ($ticket->user_id != null)
                                <p><a
                                        href="{{ route('admin.users.detail', $ticket->user_id) }}">&#64;{{ $ticket->user_email }}</a>
                                </p>
                            @else
                                <p class="message-box__mail"><span>{{ @$ticket->user_email }}</span></p>
                            @endif
                            @can('admin.ticket.delete')
                                <button class="btn btn-danger btn-sm confirmationBtn text-white"
                                    data-question="@lang('Are you sure to delete this message?')"
                                    data-action="{{ route('admin.ticket.delete', $message->id) }}"><i
                                        class="la la-trash"></i>
                                    @lang('Delete')</button>
                            @endcan
                        </div>
                        <div class="message-box__right">
                            <p class="time">
                                @lang('Posted on') {{ showDateTime($message->created_at, 'l, dS F Y @ H:i') }}
                            </p>
                            <p class="text">
                                {{-- @php
                                echo $message->message;
                            @endphp --}}
                                <iframe src="data:text/html;charset=utf-8,{{ htmlentities($message->message) }}"></iframe>
                            </p>
                            @if ($message->attachments->count() > 0)
                                <div class="my-3">
                                    @foreach ($message->attachments as $k => $image)
                                        <div class="attechment-items">
                                            @php
                                                $extension = pathinfo($image->attachment, PATHINFO_EXTENSION);
                                            @endphp
                                            @if (in_array($extension, ['jpg', 'png', 'jpeg']))
                                                <div class="ticket-attachments">
                                                    <img src="{{ getImage(getFilePath('ticket') . '/' . $image->attachment) }}"
                                                        alt="@lang('Image')">
                                                </div>
                                                <p class="attachment__name"><i class="las la-file-image"></i>
                                                    @lang('Image') {{ __($extension) }}</p>
                                            @else
                                                <div class="ticket-attachments">
                                                    <img src="{{ getImage('assets/images/default.php') }}"
                                                        alt="@lang('Image')">
                                                </div>
                                                <p class="attachment__name"><i
                                                        class="las la-file-image"></i>@lang('File. '){{ __(@$extension) }}
                                                </p>
                                            @endif
                                            <div class="d-flex justify-content-center">
                                                @can('admin.ticket.download')
                                                    <a href="{{ route('admin.ticket.download', encrypt($image->id)) }}"
                                                        class="me-2 attachment__action">@lang('Download')
                                                    </a>
                                                @endcan
                                                @can('admin.ticket.attachment.delete')
                                                    <a href="{{ route('admin.ticket.attachment.delete', encrypt($image->id)) }}"
                                                        class="me-2 attachment__action delete">@lang('Delete')
                                                    </a>
                                                @endcan
                                            </div>
                                            </>
                                    @endforeach
                                </div>
                            @endif
                            <button class="copy-btn" data-text="{{ $planText }}">
                                <i class="las la-copy"></i>
                            </button>
                        </div>
                    </div>
                @else
                    <div
                        class="message-box  {{ $message->is_private == 1 ? 'private_note_bg_color' : 'admin-bg-reply' }}">
                        <div class="message-box__left">
                            <h5 class="name">{{ @$message->admin->name }}</h5>
                            <div class="message-box__content">
                                <div>
                                    <span class="badge bg--primary">@lang('Operator')</span>
                                </div>
                                @if ($message->is_private == 1)
                                    <p class="text-danger">@lang('Privet Note')</p>
                                @endif
                                <div>
                                    @can('admin.ticket.delete')
                                        <button class="btn btn-danger btn-sm  text-white confirmationBtn"
                                            data-question="@lang('Are you sure to delete this message?')"
                                            data-action="{{ route('admin.ticket.delete', $message->id) }}"><i
                                                class="la la-trash"></i>
                                            @lang('Delete')</button>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <div class="message-box__right">
                            <p class="time">
                                @lang('Posted on') {{ showDateTime($message->created_at, 'l, dS F Y @ H:i') }}
                            </p>
                            <p class="text">
                                {{-- @php
                                echo $message->message;
                            @endphp --}}
                                <iframe src="data:text/html;charset=utf-8,{{ rawurlencode($message->message) }}"></iframe>
                            </p>
                            @if ($message->attachments->count() > 0)
                                <p class="attachment-title">@lang('Attachments')</p>
                                <div class="attechment-list">
                                    @foreach ($message->attachments as $k => $image)
                                        <div class="attechment-items">
                                            @php
                                                $extension = pathinfo($image->attachment, PATHINFO_EXTENSION);
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
                                                @can('admin.ticket.download')
                                                    <a href="{{ route('admin.ticket.download', encrypt($image->id)) }}"
                                                        class="me-2 attachment__action">@lang('Download')
                                                    </a>
                                                @endcan
                                                @can('admin.ticket.attachment.delete')
                                                    <a href="{{ route('admin.ticket.attachment.delete', encrypt($image->id)) }}"
                                                        class="me-2 attachment__action delete">@lang('Delete')
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <button class="copy-btn" data-text="{{ $planText }}">
                                <i class="las la-copy"></i>
                            </button>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Close Support Ticket!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you want to close this support ticket?')</p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="{{ route('admin.ticket.close', $ticket->ticket_number) }}">
                        @csrf
                        <input type="hidden" name="replayTicket" value="2">
                        @can('admin.ticket.close')
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal"> @lang('No')
                            </button>
                            <button type="submit" class="btn btn--primary"> @lang('Yes') </button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        /* custom tab css start here  */
        .custom--tab .nav-link {
            color: #333;
        }

        .reply-name {
            margin-right: 20px;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .markdown-body {
            min-height: 226px;
            border: 1px solid #ddd;
            border-radius: 0 0 4px 4px;
            padding: 10px;
            margin-bottom: 22px;
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

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.ticket.index') }}" />
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/table-sortable.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/marked.min.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.delete-message').on('click', function(e) {
                $('.message_id').val($(this).data('id'));
            })
            var fileAdded = 0;
            $('.extraTicketAttachment').on('click', function() {
                if (fileAdded >= 4) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(`
                        <div class="attachment-file-repeater mt-2">
                            <div class="file-upload-wrapper" data-text="@lang('Select your file!')"><input type="file" name="attachments[]" id="inputAttachments" class="file-upload-field"/></div>
                            <button type="button" class="btn add-btn btn--danger extraTicketAttachmentDelete"><i class="la la-times ms-0"></i></button>
                        </div>
                    `)
            });

            $(document).on('click', '.extraTicketAttachmentDelete', function() {
                fileAdded--;
                $(this).closest('.attachment-file-repeater').remove();
            });

            $(document).on('click', '.predefineReply', function(e) {
                var category_id = $(this).data('category_id');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.ticket.predefined.messages') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'category_id': category_id,
                    },
                    success: function(data) {
                        $('.predefinedReplies').html(data);
                    }
                });
            })

            $(document).on("click", ".reply__name", function(e) {
                const text = $(this).data("reply");
                var textarea = $(`body`).find(`#inputMessage`);
                var cursorPos = textarea[0].selectionStart;
                var textBefore = textarea.val().substring(0, cursorPos);
                var textAfter = textarea.val().substring(cursorPos, textarea.val().length);
                textarea.val(textBefore + text + textAfter);
                textarea[0].selectionStart = textarea[0].selectionEnd = cursorPos + text.length;

            });

            $('#previewButton').click(function() {
                var markdownText = $('#inputMessage').val();
                var htmlContent = marked(markdownText);
                // $('.preview').html(htmlText);
                // var htmlContent = document.getElementById('htmlInput').value;
                $('.preview').attr('src', 'data:text/html;charset=utf-8,' + encodeURIComponent(htmlContent));
                // iframe.src = 'data:text/html;charset=utf-8,' + encodeURIComponent(htmlContent);
            });

            $(".markdown-preview-btn").on('click', function() {
                $(".preview").toggleClass(`d-none`);
                $("#inputMessage").toggleClass(`d-none`);

            });

            function applyMarkdownFormat(formatFunction) {
                var textarea = $('#inputMessage');
                if (textarea.val().length <= 0) {
                    return;
                }
                var start = textarea[0].selectionStart;
                var end = textarea[0].selectionEnd;
                var value = textarea.val();
                var selectedText = value.substring(start, end);
                if (!selectedText) return;
                var newText = formatFunction(selectedText);
                var updatedText = textarea.val().substring(0, start) + newText + textarea.val().substring(end);
                textarea.val(updatedText);
            }

            $('.btn-markdown-bold').click(function() {
                applyMarkdownFormat(function(text) {
                    return "**" + text + "**";
                });
            });

            $('.btn-markdown-italic').click(function() {
                applyMarkdownFormat(function(text) {
                    return "*" + text + "*";
                });
            });

            $('.btn-markdown-heading').click(function() {
                applyMarkdownFormat(function(text) {
                    return "# " + text + "\n";
                });
            });

            $('.btn-markdown-link').on('click', function() {
                var linkText = prompt('Enter link text:');
                var linkUrl = prompt('Enter link URL:');
                if (linkText !== null && linkUrl !== null) {
                    if (!linkUrl.startsWith('https://')) {
                        linkUrl = 'https://' + linkUrl;
                    }
                    var markdownLink = '[' + linkText + '](' + linkUrl + ')';
                    var $textarea = $('#inputMessage');
                    var caretPos = $textarea[0].selectionStart;
                    var textAreaTxt = $textarea.val();
                    $textarea.val(textAreaTxt.substring(0, caretPos) + markdownLink + textAreaTxt.substring(
                        caretPos));
                }
            });
            $('.unorder-list').click(function() {
                applyMarkdownFormat(function(text) {
                    var lines = text.split('\n');
                    var newText = '';
                    for (var i = 0; i < lines.length; i++) {
                        newText += "- " + lines[i] + "\n";
                    }
                    return newText;
                });
            });

            $('.order-list').click(function() {
                applyMarkdownFormat(function(text) {
                    var lines = text.split('\n');
                    var newText = '';
                    for (var i = 0; i < lines.length; i++) {
                        newText += (i + 1) + ". " + lines[i] + "\n";
                    }
                    return newText;
                });
            });

            $('.markdown-blockquote').click(function() {
                applyMarkdownFormat(function(text) {
                    var lines = text.split('\n');
                    var newText = '';
                    for (var i = 0; i < lines.length; i++) {
                        newText += "> " + lines[i] + "\n";
                    }
                    return newText;
                });
            });

            $('.copy-btn').click(function() {
                let text = $(this).data('text');
                navigator.clipboard.writeText(text)
                notify('success', 'Text copied successfully');
            });

        })(jQuery);
    </script>
@endpush
