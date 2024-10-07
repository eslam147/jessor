$.validator.addMethod("validEgyptianPhoneNumber", function(value, element) {
    return this.optional(element) || /^01\d{9}$/.test(value);
}, "يرجى إدخال رقم محمول مصري صحيح");
