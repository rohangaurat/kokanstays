@extends('Template::layouts.' . $layout)
@section('content')
    <div class="custom--card card">
        <div class="card-header">
            <div class="card-header__wrapper align-items-center flex-row">
                <h5 class="profile-wrapper__title m-0 d-flex gap-2 flex-wrap">
                    @php echo $myTicket->statusBadge; @endphp
                    [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                </h5>
                @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)
                    <button class="btn btn--danger close-button btn--sm confirmationBtn" type="button"
                        data-question="@lang('Are you sure to close this ticket?')" data-action="{{ route('ticket.close', $myTicket->id) }}">
                        <i class="fas fa-lg fa-times-circle"></i>
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <form method="post" class="disableSubmission" action="{{ route('ticket.reply', $myTicket->id) }}"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form--label">@lang('Message')</label>
                            <textarea name="message" rows="6" class="form--control" required>{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <button type="button" class="btn btn--dark btn--md addAttachment">
                            <i class="las la-plus"></i> @lang('Add Attachment')
                        </button>
                        <p class="mb-2">
                            <span class="text--info attachment__info">
                                @lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')
                            </span>
                        </p>
                        <div class="row fileUploadsContainer"></div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn--base btn--md w-100" type="submit">
                            <i class="las la-reply"></i> @lang('Reply')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="custom--card card mt-4">
        <div class="card-body">
            @foreach ($messages as $message)
                @if ($message->admin_id == 0)
                    <div class="support-ticket">
                        <div class="flex-align gap-3 mb-2">
                            <h6 class="support-ticket-name">{{ $message->ticket->fullname }}</h6>
                            <p class="support-ticket-date"> @lang('Posted on')
                                {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                        </div>
                        <p class="support-ticket-message">{{ $message->message }}</p>
                        @if ($message->attachments->count() > 0)
                            <div class="support-ticket-file mt-2">
                                @foreach ($message->attachments as $k => $image)
                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="me-3">
                                        <span class="icon"><i class="la la-file-download"></i></span>
                                        @lang('Attachment') {{ ++$k }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="support-ticket reply">
                        <div class="flex-align gap-3 mb-2">
                            <h6 class="support-ticket-name">
                                {{ $message->admin->name }} <span class="staff">@lang('Staff')</span>
                            </h6>
                            <p class="support-ticket-date"> @lang('Posted on')
                                {{ $message->created_at->format('l, dS F Y @ H:i') }}
                            </p>
                        </div>
                        <p class="support-ticket-message">{{ $message->message }}</p>
                        @if ($message->attachments->count() > 0)
                            <div class="support-ticket-file mt-2">
                                @foreach ($message->attachments as $k => $image)
                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="me-3">
                                        <span class="icon"><i class="la la-file-download"></i></span>
                                        @lang('Attachment') {{ ++$k }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    @include('Template::partials.confirmationModal')
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            var fileAdded = 0;

            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true);
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-6 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger text--white border--danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });

            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true);
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }

        .reply-bg {
            background-color: #ffd96729
        }

        .attachment__info {
            font-size: 14px;
            margin: 5px 0;
        }
    </style>
@endpush
