window.addEventListener("load", function () {
	addWidget();
	setValues3();
});

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
