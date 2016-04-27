// Docs at http://simpleweatherjs.com

/* Where in the world are you? */
/*$(document).ready(function() {
  navigator.geolocation.getCurrentPosition(function(position) {
    loadWeather(position.coords.latitude+','+position.coords.longitude); //load weather using your lat/lng coordinates
  });
  summarySetup();
}); */

function loadWeather(location, woeid) {
  $.simpleWeather({
    location: location,
    woeid: woeid,
    unit: 'c',
    success: function(weather) {
		var wcode;
		var day;
		var forecast = [];
		
		//Setting data for the weather forecast. 0 is the default view, 1-5 is for the 5-day extended forecast
		for(i=0;i<6;i++){
			if(i==0){
				if(weather.todayCode == '3200'){
					day = '<br /><br /><img src="../../Modules/cossmiccontrol/Views/simpleweather-geolocation-js/errorWeatherIcon.png" /><br /><br />';
				}
				else{
					day = '<i class="icn-'+weather.todayCode+'"></i><br />';
				}
				day += '<h2>'+weather.temp+'&deg;C</h2><br />';
				day += '<ul><li>Estimated PV efficiency: '+pvProd(weather.code)+'%</li></ul><br />'; 
				day += '<a href="https://www.yahoo.com/?ilc=401" target="_blank"> <img src="https://poweredby.yahoo.com/white.png" width="100" height="21"/> </a>'
				
				forecast[i] = day;
			}
			else{
				if(i==1){
					day = '<h3>TODAY</h3>';
				}
				else{
					day = '<h3>'+weather.forecast[i-1].day+'</h3>';
				}
				if(weather.forecast[i-1].code == '3200'){
					day += '<br /><br /><img src="../../Modules/cossmiccontrol/Views/simpleweather-geolocation-js/errorWeatherIcon.png" /><br /><br />';
				}
				else{
					day += '<i class="icn-'+weather.forecast[i-1].code+'"></i><br />';
				}
				day += '<h3>max '+weather.forecast[i-1].high+'&deg;C</h3>';
        day += '<h3>min '+weather.forecast[i-1].low+'&deg;C</h3>';
				//day += '<a href="scheduler"><ul><li>Estimated PV efficiency: '+pvProd(weather.forecast[i-1].code)+'%</li></ul></a><br />';
				
				forecast[i] = day;
			}
		}
		
		//Injecting the weather data into the page elements.
		for(i=0; i<6; i++){
			if(i==0)
				$("#weather").html(forecast[i]);
			if(i==1)
				$("#weather1").html(forecast[i]);
			if(i==2)
				$("#weather2").html(forecast[i]);
			if(i==3)
				$("#weather3").html(forecast[i]);
			if(i==4)
				$("#weather4").html(forecast[i]);
			if(i==5)
				$("#weather5").html(forecast[i]);
		}
    },
    error: function(error) {
		$("#weather").html('<p>'+error+'</p>');
		$("#weather1").html('<p>'+error+'</p>');
		$("#weather2").html('<p>'+error+'</p>');
		$("#weather3").html('<p>'+error+'</p>');
		$("#weather4").html('<p>'+error+'</p>');
		$("#weather5").html('<p>'+error+'</p>');
    }
  });
}

//Fires if the geolocation fails.
function errorWeather(location){
	if(location == ""){
		err = '<h3>Location unavailable</h3></ br>';
		err += '<h4>Please set location</h4>';
		err += '<h4>in your profile</h4>';
		err += '<h4>(Account)</h4>';
		$("#weather").html(err);
	}
	else{
		loadWeather(location);
	}
}

//Checks weather code and gives a rough estimate in percent for PV efficiency.
//These values aren't grounded in any science, just a guesstimate so might want to fine-tune a bit....

/* Yahoo weather codes:
0 - Tornado
1 - Tropical Storm
2 - Hurricane
3 - Severe Thunderstorms
4 - Thunderstorms
5 - Mixed Rain and Snow
6 - Mixed Rain and Sleet
7 - Mixed Snow and Sleet
8 - Freezing Drizzle
9 - Drizzle
10 - Freezing Rain
11 - Showers
12 - Showers
13 - Snow Flurries
14 - Light Snow Showers
15 - Blowing Snow
16 - Snow
17 - Hail
18 - Sleet
19 - Dust
20 - Foggy
21 - Haze
22 - Smoky
23 - Blustery
24 - Windy
25 - Cold
26 - Cloudy
27 - Mostly Cloudy (night)
28 - Mostly Cloudy (day)
29 - Partly Cloudy (night)
30 - Partly Cloudy (day)
31 - Clear (night)
32 - Sunny
33 - Fair (night)
34 - Fair (day)
35 - Mixed Rain and Hail
36 - Hot
37 - Isolated Thunderstorms
38 - Scattered Thunderstorms
39 - Scattered Thunderstorms
40 - Scattered Showers
41 - Heavy Snow
42 - Scattered Snow Showers
43 - Heavy Snow
44 - Partly Cloudy
45 - Thundershowers
46 - Snow Showers
47 - Isolated Thundershowers
3200 - Not Available
*/

function pvProd(code){
	if(code == 41 || code == 43 || code == 42 || code == 46 || code == 45 || code == 47 || code == 5 || code == 6 || code == 7 || code == 27 || code == 29 || code == 31 || code == 33)
		return 10;
	else if(code == 0 || code == 1 || code == 2 || code == 3 || code == 4 || code == 8 || code == 10 || code == 13 || code == 14)
		return 20;
	else if(code == 9 || code == 15 || code == 16 || code == 28 || code == 37 || code == 38 || code == 39 || code == 40 || code == 42)
		return 30;
	else if(code == 17 || code == 18 || code == 35)
		return 40;
	else if(code == 19 || code == 21 || code == 22)
		return 50;
	else if(code == 11 || code == 12 || code == 23)
		return 60;
	else if(code == 26)
		return 70;
	else if(code == 20)
		return 80;
	else if(code == 30 || code == 44)
		return 90;
	else if(code == 36 || code == 32 || code == 34 || code == 24 || code == 25)
		return 100;
	else
		return "unknown";
}