check_all();
function check_all()
{
    var all_checked = $(".permission:checked").length;
    var total = $(".permission").length;
    if(all_checked == total){
        $(".all_permissions").prop("checked", true);
    }else{
        $(".all_permissions").prop("checked", false);
    }
}

$('body').on('change', '.all_permissions', function() {
    if ($(this).is(':checked')) {
        $(".permission").prop("checked", true);
    } else {
        $(".permission").prop("checked", false);
    }
});

$('body').on('change', '.permission', function() {
    check_all();
});