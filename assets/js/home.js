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
     * @returns {Promise<task>} A promise resolved into a task json
     */
    async createTask(title, startTime, endTime) {
        try {
            //If the type of the time arguments is Date convert them to unix millisecond.
            const p_startTime = startTime instanceof Date ? startTime.getTime() : startTime;
            const p_endTime = startTime instanceof Date ? endTime.getTime() : endTime;

            //The regex pattern for a valid interval string.
            //const validregex = new RegExp("^(c[01]{7}$)|^([dn]{1}$)", "gi");

            //Http request
            p_task = await $.ajax({
                type: "POST",
                url: "http://localhost/scheduler/includes/homeApi.php",
                data: {
                    task: {
                        title: title,
                        startTime: p_startTime,
                        endTime: p_endTime,
                    }
                }
            });

            p_task = JSON.parse(p_task);

            return p_task;
        }
        catch (error) {
            if (error.responseText != undefined) {
                throw "Error creating a task: " + error.responseText;
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
                //Fetch all
                p_task = await $.ajax({
                    type: "GET",
                    url: "http://localhost/scheduler/includes/homeApi.php"
                });
                p_task = JSON.parse(response);
                //Caches the tasks
                TasksManger.tasks = p_task;
            }
            else {
                //Fetch Single Instance
                p_task = await $.ajax({
                    type: "GET",
                    url: "http://localhost/scheduler/includes/homeApi.php",
                    data: { taskId: id }
                });
            }
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
     * @param {number} id - The id of the requested task's instance.
     * @param {boolean | int} status - The completed status. (optional)
     * @returns {Promise<response>} A promise resolved into a response string.
     */
    async completedStatus(id, status = true) {
        try {
            const response = await $.ajax({
                type: "PUT",
                url: "http://localhost/scheduler/includes/homeApi.php",
                data: {
                    completedStatus: status ? 1 : 0,
                    taskId: id
                }
            });

            return response;

        } catch (error) {
            throw "Error changing the complete status task: " + error;
        }
    },

    /**
     * 
     * @param {number} id - The id of the requested task's instance.
     * @returns {Promise<response>} A promise resolved into a response string.
     */
    async deleteTask(id) {
        try {
            const response = await $.ajax({
                type: "DELETE",
                url: "http://localhost/scheduler/includes/homeApi.php",
                data: {
                    taskId: id
                }
            });

            return response;
        }
        catch (error) {
            throw "Error deleting task: " + error.responseText;
        }
    },
}
