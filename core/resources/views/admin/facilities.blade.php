@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facilities as $item)
                                    <tr>
                                        <td>
                                            <span class="me-2">{{ $facilities->firstItem() + $loop->index }}.</span>
                                            {{ __($item->name) }}
                                        </td>
                                        <td>
                                            <img alt="" src="{{ $item->image_url }}">
                                        </td>
                                        <td> @php echo $item->statusBadge @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary cuModalBtn editBtn"
                                                    data-modal_title="@lang('Update Facility')" data-resource="{{ $item }}"
                                                    type="button">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @if ($item->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.hotel.facility.status', $item->id) }}"
                                                        data-question="@lang('Are you sure to enable this facility?')" type="button">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.hotel.facility.status', $item->id) }}"
                                                        data-question="@lang('Are you sure to disable this facility?')" type="button">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($facilities->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($facilities) }}
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
                <form action="{{ route('admin.hotel.facility.add') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label> @lang('Name')</label>
                            <input class="form-control" name="name" required type="text">
                        </div>
                        <div class="form-group">
                            <label> @lang('Image')</label>
                            <div class="input-group">
                                <input accept=".jpg,.png,.jpeg" class="form-control image-input" name="image"
                                    type="file">
                                <span class="input-group-text imagePreview"></span>
                            </div>
                            <small class="text--xsm">@lang('Supported Files: .jpg, .png, .jpeg'). @lang('Image will be resized into:')
                                {{ getFileSize('facility') }}px</small>
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
    <button class="btn btn-sm btn-outline--primary addBtn cuModalBtn" data-modal_title="@lang('Add New Facility')" type="button">
        <i class="las la-plus"></i>@lang('Add New ')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('#cuModal').on('shown.bs.modal', function(e) {
                $(document).off('focusin.modal');
            });

            $('#cuModal').on('hidden.bs.modal', function(e) {
                $(this).find('.imagePreview').html("");
            });

            $('.addBtn').on('click', function() {
                $('#cuModal').find("[for=image]").addClass('required');
                $('#cuModal').find('[name=image]').attr('required', true);
            });

            $('.editBtn').on('click', function() {
                let resource = $(this).data('resource');
                $('#cuModal').find("[for=image]").removeClass('required');
                $('#cuModal').find('[name=image]').attr('required', false);
                $('#cuModal').find('.imagePreview').html(`<img src="${resource.image_url}" alt=""/>`);
            });

            $('.image-input').on('change', function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#cuModal').find('.imagePreview').html(`<img src="${e.target.result}" alt=""/>`);
                }
                reader.readAsDataURL(this.files[0]);
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .input-group .imagePreview img {
            height: 30px;
        }
    </style>
@endpush
