@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" type="text" name="site_name" required
                                        value="{{ gs('site_name') }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" type="text" name="cur_text" required
                                        value="{{ gs('cur_text') }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" type="text" name="cur_sym" required
                                        value="{{ gs('cur_sym') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Timezone')</label>
                                <select class="select2 form-control" name="timezone">
                                    @foreach ($timezones as $key => $timezone)
                                        <option value="{{ @$key }}" @selected(@$key == $currentTimezone)>{{ __($timezone) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ gs('base_color') }}">
                                    </span>
                                    <input type="text" class="form-control colorCode" name="base_color"
                                        value="{{ gs('base_color') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Secondary Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ gs('secondary_color') }}">
                                    </span>
                                    <input type="text" class="form-control colorCode" name="secondary_color"
                                        value="{{ gs('secondary_color') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label> @lang('Record to Display Per page')</label>
                                <select class="select2 form-control" name="paginate_number"
                                    data-minimum-results-for-search="-1">
                                    <option value="20" @selected(gs('paginate_number') == 20)>@lang('20 items per page')</option>
                                    <option value="50" @selected(gs('paginate_number') == 50)>@lang('50 items per page')</option>
                                    <option value="100" @selected(gs('paginate_number') == 100)>@lang('100 items per page')</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-4 col-sm-6 ">
                                <label class="required"> @lang('Currency Showing Format')</label>
                                <select class="select2 form-control" name="currency_format"
                                    data-minimum-results-for-search="-1">
                                    <option value="1" @selected(gs('currency_format') == Status::CUR_BOTH)>@lang('Show Currency Text and Symbol Both')</option>
                                    <option value="2" @selected(gs('currency_format') == Status::CUR_TEXT)>@lang('Show Currency Text Only')</option>
                                    <option value="3" @selected(gs('currency_format') == Status::CUR_SYM)>@lang('Show Currency Symbol Only')</option>
                                </select>
                            </div>
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Maximum Star Rating')</label>
                                    <input class="form-control" min="3" name="max_star_rating" required
                                        type="number" value="{{ gs()->max_star_rating ?? '' }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>
                                        @lang('Popularity Count From')
                                        <i class="las la-info-circle text--info" title="@lang('The most popular hotels in recent times are determined by the number of bookings they have received. To ascertain their popularity, system count the bookings over a specific period of time, which you can set according to your preferences.')"></i>
                                    </label>
                                    <input autocomplete="off" class="form-control" min="1"
                                        name="popularity_count_from" required type="number"
                                        value="{{ gs()->popularity_count_from ?? '' }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Bill Per Month')</label>
                                    <div class="input-group">
                                        <input class="form-control" min="0" name="bill_per_month" required
                                            step="any" type="number" value="{{ getAmount(gs()->bill_per_month) }}">
                                        <span class="input-group-text site_currency">{{ __(gs()->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Payment Before') <i class="las la-info-circle text--info"
                                            title="@lang('The owner must pay the monthly bill before the specified number of days has passed')"></i></label>
                                    <div class="input-group">
                                        <input class="form-control" min="0" name="payment_before" required
                                            type="number" value="{{ gs()->payment_before }}">
                                        <span class="input-group-text">@lang('Days')</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Maximum Payment Month') <i class="las la-info-circle text--info"
                                            title="@lang('The owner can make a payment for the maximum allowable number of months')"></i></label>
                                    <div class="input-group">
                                        <input class="form-control" min="0" name="maximum_payment_month" required
                                            type="number" value="{{ gs()->maximum_payment_month }}">
                                        <span class="input-group-text">@lang('Month')</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Upcoming Bill Payment Remind Before')</label>
                                    <select class="select2 select2-multi-select" multiple name="remind_before_days[]"
                                        required>
                                        @for ($i = 1; $i < gs()->payment_before; $i++)
                                            <option @selected(in_array($i, gs()->remind_before_days ?? [])) value="{{ $i }}">
                                                {{ $i . ' ' . __('Day Ago') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>
                                        @lang('App Video')
                                        <em><small class="text-muted text--info">@lang('Supported Files: mp4, mov, ogg, gt')</small></em>
                                    </label>
                                    <input type="file" name="app_video" class="form-control" @required(gs()->app_video == null)
                                        accept=".mp4,.mov,.ogg,.gt">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>
                                        @lang('Maximum Image Size')
                                        <i class="las la-info-circle text--info" title="@lang('This is the maximum size of a single image of gallery or room type image.')"></i>
                                    </label>
                                    <span class="input-group">
                                        <input type="number" name="max_image_size" class="form-control"
                                            value="{{ gs('max_image_size') }}" required>
                                        <span class="input-group-text">@lang('MB')</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>
                                        @lang('Maximum Image Upload')
                                        <i class="las la-info-circle text--info" title="@lang('This is the maximum number of gallery images that can be uploaded by a vender. This number will be also applicable to room type images.')"></i>
                                    </label>
                                    <input type="number" name="max_photo_count" class="form-control"
                                        value="{{ gs('max_photo_count') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

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
        })(jQuery);
    </script>
@endpush
