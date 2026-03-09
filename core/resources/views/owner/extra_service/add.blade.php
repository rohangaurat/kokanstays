@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6 mb-30">
            <div class="card">
                <div class="card-body p-sm-4 p-3">
                    <form action="{{ route('owner.extra.service.save') }}" class="add-service-form" method="POST">
                        @csrf
                        <div class="row append-service">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group flex-fill">
                                    <label>@lang('Service Date')</label>
                                    <input autocomplete="off" class="datePicker form-control" name="service_date" required type="text" value="{{ todaysDate() }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group flex-fill">
                                    <label>@lang('Room Number')</label>
                                    <input class="form-control" name="room_number" required type="text" value="{{ request()->room }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-between align-items-end mb-0">
                            <label class="required">@lang('Services')</label>
                            <button class="btn btn-sm btn--success addServiceBtn mb-2" type="button">
                                <i class="las la-plus"></i>@lang('Add More')
                            </button>
                        </div>
                        <div class="service-wrapper">
                            <div class="first-service-wrapper">
                                <div class="d-flex service-item position-relative mb-3 flex-wrap">
                                    <select class="custom-select no-right-radius flex-fill form-control w-50" name="services[]" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($extraServices as $extraService)
                                            <option value="{{ $extraService->id }}">
                                                {{ __($extraService->name) }} - {{ gs()->cur_sym . showAmount($extraService->cost, currencyFormat: false) }}/@lang('piece')
                                            </option>
                                        @endforeach
                                    </select>
                                    <input class="form-control w-unset flex-fill no-left-radius w-50" name="qty[]" placeholder="@lang('Quantity')" required type="text">
                                </div>
                            </div>
                        </div>
                        @can('owner.extra.service.save')
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@can('owner.extra.service.list')
    @push('breadcrumb-plugins')
        <a class="btn btn--primary" href="{{ route('owner.extra.service.list') }}"><i class="la la-list-alt"></i>@lang('View Log')</a>
    @endpush
@endcan

@push('style')
    <style>
        .no-right-radius {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .no-left-radius {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .removeServiceBtn {
            position: absolute;
            height: 25px;
            width: 25px;
            border-radius: 50%;
            text-align: center;
            line-height: 13px;
            font-size: 12px;
            right: -8px;
            top: -8px;
        }
    </style>
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
        "use strict";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $('.datePicker').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            maxDate: moment(),
            locale: {
                cancelLabel: 'Clear',
                format:'YYYY-MM-DD'
            }
        });

        const changeDatePickerText = (event, startDate, endDate) => {
            $(event.target).val(startDate.format('YYYY-MM-DD'));
        }

        $('.datePicker').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));

        $('.addServiceBtn').on('click', function() {
            const content = `<div class="d-flex service-item position-relative mb-3 flex-wrap">
                <select class="custom-select no-right-radius flex-fill w-50" name="services[]" required>
                    <option value="">@lang('Select One')</option>
                    @foreach ($extraServices as $extraService)
                        <option value="{{ $extraService->id }}">
                            {{ __($extraService->name) }} - {{ gs()->cur_sym . showAmount($extraService->cost, currencyFormat: false) }}/@lang('piece')
                        </option>
                    @endforeach
                </select>
                <input class="form-control w-unset flex-fill no-left-radius w-50" name="qty[]" placeholder="@lang('Quantity')" required type="text">

                <button class="btn--danger removeServiceBtn border-0" type="button">
                    <i class="las la-times"></i>
                </button>`;
            $('.service-wrapper').append(content);
        });

        $(document).on('click', '.removeServiceBtn', function() {
            $(this).parents('.service-item').remove();

        });

        let serviceForm = $('.add-service-form');

        serviceForm.on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let url = $(this).attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        notify('success', response.success);
                        let firstItem = $('.first-service-wrapper .service-item');
                        $(document).find('.service-wrapper').find('.service-item').not(firstItem).remove();
                        serviceForm.trigger("reset");
                    } else {
                        $.each(response.error, function(key, value) {
                            notify('error', value);
                        });
                    }
                },
            });
        });
    </script>
@endpush
