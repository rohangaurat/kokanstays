@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="row gy-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Vendor Information')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                    <span>@lang('Name')</span>
                                    <span>{{ $owner->fullname }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                    <span>@lang('Mobile')</span>
                                    <a href="tel:{{ $owner->mobile }}">+{{ $owner->mobile }}</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                    <span>@lang('Email')</span>
                                    <a href="mailto:{{ $owner->email }}">{{ $owner->email }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                @lang('Hotel Information')
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                    <span>@lang('Hotel Name')</span>
                                    <span>{{ @$owner->hotelSetting->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                    <span>@lang('Location')</span>
                                    <span>{{ __(@$owner->hotelSetting->location->name) }}, {{ __(@$owner->hotelSetting->city->name) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                    <span>@lang('Country')</span>
                                    <span>{{ __(@$owner->hotelSetting->country->name) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Submitted Documents for Verification')</h5>
                        </div>
                        <div class="card-body">
                            @if ($owner->form_data)
                                <ul class="list-group list-group-flush">

                                    @foreach ($owner->form_data as $formData)
                                        @continue(!$formData->value)
                                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                            <span>{{ __($formData->name) }}</span>
                                            <div>
                                                @if ($formData->type == 'checkbox')
                                                    {{ implode(',', $formData->value) }}
                                                @elseif($formData->type == 'file')
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $formData->value)) }}"><i class="la la-file"></i> @lang('Attachment')</a>
                                                    </div>
                                                @else
                                                    {{ __($formData->value) }}
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn--danger confirmationBtn" data-action="{{ route('admin.vendor.request.reject', $owner->id) }}" data-question="@lang('Are you sure you want to reject this request?')" type="button"><i class="las la-trash-alt"></i>@lang('Reject')</button>

                    <button class="btn btn--primary confirmationBtn" data-action="{{ route('admin.vendor.request.approve', $owner->id) }}" data-question="@lang('Are you sure you want to approve this request?')" type="button"><i class="las la-check"></i>@lang('Approve')</button>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
