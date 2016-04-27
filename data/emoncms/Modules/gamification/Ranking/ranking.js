var dummyAchievements = {
	"achievements": [
		{
			"achieved": true, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement1.png", 
			"title":    "Achievement #1", 
			"subtitle": "Congratulations, you have this achievement!"
		},
		{
			"achieved": true, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement2.png", 
			"title":    "Achievement #2", 
			"subtitle": "Congratulations, you have this achievement!"
		},
		{
			"achieved": true, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement3.png", 
			"title":    "Achievement #3", 
			"subtitle": "Congratulations, you have this achievement!"
		},
		{
			"achieved": true, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement4.png", 
			"title":    "Achievement #4", 
			"subtitle": "Congratulations, you have this achievement!"
		},
		{
			"achieved": true, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement1.png", 
			"title":    "Achievement #5", 
			"subtitle": "Congratulations, you have this achievement!"
		},
		{
			"achieved": true, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement2.png", 
			"title":    "Achievement #6", 
			"subtitle": "Congratulations, you have this achievement!"
		},
		{
			"achieved": false, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement5.png", 
			"title":    "Achievement #7", 
			"subtitle": "You gotta do better to get this achievement!"
		},
		{
			"achieved": false, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement6.png", 
			"title":    "Achievement #8", 
			"subtitle": "You gotta do better to get this achievement!"
		},
		{
			"achieved": false, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement7.png", 
			"title":    "Achievement #9", 
			"subtitle": "You gotta do better to get this achievement!"
		},
		{
			"achieved": false, 
			"src":      "Modules/gamification/Artwork/Achievements/achievement5.png", 
			"title":    "Achievement #10", 
			"subtitle": "You gotta do better to get this achievement!"
		}
	]
};

var dummyLeaderboardData = {
	"currentUserIndex": 5,
	"leaderboard": [
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile1.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank1.png",
			"name":			 "Larsen",
			"score":		 27839,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile2.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank2.png",
			"name":			 "Ødegård",
			"score":	     23189,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile3.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank3.png",
			"name":			 "Solvang",
			"score":		 21432,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile5.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank4.png",
			"name":			 "Jenssen",
			"score":		 21123,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile4.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank5.png",
			"name":			 "Pettersen",
			"score":		 20846,
			"isCurrentUser": true
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile6.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank6.png",
			"name":			 "Smith",
			"score":	     19283,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile5.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank4.png",
			"name":			 "Jenssen",
			"score":		 19026,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile4.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank5.png",
			"name":			 "Pettersen",
			"score":		 18672,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile6.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank6.png",
			"name":			 "Smith",
			"score":	     18267,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile1.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank1.png",
			"name":			 "Larsen",
			"score":		 18192,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile2.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank2.png",
			"name":			 "Ødegård",
			"score":	     16782,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile3.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank3.png",
			"name":			 "Solvang",
			"score":		 16256,
			"isCurrentUser": false
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile5.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank4.png",
			"name":			 "Jenssen",
			"score":		 10278,
			"isCurrentUser": false
		}
	]
};


//When document is ready, populate the lists
$(document).ready( function() {
	addAchievements(dummyAchievements);
	addLeaderboardData(dummyLeaderboardData);
	highlightPageLink();
});

function addAchievements(achievements) {
	/*
		EXAMPLE
		
		<div class="achievementElement">
			<img src="<?php echo $path; ?>Modules/gamification/Artwork/Achievements/achievement1.png" class="achievementIcon achieved">
			<h4>Achievement #1</h4>
			<p>Congratulations, you have this achievement!</p>
		</div>		
	*/
	
	var achievementsList = achievements["achievements"];
	
	for (var i = 0; i < achievementsList.length; i++) {
		var achievement = achievementsList[i];
		var achieved = achievement.achieved ? 'achieved' : 'notAchieved';
		
		var element = '<div class="achievementElement ' + achieved + '" style="background-color: rgba(255, 255, 255, 0.2);">';
		element += '<img src="' + path + achievement.src + '" class="achievementIcon">';
		element += '<h4>' + achievement.title + '</h4>';
		element += '<p>' + achievement.subtitle + '</p>';
		element += '</div>';
		$("#achievementsContainer").append(element);
	}
}

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
	
	leaderboardList = leaderboardData["leaderboard"];
		
	for (var i = 0; i < leaderboardList.length; i++) {
		var leaderboardItem = leaderboardList[i];
		var element = '<div class="leaderboardElement'; 
		element += i == leaderboardData["currentUserIndex"] ? ' currentUser">' : '">';
		element += '<div class="imageContainer">';
		element +=     '<img src="' + path + leaderboardItem.profilePicSrc + '" class="leaderboardProfilePic">';
		element +=     '<img src="' + path + leaderboardItem.rankPicSrc    + '" class="leaderboardProfileRank">';
		element += '</div>';
		element += '<span class="leaderboardName">' + (i + 1) + '. ' + leaderboardItem.name + '</span>';
		element += '<p class="leaderboardScore">' + numberWithThousandsSeparator(leaderboardItem.score) + '</p>';
		element += '</div>';
		$("#leaderboardListContainer").append(element);
	}
}


//function to find what page we are on and add the currentLink id to that navbar link to highlight current page
function highlightPageLink(){
	var a = document.getElementsByTagName("a");
    for(var i=0;i<a.length;i++){
        if(a[i].href.split("#")[0] == window.location.href.split("#")[0]){
            a[i].id = "currentLink";
        }
    }
}

// Takes an int, and returns the number as a string with a space to separate thousands
function numberWithThousandsSeparator(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function refreshLeaderboard() {
	var endTime = new Date();
	var startTime;
	
	if (document.getElementById("month-input").checked) {
		console.log("Fetch leaderboard for the last month.");
	} else if (document.getElementById("quarter-input").checked) {
		console.log("Fetch leaderboard for the last quarter.");
	} else if (document.getElementById("year-input").checked) {
		console.log("Fetch leaderboard for the last year.");
	} else {
		console.log("Unknown selection");
	}
	
	var url = "http://178.79.153.226/php/retrieveLeaderboards.php?leaderboard_mode=timed&start_date=2016-04-01&end_date=2016-05-01";
}

