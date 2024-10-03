function toEnglishNumber(strNum) {
    var ar = '٠١٢٣٤٥٦٧٨٩'.split('');
    var en = '0123456789'.split('');
    strNum = strNum.replace(/[٠١٢٣٤٥٦٧٨٩]/g, x => en[ar.indexOf(x)]);
    strNum = strNum.replace(/[^\d]/g, '');
    return strNum;
 }
 
 $(document).on('change', '.ar_num_convert', function(e) {
    var val = toEnglishNumber($(this).val())
    $(this).val(val)
 });