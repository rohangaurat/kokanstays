@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="row gy-4">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget bg="primary" icon="las la-wallet" style="3" title="Balance"
                        value="{{ showAmount($owner->balance) }}" />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget bg="dark" icon="las la-users" style="3" title="Total Staff"
                        value="{{ $widget['total_staff'] }}" />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget bg="info" icon="las la-hotel" style="3" title="Total Room Type"
                        value="{{ $widget['total_room_type'] }}" />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget bg="3" icon="las la-list" style="3" title="Total Bookings"
                        value="{{ $widget['total_booking'] }}" />
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3">
                <div class="flex-fill">
                    <button class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add"
                        data-bs-target="#addSubModal" data-bs-toggle="modal">
                        <i class="las la-plus-circle"></i> @lang('Balance')
                    </button>
                </div>
                <div class="flex-fill">
                    <button class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub"
                        data-bs-target="#addSubModal" data-bs-toggle="modal">
                        <i class="las la-minus-circle"></i> @lang('Balance')
                    </button>
                </div>
                <div class="flex-fill">
                    <a class="btn btn--primary btn--shadow w-100 btn-lg"
                        href="{{ route('admin.report.owner.login.history') }}?search={{ $owner->email }}">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>
                <div class="flex-fill">
                    <a class="btn btn--secondary btn--shadow w-100 btn-lg"
                        href="{{ route('admin.owners.notification.log', $owner->id) }}">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>
                <div class="flex-fill">
                    @if ($owner->status == Status::USER_ACTIVE)
                        <button class="btn btn--warning btn--shadow w-100 btn-lg" data-bs-target="#ownerStatusModal"
                            data-bs-toggle="modal" type="button">
                            <i class="las la-ban"></i>@lang('Ban Vendor')
                        </button>
                    @else
                        <button class="btn btn--success btn--shadow w-100 btn-lg" data-bs-target="#ownerStatusModal"
                            data-bs-toggle="modal" type="button">
                            <i class="las la-undo"></i>@lang('Unban Vendor')
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Information of') {{ $owner->fullname }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.owners.update', $owner->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" name="firstname" required type="text"
                                        value="{{ $owner->firstname }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Last Name')</label>
                                    <input class="form-control" name="lastname" required type="text"
                                        value="{{ $owner->lastname }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" name="email" required type="email"
                                        value="{{ $owner->email }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number') </label>
                                    <div class="input-group">
                                        <span class="input-group-text mobile-code">{{ $owner->dial_code }}</span>
                                        <input class="form-control" id="mobile" name="mobile" required type="number"
                                            value="{{ $owner->mobile }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" name="address" type="text"
                                        value="{{ @$owner->address->address }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" name="city" type="text"
                                        value="{{ @$owner->address->city }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('State')</label>
                                    <input class="form-control" name="state" type="text"
                                        value="{{ @$owner->address->state }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" name="zip" type="text"
                                        value="{{ @$owner->address->zip }}">
                                </div>
                            </div>
                            <div class=" col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Country')</label>
                                    <select class="form-control select2" name="country">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($countries as $country)
                                            <option data-mobile_code="{{ $country->dial_code }}"
                                                value="{{ $country->code }}" @selected($owner->country_code == $country->code)>
                                                {{ __($country->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('2FA Verification') </label>
                                    <input @if ($owner->ts) checked @endif data-bs-toggle="toggle"
                                        data-height="50" data-off="@lang('Disable')" data-offstyle="-danger"
                                        data-on="@lang('Enable')" data-onstyle="-success" data-width="100%"
                                        name="ts" type="checkbox">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSubModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.owners.add.sub.balance', $owner->id) }}" method="POST">
                    @csrf
                    <input name="act" type="hidden">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input class="form-control" name="amount" placeholder="@lang('Please provide positive amount')" required
                                    step="any" type="number">
                                <div class="input-group-text">{{ __(gs()->cur_text) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" name="remark" placeholder="@lang('Remark')" required rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ownerStatusModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($owner->status == Status::USER_ACTIVE)
                            <span>@lang('Ban Vendor')</span>
                        @else
                            <span>@lang('Unban Vendor')</span>
                        @endif
                    </h5>
                    <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.owners.status', $owner->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($owner->status == Status::USER_ACTIVE)
                            <h6 class="mb-2">@lang('If you ban this vendor he/she won\'t able to access his/her dashboard.')</h6>
                            <div class="form-group">
                                <label>@lang('Reason')</label>
                                <textarea class="form-control" name="reason" required rows="4"></textarea>
                            </div>
                        @else
                            <p><span>@lang('Ban reason was'):</span></p>
                            <p>{{ $owner->ban_reason }}</p>
                            <h4 class="text-center mt-3">@lang('Are you sure to unban this vendor?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($owner->status == Status::USER_ACTIVE)
                            <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                        @else
                            <button class="btn btn--dark" data-bs-dismiss="modal"
                                type="button">@lang('No')</button>
                            <button class="btn btn--primary" type="submit">@lang('Yes')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($owner->form_data)
        <div id="ownerFormModal" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Vendor Form Data')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="" method="POST">
                        @csrf
                        <div class="modal-body">
                            <ul class="list-group list-group-flush">
                                @foreach ($owner->form_data as $formData)
                                    @continue(!$formData->value)
                                    <li class="list-group-item d-flex justify-content-between flex-wrap gap-1">
                                        <span class="fw-bold">{{ __($formData->name) }}</span>
                                        @if ($formData->type == 'checkbox')
                                            <span>{{ implode(',', $formData->value) }}</span>
                                        @elseif($formData->type == 'file')
                                            <span>
                                                <a
                                                    href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $formData->value)) }}">
                                                    <i class="fa fa-file"></i> @lang('Attachment')
                                                </a>
                                            </span>
                                        @else
                                            <span>{{ __($formData->value) }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn-outline--dark" href="{{ route('admin.owners.login', $owner->id) }}" target="_blank">
        <i class="las la-sign-in-alt"></i>@lang('Login as Vendor')
    </a>
    @if ($owner->form_data)
        <button class="btn btn-sm btn-outline--info" type="button" data-bs-target="#ownerFormModal"
            data-bs-toggle="modal">
            <i class="las la-eye"></i>@lang('See Form Data')
        </button>
    @endif
    <a href="{{ route('admin.owners.hotel.setting', $owner->id) }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-cog"></i>@lang('Hotel Configuration')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.bal-btn').on("click", function() {
                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });

            let mobileElement = $('.mobile-code');
            $('select[name=country]').on('change', function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });
        })(jQuery);
    </script>
@endpush
