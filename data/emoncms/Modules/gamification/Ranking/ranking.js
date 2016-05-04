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
	"currentUserIndex": 2,
	"leaderboard": [
		{
			"email_hash": "6f64fce01ffa86130c3fae06b1fcadef",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank1.png",
			"username"  : "Larsen",
			"score"     : 27839
		},
		{
			"email_hash": "205e460b479e2e5b48aec07710c08d50",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank2.png",
			"username":	  "Ødegård",
			"score":	  23189
		},
		{
			"email_hash": "17239e25b62e838eb4340418a8c0d4ae",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank3.png",
			"username":	  "Theriault",
			"score":	  21432
		},
		{
			"email_hash": "abc7768058e25c1382df18414db72a10",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank4.png",
			"username":	  "Jenssen",
			"score":	  21123
		},
		{
			"email_hash": "6ac00e047724e6fbed304a083bc8234f",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank5.png",
			"username":	  "Pettersen",
			"score":	  20846
		},
		{
			"email_hash": "6e42063400331743a848740f8827c90e",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank6.png",
			"username":	  "Smith",
			"score":	  19283
		},
		{
			"email_hash": "87c1c7daf06754ee8653cd84efda14ab",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank4.png",
			"username":	  "Jenssen",
			"score":	  19026
		},
		{
			"email_hash": "ec9385dc533f1a6a93769077f852503e",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank5.png",
			"username":	  "Pettersen",
			"score":	  18672
		},
		{
			"email_hash": "61ba7c5f62eeace4d02d3f643f99a2d1",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank6.png",
			"username":	  "Smith",
			"score":	  18267
		},
		{
			"email_hash": "157c96de3d125abc1c363ff25c20dfaf",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank1.png",
			"username":	  "Larsen",
			"score":	  18192
		},
		{
			"email_hash": "04798ba8ef6591a9c23c5868dc0bf664",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank2.png",
			"username":	  "Ødegård",
			"score":	  16782
		},
		{
			"email_hash": "ca71042d9d6724f4cbc2598195662eb0",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank3.png",
			"username":	  "Solvang",
			"score":	  16256
		},
		{
			"email_hash": "7feab598abfc27783641fe2b796a0155",
			"rankPicSrc": "Modules/gamification/Artwork/Ranks/rank4.png",
			"username":	  "Jenssen",
			"score":	  10278
		}
	]
};


//When document is ready, populate the lists
$(document).ready( function() {
	//list.data = user.get()
// 	console.log(list.data);
	
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
	
	// Remove the previous leaderboard from the DOM
	var leaderboardContainer = document.getElementById("leaderboardListContainer");
	while (leaderboardContainer.firstChild) {
	    leaderboardContainer.removeChild(leaderboardContainer.firstChild);
	}
	
	leaderboardList = leaderboardData["leaderboard"];

	for (var i = 0; i < leaderboardList.length; i++) {
		var leaderboardItem = leaderboardList[i];
		var element = '<div class="leaderboardElement'; 
		element += i == leaderboardData["currentUserIndex"] ? ' currentUser">' : '">';
		element += '<div class="imageContainer">';
		element +=     '<img src="http://www.gravatar.com/avatar/' + leaderboardItem.email_hash + '?s=256" class="leaderboardProfilePic">';
		element +=     '<img src="' + path + leaderboardItem.rankPicSrc    + '" class="leaderboardProfileRank">';
		element += '</div>';
		element += '<span class="leaderboardName">' + (i + 1) + '. ' + leaderboardItem.username + '</span>';
		element += '<p class="leaderboardScore">' + numberWithThousandsSeparator(leaderboardItem.score) + '</p>';
		element += '</div>';
		$("#leaderboardListContainer").append(element);
	}
		
/*
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
*/
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
	var endDate = new Date();
	var startDate = new Date(); // This will change below
		
	if 		(document.getElementById("month-input")  .checked) { startDate.setDate(startDate.getDate() - 30);  } 
	else if (document.getElementById("quarter-input").checked) { startDate.setDate(startDate.getDate() - 90);  } 
	else if (document.getElementById("year-input")   .checked) { startDate.setDate(startDate.getDate() - 365); }
	
	// Dates are in the format yyyy-mm-dd
	var endString   =   endDate.getFullYear() + "-" + ("0" + (  endDate.getMonth() + 1)).slice(-2) + "-" + ("0" +   endDate.getDate()).slice(-2);
	var startString = startDate.getFullYear() + "-" + ("0" + (startDate.getMonth() + 1)).slice(-2) + "-" + ("0" + startDate.getDate()).slice(-2);
	var url = "http://178.79.153.226/php/retrieveLeaderboards.php?callback=?&leaderboard_mode=timed&start_date=" + startString + "&end_date=" + endString;

	$.ajax({
		type: "GET",
		url: url,
		contentType: 'application/json',
		dataType: 'jsonp',
		success: function(data, textStatus, jqXHR){
			console.log(data);
		}
	});
}





/*
var dummyLeaderboardData = {
	"currentUserIndex": 2,
	"leaderboard": [
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile1.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank1.png",
			"name":			 "Larsen",
			"score":		 27839,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile2.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank2.png",
			"name":			 "Ødegård",
			"score":	     23189,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile3.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank3.png",
			"name":			 "Solvang",
			"score":		 21432,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile5.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank4.png",
			"name":			 "Jenssen",
			"score":		 21123,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile4.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank5.png",
			"name":			 "Pettersen",
			"score":		 20846,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile6.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank6.png",
			"name":			 "Smith",
			"score":	     19283,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile5.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank4.png",
			"name":			 "Jenssen",
			"score":		 19026,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile4.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank5.png",
			"name":			 "Pettersen",
			"score":		 18672,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile6.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank6.png",
			"name":			 "Smith",
			"score":	     18267,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile1.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank1.png",
			"name":			 "Larsen",
			"score":		 18192,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile2.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank2.png",
			"name":			 "Ødegård",
			"score":	     16782,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile3.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank3.png",
			"name":			 "Solvang",
			"score":		 16256,
		},
		{
			"profilePicSrc": "Modules/gamification/Artwork/Leaderboards/profile5.png",
			"rankPicSrc":	 "Modules/gamification/Artwork/Ranks/rank4.png",
			"name":			 "Jenssen",
			"score":		 10278,
		}
	]
};
*/