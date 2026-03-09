@extends('Template::layouts.master')
@section('content')
    <div class="custom--card card">
        <div class="card-header">
            <div class="card-header__wrapper flex-wrap">
                <div>
                    <h5 class="profile-wrapper__title">{{ __($pageTitle) }}</h5>
                    <p class="profile-wrapper__text mb-0">
                        @lang('Review the details and status of your support request.')
                    </p>
                </div>
                <div>
                    <a href="{{ route('ticket.index') }}" class="btn btn--md btn--base mb-2">
                        <i class="las la-list"></i> @lang('My Tickets')
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('ticket.store') }}" class="disableSubmission" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="form-group">
                            <label class="form--label">@lang('Subject')</label>
                            <input type="text" class="form--control" name="subject" value="{{ old('subject') }}"
                                required>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="form-group">
                            <label class="form--label">@lang('Priority')</label>
                            <select name="priority" class="form--control select2" data-minimum-results-for-search="-1"
                                required>
                                <option value="3" @selected(old('priority') == Status::PRIORITY_HIGH)>@lang('High')</option>
                                <option value="2" @selected(old('priority') == Status::PRIORITY_MEDIUM)>@lang('Medium')</option>
                                <option value="1" @selected(old('priority') == Status::PRIORITY_LOW)>@lang('Low')</option>
                            </select>
                        </div>
                    </div>
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
                            <i class="las la-paper-plane"></i> @lang('Submit')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                                <input type="file" name="attachments[]" class="form--control form-control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile text--white bg--danger border--danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>`);
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

        .attachment__info{
            font-size: 14px;
            margin: 5px 0;
        }
    </style>
@endpush
