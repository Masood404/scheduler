var $ = jQuery;

let lattitude;
let longitude;

if(localStorage.getItem("weatherApi") != null){
    lattitude = JSON.parse(localStorage.getItem("weatherApi")).lat;
    longitude = JSON.parse(localStorage.getItem("weatherApi")).lon; 
}
else{
    let coordinatesObj;

    lattitude = 40.7128; // New york coordinates
    longitude = -74.0060;

    coordinatesObj = {
        lat : lattitude,
        lon : longitude
    }

    localStorage.setItem("weatherApi", JSON.stringify(coordinatesObj));
} 
let weatherapi = WeatherAPI(lattitude, longitude);

$(document).ready(function () {
    //inject weather api to header
    let todayWeatherE = $("header .todayWeather");
    let tommorowWeatherE = $("header .tommorowWeather");
    let afterTommorowE = $("header .afterTommorowWeather");

    todayWeatherE.html(`
        <img src = "${weatherapi.today.condition.icon}">
        <h4>${weatherapi.today.dayName}</h4>
        <p><b>Min:</b> ${weatherapi.today.minTemp} C&deg;</p>
        <p><b>Current:</b> ${weatherapi.currentTemp} C&deg;</p>
        <p><b>Max:</b> ${weatherapi.today.maxTemp} C&deg;</p>
    `);
    tommorowWeatherE.html(`
        <img src = "${weatherapi.tommorow.condition.icon}">
        <h4>${weatherapi.tommorow.dayName}</h4>
        <p><b>Min:</b> ${weatherapi.tommorow.minTemp} C&deg;</p>
        <p><b>Max:</b> ${weatherapi.today.maxTemp} C&deg;</p>
    `)
    afterTommorowE.html(`
        <img src = "${weatherapi.afterTommorow.condition.icon}">
        <h4>${weatherapi.afterTommorow.dayName}</h4>
        <p><b>Min:</b> ${weatherapi.afterTommorow.minTemp} C&deg;</p>
        <p><b>Max:</b> ${weatherapi.afterTommorow.maxTemp} C&deg;</p>
    `)

    //menu animation based on navButton click
    let navUl = $("nav ul");
    let navButton = $(".menuButtonContainer .navButton");
    let isMenuActive = false;   

    navButton.click(function () { 
        isMenuActive = !isMenuActive;
        if(isMenuActive){
            navUl.addClass("navAnim");
        }
        else{
            navUl.removeClass("navAnim");
        }
    });
});
function WeatherCard(weatherFI, appendTo = "body", apiInstance){
    let ordinalChDatesMap = {}; //ordinal checeked dates map
    //check for ordinals in weather api dates
    for(let i = 1; i < 32; i++){
        if(i > 10 && i < 20){
            ordinalChDatesMap[i] = i + "th";
        }
        else{
            if(i % 10 == 1){
                ordinalChDatesMap[i] = i + "st";
            }
            else if(i % 10 == 2){
                ordinalChDatesMap[i] = i + "nd";
            }   
            else if(i % 10 == 3){
                ordinalChDatesMap[i] = i + "rd";
            }
            else{
                ordinalChDatesMap[i] = i + "th";
            }
        }
    }
    let obj = {
        minTemp: apiInstance[weatherFI].minTemp,
        avgTemp: apiInstance[weatherFI].avgTemp,
        maxTemp: apiInstance[weatherFI].maxTemp,

        conditionName: apiInstance[weatherFI].condition.name,
        conditionIcon: apiInstance[weatherFI].condition.icon,

        dayName: apiInstance[weatherFI].dayName,
        date: apiInstance[weatherFI].date,
        month: apiInstance[weatherFI].month,
        monthName: apiInstance[weatherFI].monthName,
        locationName: apiInstance[weatherFI].locationName
    };
    let html = `
    <div class="weatherWidget">
        <div class="weatherIconContainer">
            <img src="${obj.conditionIcon}">
        </div>
        <div class="weatherInfo">
            <div class="temperature">
                <span class="minTemp"><strong>min: </strong>${obj.minTemp}&deg;</span>
                <span class="avgTemp"><strong>avg: </strong>${obj.avgTemp}&deg;</span>
                <span class="maxTemp"><strong>max: </strong>${obj.maxTemp}&deg;</span>
            </div>
            <div class="conditionAndRegion">
                <h4 class="condition">${obj.conditionName}</h4>
                <h4 class="region">${obj.locationName}</h4>
            </div>
            <div class="date">
                <strong>${obj.dayName}</strong>
                ${ordinalChDatesMap[obj.date]}
                ${obj.monthName}
            </div>
        </div>
    </div>
    `;
    obj["html"] = html;

    $(appendTo).append(obj.html);

    $(".temperature").hover(function(){
        $(this).find(".minTemp, .maxTemp").addClass("weatherWidgetTempAnim");
    }, function(){
        $(this).find(".minTemp, .maxTemp").removeClass("weatherWidgetTempAnim");
    })

    return obj;
}
function WeatherAPI(lattitude, longitude){
    let forecast = {errorMessage: "could not retrive weather data"};

    let isOk = false;
    const url = `http://localhost/scheduler/includes/weatherApi.php?lattitude=${lattitude}&longitude=${longitude}`;

    let conditionMap = [];
    $.ajax({
        type: "GET",
        url: "/scheduler/assets/js/weather-condition-map.json",
        async: false,
        success: function (response) {
            conditionMap = response;
        },
        error: function (){
            console.log("could not get weather condition json map")
        }
    });
    let updatedConditionMap = {};

    for(let i = 0; i < conditionMap.length; i++){
        let conditionCode = conditionMap[i].code;
        let conditionName = conditionMap[i].day;

        updatedConditionMap[conditionCode] = conditionName;
    }
    
    $.ajax({
        type: "GET",
        url: url,
        async: false,
        success: function (response) {
            forecast = response;
            isOk = true;
        },
        error: function(){
            isOk = false;
        }
    });
    if(isOk){
        let forecastDay = forecast.forecast.forecastday;
        let forecastLimit = forecastDay.length;

        const dayName = {
            0: "Sunday",
            1: "Monday",
            2: "Tuesday",
            3: "Wednesday",
            4: "Thursday",
            5: "Friday",
            6: "Saturday" 
        };
        const monthName = {
            0: "January",
            1: "Ferbuary",
            2: "March",
            3: "April",
            4: "May", 
            5: "June",
            6: "July",
            7: "August",
            8: "September", 
            9: "October",
            10: "November",
            11: "December"
        }

        let dayNamesI = getForecastDays(forecastLimit);
        let dates = getForecastDates(forecastLimit);
        let apiObj = {
            currentTemp: forecast.current.temp_c,
            today: getForecastDayData(0),
            tommorow: getForecastDayData(1),
            afterTommorow: getForecastDayData(2),

            lon: longitude,
            lat: lattitude,
            forecastLimit : forecastLimit
        };
        for(let i = 0; i < forecastLimit; i++){
            apiObj[i] = getForecastDayData(i);
        }
        return apiObj;
        function getForecastDayData(index){
            let conditionCode = forecastDay[index].day.condition.code;
            return{
                minTemp: forecastDay[index].day.mintemp_c,
                avgTemp: forecastDay[index].day.avgtemp_c,
                maxTemp: forecastDay[index].day.maxtemp_c,
                condition: {
                    icon: forecastDay[index].day.condition.icon,
                    code: conditionCode,
                    name: updatedConditionMap[conditionCode]
                },
                dayName : dayName[dayNamesI[index]],
                date : dates.dates[index],
                month : dates.months[index] + 1,
                monthName: monthName[dates.months[index]],
                locationName: forecast.location.name,
            }
        }
        function getForecastDates(forecastLimit){
            obj = {dates: {}, months: {}};
            for(let i = 0; i < forecastLimit; i++){
                obj.dates[i] = new Date(forecastDay[i].date_epoch * 1000).getDate();
                obj.months[i] = new Date(forecastDay[i].date_epoch * 1000).getMonth();
            }
            return obj;
        }
        function getForecastDays(forecastLimit){
            obj = {};
            for(let i = 0; i < forecastLimit; i++){
                obj[i] = new Date(forecastDay[i].date_epoch * 1000).getDay();
            }
            return obj;
        }
    }
    else{
        return null;
    }

}