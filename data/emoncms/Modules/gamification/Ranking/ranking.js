
//When document is ready, populate the lists
$(document).ready( function() {
	list.data = user.get();
	
	addAchievements();
	refreshLeaderboard();
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



/****************/
/*   TOOLTIPS   */
/****************/


function setTooltips() {

        $("#leaderboardHelp").data("powertip", function(){

                var tooltip =   "Here's to the crazy ones. The misfits. The rebels. The troublemakers. The round pegs in the square holes. " + 
                "<br>The ones who see things differently. They're not fond of rules, and they have no respect for the status quo. You can quote " + 
                "<br>them, disagree with them. Glorify, or vilify them. About the only thing you can't do is ignore them. Because they change things. " +
                "<br>They push the human race forward. While some may see them as the crazy ones, we see genius. Because the people who are crazy " +
                "<br>enough to think they can change the world, are the ones who do.";

                    return tooltip;
                });
        $("#achievementsHelp").data("powertip", function(){
            var tooltip =   "Here's to the crazy ones. The misfits. The rebels. The troublemakers. The round pegs in the square holes. " + 
                "<br>The ones who see things differently. They're not fond of rules, and they have no respect for the status quo. You can quote " + 
                "<br>them, disagree with them. Glorify, or vilify them. About the only thing you can't do is ignore them. Because they change things. " +
                "<br>They push the human race forward. While some may see them as the crazy ones, we see genius. Because the people who are crazy " +
                "<br>enough to think they can change the world, are the ones who do."

            return tooltip;
        });

		$("#rankHelp").data("powertip", function(){
			var tooltip = "Here's to the crazy ones. The misfits. The rebels. The troublemakers. The round pegs in the square holes. " + 
                "<br>The ones who see things differently. They're not fond of rules, and they have no respect for the status quo. You can quote " + 
                "<br>them, disagree with them. Glorify, or vilify them. About the only thing you can't do is ignore them. Because they change things. " +
                "<br>They push the human race forward. While some may see them as the crazy ones, we see genius. Because the people who are crazy " +
                "<br>enough to think they can change the world, are the ones who do."
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
