@if ($setting->complete_step >= 3)
    <div class="row">
        <div class="col-12">
            <div class="form-group position-relative">
                @php $facilityIds = $setting->facilities()->pluck('facility_id')->toArray(); @endphp
                <label> @lang('Facilities')</label>
                <select class="form-control select2-auto-tokenize" multiple="multiple" name="facilities[]">
                    @if (@$facilities)
                        @foreach (@$facilities as $item)
                            <option value="{{ $item->id }}" @selected(in_array($item->id, $facilityIds))>
                                {{ __($item->name) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group position-relative">
                <label> @lang('Complements')</label>
                <select class="form-control select2-auto-tokenize" multiple="multiple" name="complements[]">
                    @if (@$setting->complements)
                        @foreach (@$setting->complements as $item)
                            <option value="{{ $item }}" selected>{{ __($item) }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group position-relative">
                @php $amenitiesIds = $setting->amenities()->pluck('amenities_id')->toArray(); @endphp
                <label> @lang('Amenities')</label>
                <select class="form-control select2-auto-tokenize" multiple="multiple" name="amenities[]">
                    @if (@$amenities)
                        @foreach (@$amenities as $item)
                            <option value="{{ $item->id }}" @selected(in_array($item->id, $amenitiesIds))>
                                {{ __($item->title) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            (function($) {
                "use strict";

                $.each($('.select2-auto-tokenize'), function() {
                    $(this)
                        .wrap(`<div class="position-relative"></div>`)
                        .select2({
                            tags: true,
                            dropdownParent: $(this).parent()
                        });
                });
            })(jQuery);
        </script>
    @endpush

    @push('style')
        <style>
            .select2-container {
                z-index: unset !important;
            }
        </style>
    @endpush
@endif
