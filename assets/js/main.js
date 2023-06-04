var $ = jQuery;

$(document).ready(function () {
    
});

function WeatherAPI(latitude, longitude){
    let forecast = {errorMessage: "could not retrive weather data"};
    let location = `${latitude},${longitude}`;

    let isOk = false;

    const apiKey = "1130fb0356d44afba8474247230206";
    const url = `http://api.weatherapi.com/v1/forecast.json?key=${apiKey}&q=${location}&days=3`;
    
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
        return {
            forecast: forecast,
            forecastDay: forecastDay,

            currentTemp: forecast.current.temp_c,
            todayTemp: {
                minTemp: forecastDay[0].day.minTemp, 
                maxTemp: forecastDay[0].day.maxTemp
            },
            tommorowTemp: {
                minTemp: forecastDay[1].day.minTemp,
                maxTemp: forecastDay[1].day.maxTemp
            },
            afterTommorowTemp: {
                minTemp: forecastDay[2].day.minTemp,
                maxTemp: forecastDay[2].day.maxTemp
            }

        }
    }
    else{
        return null;
    }

}