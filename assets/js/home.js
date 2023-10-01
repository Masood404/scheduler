var $ = jQuery;

$(document).ready(function () {

    const currentDate = $("#calendar-month"),
        daysTag = $(".calendar-days"),
        prevNextIcon = $(".calendar-month-changer"),
        daysTags = $(".calendar-days li"),
        todayDate = new Date();

    let date = new Date(),
        currYear = date.getFullYear(),
        currMonth = date.getMonth(),
        selectedDate = date;

    let isNextSelectionAcitve = false;
    let isNextActive = false;
    let nextSelectedDate;

    const roundedHourMinute = (dateToRound) => {
        let hours = dateToRound.getHours();
        let minutes = dateToRound.getMinutes();

        let roundedMinute = (Math.round(minutes / 15) * 15) % 60;
        let roundedHour = minutes > 52 ? (hours === 23 ? 0 : ++hours) : hours;

        return {
            roundedMinute : roundedMinute,
            roundedHour : roundedHour
        };
    };

    const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    ];

    const renderCalender = () => {
        let firstDayOfMonth = new Date(currYear, currMonth, 1).getDay();
        let lastDateOfMonth = new Date(currYear, currMonth + 1, 0).getDate(); // getting last date of month. Passing 0 for the date argument will return the last date of the month.
        let lastDateOfPrevMonth = new Date(currYear, currMonth, 0).getDate();
        let liTag = "";

        let isMonthActive = true;

        if (currYear < todayDate.getFullYear()) {
            isMonthActive = false;
        }
        else if (currMonth < todayDate.getMonth() && currYear == todayDate.getFullYear()) {
            isMonthActive = false;
        }
        else {
            isMonthActive = true;
        }

        for (let i = firstDayOfMonth; i > 0; i--) {
            liTag += `<li class="inactive-days">${lastDateOfPrevMonth - i + 1}</li>`;
        }

        for (let i = 1; i <= lastDateOfMonth; i++) {
            let isDayActive = currMonth == todayDate.getMonth() && currYear == todayDate.getFullYear() ? i >= todayDate.getDate() : true //Sorry :(;

            if (i == selectedDate.getDate() && isDayActive && isMonthActive) {
                liTag += `<li id="calendar-selected-day">${i}</li>`;
            }
            else if (!isMonthActive || !isDayActive) {
                liTag += `<li class="inactive-days">${i}</li>`;
            }
            else {
                liTag += `<li>${i}</li>`;
            }
        }

        currentDate.html(`${months[currMonth]} ${currYear}`);
        daysTag.html(liTag);
        selectedDate.setMonth(currMonth);
    }
    renderCalender();

    const renderHours = (isTaskBoxEnabled = false) => {
        const generateHourHtml = (hour, isPm = false) => {
            const selectedTimeIdAttr = (min) => {
                let selectedTimeIdAttr = '';

                if (hour == roundedHourMinute(selectedDate).roundedHour && min == roundedHourMinute(selectedDate).roundedMinute) {
                    selectedTimeIdAttr += 'id="selectedTime"';
                }
                else {
                    selectedTimeIdAttr = '';
                }
                if (isNextActive && hour == roundedHourMinute(nextSelectedDate).roundedHour && min == roundedHourMinute(nextSelectedDate).roundedMinute) {
                    selectedTimeIdAttr += ' id="nextSelectedTime"';
                }
                return selectedTimeIdAttr;
            };

            return /*html*/ `
            <div class="hour">
                <span>${hour > 12 ? hour - 12 : hour} ${isPm ? "PM" : "AM"}</span>
                <div>
                    <div ${selectedTimeIdAttr(0)} class="hour-wrapper hour-quarters-wrapper">
                        <hr data-minute="0" data-hour="${hour}">
                    </div>
                    <div ${selectedTimeIdAttr(15)} class="hour-quarters-wrapper">
                        <hr class="hour-quarters" data-minute="15" data-hour="${hour}">
                    </div>
                    <div ${selectedTimeIdAttr(30)} class="hour-quarters-wrapper">
                        <hr class="hour-quarters" data-minute="30" data-hour="${hour}">
                    </div>
                    <div ${selectedTimeIdAttr(45)} class="hour-quarters-wrapper">
                        <hr class="hour-quarters" data-minute="45" data-hour="${hour}">
                    </div>
                </div>
            </div>
            `;

            //circle icon: <i class="fi fi-ss-circle">
        }

        let hours = "";

        for (let i = 0; i < 24; i++) {
            if (i < 12) {
                hours += generateHourHtml(i, false);
            }
            else {
                hours += generateHourHtml(i, true);
            }
        }

        //Render
        $(".hours").html(hours);
        $("#selectedTime").prepend('<i class="fi fi-ss-circle">');

        if (isTaskBoxEnabled) {
            const topPosition = $("#selectedTime").position().top + parseFloat($(".hour-quarters-wrapper hr").css("border-top-width").replace("px",''));
            const leftPosition = $(".hour div").position().left;
            const bottomPosition = $("#nextSelectedTime").position().top;
            const hoursScrollTop = $(".hours").scrollTop()

            const selectedDateHour = selectedDate.getHours();
            const selectedDateMinute = selectedDate.getMinutes();
            const nextSelectedDateHour = nextSelectedDate.getHours();
            const nextSelectedDateMinute = nextSelectedDate.getMinutes();

            let taskBox = /*html*/
            `<div id="hours-task-box">
                (Untitled)
                ${
                    (selectedDateHour > 12 ? selectedDateHour - 12 : selectedDateHour) + ":" +
                    (selectedDateMinute.toString().length < 2 ? "0" + selectedDateMinute : selectedDateMinute) + " " +
                    (selectedDateHour > 12 ? "PM" : "AM")
                } - 
                ${
                    (nextSelectedDateHour > 12 ? nextSelectedDateHour - 12 : nextSelectedDateHour) + ":" +
                    (nextSelectedDateMinute.toString().length < 2 ? "0" + nextSelectedDateMinute : nextSelectedDateMinute) + " " +
                    (nextSelectedDateHour > 12 ? "PM" : "AM")
                }
                </div>`;
            let fontSize = Math.round((bottomPosition - topPosition) * 0.8)

            if(fontSize > 20){
                fontSize = 20;
            }

            $(".hours").append(taskBox);
            $("#hours-task-box").css({
                "top": topPosition + hoursScrollTop,
                "left": leftPosition,
                "height": bottomPosition - topPosition,
                "font-size": fontSize
            });
        }

    }
    renderHours();

    $(".calendar-month-changer").on("click", function () {
        currMonth = $(this).attr("id") === "month-prev" ? currMonth - 1 : currMonth + 1;


        if (currMonth < 0 || currMonth > 11) {
            date = new Date(currYear, currMonth);
            currYear = date.getFullYear();
            currMonth = date.getMonth();
        }
        renderCalender();
    });

    $(".calendar-days").on("click", "li", function () {
        if ($(this).attr("class") != "inactive-days") {
            selectedDate = new Date(currYear, currMonth, $(this).html());
            renderCalender();
        }
    });

    $(".hours").on("click", ".hour div hr", function () {
        const limitDate = new Date();

        let hourTime = parseInt($(this).attr("data-hour"));
        let minuteTime = parseInt($(this).attr("data-minute"));
        let thisDate = () => {
            let thisDate = new Date(currYear, currMonth, selectedDate.getDate(), 0, 0);

            thisDate.setHours(hourTime);
            thisDate.setMinutes(minuteTime);
            thisDate.setSeconds(0);

            return thisDate;
        }
        if (isNextSelectionAcitve) {
            isNextSelectionAcitve = false;
            isNextActive = true;
            nextSelectedDate = new Date();
            if (thisDate().getTime() <= limitDate.getTime() || thisDate().getTime() <= selectedDate.getTime()) {
                nextSelectedDate.setHours(selectedDate.getHours() + 1);
                nextSelectedDate.setMinutes(0);
                nextSelectedDate.setSeconds(0);
            }
            else {
                nextSelectedDate = thisDate();
            }
        }
        else {
            isNextActive = false;
            if (thisDate().getTime() <= limitDate.getTime()) {
                selectedDate = limitDate;
            }
            else {
                selectedDate = thisDate();
            }
        }
        renderHours(isNextActive);
    })

    $("#create-task").on("click", function () {
        isNextSelectionAcitve = true;
        $(".hours .hour div hr").css({
            "cursor": "pointer"
        });
        for(let i = 0, quarters = $("[data-hour]"); i < quarters.length; i++){
            const hourTime = $(quarters[i]).attr("data-hour");
            const minuteTime = $(quarters[i]).attr("data-minute");
            const thisDate = new Date(currYear, currMonth, selectedDate.getDate(), hourTime, minuteTime);

            if(thisDate.getTime() < selectedDate.getTime()){
                $(quarters[i - 1]).css({
                    "opacity":0.4
                });
            }
        }
    });

});