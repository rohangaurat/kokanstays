<div class="card b-radius--5 overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex align-items-center p-3 bg--primary">
            <div class="avatar avatar--lg">
                <img src="{{ getImage(getFilePath('ownerProfile') . '/' . $owner->image, getFileSize('ownerProfile')) }}" alt="@lang('Image')">
            </div>
            <div class="ps-3">
                <h4 class="text--white">{{ __($owner->fullname) }}</h4>
            </div>
        </div>
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                @lang('Name')
                <span class="fw-bold">{{ __($owner->fullname) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                @lang('Mobile')
                <span class="fw-bold">+{{ __($owner->mobile) }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                @lang('Email')
                <span class="fw-bold">{{ $owner->email }}</span>
            </li>
        </ul>
    </div>
</div>
