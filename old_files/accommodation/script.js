$(document).ready(function () {
    // Load floors
    $(document).on('click', '.building', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $('#floors, #rooms, #beds').empty();
        $.get('get_floors.php', { id }, function (data) {
            $('#floors').html(data);
        });
    });

    // Load rooms
    $(document).on('click', '.floor', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $('#rooms, #beds').empty();
        $.get('get_rooms.php', { id }, function (data) {
            $('#rooms').html(data);
        });
    });

    // Load beds
    $(document).on('click', '.room', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $('#beds').empty();
        $.get('get_beds.php', { id }, function (data) {
            $('#beds').html(data);
        });
    });

    // Assign bed
    $(document).on('click', '.assign-btn', function () {
        const bed_id = $(this).data('id');
        const emp_no = $(this).closest('td').find('.autocomplete-emp').val().trim();
        if (!emp_no) return alert("Please enter employee number");

        $.post('assign_bed.php', { bed_id, emp_no }, function (res) {
            alert(res);
            // Instead of re-clicking the .room button, reload beds directly if needed
            $('.room.active').click(); 
        });
    });

    // Unassign bed
    $(document).on('click', '.unassign-btn', function () {
        const bed_id = $(this).data('id');
        $.post('unassign_bed.php', { bed_id }, function (res) {
            alert(res);
            $('.room.active').click();
        });
    });

    // jQuery UI autocomplete initialization for dynamic fields
    $(document).on('focus', '.autocomplete-emp', function () {
        if (!$(this).data("ui-autocomplete")) {
            $(this).autocomplete({
                source: "search_employee.php",
                minLength: 2
            });
        }
    });
});
