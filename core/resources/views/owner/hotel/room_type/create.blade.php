@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('owner.hotel.room.type.save', @$roomType ? $roomType->id : 0) }}"
                enctype="multipart/form-data" method="POST">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"> @lang('General Information')</h5>
                    </div>
                    <div class="card-body">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input class="form-control" name="name" required type="text"
                                        value="{{ old('name', @$roomType->name) }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label class="required" for="fare">@lang('Fare') /@lang('Night')</label>
                                    <div class="input-group">
                                        <input class="form-control" id="fare" min="0" name="fare" required
                                            step="any" type="number"
                                            value="{{ old('fare', @$roomType->fare ? getAmount(@$roomType->fare) : '') }}">
                                        <span class="input-group-text">{{ __(@gs()->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label>@lang('Discount Percentage')</label>
                                    <div class="input-group">
                                        <input class="form-control" min="0" name="discount_percentage" step="any"
                                            type="number"
                                            value="{{ old('discount_percentage', @$roomType->discount ? getAmount(@$roomType->discount_percentage) : '') }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label>@lang('Total Adult')</label>
                                    <input class="form-control" min="1" name="total_adult" required type="number"
                                        value="{{ old('total_adult', @$roomType->total_adult) }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label>@lang('Total Child')</label>
                                    <input class="form-control" min="0" name="total_child" required type="number"
                                        value="{{ old('total_child', @$roomType->total_child) }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label>@lang('Cancellation Fee') /@lang('Night')</label>
                                    <div class="input-group">
                                        <input class="form-control cancellationFee" min="0" name="cancellation_fee"
                                            required step="any" type="number"
                                            value="{{ old('cancellation_fee', @$roomType->cancellation_fee ? getAmount(@$roomType->cancellation_fee) : '') }}">
                                        <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                            @php
                                $selectedAmenities = old('amenities', @$roomType ? $roomType->amenities->pluck('id')->toArray() : []);
                                $selectedFacilities = old('facilities', @$roomType ? $roomType->facilities->pluck('id')->toArray() : []);
                            @endphp
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label> @lang('Amenities')</label>
                                    <select class="select2-auto-tokenize" multiple="multiple" name="amenities[]">
                                        @foreach ($amenities as $item)
                                            <option value="{{ $item->id }}" @selected(in_array($item->id, $selectedAmenities))>
                                                {{ $item->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label> @lang('Facilities')</label>
                                    <select class="select2-auto-tokenize" multiple="multiple" name="facilities[]">
                                        @foreach ($facilities as $item)
                                            <option value="{{ $item->id }}" @selected(in_array($item->id, $selectedFacilities))>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-4">
                                <div class="form-group">
                                    <label> @lang('Featured')
                                        <small class="text--primary">(@lang('Featured rooms will be displayed in featured rooms section'))</small>
                                    </label>
                                    <input @if (old('is_featured', @$roomType->is_featured)) checked @endif data-bs-toggle="toggle"
                                        data-height="50" data-off="@lang('Unfeatured')" data-offstyle="-danger"
                                        data-on="@lang('Featured')" data-onstyle="-success" data-size="large"
                                        data-width="100%" name="is_featured" type="checkbox">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            @lang('Bed Per Room')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex justify-content-center mb-3">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <h4 class="mb-1">@lang('Total Bed')</h4>
                                    <input @isset($roomType) readonly @endisset class="form-control"
                                        min="1" name="total_bed" required type="number"
                                        value="{{ @$roomType ? count(@$roomType->beds) : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="bed">
                            @if (@$roomType)
                                <div class="row border-top pt-3">
                                    @foreach ($roomType->beds as $bed)
                                        <div class="col-md-3 number-field-wrapper bed-content">
                                            <div class="form-group">
                                                <label class="required" for="bed">
                                                    @lang('Bed') -
                                                    <span class="serialNumber">{{ $loop->iteration }}</span>
                                                </label>
                                                <div class="input-group">
                                                    <select class="form-control bedType"
                                                        name="bed[{{ $loop->iteration }}]">
                                                        <option value="">@lang('Select One')</option>
                                                        @foreach ($bedTypes as $bedType)
                                                            <option @if ($bedType->name == $bed) selected @endif
                                                                value="{{ $bedType->name }}">
                                                                {{ $bedType->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button class="input-group-text bg-danger btnRemove border-0"
                                                        data-name="bed" type="button">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-outline--primary addMore btn-sm" type="button">
                                    <i class="la la-plus"></i>@lang('Add More')
                                </button>
                            @elseif(old('bed'))
                                <div class="row border-top pt-3">
                                    @foreach (old('bed') as $bed)
                                        <div class="col-md-3 number-field-wrapper bed-content">
                                            <div class="form-group">
                                                <label class="required" for="bed">@lang('Bed') - <span
                                                        class="serialNumber">{{ $loop->iteration }}</span></label>
                                                <div class="input-group">
                                                    <select class="form-control bedType"
                                                        name="bed[{{ $loop->iteration }}]">
                                                        <option value="">@lang('Select One')</option>
                                                        @foreach ($bedTypes as $bedType)
                                                            <option @if ($bedType->name == $bed) selected @endif
                                                                value="{{ $bedType->name }}">
                                                                {{ $bedType->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button class="input-group-text bg-danger btnRemove border-0"
                                                        data-name="bed" type="button"><i
                                                            class="las la-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn--success addMore" type="button"> <i
                                        class="la la-plus"></i>@lang('Add More')</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-3">
                            @lang('Images')
                        </h5>
                        <div class="info mb-3">
                            <strong><i class="las la-info-circle"></i> @lang('Please Note'):</strong>
                            @lang('You may upload a maximum of')
                            <strong>{{ gs('max_photo_count') ?? 6 }}</strong>
                            @lang('images for this listing. Each image must not exceed')
                            <strong>{{ gs('max_image_size') ?? 2 }} @lang('MB')</strong>
                            @lang('in size. Ensure your files meet these requirements to complete the upload successfully.')
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="input-images pb-3"></div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            @lang('Description')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea class="form-control" id="description" name="description" rows="6">{{ @$roomType->description ?? old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            @lang('Cancellation Policy')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <textarea class="form-control" name="cancellation_policy" rows="6">{{ old('cancellation_policy', @$roomType->cancellation_policy) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @can('owner.hotel.room.type.save')
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </form>
        </div>
    </div>
@endsection

@can('owner.hotel.room.type.all')
    @push('breadcrumb-plugins')
        <x-back route="{{ route('owner.hotel.room.type.all') }}" />
    @endpush
@endcan

@push('script-lib')
    <script src="{{ asset('assets/global/js/image-uploader.min.js') }}"></script>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/global/css/image-uploader.min.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let bedTypes = @json($bedTypes);

            @if (isset($images))
                let preloaded = @json($images);
            @else
                let preloaded = [];
            @endif

            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'images',
                preloadedInputName: 'old',
                maxSize: `{{ gs('max_image_size') ?? 2 }}` * 1024 * 1024,
                maxFiles: `{{ gs('max_photo_count') ?? 6 }}`
            });

            var amenities = @json(old('amenities') ?? []);
            var facilities = @json(old('facilities') ?? []);

            if (amenities.length > 0) {
                $.each(amenities, function(i, amenity) {
                    $(`select[name="amenities[]"] option[value=${amenity}]`).prop('selected', true);
                });
            }

            if (facilities.length > 0) {
                $.each(facilities, function(i, facility) {
                    $(`select[name="facilities[]"] option[value=${facility}]`).prop('selected', true);
                });
            }

            // room js
            $('[name=total_room]').on('input', function() {
                var totalRoom = $(this).val();
                if (totalRoom) {
                    let content = '<div class="row border-top pt-3">';
                    for (var i = 1; i <= totalRoom; i++) {
                        content += getRoomContent(i);
                    }
                    content += '</div>';
                    $('.room').html(content);
                }
            });

            function getRoomContent(number) {
                return `
                <div class="col-md-3 number-field-wrapper room-content">
                    <div class="form-group">
                        <label for="room" class="required">@lang('Room') - <span class="serialNumber">${number}</span></label>
                        <div class="input-group">
                            <input type="text" name="room[]" class="form-control roomNumber" required>
                            <button type="button" class="input-group-text bg-danger border-0 btnRemove" data-name="room"><i class="las la-times"></i></button>
                        </div>
                    </div>
                </div>`;
            }

            function setTotalRoom() {
                var totalRoom = $('.roomNumber').length;
                $('[name=total_room]').val(totalRoom);
            }

            //bed js
            $('[name=total_bed]').on('input', function() {
                var totalBed = $(this).val();
                if (totalBed) {
                    let content = '<div class="row border-top pt-3">';
                    for (var i = 1; i <= totalBed; i++) {
                        content += getBedContent(i);
                    }
                    content += '</div>';
                    $('.bed').html(content);
                }
            });

            function getBedContent(number) {
                return `
                    <div class="col-md-3 number-field-wrapper bed-content">
                        <div class="form-group">
                            <label for="bed" class="required">@lang('Bed') - <span class="serialNumber">${number}</span></label>
                            <div class="input-group">
                                <select class="form-control bedType" name="bed[${number}]">
                                        <option value="">@lang('Select One')</option>
                                        ${allBedType()}
                                </select>
                                <button type="button" class="input-group-text bg-danger border-0 btnRemove" data-name="bed">
                                    <i class="las la-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
            }

            function setTotalBed() {
                var totalBed = $('.bedType').length;
                $('[name=total_bed]').val(totalBed);
            }

            function allBedType() {
                var options;
                $.each(bedTypes, function(i, e) {
                    options += `<option value="${e.name}">${e.name}</option>`;
                });
                return options;
            }

            //common js
            $('[name=total_bed]').on('input', function() {
                var totalBed = $(this).val();
                if (totalBed) {
                    let content = '<div class="row border-top pt-3">';
                    for (var i = 1; i <= totalBed; i++) {
                        content += getBedContent(i);
                    }
                    content += '</div>';
                    $('.bed').html(content);
                }
            });

            $(document).on('click', '.btnRemove', function() {
                $(this).closest('.number-field-wrapper').remove();
                let divName = null;
                if ($(this).data('name') == 'bed') {
                    setTotalBed();
                    divName = $('.bed-content').find('.serialNumber');
                } else {
                    divName = $('.room-content').find('.serialNumber');
                    setTotalRoom();
                }
                resetSerialNumber(divName);
            });

            function resetSerialNumber(divName) {
                $.each(divName, function(i, e) {
                    $(e).text(i + 1)
                });
            }

            $('.addMore').on('click', function() {
                if ($(this).parents().hasClass('room')) {
                    var total = $('.roomNumber').length;
                    total += 1;

                    $('.room .row').append(getRoomContent(total));
                    setTotalRoom();
                    return;
                }

                var total = $('.bedType').length;
                total += 1;

                $('.bed .row').append(getBedContent(total));
                setTotalBed();
            });

            // Edit part
            // let roomType = @json(@$roomType);
            // if (roomType) {
            //     $.each(roomType.amenities, function(i, e) {
            //         $(`select[name="amenities[]"] option[value=${e.id}]`).prop('selected', true);
            //     });

            //     $.each(roomType.facilities, function(i, e) {
            //         $(`select[name="facilities[]"] option[value=${e.id}]`).prop('selected', true);
            //     });
            // }

            // $.each($('.multi-select'), function(index, element) {
            //     var parent = $(this).closest('.multiSelectParent');

            //     $(element).select2({
            //         dropdownParent: parent
            //     });
            // });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .f-size-12 {
            font-size: 12px !important;
        }

        .card-header .info {
            font-size: 14px !important;
            background-color: #eb222210 !important;
            border-radius: 8px !important;
            border-left: 3px solid #eb2222 !important;
            padding: 10px 10px !important;
            color: #eb2222 !important;
        }
    </style>
@endpush
