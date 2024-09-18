$(document).on("change", ".answer_qs", function () {
    let dateParent = $(this).closest("li").data("date");
    $(`.events a[data-date="${dateParent}"]`).addClass("solved");
});
$(".send_exam").click(function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $("#quizForm").submit();
});
$(".switch_btn").on("timeline-changed", function () {
    $(".send_exam,.switch_btn.prev,.switch_btn.next").addClass("d-none");
    if (window.timeline.currentIndex == window.timeline.lastIndex) {
        $(".send_exam,.switch_btn.prev").removeClass("d-none");
    } else if (
        window.timeline.currentIndex > 0 &&
        window.timeline.currentIndex < window.timeline.lastIndex
    ) {
        $(".switch_btn.next,.switch_btn.prev").removeClass("d-none");
    } else if (window.timeline.currentIndex == 0) {
        $(".switch_btn.next").removeClass("d-none");
    }
});
const examEndTimeDate = new Date(examEndTime);

const timerDuration = examEndTime;
const timerElement = $("#timer");
let timeRemaining = timerDuration * 60;
$("#quizForm").submit(function (e) {
    e.preventDefault();
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Submit it!",
    }).then((result) => {
        if (result.isConfirmed) {
            window.removeEventListener("beforeunload", examWillLeave);
            document.getElementById("quizForm").submit();
        }
    });
});
let showFiveMinToast = false;
let showThreeMinToast = false;

let countdown = setInterval(updateTimeLeft, 1000);

function updateTimeLeft() {
    var now = new Date();
    var timeLeft = examEndTimeDate - now;
    var hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
    timerElement.text(
        `${hours}:${minutes.toString().padStart(2, "0")}:${seconds
            .toString()
            .padStart(2, "0")}`
    );
    // timeRemaining--;

    if (timeLeft < 0) {
        clearInterval(countdown);
        $().text("Time's up");
        window.removeEventListener("beforeunload", examWillLeave);
        document.getElementById("quizForm").submit();
    } else {
        if (!showFiveMinToast && timeLeft === 5 * 60) {
            Swal.fire({
                position: "top-end",
                icon: "info",
                title: "5 minutes remaining!",
                showConfirmButton: false,
                timer: 1500,
                toast: true,
            });
            showFiveMinToast = true;
        }

        // Check for 3 minutes remaining
        if (!showThreeMinToast && timeLeft === 3 * 60) {
            Swal.fire({
                position: "top-end",
                icon: "warning",
                title: "3 minutes remaining!",
                showConfirmButton: false,
                timer: 1500,
                toast: true,
            });
            showThreeMinToast = true;
        }
    }
    // if (timeLeft > 0) {
    //     var minutesLeft = Math.floor(timeLeft / 1000 / 60);
    //     var secondsLeft = Math.floor((timeLeft / 1000) % 60);

    //     document.getElementById('time-left').textContent = minutesLeft + " minutes " + secondsLeft + " seconds";
    // } else {
    //     document.getElementById('time-left').textContent = "Time's up!";
    // }
}
