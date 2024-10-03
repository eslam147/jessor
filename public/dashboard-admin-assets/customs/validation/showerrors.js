function showErrors(err) {
    if (!err || !err.responseJSON || !err.responseJSON.msg) {
        new Notify({
            status: "error",
            title: "Error",
            text: "حدث خطأ غير معروف.",
            effect: "slide",
            speed: 300,
            customClass: "",
            customIcon: "",
            showIcon: true,
            showCloseButton: true,
            autoclose: true,
            autotimeout: 4000,
            gap: 20,
            distance: 20,
            type: 3,
            position: "x-center top",
        });
        return;
    }

    if (Array.isArray(err.responseJSON.msg)) {
        // The 'msg' attribute is an array, so it contains multiple error messages
        let errorMessage = err.responseJSON.msg.join('<br>');
        new Notify({
            status: "error",
            title: "Error",
            text: errorMessage,
            effect: "slide",
            speed: 300,
            customClass: "",
            customIcon: "",
            showIcon: true,
            showCloseButton: true,
            autoclose: true,
            autotimeout: 4000,
            gap: 20,
            distance: 20,
            type: 3,
            position: "x-center top",
        });
    } else if (typeof err.responseJSON.msg === 'string') {
        // The 'msg' attribute is a string, so it contains a single error message
        new Notify({
            status: "error",
            title: "خطأ",
            text: err.responseJSON.msg,
            effect: "slide",
            speed: 300,
            customClass: "",
            customIcon: "",
            showIcon: true,
            showCloseButton: true,
            autoclose: true,
            autotimeout: 4000,
            gap: 20,
            distance: 20,
            type: 3,
            position: "x-center top",
        });
    } else {
        // In case the 'msg' attribute is neither an array nor a string
        new Notify({
            status: "error",
            title: "خطأ",
            text:  "حدث خطأ غير معروف.",
            effect: "slide",
            speed: 300,
            customClass: "",
            customIcon: "",
            showIcon: true,
            showCloseButton: true,
            autoclose: true,
            autotimeout: 4000,
            gap: 20,
            distance: 20,
            type: 3,
            position: "x-center top",
        });
    }
}
