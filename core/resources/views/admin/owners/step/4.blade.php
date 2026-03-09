<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>@lang('Cancellation Policy')</label>
            <textarea name="cancellation_policy" class="form-control" rows="6" required>{{ old('cancellation_policy', $setting->cancellation_policy) }}</textarea>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group position-relative">
            <label> @lang('Instructions')</label>
            <select class="form-control select2-auto-tokenize" multiple="multiple" name="instructions[]">
                @if (@$setting->instructions)
                    @foreach (@$setting->instructions as $item)
                        <option value="{{ $item }}" selected>{{ __($item) }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group position-relative">
            <label> @lang('Early Check-in Policy')</label>
            <input type="text" name="early_check_in_policy" class="form-control"
                value="{{ old('early_check_in_policy', $setting->early_check_in_policy) }}">
        </div>
    </div>
    <div class="col-12">
        <div class="form-group position-relative">
            <label> @lang('Child Policy')</label>
            <select class="form-control select2-auto-tokenize" multiple="multiple" name="child_policy[]">
                @if (@$setting->child_policy)
                    @foreach (@$setting->child_policy as $item)
                        <option value="{{ $item }}" selected>{{ __($item) }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group position-relative">
            <label> @lang('Pet Policy')</label>
            <select class="form-control select2-auto-tokenize" multiple="multiple" name="pet_policy[]">
                @if (@$setting->pet_policy)
                    @foreach (@$setting->pet_policy as $item)
                        <option value="{{ $item }}" selected>{{ __($item) }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group position-relative">
            <label> @lang('Other Policy')</label>
            <select class="form-control select2-auto-tokenize" multiple="multiple" name="other_policy[]">
                @if (@$setting->other_policy)
                    @foreach (@$setting->other_policy as $item)
                        <option value="{{ $item }}" selected>{{ __($item) }}</option>
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
