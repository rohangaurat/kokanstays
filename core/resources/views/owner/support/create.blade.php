@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('owner.ticket.store') }}" class="form-horizontal" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Subject')</label>
                                    <input class="form-control" name="subject" required type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Priority')</label>
                                    <select class="form-control" name="priority" required>
                                        <option value="3">@lang('High')</option>
                                        <option value="2">@lang('Medium')</option>
                                        <option value="1">@lang('Low')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message')</label>
                                    <textarea class="form-control" id="inputMessage" name="message" required rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-end">
                                    <button class="btn btn--dark extraTicketAttachment" type="button"><i class="las la-plus"></i>@lang('Add New')</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputAttachments">@lang('Attachments')</label> <span class="text--danger">@lang('Max 5 files can be uploaded. Maximum upload size is') {{ ini_get('upload_max_filesize') }}</span>
                                    <input class="form-control" id="inputAttachments" name="attachments[]" type="file" />
                                </div>
                            </div>
                            <div class="col-md-12 m-0" id="fileUploadsContainer"></div>
                            <small class="text-muted">@lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'), .@lang('pdf'), .@lang('doc'), .@lang('docx')</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    @can('owner.ticket.index')
        @push('breadcrumb-plugins')
            <a class="btn btn-sm btn--primary" href="{{ route('owner.ticket.index') }}"><i class="las la-list"></i>@lang('My Tickets')</a>
        @endpush
    @endcan

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
                        <div class="form-group parent-div">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control" required/>
                                <button type="button" class="btn btn--danger input-group-text extraTicketAttachmentDelete"><i class="la la-times ms-0"></i></button>
                            </div>
                        </div>
                `)
                });

                $(document).on('click', '.extraTicketAttachmentDelete', function() {
                    fileAdded--;
                    $(this).parents('.parent-div').remove();
                });
            })(jQuery);
        </script>
    @endpush
