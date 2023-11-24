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
     * @param {Function} callback - The function to run on success. can pass in a parameter in this that will store the created task.
     */
    createTask(title, startTime, endTime, callback = () => null, errorCallback = () => null) {
        //If the type of the time arguments is Date convert them to unix millisecond.
        const p_startTime = startTime instanceof Date ? startTime.getTime() : startTime;
        const p_endTime = startTime instanceof Date ? endTime.getTime() : endTime;

        //The regex pattern for a valid interval string.
        //const validregex = new RegExp("^(c[01]{7}$)|^([dn]{1}$)", "gi");

        let p_task = {}; //Object to store the returned task object with a id number.
        //Http request
        $.ajax({
            type: "POST",
            url: "http://localhost/scheduler/includes/homeApi.php",
            data: {
                task: {
                    title: title,
                    startTime: p_startTime,
                    endTime: p_endTime,
                }
            },
            success: function (response) {
                p_task = JSON.parse(response); //Store the task object
                callback(p_task);
            },
            error: errorCallback
        });
    },
    /**
     * @param {Function} callback - The function to run on success. can pass in a parameter in this that will store the returned result.
     * @param {number | null} id - The requested id of the task, negative or no value will return all the task instances. (optional)
     * @param {Function} errorCallback - The function to run on error of any network error or server error. (optional)
     */
    fetchTask(callback, id = null, errorCallback = () => null) {
        let p_task;
        if (id < 0 || id == null) {
            //Fetch all
            $.ajax({
                type: "GET",
                url: "http://localhost/scheduler/includes/homeApi.php",
                success: function (response) {
                    p_task = JSON.parse(response);
                    TasksManger.tasks = p_task;

                    callback(p_task);
                },
                error: errorCallback
            });
        }
        else {
            //Fetch Single Instance
            $.ajax({
                type: "GET",
                url: "http://localhost/scheduler/includes/homeApi.php",
                data: { taskId: id },
                success: function (response) {
                    p_task = JSON.parse(response);
                    callback(p_task);
                },
                error: errorCallback
            });
        }
    },
    /**
     * Marks the completed status of the task's instance.
     * @param {number} id - The id of the requested task's instance.
     * @param {boolean | int} status - The completed status. (optional)
     * @param {Function} callback - The function to call on success of the http request, can pass in a parameter to get a response. (optional)
     * @param {Function} errorCallback - The function to call on error of the http request. (optional)
     */
    completedStatus(id, status = true, callback = () => null, errorCallback = () => null) {
        $.ajax({
            type: "PUT",
            url: "http://localhost/scheduler/includes/homeApi.php",
            data: {
                completedStatus: status ? 1 : 0,
                taskId: id
            },
            success: (response) => {
                callback(response);
            },
            error: errorCallback
        });
    },

    /**
     * 
     * @param {number} id - The id of the requested task's instance.
     * @param {Function} callback - The function to call on success of the http request, can pass in a parameter to get a response. (optional)
     * @param {Function} errorCallback - The function to call on error of the http request. (optional)
     */
    deleteTask(id, callback = () => null, errorCallback = () => null) {
        $.ajax({
            type: "DELETE",
            url: "http://localhost/scheduler/includes/homeApi.php",
            data: {
                taskId: id
            },
            success: function (response) {
                callback(response);
            },
            error: errorCallback
        });
    },
}

NotifManager.registerSw("service-worker.js");

$(document).ready(function () {
    let startDate = new Date(); //Now
    let endDate = roundDateToInterval(manipulateDate(startDate, 15), 15); //Now + 15 rounded minutes to the intervals of 15.

    const $startHour = $("#start-hour");
    const $startMinute = $("#start-minute");
    const $endhour = $("#end-hour");
    const $endMinute = $("#end-minute");
    const $taskId = $("#task-id");

    const $createTask = $("#create-task");
    const $fetchTasks = $("#fetch-tasks");
    const $completeTask = $("#complete-task");
    const $deleteTask = $("#delete-task");
    const $subscribe = $("#subscribe");
    const $submit = $("#submit");

    $startHour.val(startDate.getHours());
    $startMinute.val(startDate.getMinutes());
    $endhour.val(endDate.getHours());
    $endMinute.val(endDate.getMinutes());

    $createTask.click(() => {
        startDate.setHours($startHour.val());
        startDate.setMinutes($startMinute.val());
        endDate.setHours($endhour.val());
        endDate.setMinutes($endMinute.val());

        console.log(startDate);

        TasksManger.createTask("Untitled", startDate, endDate, function (instance) {

        });
    });
    $fetchTasks.click(() => renderTasks());
    $completeTask.click(() => TasksManger.completedStatus($taskId.val(), true,
        (response) => {
            console.log(response);
        }, () => {
            console.log("error");
        })
    )
    $deleteTask.click(() => TasksManger.deleteTask($taskId.val(), () => {
        console.log("success");
    }, () => {
        console.log("error");
    }));

    $submit.click(() => {
        const username = $("#username").val();
        const password = $("#password").val();
        const email = $("#email").val() == "" /*Or a regex match*/ ? null : $("#email").val();

        if ($subscribe.is(":checked")) {
            NotifManager.requestSubscribe((subscription) => {
                Users.createUser(username, password, email, subscription)
                    .then((response) => {
                        console.log(response);
                    })
                    .catch((response) => {
                        console.log("failed to create a user: " + response);
                    })
            })
        }
        else {
            Users.createUser(username, password, email)
                .then((response) => {
                    console.log(response);
                })
                .catch((response) => {
                    console.log("failed to create a user: " + response);
                })
        }
    })

    //const cronExp = new RegExp(/^(\*|([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])|\*\/([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])) (\*|([0-9]|1[0-9]|2[0-3])|\*\/([0-9]|1[0-9]|2[0-3])) (\*|([1-9]|1[0-9]|2[0-9]|3[0-1])|\*\/([1-9]|1[0-9]|2[0-9]|3[0-1])) (\*|([1-9]|1[0-2])|\*\/([1-9]|1[0-2])) (\*|([0-6])|\*\/([0-6]))$/);

    //#region Render Modules
    function renderTasks() {
        //Activate Loader


        TasksManger.fetchTask(function (a_tasks) {
            //Deactivate Loader and render


        }, null, function () {
            //Deactivate Loader and show error

        })
    }
    //#endregion 
});