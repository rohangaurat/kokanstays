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
                                    <th>@lang('City')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Locations')</th>
                                    <th>@lang('Is Popular')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cities as $city)
                                    @php
                                        $city->image_with_path = getImage(
                                            getFilePath('city') . '/' . @$city->image,
                                            getFileSize('city'),
                                        );
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb me-2">
                                                    <img alt="" class="thumb" src="{{ $city->image_with_path }}">
                                                </div>
                                                <span>{{ __($city->name) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ __(@$city->country->name) }}</td>
                                        <td>
                                            <a href="{{ route('admin.location.all') }}?search={{ $city->name }}">
                                                <span class="badge badge--primary">{{ $city->total_location }}</span>
                                            </a>
                                        </td>
                                        <td>@php echo $city->popularBadge; @endphp</td>
                                        <td>@php echo $city->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary cuModalBtn editBtn"
                                                    data-modal_title="@lang('Update City')"
                                                    data-resource="{{ $city }}">
                                                    <i class="las la-pencil-alt"></i>@lang('Edit')
                                                </button>
                                                @if ($city->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.location.city.status.update', $city->id) }}"
                                                        data-question="@lang('Are you sure to enable this city?')" type="button">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.location.city.status.update', $city->id) }}"
                                                        data-question="@lang('Are you sure to disable this city?')" type="button">
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
                @if ($cities->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cities) }}
                    </div>
                @endif
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
                <form action="{{ route('admin.location.city.add') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <x-image-uploader :required="false" class="w-100" type="city" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Country')</label>
                            <select class="form-control select2-basic" name="country_id" required>
                                <option disabled selected value="">@lang('Select One')</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ __($country->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Is Popular')</label>
                            <input data-bs-toggle="toggle" data-height="50" data-off="@lang('No')"
                                data-offstyle="-danger" data-on="@lang('Yes')" data-onstyle="-success"
                                data-size="large" data-width="100%" name="is_popular" type="checkbox">
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
    <x-search-form />
    <button class="btn btn-sm btn-outline--primary cuModalBtn addBtn" data-modal_title="@lang('Add New City')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var modal = $('#cuModal');

            $('.addBtn').on('click', function() {
                $('.select2-basic').val('').select2({
                    dropdownParent: modal
                });
                var imgURL = "{{ getImage(null, getFileSize('city')) }}";
                modal.find(".image-upload-preview").css("background-image", `url(${imgURL})`);
                modal.find("[name=is_popular]").bootstrapToggle('off');
            });

            $('.editBtn').on('click', function() {
                let data = $(this).data('resource');
                let isPopular = data.is_popular == 1 ? 'on' : 'off';
                modal.find("[name=is_popular]").bootstrapToggle(isPopular);
                setTimeout(() => {
                    $('.select2-basic').select2({
                        dropdownParent: modal
                    });
                }, 100);
            });
        })(jQuery);
    </script>
@endpush
