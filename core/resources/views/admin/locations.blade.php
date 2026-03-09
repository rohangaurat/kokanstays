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
                                    <th>@lang('Name')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($locations as $location)
                                    <tr>
                                        <td>
                                            <span class="me-2">{{ $locations->firstItem() + $loop->index }}.</span>
                                            {{ __($location->name) }}
                                        </td>
                                        <td>{{ __(@$location->city->name) }}</td>
                                        <td>{{ __(@$location->city->country->name) }}</td>
                                        <td>@php echo $location->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary cuModalBtn editBtn"
                                                    data-modal_title="@lang('Update Location')" data-resource="{{ $location }}">
                                                    <i class="las la-pencil-alt"></i>@lang('Edit')
                                                </button>
                                                @if ($location->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.location.status.update', $location->id) }}"
                                                        data-question="@lang('Are you sure to enable this location?')" type="button">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.location.status.update', $location->id) }}"
                                                        data-question="@lang('Are you sure to disable this location?')" type="button">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="4">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($locations->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($locations) }}
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
                <form action="{{ route('admin.location.add') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Country')</label>
                            <select class="form-control select2-basic allCountries" required>
                                <option disabled selected value="">@lang('Select One')</option>
                                @foreach ($countries as $country)
                                    <option data-cities="{{ $country->cities }}" value="{{ $country->id }}">
                                        {{ __($country->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('City')</label>
                            <select class="select2-basic allCities" name="city_id" required></select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" required />
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
    <button class="btn btn-sm btn-outline--primary cuModalBtn addBtn" data-modal_title="@lang('Add New Location')">
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

                var option = `<option value="">@lang('Select Country First')</option>`;
                $('[name=city_id]').html(option);
            });

            $('.editBtn').on('click', function() {
                let data = $(this).data('resource');

                if (data.is_popular) {
                    modal.find("[name=is_popular]").bootstrapToggle('on');
                } else {
                    modal.find("[name=is_popular]").bootstrapToggle('off');
                }

                $('.allCountries').val(data.city.country_id).trigger("change");

                setTimeout(() => {
                    $('.select2-basic').select2({
                        dropdownParent: modal
                    });
                }, 100);
            });

            $('.allCountries').on('change', function() {
                let cities = $(this).find('option:selected').data('cities');

                if (cities.length > 0) {
                    var option = new Option(`@lang('Select One')`, '');
                    let newOptions = [];
                    newOptions.push(option);

                    $.each(cities, function(index, city) {
                        var option = new Option(city.name, city.id);
                        newOptions.push(option);
                    });

                    $('[name=city_id]').html(newOptions);
                }
            });
        })(jQuery);
    </script>
@endpush
