var $ = jQuery;

$(document).ready(function () {

    const currentDate = $("#calendar-month"),
    daysTag = $(".calendar-days"),
    prevNextIcon = $(".calendar-month-changer"),
    daysTags = $(".calendar-days li");

    let date = new Date(),
    currYear = date.getFullYear(),
    currMonth = date.getMonth(),
    selectedDate = date;

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

        for(let i = firstDayOfMonth; i > 0; i--){
            liTag += `<li class="inactive-days">${lastDateOfPrevMonth - i + 1}</li>`
        }

        for (let i = 1; i <= lastDateOfMonth; i++) {
            if(i == selectedDate.getDate()){
                liTag += `<li id="calendar-current-day">${i}</li>`
            }
            else{
                liTag += `<li>${i}</li>`;
            }
        }

        currentDate.html(`${months[currMonth]} ${currYear}`);
        daysTag.html(liTag);
    }
    renderCalender();

    for(let i = 0; i < prevNextIcon.length; i++){
        let icon = $(prevNextIcon[i]);

        icon.on("click", () => {
            currMonth = icon.attr("id") === "month-prev" ? currMonth - 1 : currMonth + 1;


            if(currMonth < 0 || currMonth > 11){
                date = new Date(currYear, currMonth);
                currYear = date.getFullYear();
                currMonth = date.getMonth();
            }
            renderCalender();
        })
    }
    $(".calendar-days").on("click", "li", function() {
        selectedDate = new Date(currYear, currMonth, $(this).html());
        renderCalender();
    })
});