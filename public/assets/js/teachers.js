function get_teachers(page = 1)
{
    $.ajax({
        type: "post",
        url: "/get_teachers",
        data: {_token: $('meta[name="csrf-token"]').attr("content"), page: page},
        success: function (response) {
            $('.get_teachers').html(response);
        }
    });
}
$('body').on('click', '.pagination a', function (e) {
    e.preventDefault();
    var page = $(this).attr('data-page');
    get_teachers(page);
});