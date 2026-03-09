<div class="modal fade custom--modal" id="confirmationModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mb-0">
                <h6 class="modal-title">@lang('Confirmation Alert!')</h6>
                <button type="button" class="close-icon" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="payment-information my-4">
                        <span class="text question"></span>
                    </div>
                </div>
                <div class="modal-footer p-0 pt-3">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--base btn--sm">@lang('Yes')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.confirmationBtn').on('click', function() {
                let modal = $('#confirmationModal');
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.question').text($(this).data('question'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
