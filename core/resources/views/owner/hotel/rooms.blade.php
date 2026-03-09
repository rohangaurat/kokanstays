@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Room Number')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Status')</th>
                                    @can(['owner.hotel.room.status', 'owner.hotel.room.update'])
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                    <tr>
                                        <td> {{ $room->room_number }}</td>
                                        <td>{{ __($room->roomType->name) }}</td>
                                        <td> @php echo $room->statusBadge; @endphp </td>
                                        @can(['owner.hotel.room.status', 'owner.hotel.room.update'])
                                            <td>
                                                <div class="button--group">
                                                    @can('owner.hotel.room.update')
                                                        <button class="btn btn-sm btn-outline--primary editBtn"
                                                            data-resource="{{ $room }}"><i class="las la-pencil-alt"></i>
                                                            @lang('Edit')</button>
                                                    @endcan
                                                    @can('owner.hotel.room.status')
                                                        @if ($room->status == Status::ENABLE)
                                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                                data-action="{{ route('owner.hotel.room.status', $room->id) }}"
                                                                data-question="@lang('Are your to enable this room?')" type="button">
                                                                <i class="la la-eye-slash"></i>@lang('Disable')
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                                data-action="{{ route('owner.hotel.room.status', $room->id) }}"
                                                                data-question="@lang('Are your to disable this room?')" type="button">
                                                                <i class="la la-eye"></i>@lang('Enable')
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcan
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
                @if ($rooms->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($rooms) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @can('owner.hotel.room.add')
        <div class="modal fade" id="addModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Add New Room')</h5>
                        <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('owner.hotel.room.add') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>@lang('Room Type')</label>
                                <select class="form-control" name="room_type_id" required>
                                    <option disabled selected value="">@lang('Select One')</option>
                                    @foreach ($roomTypes as $roomType)
                                        <option value="{{ $roomType->id }}">{{ __($roomType->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('Room Number')</label>

                                <div class="d-flex">
                                    <div class="input-group row gx-0">
                                        <input type="text" class="form-control" name=room_numbers[]" required>
                                    </div>
                                    <button class="btn btn--success input-group-text border-0 addItem flex-shrink-0 ms-4"
                                        type="button"><i class="las la-plus me-0"></i></button>
                                </div>
                            </div>
                            <div class="append-item d-none"></div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('owner.hotel.room.update')
        <div class="modal fade" id="editModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Update Room')</h5>
                        <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>@lang('Room Type')</label>
                                <select class="form-control" name="room_type_id" required>
                                    <option disabled selected value="">@lang('Select One')</option>
                                    @foreach ($roomTypes as $roomType)
                                        <option value="{{ $roomType->id }}">{{ __($roomType->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('Room Number')</label>
                                <input class="form-control" name="room_number" required type="text">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('owner.hotel.room.status')
        <x-confirmation-modal />
    @endcan
@endsection

@push('breadcrumb-plugins')
    <x-search-form filter='yes' />
    @can('owner.hotel.room.add')
        <button class="btn btn-outline--primary" data-bs-target="#addModal" data-bs-toggle="modal">
            <i class="las la-plus"></i> @lang('Add New')
        </button>
    @endcan
@endpush

@push('script')
    <script>
        "use strict";

        $(document).on('click', '.addItem', function() {
            var modal = $(this).parents('.modal');
            var div = modal.find('.append-item');
            div.append(`
                    <div class="form-group">
                        <div class="d-flex">
                            <div class="input-group row gx-0">
                                <input type="text" class="form-control" name=room_numbers[]" required>
                            </div>
                            <button type="button" class="btn btn--danger input-group-text border-0 removeRoomBtn flex-shrink-0 ms-4"><i class="las la-times me-0"></i></button>
                        </div>
                    </div>
                    `);
            div.removeClass('d-none');
        });

        $('.editBtn').on('click', function() {
            let modal = $('#editModal');
            let resource = $(this).data('resource');
            let route = `{{ route('owner.hotel.room.update', '') }}/${resource.id}`;

            modal.find('form').attr('action', route);
            modal.find('[name=room_type_id]').val(resource.room_type_id);
            modal.find('[name=room_number]').val(resource.room_number);
            modal.modal('show');
        });

        $(document).on('click', '.removeRoomBtn', function() {
            $(this).parents('.form-group').remove();
        });

        $('#editModal').on('shown.bs.modal', function(e) {
            $(document).off('focusin.modal');
        });

        $('#addModal').on('shown.bs.modal', function(e) {
            $(document).off('focusin.modal');
        });
        $('#addModal').on('hidden.bs.modal', function(e) {
            $(this).find('.append-item').html('');
        });
    </script>
@endpush
