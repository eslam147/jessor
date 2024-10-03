/**
 * ===================================
 *    Product Description Editor 
 * ===================================
*/

tinymce.init({
    selector: '#product-description',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
    toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
});

/**
 * ====================
 *      File Pond 
 * ====================
*/

// We want to preview images, so we register
// the Image Preview plugin, We also register 
// exif orientation (to correct mobile image
// orientation) and size validation, to prevent
// large files from being added
// FilePond.registerPlugin(
//     FilePondPluginImagePreview,
//     FilePondPluginImageExifOrientation,
//     FilePondPluginFileValidateSize,
//     // FilePondPluginImageEdit
// );

// Select the file input and use 
// create() to turn it into a pond
// let productImagesPond = FilePond.create(document.querySelector('#product_images'),{
//     acceptedFileTypes: ['image/*'],
//     instantUpload: false,
//     allowProcess: false,
//     sequentialUploads: true

// });
// $('#product_images').dropify()
// productImagesPond.processFiles()
function getRndInteger(min, max) {
  return Math.floor(Math.random() * (max - min)) + min;
}
$('#generate_sku').click(function(e) {
  e.preventDefault();
  $('#product_sku').val(getRndInteger(12000, 35000));
});
$('#regular-price,[name=profit],[name=minimum_commission]').change(function() {
  $('[name=selling_price]').val(
      Number($("#regular-price").val()) +
      Number($("[name=profit]").val()) +
      Number($("[name=minimum_commission]").val())
  );
})
$('#mainimage').dropify();
$('.edit_btn').click(function(e) {
    e.preventDefault();
    let btn = $(this);
    let text = $(this).attr('data-content');
    Swal.fire({
        title: 'انت متأكد ؟',
        text: ` انت متأكد انك عايز تغير ${text}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'لا',
        confirmButtonText: 'اه'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.hide()
            btn.parents('.product_can_edit').find('input').removeAttr('readonly');
        }
    })
});
