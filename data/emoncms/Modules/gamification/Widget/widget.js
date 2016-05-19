window.addEventListener("load", function () {
	list.data = user.get();
	
	addWidget();
	addDailyTip();
});

function addWidget() {
	var widget = document.getElementById("gamificationWidget");

	//-----------------Start of content-------------------
	var contentContainer = document.createElement("div");
	contentContainer.id = "ContentContainer";
	widget.appendChild(contentContainer);
	
	var url = "http://" + gamificationServerIP + "/php/retrieveHouseholdRanks.php?callback=?&household_id=" + list.data.id;

	$.ajax({
		type: "GET",
		url: url,
		contentType: 'application/json',
		dataType: 'jsonp',
		success: function(data, textStatus, jqXHR){			
			for (var i = 0; i < data.ranks.length; i++) {
				var rank = data.ranks[i];
				if (rank.percent >= 0.0 && rank.percent <= 1.0) {
					progressBar(rank.percent, rank.rank_id);
					break;
				}
			}
			
			addSeparatorLine();
			setValues3();
			addLeaderboard();
		}
	});	
}

function addSeparatorLine() {
	var line = document.createElement("div");
	line.id = "line";

	var sinLine = document.createElementNS("http://www.w3.org/2000/svg", "svg");
	sinLine.id = "Sline";

	var actLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
	actLine.id = "Aline";

	sinLine.appendChild(actLine);
	line.appendChild(sinLine);
	document.getElementById("ContentContainer").appendChild(line);
}

function setValues3() {
	//-----------------Start of content-------------------
	$('#ContentContainer').css('width', '100%');
	$('#ContentContainer').css('height', '100%').css('height', '-=' + $('#TC').outerHeight() + '');

	//-----------------Line between content-------------------
	document.getElementById("line").style.width = "100%";
	document.getElementById("line").style.height = "2px";

	//Image
	document.getElementById("Sline").style.width = "100%";
	document.getElementById("Sline").style.height = "100%";

	//Line
	document.getElementById("Aline").setAttribute("x1", "2%");
	document.getElementById("Aline").setAttribute("y1", "0");
	document.getElementById("Aline").setAttribute("x2", "98%");
	document.getElementById("Aline").setAttribute("y2", "0");
}


/*******************/
/*   WIDGET RANK   */
/*******************/

function progressBar(decimal, rank_id) {

    //Using the size from previous image
    var size = 110;

    //Not sure if this is working
    var url = path + "Modules/gamification/Artwork/Ranks/rank" + rank_id + ".png"

    var rank_content = document.createElement("div");
    rank_content.id = "widget-rank";

	document.getElementById("ContentContainer").appendChild(rank_content);

    $('#widget-rank').circleProgress({
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

    document.getElementById("widget-rank").appendChild(img);
}




/**********************/
/*   TIP OF THE DAY   */
/**********************/

function addDailyTip() {
	var url = "http://" + gamificationServerIP + "/php/retrieveTip.php?callback=?&household_id=" + list.data.id;
	
	$.ajax({
		type: "GET",
		url: url,
		contentType: 'application/json',
		dataType: "jsonp",
		success: function(data, textStatus, jqXHR) {
			$("#widget-tip").html(data["tip"]);
		}
	});	
}




/***************************/
/*   WIDGET LEADERBOARDS   */
/***************************/

// Fetches the data and calls insertLeaderboardData
function addLeaderboard() {
	var endDate = new Date();
	var startDate = new Date();
	startDate.setDate(startDate.getDate() - 30);
		
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
			insertLeaderboardData(data["data"]);
		}
	});
}

// Inserts a row with leaderboard data to the given table
function insertRow(table, isCurrentUser, position, name, score) {
	var row         = document.createElement("tr");
	var positionCol = document.createElement("td");
	var nameCol     = document.createElement("td");
	var scoreCol    = document.createElement("td");
	
	positionCol.innerHTML = position + ".";
	nameCol.innerHTML = name;
	scoreCol.innerHTML = numberWithThousandsSeparator(score);
	
	if (isCurrentUser) {
		positionCol.innerHTML = (position + ".").bold().big();
		nameCol.innerHTML = name.bold().big();
		scoreCol.innerHTML = numberWithThousandsSeparator(score).bold().big();
	}
	
	row.appendChild(positionCol);
	row.appendChild(nameCol);
	row.appendChild(scoreCol);
	
	table.appendChild(row);
}

// Takes an int, and returns the number as a string with a space to separate thousands
function numberWithThousandsSeparator(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

// Creates a table and adds it to the ContentContainer div
function insertLeaderboardData(json) {
	console.log(json);
	var currentUserIndex = json["currentHouseholdIndex"];
	var leaderboardJSON = json["leaderboard"];
	
	var leaderboardDiv = document.createElement("div");
	leaderboardDiv.id = "widget-leaderboard";
	
	var leaderboardTable = document.createElement("table");
	
	var userData = leaderboardJSON[currentUserIndex];
	
	if (currentUserIndex > 0) {
		var aboveData = leaderboardJSON[currentUserIndex - 1];
		insertRow(leaderboardTable, false, currentUserIndex , aboveData.username, aboveData.score);
	}
	
	insertRow(leaderboardTable, true, currentUserIndex + 1, userData.username, userData.score);
	
	if (currentUserIndex < leaderboardJSON.length - 1) {
		var belowData = leaderboardJSON[currentUserIndex + 1];
		insertRow(leaderboardTable, false, currentUserIndex + 2, belowData.username, belowData.score);
	}
	
	leaderboardDiv.appendChild(leaderboardTable);
	document.getElementById("ContentContainer").appendChild(leaderboardDiv);
}


