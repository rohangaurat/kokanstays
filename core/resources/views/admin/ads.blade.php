@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Hotel')</th>
                                    <th>@lang('URL')</th>
                                    <th>@lang('End Date')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ads as $ad)
                                    @php
                                        $ad->image_with_path = getImage(
                                            getFilePath('ads') . '/' . @$ad->image,
                                            getFileSize('ads'),
                                        );
                                        $ad->redirect_to = $ad->url ? 'url' : ($ad->owner_id ? 'owner_id' : '');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img alt="" src="{{ $ad->image_with_path }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($ad->owner_id)
                                                <span>{{ __($ad->owner->hotelSetting->name) }}</span> <br>
                                                <a
                                                    href="{{ route('admin.owners.detail', $ad->owner_id) }}">{{ __($ad->owner->fullname) }}</a>
                                            @else
                                                <span>---</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ad->url)
                                                <a href="{{ $ad->url }}" target="_blank">{{ $ad->url }}</a>
                                            @else
                                                <span>---</span>
                                            @endif
                                        </td>
                                        <td>{{ showDateTime($ad->end_date, 'd M, Y') }}</td>
                                        <td>@php echo $ad->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary cuModalBtn editBtn"
                                                    data-modal_title="@lang('Update Ad')"
                                                    data-resource="{{ $ad }}">
                                                    <i class="las la-pencil-alt"></i>@lang('Edit')
                                                </button>
                                                @if ($ad->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.ads.status', $ad->id) }}"
                                                        data-question="@lang('Are you sure to enable this ad?')" type="button">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.ads.status', $ad->id) }}"
                                                        data-question="@lang('Are you sure to disable this ad?')" type="button">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.ads.add') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <x-image-uploader class="w-100" type="ads" :required="false"
                                accept=".png, .jpg, .jpeg, .gif" :hint="trans('Expected ratio: ') . getFileSize('ads') . 'px.'" />
                        </div>
                        <div class="form-group">
                            <label>@lang('End Date')</label>
                            <input autocomplete="off" class="form-control dPicker" name="end_date"
                                placeholder="@lang('End Date')" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Redirect To')</label>
                            <select class="form-control redirect_to select2" data-minimum-results-for-search="-1" name="redirect_to">
                                <option value="">@lang('None')</option>
                                <option value="owner_id">@lang('Hotel')</option>
                                <option value="url">@lang('URL')</option>
                            </select>
                        </div>
                        <div class="form-group hotel d-none">
                            <label class="required">@lang('Hotel')</label>
                            <select class="select2-basic" name="owner_id">
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}" data-title="{{ $owner->fullname }}">
                                        {{ $owner->hotelSetting?->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group url d-none">
                            <label class="required">@lang('URL')</label>
                            <input type="text" name="url" class="form-control" placeholder="@lang('Enter Valid URL')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary cuModalBtn addBtn" data-modal_title="@lang('Add New Ad')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.dPicker').daterangepicker({
                singleDatePicker: true,
                autoUpdateInput: false,
                minDate: moment().add(1, 'days')
            });

            $('.dPicker').on('apply.daterangepicker', (event, picker) => {
                $(event.target).val(picker.startDate.format("YYYY-MM-DD"));
            });

            var formatState = (state, container) => {

                let title = $(state.element).data('title');
                if (title == undefined) {
                    return state.text;
                }

                let result = $("<div>");

                $('<span>', {
                    text: state.text,
                }).appendTo(result);

                $('<br>').appendTo(result);

                $('<small>', {
                    text: title
                }).appendTo(result);

                return result;
            }

            $('.addBtn').on('click', function() {
                var imgURL = "{{ getImage(null, getFileSize('ads')) }}";
                $("#cuModal").find(".image-upload-preview").css("background-image", `url(${imgURL})`);

                $('[name=owner_id]').val("").select2({
                    dropdownParent: $('#cuModal'),
                    templateResult: formatState
                });

                $('.dPicker').data('daterangepicker').setStartDate(moment().add(1, 'days'));
                $('.dPicker').data('daterangepicker').setEndDate(moment().add(1, 'days'));

                redirectToChangeHandler('');
            });

            $('.editBtn').on('click', function() {
                let resource = $(this).data('resource');
                $('[name=owner_id]').val(resource.owner_id).select2({
                    dropdownParent: $('#cuModal'),
                    templateResult: formatState
                });

                $('.dPicker').data('daterangepicker').setStartDate(moment(resource.end_date));
                $('.dPicker').data('daterangepicker').setEndDate(moment(resource.end_date));

                redirectToChangeHandler(resource.redirect_to);
            });

            const redirectToChangeHandler = (value) => {
                $('.hotel, .url').addClass('d-none');
                $('.hotel, .url').find('input, select').removeAttr('required');

                if (value) {
                    $(`[name=${value}]`).parent().removeClass('d-none');
                    $(`[name=${value}]`).attr('required', true);
                }

                $('[name=redirect_to]').val(value);
            }

            $('[name=redirect_to]').on('change', function() {
                redirectToChangeHandler(this.value);
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .datepicker {
            z-index: 9999;
        }
    </style>
@endpush
