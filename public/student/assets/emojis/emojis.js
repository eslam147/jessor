$(document).ready(function () {
    // عند اختيار صورة
    $('.image').on('change', function (e) {
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
                $('.image-preview_'+id).addClass('show');
                $('.image-preview_' + id).append(imageHTML);
            };
            reader.readAsDataURL(file);
        }
    });
    // عند النقر على زر X لإزالة الصورة
    $('body').on('click', '.remove-image', function () {
        var id = $(this).attr('data-id');
        $('.image-preview_'+id).removeClass('show');
        $(this).parent('.image-container').remove(); // إزالة الصورة من منطقة الكتابة
        $('.image[data-id="' + id + '"]').val(null);
    });
    $('.emoji-wysiwyg-editor').each(function() {
        var editorElement = $(this);
        var id = editorElement.attr('data-id');
        editorElement.emojioneArea({
            autoHideFilters: true,
            pickerPosition: "bottom",
            filtersPosition: "bottom",
            hidePickerOnBlur: false,
            standalone: true,
            events: {
                show: function () {
                    const editor = editorElement.data('emojioneArea');
                    const picker = editor.getPicker().clone().show();
                    $('.emoji-picker_' + id).html(picker).show();
                },
                emojibtn_click: function (button, event) {
                    const emojiUnicode = button.data("name");
                    const emojiHTML = emojione.toImage(emojiUnicode);
                    const myText = $('.mytext_' + id).get(0); // استخدام معرّف فريد لكل .mytext
                    var selection = window.getSelection();
                    var range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
                    if (range) {
                        range.deleteContents();
                        const fragment = document.createRange().createContextualFragment(emojiHTML + "&nbsp;");
                        const lastNode = fragment.lastChild || fragment.firstChild;
                        range.insertNode(fragment);
                        if (lastNode) {
                            range.setStartAfter(lastNode);
                            range.setEndAfter(lastNode);
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }
                    } else {
                        $(myText).append(emojiHTML + "&nbsp;");
                        const range = document.createRange();
                        range.selectNodeContents(myText);
                        range.collapse(false);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }
                    myText.focus();
                }
            }
        });
    });

    $('.mytext').on('click keyup', function (e) {
        var selection = window.getSelection();
        if (selection.rangeCount > 0) {
            currentRange = selection.getRangeAt(0);
        }
    });

    $('body').on('click', '.emoji-trigger', function (e) {
        e.preventDefault();
        $('.emoji-picker').hide();
        var id = $(this).attr('data-id');
        const editor = $('.emoji-wysiwyg-editor[data-id="' + id + '"]').data('emojioneArea');
        editor.showPicker();
        $('.mytext_' + id).focus();
        $('.emoji-picker_' + id).toggle();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.emoji-picker, .emoji-trigger').length) {
            $('.emoji-picker').hide();
        }
    });

    $('.emoji-picker').on('click', function (e) {
        e.stopPropagation();
    });
});