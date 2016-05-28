
//When document is ready, populate the lists
$(document).ready( function() {
	list.data = user.get();
	
	addAchievements();
	refreshLeaderboard();
	fetchRanks();
	highlightPageLink();
	setTooltips();
});

//function to find what page we are on and add the currentLink id to that navbar link to highlight current page
function highlightPageLink(){
	var a = document.getElementsByTagName("a");
    for(var i=0;i<a.length;i++){
        if(a[i].href.split("#")[0] == window.location.href.split("#")[0]){
            a[i].id = "currentLink";
        }
    }
}



/********************/
/*   ACHIEVEMENTS   */
/********************/

// Fetches leaderboard data from the gamification server, and adds it to the DOM.
function addAchievements() {
	/*
		EXAMPLE
		
		<div class="achievementElement">
			<img src="<?php echo $path; ?>Modules/gamification/Artwork/Achievements/achievement1.png" class="achievementIcon achieved">
			<h4>Achievement #1</h4>
			<p>Congratulations, you have this achievement!</p>
		</div>
	*/
	
	var url = "http://" + gamificationServerIP + "/php/retrieveHouseholdAchievements.php?callback=?&household_id=" + list.data.id;
	
	$.ajax({
		type: "GET",
		url: url,
		contentType: 'application/json',
		dataType: 'jsonp',
		success: function(data, textStatus, jqXHR){
			var achievementsList = data["data"];
			
			for (var i = 0; i < achievementsList.length; i++) {
				var achievement = achievementsList[i];
				var achieved = achievement.achieved == "1" ? 'achieved' : 'notAchieved';
				
				var element = '<div class="achievementElement ' + achieved + '" style="background-color: rgba(255, 255, 255, 0.2);">';
				element += '<img src="' + path + "Modules/gamification/Artwork/Achievements/achievement" + (parseInt(achievement.achievement_id) + 1) + ".jpg" + '" class="achievementIcon">';
				element += '<h4>' + achievement.achievement_name + '</h4>';
				element += '<p>' + achievement.description + '</p>';
				element += '</div>';
				$("#achievementsContainer").append(element);
			}
		}
	});
}




/*******************/
/*   LEADERBOARD   */
/*******************/

// Removes the previous leaderboard from the DOM, and adds the new data from the passed in JSON to the DOM.
function addLeaderboardData(leaderboardData) {
	/*
		EXAMPLE
		
		<div class="leaderboardElement">
			<div class="imageContainer">
				<img src="<?php echo $path; ?>Modules/gamification/Artwork/Leaderboards/profile1.png" class="leaderboardProfilePic">
				<img src="<?php echo $path; ?>Modules/gamification/Artwork/Ranks/rank1.png" class="leaderboardProfileRank">
			</div>
			<span class="leaderboardName">1. Nordmann</span>
			<p class="leaderboardScore">27 839</p>
		</div>
	*/
	
	// Remove the previous leaderboard from the DOM
	var leaderboardContainer = document.getElementById("leaderboardListContainer");
	while (leaderboardContainer.firstChild) {
	    leaderboardContainer.removeChild(leaderboardContainer.firstChild);
	}
	
	leaderboardList = leaderboardData["leaderboard"];

	for (var i = 0; i < leaderboardList.length; i++) {
		var leaderboardItem = leaderboardList[i];
		var element = '<div class="leaderboardElement'; 
		element += i == leaderboardData["currentHouseholdIndex"] ? ' currentUser">' : '">';
		element += '<div class="imageContainer">';
		element +=     '<img src="http://www.gravatar.com/avatar/' + leaderboardItem.email_hash + '?s=256" class="leaderboardProfilePic">';
		element +=     '<img src="' + path + "Modules/gamification/Artwork/Ranks/rank" + leaderboardItem["rank_id"] + ".png" + '" class="leaderboardProfileRank">';
		element += '</div>';
		element += '<span class="leaderboardName">' + (i + 1) + '. ' + leaderboardItem.username + '</span>';
		element += '<p class="leaderboardScore">' + numberWithThousandsSeparator(leaderboardItem.score) + '</p>';
		element += '</div>';
		$("#leaderboardListContainer").append(element);
	}

}

// Takes an int, and returns the number as a string with a space to separate thousands
function numberWithThousandsSeparator(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

// Fetches new leaderboard data from the gamification server based on the currently selected time frame. It calls addLeaderboardData when it's done.
function refreshLeaderboard() {
	var endDate = new Date();
	var startDate = new Date(); // This will change below
		
	if 		(document.getElementById("month-input")  .checked) { startDate.setDate(startDate.getDate() - 30);  } 
	else if (document.getElementById("quarter-input").checked) { startDate.setDate(startDate.getDate() - 90);  } 
	else if (document.getElementById("year-input")   .checked) { startDate.setDate(startDate.getDate() - 365); }
		
	// Dates are in the format yyyy-mm-dd
	var endString   =   endDate.getFullYear() + "-" + ("0" + (  endDate.getMonth() + 1)).slice(-2) + "-" + ("0" +   endDate.getDate()).slice(-2);
	var startString = startDate.getFullYear() + "-" + ("0" + (startDate.getMonth() + 1)).slice(-2) + "-" + ("0" + startDate.getDate()).slice(-2);
	var url = "http://" + gamificationServerIP + "/php/retrieveLeaderboards.php?callback=?&household_id=" + list.data.id + "&leaderboard_mode=timed&start_date=" + startString + "&end_date=" + endString;

	$.ajax({
		type: "GET",
		url: url,
		contentType: 'application/json',
		dataType: 'jsonp',
		success: function(data, textStatus, jqXHR){
			addLeaderboardData(data["data"]);
		}
	});
}




/***************/
/*   RANKING   */
/***************/

// Fetches ranks data from the gamification server. Calls popRanks with the JSON it received.
function fetchRanks() {
	var url = "http://" + gamificationServerIP + "/php/retrieveHouseholdRanks.php?callback=?&household_id=" + list.data.id ;
	
	$.ajax({
		type: "GET",
		url: url,
		contentType: 'application/json',
		dataType: 'jsonp',
		success: function(data, textStatus, jqXHR){
			popRanks(data);
		}
	});
}

// Add the rank circles to the DOM based on the JSON passed in.
function popRanks(ranks) {
    var rankList = ranks["ranks"];
    for (var i = -1; i < rankList.length; i++) {
        var div = document.createElement("div");
        div.id = "div_" + (i + 1);
        div.className = "circle rank-image";
        /*div.width = $('#rankContainer').outerHeight()*0.5 + '';
        div.height = $('#rankContainer').outerHeight()*0.5 + '';*/

		var boxSize = 300;
		var scale = 0.45;

		div.width = boxSize * scale + '';
		div.height = 150;
        div.padding = 5;
        
        $('#rankContainer').append(div);
        
        if (i < 0) {
            progressBar(boxSize * scale, 1, div.id, path + "Modules/gamification/Artwork/Ranks/rank1.png");
        } else {
            var rank = rankList[i];
            if (rank.percent < 0) {
                div.className += " unfinished";
            } else if (rank.percent > 0 && rank.percent < 1) {
                div.width += 50;
                div.height += 50;
            }
            progressBar(boxSize * scale, rank.percent, div.id, path + "Modules/gamification/Artwork/Ranks/rank" + rank.rank_id + ".png");
        }
    }
}

//Function that needs four values
//  "size" - Diameter of the circle and size of container
//  "decimal" - How much the specified circle is filled
//  "div" - Id of the div container used
//  "url" - location of the image file used in the center
function progressBar(size, decimal, div, url) {
    //Failswitch for decimal value
    if (decimal > 1) {
        var decimal = 1;
    } else if (decimal < 0) {
        var decimal = 0;
    }
    $('#' + div).circleProgress({
        value: decimal,
        size: (size + 20),
        startAngle: 1.5 * Math.PI,
        animation: false,
        emptyFill: "#bebebe",
        fill: {
            color: "#58B31F"
        }
    });

    var img = document.createElement('IMG');
    img.src = url;
    img.width = (size + 10);
    img.height = (size + 10);

    $('#' + div).append(img);

}



/****************/
/*   TOOLTIPS   */
/****************/

// Adds hover info to the "?" buttons. This is currently dummy data, and should be replaced with something more appropriate
function setTooltips() {

    $("#leaderboardHelp").data("powertip", function(){

        var tooltip =   "The leaderboards display the current household leaders for the specific month, quarter and year. " + 
        			"<br>Together with the rank that you have achieved over the course of joining CoSSMunity. ";

            return tooltip;
        });
    $("#achievementsHelp").data("powertip", function(){
        var tooltip =   "Being a part of CoSSMunity earns you achievements as you perform various energy saving operations. " + 
            		"<br>The title and description of the achievments describe what you need to accomplish to earn that achievement. ";

        return tooltip;
    });

	$("#rankHelp").data("powertip", function(){
		var tooltip =	"Points earned by being a part of CoSSMunity increases your rank. " + 
            	  	"<br>Your current progress is displayed below.";
		return tooltip;
	});

    $("#leaderboardHelp").powerTip({
        placement: "se",
        mouseOnToPopup:true
    });

    $("#achievementsHelp").powerTip({
        placement: "se",
        mouseOnToPopup: true
    });

	$("#rankHelp").powerTip({
		placement: "se",
		mouseOnToPopup: true
	});
}