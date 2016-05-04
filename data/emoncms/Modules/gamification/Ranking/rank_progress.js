var dummyRanks = {
    "ranks": [
		{
		    "percent": "2.3438",
		    "rank_id": "2",
		},
		{
		    "percent": "1.3438",
		    "rank_id": "3",
		},
        {
            "percent": "0.1719",
            "rank_id": "4",
        },
        {
            "percent": "-0.8281",
            "rank_id": "5",
        },
        {
            "percent": "-1.2188",
            "rank_id": "6",
        }
    ]
};

$(document).ready( function() {
    popRanks(dummyRanks);
});

function popRanks(ranks) {
    var rankList = ranks["ranks"];
    for (var i = -1; i < rankList.length; i++) {
        var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.id = "svg_"+i;
/*
        svg.style.width = $('#rankContainer').outerHeight()*0.8 + '';
        svg.style.height = $('#rankContainer').outerHeight()*0.8 + '';
        if (i < 0) {
            progressBar(svg.clientWidth, 1, svg, 1, path + "Modules/gamification/Artwork/Ranks/rank"+1+".png");
        } else {
            var rank = rankList[i];
            progressBar(svg.clientWidth, rank.percent, svg, rank.rank_id, path + "Modules/gamification/Artwork/Ranks/rank" + rank.rank_id + ".png");
        }
*/

		svg.style.width = 300*0.8 + '';
        svg.style.height = 300*0.8 + '';

        if (i < 0) {
            progressBar(300*0.8, 1, svg, 1, path + "Modules/gamification/Artwork/Ranks/rank"+1+".png");
        } else {
            var rank = rankList[i];
            progressBar(300*0.8, rank.percent, svg, rank.rank_id, path + "Modules/gamification/Artwork/Ranks/rank" + rank.rank_id + ".png");
        }
        $('#rankContainer').append(svg);
    }
}

//Function that needs five values
//  "size" - Diameter of the circle and size of container
//  "decimal" - How much the specified circle is filled
//  "svg" - Id of the svg container used
//  "level" - Number in image path (may be changed)
//  "url" - location of the image file used in the center
function progressBar(size, decimal, svg, level, url) {
	console.log(size);
	console.log(decimal);
	console.log(svg);
	console.log(level);
	console.log(url);
    //Failswitch for decimal value
    if (decimal > 1) {
        var decimal = 1;
    } else if (decimal < 0) {
        var decimal = 0;
    }
    //creating first circle
    var circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
    circle.setAttribute("cx", size * 0.5);
    circle.setAttribute("cy", size * 0.5);
    circle.setAttribute("r", size * 0.5);
    //Making paths for how much the circle is filled
    var path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    var path2 = document.createElementNS("http://www.w3.org/2000/svg", "path");
    var x = (size * 0.5) + ((size * 0.5) * (Math.cos((2 * Math.PI) * ((decimal - 0.25)))));
    var y = size * 0.5 + ((size * 0.5) * (Math.sin((2 * Math.PI) * ((decimal - 0.25)))));
    if (x < (size * 0.5)) {
        path.setAttribute("d", "M" + size * 0.5 + "," + size * 0.5 + " L" + size * 0.5 + "," + 0 + " A" + size * 0.5 + "," + size * 0.5 + " 1 0,1 " + size * 0.5 + "," + (size * 0.5 + size * 0.5) + " z");
        path2.setAttribute("d", "M" + size * 0.5 + "," + size * 0.5 + " L" + size * 0.5 + "," + size + " A" + size * 0.5 + "," + size * 0.5 + " 1 0,1 " + x + "," + y + " z");
    } else {
        path.setAttribute("d", "M" + size * 0.5 + "," + size * 0.5 + " L" + size * 0.5 + "," + 0 + " A" + size * 0.5 + "," + size * 0.5 + " 1 0,1 " + x + "," + y + " z");
    }

    //calculating values for image positioning
    var innerSize = size * 0.9;
    var img = document.createElement('img');
    img.src = url;
    var widthScale = ((innerSize / img.width) * 100);
    var heightScale = ((innerSize / img.height) * 100);
    var locX = (size - (img.width * (widthScale / 100))) / 2;
    var locY = (size - (img.height * (heightScale / 100))) / 2;

    //setting values for pattern
    var pattern = document.createElementNS("http://www.w3.org/2000/svg", 'pattern');
    pattern.setAttribute('id', 'img_' + svg);
    pattern.setAttribute('patternUnits', 'userSpaceOnUse');
    pattern.setAttribute('width', "100%");
    pattern.setAttribute('height', "100%");

    //making image with calculated values
    var image = document.createElementNS("http://www.w3.org/2000/svg", 'image');
    image.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', url);
    image.setAttribute('x', locX);
    image.setAttribute('y', locY);
    image.setAttribute('width', img.width * (widthScale / 100));
    image.setAttribute('height', img.height * (heightScale / 100));

    var defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');

    pattern.appendChild(image);
    defs.appendChild(pattern);

    //making the second circle
    var circleImage = document.createElementNS("http://www.w3.org/2000/svg", "circle");
    circleImage.setAttribute("cx", size * 0.5);
    circleImage.setAttribute("cy", size * 0.5);
    circleImage.setAttribute("r", innerSize * 0.5);

    //styling some elements
    circle.style.fill = "#bebebe";
    path.style.fill = "#58B31F";
    path2.style.fill = "#58B31F";
    circleImage.style.fill = "url(#img_" + svg + ")";

    //creating a hover-message for the inner svg
    var popup = document.createElementNS("http://www.w3.org/2000/svg", 'title');
    if (decimal == 1) {
        popup.textContent = "Level " + level + "\nYou have completed this level.";
    } else if (decimal == 0) {
        popup.textContent = "Level " + level + "\nYou haven't reached this level yet.";
    } else {
        popup.textContent = "Level " + level + "\nYou are " + parseInt(decimal * 100) + "% of the way to the next level.";
    }
    circleImage.appendChild(popup);

    svg.appendChild(defs);
    svg.appendChild(circle);
    svg.appendChild(path);
    svg.appendChild(path2);
    svg.appendChild(circleImage);

}