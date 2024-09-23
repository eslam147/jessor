get_subjects()
function get_subjects(page = 1, subject = '', class_id = '') {
    var teacher = $('.teacher').attr('teacher');
    $.ajax({
        type: "post",
        url: "/get_subjects",
        data: { _token: $('meta[name="csrf-token"]').attr("content"), teacher: teacher, page: page, subject: subject, class_id: class_id },
        success: function (response) {
            $('.get_subjects').html(response);
        }
    });
}

$('body').on('click', '.pagination a', function (e) {
    e.preventDefault();
    var page = $(this).attr('data-page');
    var subject = $('.subject').val();
    var calss_id = $('.calss_id').val();
    get_subjects(page, subject, calss_id);
});

$('body').on('change', '.subject', function (e) {
    e.preventDefault();
    var page = 1;
    var subject = $(this).val();
    var calss_id = $('.calss_id').val();
    get_subjects(page, subject, calss_id);
});

$('body').on('change', '.calss_id', function (e) {
    e.preventDefault();
    var page = 1;
    var subject = $('.subject').val();
    var calss_id = $(this).val();
    get_subjects(page, subject, calss_id);
});
$('body').on('click', '.like', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var active = $(this).find('i').hasClass('fa-solid');
    if (active === false) {
        $(this).find('i').addClass('fa-solid');
        $(this).find('i').removeClass('fa-regular');
        $('.dislike_' + id).find('i').removeClass('fa-solid');
        $('.dislike_' + id).find('i').addClass('fa-regular');
    } else {
        $(this).find('i').addClass('fa-regular');
        $(this).find('i').removeClass('fa-solid');
    }
    $.ajax({
        type: "post",
        url: "/like",
        data: { _token: $('meta[name="csrf-token"]').attr("content"), id: id },
    });
});
$('body').on('click', '.dislike', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var active = $(this).find('i').hasClass('fa-solid');
    if (active === false) {
        $(this).find('i').addClass('fa-solid');
        $(this).find('i').removeClass('fa-regular');
        $('.like_' + id).find('i').removeClass('fa-solid');
        $('.like_' + id).find('i').addClass('fa-regular');
    } else {
        $(this).find('i').addClass('fa-regular');
        $(this).find('i').removeClass('fa-solid');
    }
    $.ajax({
        type: "post",
        url: "/dislike",
        data: { _token: $('meta[name="csrf-token"]').attr("content"), id: id },
    });
});

$('body').on('click', '.follow-btn', function (e) {
    var id = $(this).attr('data-id');
    var text = $(this).text().trim();
    console.log(text);
    if (text === 'follow') {
        $(this).html('<i class="ti-minus"></i> unfollow');
        $(this).addClass('active');
    } else {
        $(this).html('<i class="ti-plus"></i> follow');
        $(this).removeClass('active');
    }

    $.ajax({
        type: "post",
        url: "/follow",
        data: { _token: $('meta[name="csrf-token"]').attr("content"), id: id },
    });
});

$(document).ready(function () {
    $("time.timeago").timeago();
});

$('body').on('click', '.drop-action', function () {
    $('.action-list').removeClass('show');
    // العثور على العنصر .media الأب المباشر
    var $currentMedia = $(this).closest('.media');
    // إغلاق جميع القوائم .action-list داخل العناصر .media الأبناء
    $currentMedia.siblings('.media').find('.action-list').removeClass('show');
    // العثور على القائمة .action-list داخل العنصر .media الحالي
    var $currentActionList = $currentMedia.find('.action-list');
    // تبديل حالة القائمة .action-list داخل العنصر .media الحالي
    // تأكد من عدم فتح .action-list داخل العناصر الأبناء .media
    $currentActionList.not($currentMedia.find('.media').find('.action-list')).toggleClass('show');
    $(this).closest('p').toggleClass('no-select');
});

$(document).on('click', function (event) {
    if (!$(event.target).closest('.drop-action').length) {
        $('.action-list').removeClass('show'); // Hide all action-lists
    }
});

$('body').on('click', '.replay', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var $closestMedia = $(this).closest('.media');
    if ($closestMedia.length > 0) {
        $('.replay-media-' + id).toggleClass('show');
    } else {
        var $currentBox = $(this).closest('.box');
        var $mediaListDivided = $currentBox.find('.media-list-divided');
        $mediaListDivided.toggleClass('show');
    }
});
var page = 1;
function get_comments(page = 1) {
    var id = $('.teacher').attr('teacher');
    $.ajax({
        method: 'get',
        url: '/comments/' + id,
        data: {
            page: page
        },
        success: function (data) {
            $('.comments_place').append(data);
            $(".timeago").timeago();
        },
    });
}
$(window).on('scroll', function() {
    if($(window).scrollTop() + $(window).height() >= ($(document).height() - 600)) {
        page++;
        get_comments(page);
    }
});
var user = $.ajax({
    type: "post",
    url: "/get_auth",
    dataType: "json",
    data: { _token: $('meta[name="csrf-token"]').attr("content") },
    async: false,
    success: function (data) {
        user = data;
    }
}).responseJSON;
$('body').on('submit', '.comment', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var id = $('.teacher').attr('teacher');
    var msg = $('.comment_text').html();
    var fileInput = $('.image')[0];
    var file = '';
    if (fileInput.files.length > 0) {
        var file = URL.createObjectURL(fileInput.files[0]);
        file = `<img class="img-comment img-thumbnail" src="${file}" alt="Uploaded Image">`;
    }
    var html = `
        <div class="box">
            <div class="media bb-1 border-fade">
                <img class="avatar avatar-lg" src="../images/avatar/3.jpg" alt="...">
                <div class="media-body">
                    <p>
                        <strong>${user.full_name}</strong>
                        <svg class="clock float-end" viewBox="0 0 100 100">
                            <circle class="clock-face" cx="50" cy="50" r="45"></circle>
                            <line class="hour-hand" x1="50" y1="50" x2="50" y2="30"></line> <!-- طول العقرب أقصر -->                            
                            <line class="minute-hand" x1="50" y1="50" x2="50" y2="22"></line> <!-- طول العقرب أقصر -->
                        </svg>
                    </p>
                </div>
            </div>
            <div class="box-body bb-1 border-fade">
                <p class="lead">
                    ${file}
                    ${msg}
                </p>
            </div>
        </div>
    `;
    formData.append('id', id);
    formData.append('msg', msg);
    $.ajax({
        url: '/comments/store',
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        beforeSend: function () {
            $('.comment_text').html('');
            $('.image-preview_comment').html('');
            $('.image-preview_comment').removeClass('show');
            $('.comment')[0].reset();
            if (msg || file || msg && file) {
                $('.comments_place').prepend(html);
            }
        },
        success: function () {
            $(".comments_place").load(location.href + " .comments_place", function () {
                // بعد تحميل المحتوى الجديد، قم بتطبيق timeago
                $(".timeago").timeago();
            });
        },
        error: function (data) {
            var errors = data.responseJSON.errors;
            $.each(errors, function (key, error) {
                toastr.error(error);
            });
            $(".comments_place").load(location.href + " .comments_place", function () {
                // بعد تحميل المحتوى الجديد، قم بتطبيق timeago
                $(".timeago").timeago();
            });
        }
    });
});

function get_replaies_comment(id, page = 1, myreturn) {
    $.ajax({
        type: "get",
        url: "/comments/get_replaies_comment/" + id,
        data: { _token: $('meta[name="csrf-token"]').attr("content"), page: page },
        success: function (data) {
            $(myreturn + '_' + id).append(data);
            $(".timeago").timeago();
        }
    });
}
function refresh_div(id) {
    $.ajax({
        url: location.href,  // حمل الصفحة نفسها
        success: function (response) {
            // استخراج المحتوى المحدد من الصفحة
            var newContent = $(response).find(".replaies_" + id).html();

            // تحديث المنطقة المطلوبة بدون إنشاء div مكرر
            $(".replaies_" + id).html(newContent);

            // إضافة الصفوف المطلوبة فقط إذا كانت غير موجودة
            $('.replay-media-' + id).addClass('show');

            // تفعيل timeago بعد التحديث
            $(".timeago").timeago();
        }
    });
}
function refresh_comment(id) {
    $.ajax({
        url: location.href,
        success: function (response) {
            var newContent = $(response).find(".box_" + id).html();
            $(".box_" + id).html(newContent);
            $(".timeago").timeago();
        }
    });
}
var sub_page = 1;

$('body').on('submit', '.replay_comment', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var id = $(this).attr('data-id');
    if ($(".replay_comment_" + id).parent().hasClass("replay-media-" + id) == true) {
        var $closestMedia = $(this).closest('.media');
        var msg = $closestMedia.find('.mytext_' + id).html();
        var fileInput = $closestMedia.find('.image_' + id)[0];
    }
    else {
        var $currentBox = $(this).closest('.get_replay_comment');
        var msg = $currentBox.find('.mytext_' + id).html();
        var fileInput = $currentBox.find('.image_' + id)[0];
    }
    var file = '';
    if (fileInput.files.length > 0) {
        var file = URL.createObjectURL(fileInput.files[0]);
        file = `<img class="img-comment img-thumbnail" src="${file}" alt="Uploaded Image">`;
    }
    var html = `
                    <div class="media">
                        <a class="avatar" href="#">
                            <img class="avatar avatar-lg" src="" alt="...">
                        </a>
                    <div class="media-body">
                    <p>
                        <strong>${user.full_name}</strong>
                        <svg class="clock float-end" viewBox="0 0 100 100">
                            <circle class="clock-face" cx="50" cy="50" r="45"></circle>
                            <line class="hour-hand" x1="50" y1="50" x2="50" y2="30"></line> <!-- طول العقرب أقصر -->                            
                            <line class="minute-hand" x1="50" y1="50" x2="50" y2="22"></line> <!-- طول العقرب أقصر -->
                        </svg>
                    </p>
                <p>${file} ${msg}</p>
                <div class="replay-media"></div>
            </div>
    `;
    var $this = $(this);
    formData.append('id', id);
    formData.append('msg', msg);
    $.ajax({
        url: '/comments/replay_comment',
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        beforeSend: function () {
            $('.mytext_' + id).html('');
            $('.image-preview_' + id).html('');
            $('.image-preview_' + id).removeClass('show');
            $this[0].reset();
            if ($(".replay_comment_" + id).parent().hasClass("replay-media-" + id) == true) {
                if (msg || file || msg && file) {
                    $(".replaies_" + id).prepend(html);
                }
            }
            else {
                if (msg || file || msg && file) {
                    $(".get_replaies_comment").prepend(html);
                }
            }
        },
        success: function () {
            if ($(".replay_comment_" + id).parent().hasClass("replay-media-" + id) == true) {
                refresh_div(id);
            }
            else {
                $(".get_replaies_comment_" + id).load(location.href + " .get_replaies_comment_" + id, function () {
                    $('.media-list-' + id).addClass('show');
                    $(".timeago").timeago();
                });
            }
        },
        error: function (data) {
            var errors = data.responseJSON.errors;
            $.each(errors, function (key, error) {
                toastr.error(error);
            });
            if ($(".replay_comment_" + id).parent().hasClass("replay-media-" + id) == true) {
                refresh_div(id);
            }
            else {
                $(".get_replaies_comment_" + id).load(location.href + " .get_replaies_comment_" + id, function () {
                    $('.media-list-' + id).addClass('show');
                    $(".timeago").timeago();
                });
            }
        }
    });
});

function count_replaies_comment(id) {
    var count = $.ajax({
        type: "get",
        url: "/comments/count_replaies_comment/" + id,
        data: { _token: $('meta[name="csrf-token"]').attr("content") },
        async: false,
        success: function (data) {
            count = data;
        }
    }).responseJSON;
    return count;
}

$('body').on('click', '.show_more', function (e) {
    var page = $(this).attr('data-page');
    var id = $(this).attr('data-id');
    page = parseInt(page) + 1;
    $(this).attr('data-page', page);
    if ($(this).parent().hasClass("replay-media-" + id) == true) {
        get_replaies_comment(id, page, ".replaies");
    }
    else {
        get_replaies_comment(id, page, ".get_replaies_comment");
    }
});

$('body').on('click', '.edit', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    $('.edit_comment').not('.edit_comment_' + id).html('');
    $('.replay_comment_'+id).addClass('d-none');
    $('.media-list-'+id).addClass('show');
    $('.replay_comment').not('.replay_comment_'+id).removeClass('d-none');
    $('.replay-media-'+id).addClass('show');
    $.ajax({
        type: "GET",
        url: "/comments/edit/" + id,
        success: function (data) {
            $('.edit_comment_' + id).html(data);
        }
    });
});

$('body').on('submit','.update_comment',function(e){
    e.preventDefault();
    var formData = new FormData(this);
    var id = $(this).attr('data-id');
    var msg = $(this).find('.mytext_' + id).html();
    var $this = $(this);
    var oldImg = null;
    if($('.image-preview_'+id).find('.myimg').length > 0){
        oldImg = $('.image-preview_'+id).find('.myimg').attr('alt');
    }
    formData.append('id', id);
    formData.append('msg', msg);
    formData.append('oldImg', oldImg);
    $.ajax({
        type: "post",
        url: "/comments/update",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            if ($this.parent().parent().hasClass("replay-media-"+ id) == true) {
                refresh_div(id);
            }
            else {
                refresh_comment(id);
            }
        },
        error: function (data) {
            var errors = data.responseJSON.errors;
            $.each(errors, function (key, error) {
                toastr.error(error);
            });
            if ($(".replay_comment_" + id).parent().hasClass("replay-media-" + id) == true)
            {
                refresh_div(id);
            }
            else 
            {
                refresh_comment(id);
            }
        }
    });
});


$('body').on('click', '.delete', function (e) {
    var id = $(this).attr('data-id');
    $.ajax({
        type: "GET",
        url: "/comments/delete/" + id,
        success: function (data) {
            $('.success').html(data);
            $('.delete_comment_'+id).modal('show');
        }    
    });
});

$('body').on('submit','.remove_comment',function(e){
    e.preventDefault();
    var id = $(this).attr('data-id');
    $.ajax({
        type: "post",
        url: "/comments/remove",
        data: { _token: $('meta[name="csrf-token"]').attr("content"), id: id},
        success: function (data) {
            $('.delete_comment_'+id).modal('hide');
            $('.media_'+id).remove();
            $('.box_'+id).remove();
        },
    });
});