$(document).ready(function () {
    // عند اختيار صورة
    $('body').on('change','.image', function (e) {
        var id = $(this).data('id');
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var imageUrl = e.target.result;
                var imageHTML = `
                    <div class="image-container">
                        <img class="myimg" src="${imageUrl}" alt="Selected Image" />
                        <span data-id="${id}" class="remove-image" data-id="${id}">X</span>
                    </div>
                `;
                // وضع الصورة في منطقة الكتابة (mytext) مع زر الحذف
                $('.image-preview_' + id).addClass('show');
                $('.image-preview_' + id).append(imageHTML);
                
            };
            reader.readAsDataURL(file);
        }
    });

    // عند النقر على زر X لإزالة الصورة
    $('body').on('click', '.remove-image', function () {
        var id = $(this).attr('data-id');
        $('.image-preview_' + id).removeClass('show');
        $(this).parent('.image-container').remove(); // إزالة الصورة من منطقة الكتابة
        $('.image[data-id="' + id + '"]').val(null);
    });
    // تهيئة المحرر عند الضغط على الزر
    $('body').on('click', '.emoji-trigger', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        // التحقق إذا لم يكن المحرر قد تم تهيئته مسبقاً
        initializeEmojiEditor(id);    
        var editor = $('.emoji-wysiwyg-editor_'+id).data('emojioneArea');
        setTimeout(function () {
            editor.showPicker();  // إذا كان مخفيًا، قم بعرضه
        }, 1000); // تأخير 1000 مللي ثانية
    
        $('.emoji-picker').not('.emoji-picker_' + id).hide();
        $('.emoji-picker_' + id).toggle();
        $('.mytext_' + id).focus();
    });
    $('body').on('click', '.emoji-trigger-edit', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        initializeEmojiEditor(id,true);    
        var editor = $('.edit-emoji-wysiwyg-editor_'+id).data('emojioneArea');
        setTimeout(function () {
            editor.showPicker();
        }, 1000);
        $('.emoji-picker_edit_'+id).toggle();
        $('.mytext_' + id).focus();

    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.emoji-picker, .emoji-trigger').length && !$(e.target).closest('.emoji-picker, .emoji-trigger-edit').length) {
            $('.emoji-picker').hide();
        }
    });
});

function initializeEmojiEditor(id, edit = false) {
    // التأكد من عدم تهيئة محرر الإيموجي أكثر من مرة
    if(edit == false) {        
        if (!$('.emoji-wysiwyg-editor_' + id).data('emojioneArea')) {
            $('.emoji-wysiwyg-editor_' + id).emojioneArea({
                autoHideFilters: true,
                pickerPosition: "bottom",
                filtersPosition: "bottom",
                hidePickerOnBlur: false,
                standalone: true,
                events: {
                    show: function () {
                        var editor = $('.emoji-wysiwyg-editor_' + id).data('emojioneArea');
                        var picker = editor.getPicker().clone().show();
                        $('.emoji-picker_' + id).html(picker).show();
                    },
                    emojibtn_click: function (button, event) {
                        var emojiUnicode = button.data("name");
                        var emojiHTML = emojione.toImage(emojiUnicode);
                        var myText = $('.mytext_' + id).get(0);
                        var selection = window.getSelection();
                        var range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
                        if (range) {
                            range.deleteContents();
                            var fragment = document.createRange().createContextualFragment(emojiHTML + "&nbsp;");
                            var lastNode = fragment.lastChild || fragment.firstChild;
                            range.insertNode(fragment);
                            if (lastNode) {
                                range.setStartAfter(lastNode);
                                range.setEndAfter(lastNode);
                                selection.removeAllRanges();
                                selection.addRange(range);
                            }
                        } else {
                            $(myText).append(emojiHTML + "&nbsp;");
                            var range = document.createRange();
                            range.selectNodeContents(myText);
                            range.collapse(false);
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }
                        myText.focus();
                    }
                }
            });
        }
    }
    else
    {
        if (!$('.edit-emoji-wysiwyg-editor_' + id).data('emojioneArea')) {
            $('.edit-emoji-wysiwyg-editor_' + id).emojioneArea({
                autoHideFilters: true,
                pickerPosition: "bottom",
                filtersPosition: "bottom",
                hidePickerOnBlur: false,
                standalone: true,
                events: {
                    show: function () {
                        var editor = $('.edit-emoji-wysiwyg-editor_' + id).data('emojioneArea');
                        var picker = editor.getPicker().clone().show();
                        $('.emoji-picker_edit_'+id).html(picker).show();
                    },
                    emojibtn_click: function (button, event) {
                        var emojiUnicode = button.data("name");
                        var emojiHTML = emojione.toImage(emojiUnicode);
                        var myText = $('.mytext_' + id).get(0);
                        var selection = window.getSelection();
                        var range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
                        if (range) {
                            range.deleteContents();
                            var fragment = document.createRange().createContextualFragment(emojiHTML + "&nbsp;");
                            var lastNode = fragment.lastChild || fragment.firstChild;
                            range.insertNode(fragment);
                            if (lastNode) {
                                range.setStartAfter(lastNode);
                                range.setEndAfter(lastNode);
                                selection.removeAllRanges();
                                selection.addRange(range);
                            }
                        } else {
                            $(myText).append(emojiHTML + "&nbsp;");
                            var range = document.createRange();
                            range.selectNodeContents(myText);
                            range.collapse(false);
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }
                        myText.focus();
                    }
                }
            });
        }
    }
}
