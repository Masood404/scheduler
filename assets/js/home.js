//#region Utillities
/**
 * Manipulates a date object by performing various operations such as addition, subtraction, multiplication, or division.
 *
 * @param {Date} baseDate - The base date object to manipulate.
 * @param {number} value - The numeric value by which to modify the base date. The actual effect depends on the chosen time unit and operation.
 * @param {string} unit - The time unit for the operation. Accepts values like "minute", "hour" and "day". (optional) 
 * @param {string} op - The type of operation to perform. Accepts values like "add," "subtract," "multiply," or "divide." (optional)
 *
 * @returns {Date} A new date object representing the result of applying the specified operation to the base date using the provided value and time unit.
 * @example
 * const now = new Date();
 * console.log(manipulateDate(now, 15, "minute", "add")); //now Date + 15 minutes
 */
function manipulateDate(baseDate, value, unit = "minute", op = "add") {
    const switchOperation = (unitEvaluated) => {
        switch (op) {
            case "add":
                return new Date(baseDate.getTime() + unitEvaluated);
            case "subtract":
                return new Date(baseDate.getTime() - unitEvaluated);
            case "multiply":
                return new Date(baseDate.getTime() * unitEvaluated);
            case "divide":
                return new Date(baseDate.getTime() / unitEvaluated);
            default:
                return new Date(baseDate.getTime() + unitEvaluated);
        }
    };

    switch (unit) {
        case "minute":
        case "min":
        case "m":
            return switchOperation(value * 60 * 1000);
        case "hour":
        case "h":
            return switchOperation(value * 60 * 60 * 1000);
        case "day":
        case "d":
            return switchOperation(value * 24 * 60 * 60 * 1000);
        default:
            return switchOperation(value * 60 * 1000);
    }
}

/**
 * Rounds a date object to the nearest specified time interval.
 *
 * @param {Date} date - The date object to be rounded.
 * @param {number} interval - The time interval (in minutes) to round the date to.
 *
 * @returns {Date} A new date object representing the rounded date.
 * @example
 * const now = new Date();
 * const roundedInterval = roundDateToInterval(now, 15);
 * console.log(roundedInterval); // Output will be the date rounded to the nearest 15-minute interval.
 */
function roundDateToInterval(date, interval) {
    const minutes = date.getMinutes();
    const roundedMinutes = Math.round(minutes / interval) * interval;
    date.setMinutes(roundedMinutes);
    date.setSeconds(0);
    date.setMilliseconds(0);

    return date;
}
/**
 * Converts a given date to a specified time unit (seconds, minutes, hours, or days).
 *
 * @param {Date} date - The input date to be converted.
 * @param {string} proximity - The desired time unit ('sec', 'min', 'hour', or 'day').
 * @returns {number} - The converted value in the specified time unit.
 * @throws {string} - Throws an error if an invalid proximity argument is provided.
 */
function dateToProx(date, proximity = 'sec') {
    const newDate = new Date(date);

    switch (proximity) {
        case 'sec':
            newDate.setMilliseconds(0);
            break;
        case 'min':
            newDate.setMilliseconds(0);
            newDate.setSeconds(0);
            break;
        case 'hour':
            newDate.setMilliseconds(0);
            newDate.setSeconds(0);
            newDate.setMinutes(0);
            break;
        case 'day':
            newDate.setMilliseconds(0);
            newDate.setSeconds(0);
            newDate.setMinutes(0);
            newDate.setHours(0);
            break;
        default:
            throw new Error('Invalid argument for proximity');
    }

    return newDate.getTime();
}
/**
 * Checks whether if a date object passes today's date or in simple terms, if a date is greater than today's date.
 * @param {Date} dateTime The date object that will be compared with today's date.
 * @param {string} proximity The check proximity. Can be 'day', 'hour', 'minute', 'second', or 'millisecond'. (optional)
 * @returns {boolean} Returns true if the date has passed today's date based on the specified proximity.
 */
function passedDateTime(dateTime, proximity = "day") {
    // Ensure proximity is valid
    if (!["day", "hour", "minute", "second", "millisecond"].includes(proximity)) {
        throw new Error("Invalid proximity. Proximity must be 'day', 'hour', 'minute', 'second', or 'millisecond'.");
    }

    // Cache today's date's datas.
    const today = new Date();
    const todayYear = today.getFullYear();
    const todayMonth = today.getMonth();
    const todayDate = today.getDate();
    const todayTime = today.getTime()

    // Get date and time components of the provided date
    const year = dateTime.getFullYear();
    const month = dateTime.getMonth();
    const date = dateTime.getDate();
    const time = dateTime.getTime();

    // Calculate passedDayDateLogic
    const passedDayDateLogic = (
        year < todayYear ||
        (year === todayYear && month < todayMonth) ||
        (year === todayYear && month === todayMonth && date < todayDate)
    );

    // Calculate time difference based on proximity
    switch (proximity) {
        case "day":
            return passedDayDateLogic;
        case "hour":
            return passedDayDateLogic && Math.round((todayTime - time) / (60 * 60 * 1000)) > 0;
        case "minute":
            return passedDayDateLogic && Math.round((todayTime - time) / (60 * 1000)) > 0;
        case "second":
            return passedDayDateLogic && Math.round((todayTime - time) / 1000) > 0;
        case "millisecond":
            return passedDayDateLogic && todayTime - time > 0;
        default:
            throw new Error("Invalid proximity. Proximity must be 'day', 'hour', 'minute', 'second', or 'millisecond'.");
    }
};
/**
 * @param {Date} date - The date object to format
 * @returns The date object formated in 12 hour clock as string
 */
function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

//#endregion
const TasksManger = {
    //All the instances of the tasks cached in this property
    tasks: [],

    /**
     * The task id will be initalized by the server
     * 
     * @param {String} title - Title of the task.
     * @param {Date | number} startTime - Start time of the task.
     * @param {Date |number} endTime - End time of the task.
     * @returns {Promise<task>} A promise resolved into a task json
     */
    async createTask(title, startTime, endTime) {
        try {
            //If the type of the time arguments is Date convert them to unix millisecond.
            let p_startTime = startTime instanceof Date ? startTime.getTime() : startTime;
            let p_endTime = startTime instanceof Date ? endTime.getTime() : endTime;

            // Convert length appropriate for seconds which means milliseconds data will not be stored.
            p_startTime /= 1000;
            p_endTime /= 1000;

            setAuthorizationHeader();

            //Http request
            p_task = await $.ajax({
                type: "POST",
                url: "http://localhost/scheduler/api/tasks/create.php",
                data: {
                    task: {
                        title: title,
                        startTime: p_startTime,
                        endTime: p_endTime,
                    }
                }
            });

            return p_task;
        }
        catch (error) {
            if (error.responseText != undefined) {
                throw error.responseText;
            }
            else {
                throw "Error creating a task: " + error;
            }
        }
    },
    /**
     * @param {number | null} id - The requested id of the task, negative or no value will return all the task instances. (optional)
     * @returns {Promise<task|tasks>} A promise resolved into a task or tasks json.
     */
    async fetchTask(id = null) {
        try {
            let p_task;
            /*  p_task = JSON.parse(response);
            TasksManger.tasks = p_task; */
            if (id < 0 || id == null) {
                setAuthorizationHeader();

                //Fetch all
                p_task = await $.ajax({
                    type: "GET",
                    url: "http://localhost/scheduler/api/tasks/get.php"
                });
                //Caches the tasks
                TasksManger.tasks = p_task;
            }
            else {
                setAuthorizationHeader();

                //Fetch Single Instance
                p_task = await $.ajax({
                    type: "GET",
                    url: "http://localhost/scheduler/api/tasks/get.php",
                    data: { taskId: id }
                });
            }

            return p_task;
        }
        catch (error) {
            if (error.responseText != undefined) {
                throw "Error fetching task: " + error.responseText;
            }
            else {
                throw "Error fetching task: " + error;
            }
        }
    },
    /**
     * Marks the completed status of the task's instance.
     * @param {Array} tasks An array of task objects, the two keys for the objects would be id and completed(optional).
     * @returns {Promise<response>} A promise resolved into a response string.
     */
    async completedStatus(tasks) {
        try {
            for (let i = 0; i < tasks.length; i++) {
                if (tasks[i].id == undefined | tasks[i].id == null) {
                    throw 'For completedStatus, an object in the array tasks is missing the id key in the array tasks.';
                }
            }

            setAuthorizationHeader();

            const response = await $.ajax({
                type: "PUT",
                url: "http://localhost/scheduler/api/tasks/complete.php",
                contentType: 'application/json',
                data: JSON.stringify({
                    tasks: tasks
                })
            });

            return response;

        } catch (error) {
            if (error.responseText != undefined) {
                throw error.responseJSON.error;
            }
            throw "Error changing the complete status task: " + error;
        }
    },

    /**
     * Deletes a array of tasks with provided ids and also fetches all the tasks for the response.
     * @param {Array} ids - The array of task ids.
     * @returns {Promise<response>} A promise resolved into a response string.
     */
    async deleteTask(ids) {
        try {
            const response = await $.ajax({
                type: "DELETE",
                url: "http://localhost/scheduler/api/tasks/delete.php",
                contentType: 'application/json',
                data: JSON.stringify({
                    ids: ids
                })
            });

            /* Cache all the tasks from the response just like how the fetchTask() method caches.
            Thus we would only need single request to both delete and fetch (all). */
            this.tasks = response;

            return response;
        }
        catch (error) {
            throw "Error deleting task: " + error;
        }
    },
    /**
     * Get a cached task by its id.
     * This does not get the true task that of task in the database.
     * In simple terms this does not contact the database rather uses the cached tasks.
     */
    getById(id) {
        for (let i = 0; i < this.tasks.length; i++) {
            const task = this.tasks[i];
            if (id == task.id) {
                return task;
            }
        }


        return null;
    }
}

$(async () => {
    if (!isLogged) {
        // Empty the section element to the right.
        $('section').html('');

        const unloggedHtml = /* html */ `
            <div style="text-align: center; margin-top: 1em;">
                <div style="margin-top: 1em;">User is not logged in!</div>
                <a href="${__project_url__}/login" style="display: block; margin-top: 0.5em;">Login Here</a>
            </div>
        `;
        //Render the message when the user is not logged in.
        $("aside").html(unloggedHtml);

        //Break code execution.
        return;
    }

    await TasksManger.fetchTask();

    // Also the current selected dateTime
    let dateTime = new Date();
    // Have the 'selected' endDateTime more than 15 minutes by default
    let endDateTime = new Date(dateTime.getTime() + 15 * 1000 * 60);
    // The title of the task.
    let title = 'Untitled';

    let isSelectionActive = false;

    const monthMap = [
        "January",
        "Febuary",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "Septemeber",
        "October",
        "November",
        "December"
    ];
    const dayMap = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
    ];
    // A map which converts the quarters between 0 to 3 into minutes.
    const minMap = [
        0,
        15,
        30,
        45
    ];

    //#region Methods
    const renderCalendar = () => {
        // Ensures that the selected dateTime does become less than today's dateTime.
        dateTime = passedDateTime(dateTime, "day") ? new Date() : dateTime;

        // Cache year, month and date.
        const year = dateTime.getFullYear();
        const month = dateTime.getMonth();
        const date = dateTime.getDate();

        // The first date of the current month.
        const firstDateTimeOfMonth = new Date(year, month, 1);
        // The last date of the calendar's current month.
        const lastDateOfMonth = new Date(year, month + 1, 0).getDate();
        // The index of the first Day of the month.
        const firstDayOfMonth = firstDateTimeOfMonth.getDay();
        // The last date of the previous month.
        const lastDateOfPrevMonth = new Date(year, month, 0).getDate();
        // Calendar's days in html string template.
        let calendarDays = "";

        /* 
        In the calendarDays, Add the previous month's days dates. 
        This will also help to fill the current calendar's dates according wise to their days.
        */
        for (let i = firstDayOfMonth; i > 0; i--) {
            // Include the class 'cal-inactive-dates' to seperate previous month's day|dates from the current calendar's month's render.
            calendarDays += /* html */ `
            <li class="cal-inactive-dates">${lastDateOfPrevMonth - i + 1}</li>
            `;
        }
        // In the calendarDays, Add the calendar's current month's days dates.
        for (let dateI = 1; dateI < lastDateOfMonth + 1; dateI++) {
            // This logic is used to check passed days|dates from today.
            const passedDaysLogic = passedDateTime(new Date(year, month, dateI), "day");

            if (passedDaysLogic) {
                // Include the class 'cal-inactive-days' to seperate passed days from the calendar.
                calendarDays += /* html */ `
                <li class="cal-inactive-dates">${dateI}</li>
                `;
            }
            // Check if the selected date 'date' is equals to the current date's iteration 'dateI' whilst checking if its not a passed day.
            else if (dateI == date) {
                calendarDays += /* html */ `
                <li class="cal-active-dates" id="cal-selected-date">${dateI}</li>
                `;
            }
            else {
                calendarDays += /* html */ `
                <li class="cal-active-dates">${dateI}</li>
                `;
            }
        }

        const monthName = monthMap[month];
        const template = /* html */`
            <div id="calendar-top">
                <i class="fi fi-br-angle-square-left cal-arrows" id="cal-month-left"></i>
                <div id="cal-monthYyear">${monthName} ${year}</div>
                <i class="fi fi-br-angle-square-right cal-arrows" id="cal-month-right"></i>
            </div>
            <ul id="calendar-weeks">
                <li>Sun</li>
                <li>Mon</li>
                <li>Tue</li>
                <li>Wed</li>
                <li>Thu</li>
                <li>Fri</li>
                <li>Sat</li>
            </ul>
            <ul id="calendar-days">
                ${calendarDays}
            </ul>
            `;

        $("#calendar").html(template);
    }
    /**
     * Pre-renders the selection of date in the calendar.
     * The selection is correctly render using the render() method but it requires more memory.
     * Thus this function only pre-renders the selection.
     */
    const renderSelect = (dayDate) => {
        const $selected = $(".cal-active-dates").filter(function () {
            return $(this).text() == dayDate;
        })

        // Unrender the previous selected date.
        $("#cal-selected-date").removeAttr("id");;

        // Render the newly acquired day date.
        $selected.attr("id", "cal-selected-date");

        const currentDate = new Date();
    }
    const renderCurrentQuarter = () => {
        const currentDate = new Date();

        // Remove the current quarter.
        $('[data-current-quarter]').removeAttr('data-current-quarter');

        // Only if the current selected date is also today.
        if (dateToProx(dateTime, 'day') == dateToProx(currentDate, 'day')) {
            const currHour = currentDate.getHours();
            const currMin = currentDate.getMinutes();
            const $currQuarter = $(`[data-hour=${currHour}] [data-quarter=${quarterMap(currMin)}]`);

            // Render current quarter to the DOM.
            $currQuarter.attr('data-current-quarter', '');
        }
    }
    const renderHours = () => {
        let hours = /* html */ `<div id="task-boxes-container"></div>`;
        const $hours = $('#hours');

        for (let i = 0; i < 24; i++) {
            const isPm = i > 12;
            hours += /* html */ `
            <div class="hour" data-hour="${i}">
                <span>${isPm ? i - 12 : i} ${isPm ? "PM" : "AM"}</span>
                <div>
                    <div data-quarter="0"><hr></div>
                    <div data-quarter="1"><hr></div>
                    <div data-quarter="2"><hr></div>
                    <div data-quarter="3"><hr></div>
                </div>
            </div>
            `;
        }

        $hours.html(hours);

        renderCurrentQuarter();
    }
    const renderHoursTop = () => {
        const currentDay = dayMap[dateTime.getDay()].substring(0, 3);
        const currentDate = dateTime.getDate();
        const currentMonth = monthMap[dateTime.getMonth()];

        $("#current-day").html(currentDay);
        $("#current-date").html(currentDate);
        $("#current-month").html(currentMonth);
    }
    const validizeDates = () => {
        // Validiize dateTime
        const today = new Date();
        if (dateTime.getTime() < today.getTime()) {
            dateTime = today;
        }
        // Cache dateTime's time stamp
        const dateTimestamp = dateTime.getTime();

        // Validiize endTime 
        if (endDateTime.getTime() <= dateTimestamp || dateToProx(endDateTime, 'day') != dateToProx(dateTime, 'day')) {
            endDateTime.setTime(dateTimestamp + 15 * 1000 * 60);
        }
    }
    // A functional map which converts minutes to quarters between 0 to 3.
    const quarterMap = (min) => {
        switch (true) {
            case min >= 0 && min < 15:
                // First quarter
                return 0;
            case min >= 15 && min < 30:
                // Second quarter
                return 1;
            case min >= 30 && min < 45:
                // Third quarter
                return 2;
            case min >= 45 && min < 60:
                // Fourth quarter
                return 3;
            default:
                break;
        }
    };
    const taskBox = (startDate, endDate, title, taskId = null) => {
        if (dateToProx(startDate, 'day') != dateToProx(endDate, 'day')) {
            throw Error('Task boxes with duration between two days is unsupported!');
        }
        // Only if the startDate is equals to current rendered day.
        if (dateToProx(startDate, 'day') == dateToProx(dateTime, 'day')) {
            // Determine the quarter element for start time.
            const startHour = startDate.getHours();
            const startMin = startDate.getMinutes();
            const startQuarter = quarterMap(startMin);
            const $start = $(`[data-hour=${startHour}] [data-quarter=${startQuarter}] hr`);

            // Determine the quarter element for end time.
            const endHour = endDate.getHours();
            const endMin = endDate.getMinutes();
            const endQuarter = quarterMap(endMin);
            const $end = $(`[data-hour=${endHour}] [data-quarter=${endQuarter}] hr`);

            // Determine the transforms.
            const topPosition = $start.position().top + $('#hours').scrollTop();
            const bottomPosition = $end.position().top + $('#hours').scrollTop();
            const leftPosition = $start.position().left;
            const height = bottomPosition - topPosition;

            // Bool used to check if the height is small.
            const isVerySmall = height < 26;
            const isSmall = height < 42;

            let taskBoxInputs = '';
            if (taskId != null) {
                const task = TasksManger.getById(taskId);
                const isCompleted = task.completed == 1;

                taskBoxInputs = /* html */ `
                <div class="task-box-inputs">
                    <label class="for-task-complte">Complete</label>
                    <input type="checkbox" --data-task-complete=${taskId} ${isCompleted ? 'checked' : ''}>
                    <label class="for-task-delete">Delete</label>
                    <i class="fi fi-sr-circle-xmark" --data-task-delete=${taskId}></i>
                </div>`;
            }

            const template = /* html */ `
            <div 
            class="
                task-box
                ${isSmall ? 'task-box-small' : ''}
                ${isVerySmall ? 'task-box-very-small' : ''}
            " 
            style="
                top: ${topPosition};
                height: ${height};
                left: ${leftPosition};
            ">
            <div class="task-box-info">
                <div class="task-box-title">${title}</div>
                <div class="task-box-timings">
                    (${formatAMPM(startDate)} - ${formatAMPM(endDate)})
                </div>
            </div>
                ${taskBoxInputs}
                ${taskId == null ? /* html */ `<div class="task-box-end-adjuster"></div>` : ''} 
            </div>
            `;

            return template;
        }

        return '';
    }
    const renderTaskBoxes = (prepends = '', appends = '') => {
        const allTasks = TasksManger.tasks;

        let template = prepends;

        for (let i = 0; i < allTasks.length; i++) {
            const task = allTasks[i];


            const startDate = new Date(task.startTime * 1000);
            const endDate = new Date(task.endTime * 1000);

            template += taskBox(startDate, endDate, task.title, task.id);
        }

        template += appends;

        $('#task-boxes-container').html(template);
    }
    const renderTaskSelection = () => {
        // Validize dates
        validizeDates();

        if (isSelectionActive) {
            // Add the animation class.
            $('#task-inputs').addClass('task-inputs-on');
            $('#cancel-task').show(200, 'linear');

            // Add style to hours.
            $("#hours").css({
                'cursor': 'pointer'
            });

            renderTaskBoxes(taskBox(dateTime, endDateTime, title));
        }
        else {
            // Remove the animation class.
            $('#task-inputs').removeClass('task-inputs-on');
            $('#cancel-task').hide(200, 'linear');

            // Remove style from hours
            $("#hours").removeAttr('style');

            renderTaskBoxes();
        }

        $('#task-title').val(title);
        $("#start-min").val(dateTime.getMinutes());
        $('#start-hour').val(dateTime.getHours());
        $('#end-min').val(endDateTime.getMinutes());
        $('#end-hour').val(endDateTime.getHours());

        // Retrive the selected date's hour and minutes which is converted to the value of the quarter attribute.
        const hour = dateTime.getHours();
        const min = dateTime.getMinutes();
        const $selectedHour = $(`[data-hour=${hour}] [data-quarter=${quarterMap(min)}]`);

        // Remove and re-add the selected-quarter in the DOM.
        $('#selected-quarter').removeAttr('id');
        $selectedHour.attr('id', 'selected-quarter');
    }
    const scrollToSelect = () => {
        const $hours = $('#hours');
        // The selected quarter's top position in the container minus the height of an hour.
        const newScrollTop = $('#selected-quarter').position().top + $hours.scrollTop() - $(".hour").height();

        // Change scroll to the selected quarter.
        $hours.scrollTop(newScrollTop);
    }
    const animateHours = (isRight) => {
        // Hours element to animate the fading in.
        const $hours = $("#hours");
        const hoursAnimClass = isRight ? 'hours-right-in' : 'hours-left-in';
        $hours.addClass(hoursAnimClass)

        // Animation time for hours.
        let hoursAnimTime = $hours.css('--animation-time');
        hoursAnimTime = hoursAnimTime.replace('s', '') * 1000;

        setTimeout(() => {
            // Remove the animation class.
            $hours.removeClass(hoursAnimClass)
        }, hoursAnimTime);

        scrollToSelect();
        renderCurrentQuarter();
        renderTaskBoxes();
    }
    const registerEventHandlers = () => {
        $("#calendar").on("click", "#cal-month-left, #cal-month-right", function () {
            const isMonthRight = $(this).is("#cal-month-right");
            dateTime.setMonth(dateTime.getMonth() + (isMonthRight ? 1 : -1));
            renderCalendar();
            renderHoursTop();

            // We are not using $(this) because that instance does no longer exist in the memory after render.
            const $updatedArrow = isMonthRight ? $("#cal-month-right") : $("#cal-month-left");
            $updatedArrow.addClass("cal-animate-arrows");

            // Animation time in milliseconds retrived from the css property --animation-time
            let animationTime = $updatedArrow.css("--animation-time");
            // Convert by removing the 's' which stands for seconds from the attribute animation 
            animationTime = animationTime.replace('s', "") * 1000;

            setTimeout(() => {
                // Remove the animation class.
                $updatedArrow.removeClass("cal-animate-arrows");
            }, animationTime);

            animateHours(isMonthRight);
        })
        $("#calendar").on("click", ".cal-active-dates", function () {
            const oldDate = dateTime.getDate();
            const newDate = $(this).html()

            dateTime.setDate(newDate);
            renderSelect(dateTime.getDate());
            renderHoursTop();

            const isNextDate = newDate > oldDate;
            animateHours(isNextDate);
        })

        $('#new-task, #cancel-task').click(function () {
            const isNewTask = $(this).is('#new-task');

            if (isNewTask && isSelectionActive) {
                // Use task manager to create a new task in the backend.
                TasksManger.createTask(title, dateTime, endDateTime)
                    .then(() => {
                        alert('New task created');
                        TasksManger.fetchTask()
                            .then(() => {
                                renderTaskBoxes();
                            })
                            .catch((error) => {
                                console.error(error);
                                alert(error);
                            })
                    })
                    .catch((error) => {
                        if (JSON.parse(error).error) {
                            console.error(error);
                            alert(JSON.parse(error).error);
                        }
                        else {
                            console.error(error);
                            alert(error);
                        }
                    });
                isSelectionActive = false;
                renderTaskSelection();
                renderTaskBoxes();
            }
            else {
                // Check if selection is active by checking if the current clicked element is new task.
                isSelectionActive = isNewTask;
                renderTaskSelection();
            }
        })
        $('#task-inputs input').on('change', () => {
            // Set hour and minute without effecting the day/date.
            const inputStartDate = new Date(
                dateTime.getFullYear(),
                dateTime.getMonth(),
                dateTime.getDate(),
                $('#start-hour').val(),
                $('#start-min').val()
            );

            if (dateToProx(new Date(inputStartDate.getTime() + 1000 * 60 * 15), 'day') == dateToProx(dateTime, 'day')) {
                dateTime = inputStartDate;
            }

            endDateTime.setHours($('#end-hour').val());
            endDateTime.setMinutes($('#end-min').val());

            title = $('#task-title').val();

            renderTaskSelection();
        })
        $('#hours').on('click', '#task-boxes-container .task-box [--data-task-delete]', function () {
            const taskId = $(this).attr('--data-task-delete');
            // The deleteTask method also fetches all the tasks.
            TasksManger.deleteTask([taskId])
                .then(() => {
                    alert('Task successfully deleted');
                    renderTaskBoxes();

                })
                .catch((error) => {
                    console.error(error);
                    alert(error);
                })
        })
        $('#hours').on('click', '#task-boxes-container .task-box [--data-task-complete]', function () {
            const taskId = $(this).attr('--data-task-complete');
            const completedStatus = $(this).is(':checked');

            // Change the completed status in the cached tasks property of the TasksManager.
            for (let i = 0; i < TasksManger.tasks.length; i++) {
                if (TasksManger.tasks[i].id == taskId) {
                    TasksManger.tasks[i].completed = completedStatus;
                    break;
                }
            }

            // Change the completed status in the backend.
            TasksManger.completedStatus([{ id: taskId, completed: completedStatus }])
                .catch((error) => {
                    console.error(error);
                    alert(error);
                });
        })
        $('[data-quarter]').click(function () {
            // Retrive the hour by getting the attribute of the current quarter element.
            const hour = $(this).parent().parent().attr('data-hour');
            // Retrive the quarter data.
            const quarter = $(this).attr('data-quarter');

            // Set hour and minute without effecting the day/date.
            const inputStartDate = new Date(
                dateTime.getFullYear(),
                dateTime.getMonth(),
                dateTime.getDate(),
                hour,
                minMap[quarter]
            );

            if (dateToProx(new Date(inputStartDate.getTime() + 1000 * 60 * 15), 'day') == dateToProx(dateTime, 'day')) {
                dateTime = inputStartDate;
            }

            renderTaskSelection();
        })

        // Task Box resize
        let isResize = false;
        let $taskBox;

        $('#hours').on('mousedown mouseup', '.task-box-end-adjuster', function (e) {
            e.preventDefault();
            isResize = e.type == 'mousedown';
            $taskBox = $(this).parent();
        })
        $('#hours').on('mousemove mouseup click', '.task-box', function (e) {
            isResize = e.type == 'mouseup' ? false : isResize;

            // Get the y position of the cursor
            const y = e.pageY;
            // The x position should be the right side of the task box.
            const x = $(this).offset().left + $(this).outerWidth() + 10;
            // Get the hour quarter element by x and y position.
            const $hourQuarter = $(document.elementFromPoint(x, y));

            if (isResize) {
                // Trigger the mouse enter event of that hour quarter.
                $hourQuarter.trigger('mouseenter');
            }
            else if (e.type == 'click') {
                // Trigger the mouse click event of that hour quarter.
                $hourQuarter.trigger('click');
            }
        })
        $('#hours').on('mouseenter mouseup', '[data-quarter]', function (e) {
            e.preventDefault();
            isResize = e.type == 'mouseup' ? false : isResize;

            if (isResize) {
                const startHour = dateTime.getHours();
                const startMin = dateTime.getMinutes();
                const startQuarter = quarterMap(startMin);
                const $start = $(`[data-hour=${startHour}] [data-quarter=${startQuarter}]`);

                const endHour = $(this).parent().parent().attr('data-hour');
                const endQuarter = $(this).attr('data-quarter');
                const endMin = minMap[endQuarter];
                const $end = $(`[data-hour=${endHour}] [data-quarter=${endQuarter}]`);

                // Update endDateTime
                endDateTime = new Date(
                    endDateTime.getFullYear(),
                    endDateTime.getMonth(),
                    endDateTime.getDate(),
                    endHour,
                    endMin
                );

                validizeDates();

                renderTaskBoxes(taskBox(dateTime, endDateTime, title));
            }
        })

    }
    //#endregion
    renderCalendar();
    renderHoursTop();
    renderHours();
    renderTaskSelection();
    scrollToSelect();
    registerEventHandlers();
})