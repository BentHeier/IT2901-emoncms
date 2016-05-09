window.addEventListener("load", function () {
	list.data = user.get();
	
	addWidget();
	setValues3();
	fetchTip();
});

function fetchTip() {
/*
	var tips = [
		"Heating or cooling the whole house can be expensive. Where possible, shut doors to areas you are not using and only heat or cool the rooms you spend the most time in.", 
		"In winter, heating can account for over 30% of your bill. Set your thermostat between 18 and 20 degrees. Every degree above 20 can add 10% to your heating bill. In summer, set your thermostat to 26 degrees or above.",
		"Turn off when you leave the room, or go to bed. With some ducted heating systems you can turn off the heating in the rooms that are unoccupied. Make sure all your heating or cooling is turned off when you leave the house.",
		"You can save around $115 per year by washing clothes in cold water. You can also save by making sure you select the shortest appropriate washing cycle and waiting until you have a full load.",
		"Your fridge is always on, making it one of your most expensive appliances. Make sure the door seal is tight and free from gaps so cold air can't escape.",
		"Did you know your phone charger is still using energy even when your phone is not attached? Up to 10% of your electricity could be used by gadgets and appliances that are on standby.",
		"Replace old incandescent and halogen light globes with energy-efficient globes. Energy-efficient globes save power and last longer. Light globes can sometimes be replaced for free or at reduced cost.",
		"When you are cooking, use the microwave when you can – it uses much less energy than an electric oven. If you use the stove, keep lids on your pots to reduce cooking time."
	];
	var tip = tips[Math.floor(Math.random() * tips.length)];
*/
	
	var url = "http://178.79.153.226/php/retrieveTip.php?callback=?&household_id=" + list.data.id;
	
/*
	$.ajax({
		type: "GET",
		url: url,
		contentType: 'text/plain',
		success: tipCallback
	});
*/
	
	$.ajax({
		type: "GET",
		url: url,
		contentType: 'application/json',
		dataType: "jsonp",
		success: function(data, textStatus, jqXHR) {
			console.log(data);
			$("#widget-tip").html(tip);
		}
	});

/*
	$.ajax({
		type: "GET",
		url: url,
		contentType: 'text/plain',
		success: function(data, textStatus, jqXHR){
			console.log(data);
			addLeaderboardData(data["data"]);
		}
	});
*/
	
	
}

function tipCallback(tip) {
	console.log(tip);
	$("#widget-tip").html(tip);
}

function addWidget() {
	var widget = document.getElementById("gamificationWidget");

	//-----------------Start of content-------------------
	var contentContainer = document.createElement("div");
	contentContainer.id = "ContentContainer";

	//-----------------Single content-------------------
	var rankImage = document.createElement("img");
	rankImage.src = "http://178.79.153.226/images/rankImage.php";
	rankImage.alt = "Rank Image";
	rankImage.id = "rankImage";
	contentContainer.appendChild(rankImage);

	//-----------------Line between content-------------------
	var line = document.createElement("div");
	line.id = "line";

	var sinLine = document.createElementNS("http://www.w3.org/2000/svg", "svg");
	sinLine.id = "Sline";

	var actLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
	actLine.id = "Aline";

	sinLine.appendChild(actLine);
	line.appendChild(sinLine);
	contentContainer.appendChild(line);

	//-----------------Ranking-------------------
	var multiContent = document.createElement("div");
	multiContent.id = "MC";

	var table = document.createElement("table");
	table.id = "table";

	var row, col;

	//Make the arrays for dummy-table
	var array1 = ["17.", "Normann", "3672"];
	var array2 = ["18.", "Johnsen", "1278"];
	var array3 = ["19.", "Smith", "1254"];
	var mainArray = [array1, array2, array3];

	//Function to populate the table
	for (i = 0; i < mainArray.length; i++) {
		row = document.createElement("tr");
		for (j = 0; j < mainArray[i].length; j++) {
			col = document.createElement("td");
			if (i == 1) {
				col.innerHTML = mainArray[i][j].bold().big();
			} else {
				col.innerHTML = mainArray[i][j];
			}
			if (j == 1) {
				col.style.textAlign = "left";
				col.style.width = "50%";
			} else if (j == 0) {
				col.style.textAlign = "center";
				col.style.width = "20%";
			} else {
				col.style.textAlign = "center";
				col.style.width = "30%";
			}
			row.appendChild(col);
		}
		table.appendChild(row);
	}
	multiContent.appendChild(table);
	contentContainer.appendChild(multiContent);

	widget.appendChild(contentContainer);
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

	//-----------------Ranking-------------------
	document.getElementById("MC").style.width = "100%";
	document.getElementById("MC").style.height = "45%";
}
