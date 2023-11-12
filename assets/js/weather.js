$(document).ready(function () {

    //check for errors
    if(weatherapi){
        let forecastLimit = 3;
        let forecastLimitE = $("#w_forecastLimit");
        let incForLimitE = $("#w_incForLimit");
        let decForLimitE = $("#w_decForLimit");

        incForLimitE.click(function (e) { 
            IncForecastLimit();
        });
        decForLimitE.click(function (e) { 
            DecForecastLimit();
        });

        forecastLimitE.html(forecastLimit);

        function IncForecastLimit(){
            if(forecastLimit < weatherapi.forecastLimit){
                forecastLimit += 1;
                forecastLimitE.html(forecastLimit);   
            }
            else{
                forecastLimit = weatherapi.forecastLimit;
                forecastLimitE.html(forecastLimit);   
            }
        }
        function DecForecastLimit(){
            if(forecastLimit > 0){
                forecastLimit -= 1;
                forecastLimitE.html(forecastLimit);
            }
            else{
                forecastLimit = 0;
                forecastLimitE.html(forecastLimit);
            }
        }

        let updateButton = $("#w_forecastUpdate");
        let lattitudeInputE = $("#lattitude");
        let longitudeInputE = $("#longitude");
        let lattitudeInput;
        let longitudeInput;
        let isLocValueChanged = false;

        lattitudeInputE.val(weatherapi.lat);
        longitudeInputE.val(weatherapi.lon);

        lattitudeInput = lattitudeInputE.val();
        longitudeInput = longitudeInputE.val();

        lattitudeInputE.change(function (e) { 
            isLocValueChanged = true;
        });
        longitudeInputE.change(function(e) {
            isLocValueChanged = true;
        })

        StartForecast();
        updateButton.click(function(){
            UpdateForecast();
        })

        function UpdateForecast(){
            if(isLocValueChanged){
                $(".weatherWidgets").html("");

                lattitudeInput = lattitudeInputE.val();
                longitudeInput = longitudeInputE.val();

                weatherapi = WeatherAPI(lattitudeInput, longitudeInput);

                for(let i = 0; i < forecastLimit; i++){
                    WeatherCard(i, ".weatherWidgets", weatherapi);
                }

                let obj = {
                    lat: lattitudeInput,
                    lon: longitudeInput
                }

                localStorage.setItem("weatherApi", JSON.stringify(obj));

                //update header
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
            }
            else{
                $(".weatherWidgets").html("");

                for(let i = 0; i < forecastLimit; i++){
                    WeatherCard(i, ".weatherWidgets", weatherapi);
                }
            }
        }
        function StartForecast(){
            $(".weatherWidgets").html("");

            for(let i = 0; i < forecastLimit; i++){
                WeatherCard(i, ".weatherWidgets", weatherapi);
            }
        }

        let currentLocationButton = $("#currentLocationButton");

        currentLocationButton.click(function(){
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition(function(position){
                    const geoLat = position.coords.latitude;
                    const geoLon = position.coords.longitude;

                    lattitudeInputE.val(geoLat);
                    longitudeInputE.val(geoLon);

                    isLocValueChanged = true;
                })
            }
            else{
                alert("Geolocation not supported")
            }
        })
    }

});