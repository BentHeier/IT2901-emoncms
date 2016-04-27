// get current value of a feed
function get_feedvalue(feedID) {
    var feedValue;
    var query = "&id="+feedID;
   
    $.ajax({
	url: path+'feed/value.json',
	data: query,  
	dataType: 'json',
	async: false,
	success: function(datain) { feedValue = datain; }
    });
    return feedValue;
}

// get feed data for a given feed id
function get_feed_data(feedID,start,end,dp) {
    var feedIn = [];
    var query = "&id="+feedID+"&start="+start+"&end="+end+"&dp="+dp;
   
    $.ajax({
	url: path+'feed/data.json',
	data: query,  
	dataType: 'json',
	async: false,
	success: function(datain) { feedIn = datain; }
    });
    return feedIn;
}

// get time of current value of a feed (in seconds)
function get_timevalue(feedID) {
    var feedTime;
    var query = "&id="+feedID;
   
    $.ajax({
	url: path+'feed/timevalue.json',
	data: query,  
	dataType: 'json',
	async: false,
	success: function(datain) { feedTime = datain; }
    });
    return feedTime.time;
}
