$(document).ready(function () {
    $("#search_btn").click(function (e) {
        e.preventDefault();
        $table.bootstrapTable("refreshOptions", {
            exportDataType: $(this).val(),
        });
    });
    $("#export_button").click(function (e) {
        e.preventDefault();
        var form = $("<form>", {
            method: "POST",
            action: export_url,
        });

        form.append(
            $("<input>", {
                type: "hidden",
                name: "_token",
                value: $('meta[name="csrf-token"]').attr("content"),
            })
        );
        let data = {
            medium_id: $("#coupon_filter_medium_id").val(),
            price: $("#coupon_filter_price").val(),
            class_id: $("#coupon_filter_class_id").val(),
            teacher_id: $("#coupon_filter_teacher_id").val(),
            subject_id: $("#coupon_filter_subject_id").val(),
            lesson_id: $("#coupon_filter_lesson_id").val(),
            tags: $("#coupon_filter_tags").val(),
            status: $("#coupon_filter_status").val(),
            purchased: $("#coupon_filter_used").prop("checked") ? "true" : null
        }
        for (let item in data) {
            form.append(
                $("<input>", {
                    type: "hidden",
                    name: item,
                    value: data[item],
                })
            );
            
        }

        $("body").append(form);
        form.submit();
        form.remove();
    });

    function changeStatus(href, status) {
        let url = href;
        let data = JSON.stringify({
            _token: $("meta[name=csrf-token]").attr("content"),
            _method: "PUT",
            status: status,
        });

        function successCallback(response) {
            $("#table_list").bootstrapTable("refresh");
            showSuccessToast(response.message);
        }

        function errorCallback(response) {
            showErrorToast(response.message);
        }

        ajaxRequest("put", url, data, null, successCallback, errorCallback);
    }

    function setTeachers(classSubjectId) {
        const $teacherSelect = $("#coupon_filter_teacher_id");
        $teacherSelect.empty();
        $teacherSelect.removeAttr("readonly");
        const teachersBySubject = initialData.teachers.filter((teacher) =>
            teacher.subjects.some((subject) => subject.id == classSubjectId)
        );

        $teacherSelect.append(
            `<option value="">All Teacher</option>`
        );

        teachersBySubject.forEach((teacher) => {
            $teacherSelect.append(
                `<option value="${teacher.id}">${teacher.user.first_name} ${teacher.user.last_name}</option>`
            );
        });

        $teacherSelect.trigger("change");
    }

    function setSubjects(classId) {
        const $subjectSelect = $("#coupon_filter_subject_id");
        $subjectSelect.empty();
        $subjectSelect.removeAttr("readonly");

        let filteredClassSubject = initialData.classSubjects.filter(
            (classSubject) => classSubject.class_id == Number(classId)
        );

        if (filteredClassSubject.length > 0) {
            $subjectSelect.append(
                `<option value="">All Subjects</option>`
            );
            filteredClassSubject.forEach((subject) => {
                $subjectSelect.append(
                    `<option data-class_subject-id="${subject.id}" value="${subject.subject_id}">${subject.subject.name}</option>`
                );
            });
            if ($subjectSelect.val()) {
                setTeachers(
                    $("#coupon_filter_subject_id option:selected").data(
                        "class_subject-id"
                    )
                );
            }
        }

        $subjectSelect.trigger("change");
    }

    function setLessons(teacherID, classId, subjectId) {
        const $lessonSelect = $("#coupon_filter_lesson_id");
        $lessonSelect.removeAttr("readonly");
        $lessonSelect.empty();

        const teacherLessons = initialData.lessons[Number(teacherID)] || [];

        $lessonSelect.append(
            `<option value="">All Lesson</option>`
        );

        teacherLessons.forEach((lesson) => {
            if (
                lesson.class_id == Number(classId) &&
                lesson.subject_id == Number(subjectId)
            ) {
                $lessonSelect.append(
                    `<option value="${lesson.id}">${lesson.name}</option>`
                );
            }
        });
    }

    // Function to set classes based on medium ID
    function setClasses(mediumId) {
        const $classSelect = $("#coupon_filter_class_id");
        $classSelect.empty();

        const classSections = initialData.classes[Number(mediumId)] || [];
        console.log(initialData.classes[Number(mediumId)],initialData);
        
        if (classSections.length > 0) {
            classSections.forEach((classSection) => {
                $classSelect.append(
                    `<option value="${classSection.id}">${classSection.name}</option>`
                );
            });
        }

        $classSelect.trigger("change");
    }

    $("#coupon_filter_by_medium").change(function () {
        setClasses($(this).val());
    });

    $("#coupon_filter_class_id").change(function () {
        setSubjects($(this).val());
    });

    $("#coupon_filter_subject_id").change(function () {
        setTeachers(
            $("#coupon_filter_subject_id option:selected").data(
                "class_subject-id"
            )
        );
    });

    $("#coupon_filter_teacher_id").change(function () {
        setLessons(
            $(this).val(),
            $("#coupon_filter_class_id").val(),
            $("#coupon_subject_id").val()
        );
    });

    new Tagify(document.querySelector("#coupon_filter_tags"), {
        whiteList: initialData.tags,
        originalInputValueFormat: (valuesArr) =>
            valuesArr.map((item) => item.value).join(","),
    });
});
