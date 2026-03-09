  @can('owner.booking.merge')
      <div class="modal fade" id="mergeBooking" role="dialog" tabindex="-1">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">@lang('Merging with'): <span class="booking-with"></span></h5>
                      <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                          <i class="las la-times"></i>
                      </button>
                  </div>
                  <form action="" method="post">
                      @csrf
                      <div class="modal-body">

                          <div class="form-group">
                              <label class="required">@lang('Booking Number')</label>
                              <div class="d-flex">
                                  <div class="input-group row gx-0">
                                      <input class="form-control" name="booking_numbers[]" required type="text">
                                  </div>
                                  <button class="btn btn--success input-group-text addMoreBookingBtn ms-4 flex-shrink-0 border-0" type="button">
                                      <i class="las la-plus"></i>
                                  </button>
                              </div>
                          </div>
                          <div class="more-bookings"></div>
                      </div>
                      <div class="modal-footer">
                          <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  @endcan

  @can('owner.booking.key.handover')
      <div class="modal fade" id="keyHandoverModal" role="dialog" tabindex="-1">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">@lang('Key Handover')</h5>
                      <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                          <i class="las la-times"></i>
                      </button>
                  </div>
                  <form action="" method="post">
                      @csrf
                      <div class="modal-body">
                          <p class="fw-bold"> @lang('Rooms')</p>
                          <div class="bookedRooms"></div>
                      </div>

                      <div class="modal-footer">
                          <button class="btn btn--primary w-100 h-45" type="submit">@lang('Handover Now')</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  @endcan

  @push('script')
      <script>
          (function($) {
              "use strict";

              $(document).on('click', '.mergeBookingBtn', function(e) {
                  e.preventDefault();
                  let modal = $('#mergeBooking');
                  let orderNumber = $(this).data('booking_number');
                  let form = modal.find('form')[0];
                  form.action = `{{ route('owner.booking.merge', '') }}/${$(this).data('id')}`
                  modal.find('.booking-with').text(
                      `${orderNumber}`
                  );
                  modal.modal('show');
              });

              $(document).on('click', '.addMoreBookingBtn', function() {
                  let addMoreBooking = $('.more-bookings');
                  addMoreBooking.append(`
                    <div class="form-group">
                        <div class="d-flex">
                            <div class="input-group row gx-0">
                                <input type="text" class="form-control" name="booking_numbers[]" required>
                            </div>
                            <button type="button" class="input-group-text bg-danger border-0 btnRemove flex-shrink-0 ms-4"><i class="las la-times"></i></button>
                        </div>
                    </div>
                `);
              });

              $(document).on('click', '.btnRemove', function() {
                  $(this).parents('.form-group').remove();
              });

              $(document).on('click', '.handoverKeyBtn', function() {
                  let data = $(this).data();
                  let modal = $('#keyHandoverModal');
                  let roomsHtml = '';

                  $.each(data.booked_rooms, function(index, bookedRoom) {
                      roomsHtml += `<span class="booked_room">${bookedRoom.room.room_number}</span>`;

                  });

                  modal.find('.bookedRooms').html(`
                    ${roomsHtml}
                `);

                  modal.find('form').attr('action', `{{ route('owner.booking.key.handover', '') }}/${data.id}`);
                  modal.modal('show');
              });
          })(jQuery);
      </script>
  @endpush

  @push('style')
      <style>
          #keyHandoverModal .booked_room {
              display: inline-block;
              padding: 3px 20px;
              margin: 5px;
              background: #e3e0e0;
              color: #3e3d3d;
              border-radius: 5px;
              font-size: 1.1rem;
          }
      </style>
  @endpush
