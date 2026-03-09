function selectRooms(roomType, selectedRooms) {
    var selected = 0;
    var firstSelected;
    var lastSelected;
    var prevSibling;
    var nextSibling;
    var dateColumn;
    var availableRooms;
    var needChanged = false;

    $.each(selectedRooms, function (index, element) {
        var singleRoom = $(`[room=room-${element}]`).not(`:disabled`);
        if (singleRoom.hasClass('available')) {
            singleRoom.removeClass('btn--primary').addClass('btn--success selected');
            singleRoom.data('booked_status', 1);
        }
    });

    var roomColumns = $(`.room-column[data-room_type_id="${roomType.id}"]`);

    $.each(roomColumns, function (i, element) {

        selected = $(element).find('.selected').length;
        var numberOfRooms = Number($(this).data('number_of_rooms'));

        if (selected < numberOfRooms) {
            availableRooms = $(element).find('.available.selected');

            firstSelected = availableRooms.first();
            lastSelected = availableRooms.last();

            prevSibling = $(firstSelected).prev().not(':disabled');
            nextSibling = $(lastSelected).next().not(':disabled');
            dateColumn = $(element).siblings("td").first();
            if (prevSibling.length) {
                prevSibling.addClass('btn--success selected');
                prevSibling.data('booked_status', 1);
                dateColumn.addClass('text-warning');
                needChanged = true;
            } else if (nextSibling.length) {
                nextSibling.addClass('btn--success selected');
                nextSibling.data('booked_status', 1);
                dateColumn.addClass('text-warning');
                needChanged = true;
            } else {
                var closestRoom = $(element).find('.available:not(.selected)').first();
                closestRoom.addClass('btn--success selected');
                closestRoom.data('booked_status', 1);
                dateColumn.addClass('text-warning');
                if (closestRoom.length) {
                    needChanged = true;
                }
            }
        }
    });
}