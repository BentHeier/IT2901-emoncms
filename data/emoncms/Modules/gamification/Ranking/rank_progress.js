
$(document).ready( function() {
	list.data = user.get();
	fetchRanks()
});


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
