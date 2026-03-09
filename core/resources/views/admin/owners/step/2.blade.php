<div class="row">
    <div class="col-xxl-4">
        <div class="form-group">
            <label>@lang('Image')</label>
            <x-image-uploader :image="@$setting->image" type="hotelImage" :required="false" class="w-100" />
        </div>
    </div>
    <div class="col-xxl-8">
        <div class="form-group">
            <label>@lang('Cover Image')</label>
            <x-image-uploader :image="@$setting->cover_image" type="hotelCoverImage" name="cover_image" id="cover_image"
                :required="false" class="w-100" />
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label>@lang('Gallery')</label>
            <div class="input-images"></div>
            <div class="info my-3">
                <strong><i class="las la-info-circle"></i> @lang('Please Note'):</strong>
                @lang('You may upload a maximum of')
                <strong>{{ gs('max_photo_count') ?? 6 }}</strong>
                @lang('gallery images for this listing. Each gallery image must not exceed')
                <strong>{{ gs('max_image_size') ?? 2 }} @lang('MB')</strong>
                @lang('in size. Ensure your files meet these requirements to complete the upload successfully.')
            </div>
        </div>
    </div>
</div>

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
            @if (isset($images))
                let preloaded = @json($images);
            @else
                let preloaded = [];
            @endif

            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'cover_photos',
                preloadedInputName: 'old',
                maxSize: Number(`{{ gs('max_image_size') ?? 2 }}`) * 1024 * 1024,
                maxFiles: `{{ gs('max_photo_count') ?? 6 }}`
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .info {
            font-size: 14px !important;
            background-color: #eb222210 !important;
            border-radius: 8px !important;
            border-left: 3px solid #eb2222 !important;
            padding: 10px 10px !important;
            color: #eb2222 !important;
        }
    </style>
@endpush
