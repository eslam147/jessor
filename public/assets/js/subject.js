get_lessons();
function get_lessons(page = 1)
{
    var subject_id = $('.subject').attr('subject');
    $.ajax({
        type: "post",
        url: "/get_lessons",
        data: {_token: $('meta[name="csrf-token"]').attr("content"), page: page,subject_id:subject_id},
        success: function (response) {
            $('.get_lessons').html(response);
        }
    });
}
$('body').on('click', '.pagination a', function (e) {
    e.preventDefault();
    var page = $(this).attr('data-page');
    get_lessons(page);
});

$('body').on('change', '.follow', function (e) {
    var id = $(this).attr('data-id');
    var text = $('.follow-btn').text().trim();
    if (text === 'follow') {
        $('.follow-btn').html('<i class="ti-minus"></i> unfollow');
        $('.follow-btn').addClass('active');
    } else {
        $('.follow-btn').html('<i class="ti-plus"></i> follow');
        $('.follow-btn').removeClass('active');
    }
    $.ajax({
        type: "post",
        url: "/follow",
        data: {_token: $('meta[name="csrf-token"]').attr("content"), id: id},
    });
});
$('body').on('click', '.follow-btn', function (e) {
    var id = $('.follow').attr('data-id');
    var text = $(this).text().trim(); // إزالة أي مسافات غير مرغوب فيها
    
    if (text === 'follow') {
        $(this).html('<i class="ti-minus"></i> unfollow');
        $(this).addClass('active');
    } else {
        $(this).html('<i class="ti-plus"></i> follow');
        $(this).removeClass('active');
    }
    $('.follow').prop('checked', function (i, value) {
    return !value;
    });
    $.ajax({
        type: "post",
        url: "/follow",
        data: {_token: $('meta[name="csrf-token"]').attr("content"), id: id},
    });
});