// the code checking showxaxis as to not always show the numbers on the X axis was broken. The numbers would not appear for any graph
// it has been commented now and as a consequence we have the values for both upper and lower graph


// line colors for devices graphs
var colors =  ["#000000", "#FF0000", "#00FF00", "#0000FF", "#FFFF00"
, "#00FFFF", "#FF00FF", "#333300", "#FF6600", "#669900"
, "#660066", "#FFCC66", "#CC99FF", "#66FFCC", "#669999"
, "#CCFFFF", "#666699", "#99FF99", "#990000", "#CCFFFF"];
// constant variables delimiting plotting options for cg (consumption/generation) graphs
var optionsHash = new Object();
optionsHash["day"] = {
            grid: { show: true, hoverable: false, clickable: false, color: "#fff", backgroundColor: "#1192d3", tickColor: "#fff" },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "hour"], timeformat: "%H", labelHeight:15, reserveSpace: true, font: {color: "#fff"} },
            yaxis: { min: 0, font: {color: "#fff"}},
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: true }
    	};
optionsHash["month"] = {
            grid: { show: true, hoverable: false, clickable: false, color: "#fff", backgroundColor: "#1192d3", tickColor: "#fff" },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "day"], timeformat: "%e", labelHeight:15, reserveSpace: true, font: {color: "#fff"}  },
            yaxis: { min: 0, font: {color: "#fff"}},
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: false },
	    bars: { show: true, align: "center", fill: false, barWidth: 3600*1000*12} // 1h *12, so that each bar space is equal to 1/2 day
    	};
optionsHash["year"] = {
            grid: { show: true, hoverable: false, clickable: false, color: "#fff", backgroundColor: "#1192d3", tickColor: "#fff" },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "month"], timeformat: "%b",labelHeight:15, reserveSpace: true, font: {color: "#fff"}  },
            yaxis: { min: 0, font: {color: "#fff"}},
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: false },
	    bars: { show: true, align: "center", fill: false, barWidth: 3600*1000*24*15}   // 1 day * 15
    	};
optionsHash["total"] = {
            grid: { show: true, hoverable: false, clickable: false, color: "#fff", backgroundColor: "#1192d3", tickColor: "#fff" },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "year"], timeformat: "%Y",  labelHeight:15, reserveSpace: true, font: {color: "#fff"}  },
            yaxis: { min: 0, font: {color: "#fff"}},
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: false },
	    bars: { show: true, align: "center", fill: false, barWidth: 3600*1000*24*30*6}  // 1 month * 6
    	};

// create year dropdown selection menu
function create_year_dropdown(element_id, first_year, current_year) {
    year_dropdown = "";
    for (i = first_year; i <= current_year; i++) {
	year_dropdown = year_dropdown + "<option value='"+i+"'>"+i+"</option>";
    }
    $(element_id).append(year_dropdown);
}

// format the date to output
function ds_format_date(d, m, y) {
    // 2 digits month.
    m2 = '00' + m;
    m2 = m2.substr(m2.length - 2);
    // 2 digits day.
    d2 = '00' + d;
    d2 = d2.substr(d2.length - 2);
    // MM/DD/YYYY
    return m2 + '/' + d2 + '/' + y;
}

// multiply y-values in [x,y]-array by a scalar
function scalarMultiplyY(arr, multiplier) {
    for (var i = 0; i < arr.length; i++) {
        arr[i][1] *= multiplier;
    }
}

// returns true if leap year, false otherwise
function isLeapYear(year) {
    return (((year%4==0) && (year%100!=0)) || (year%400==0));
}

// returns number of days in a given month for a given year
function days_in_month(month,year) {
    month = parseInt(month);
    year = parseInt(year);
    var days;
    switch (month) {
        case 0: case 2: case 4: case 6: case 7: case 9: case 11:
            days = 31;
            break;
        case 1:
            days = isLeapYear(year)?29:28;
            break;
        case 3: case 5: case 8: case 10:
            days = 30;
            break;
    }
    return days;
}

// convert feed list to plot list (devices plot)
function convert_to_plotlist(feedlist) {
    var plotlist = [];
    for (var i in feedlist){
        plotlist[i] = {
            id: feedlist[i]['feedid'],
            plot: {
                data: null,
                label: feedlist[i]['devicename']
            }
         };
    }
    return plotlist;
}

// create plot list (consumption/generation plot)
function create_plotlist(feedid1, feedid2, feedid3) {
    var plotlist = [];
    plotlist[0] = {
	id: feedid1,
	plot: {
	    data: null,
	    lines: { fill: true },
	    bars: {fill: true },
	    stack: true
	}
    };
    plotlist[1] = {
        id: feedid2,
	plot: {
	    data: null,
	    lines: { fill: true },
	    bars: {fill: true },
	    stack: true
	}
    };
    plotlist[2] = {
	id: feedid3,
	plot: {
	    data: null
	}
    };
    return plotlist;
}

// get data to be plotted
function get_plotdata_mode(plotlist, start, end, mode) {
    for(var i in plotlist) {
        if (timeWindowChanged)
        {
            plotlist[i].plot.data = null;
        }
        if (!plotlist[i].plot.data)
        {
    	    // power (day)
    	    if (mode == "day") plotlist[i].plot.data = get_feed_data(plotlist[i].id, start, end, 400);
    	    // histogram with kWh/day (month)
        	if (mode == "month") {
        		plotlist[i].plot.data = get_feed_data(plotlist[i].id, start, end, 400);
                if(null != plotlist[i].plot.data && ( (plotlist[i].plot.data) instanceof Array )){// added the array check to avoid error Thomas
            		// kWh for the current day
            		var kwhtoday = [];
            		kwhtoday[0] = new Date().setHours(0,0,0,0);
            		kwhtoday[1] = get_feedvalue(plotlist[i].id);
            		plotlist[i].plot.data.push(kwhtoday);
                }
        	}
    	    // histogram with kWh summed up for each month
    	    else if (mode == "year") {
        		plotlist[i].plot.data = [];
        		var data = get_feed_data(plotlist[i].id, start, end, end-start);
        		var d = new Date();
        		var month = 0, year = 0, lmonth, lyear;
        		var sum = 0, j = 0;
        	    	for (var z in data) {
            		    lmonth = month;
            		    lyear = year;
            		    d.setTime(data[z][0]);
            		    month = d.getMonth(); year = d.getFullYear();
            		    if (month!=lmonth && z!=0) {
                			var tmp = [];
                			tmp[0] = Date.UTC(lyear,lmonth,1);
                			tmp[1] = sum;
                			plotlist[i].plot.data[j] = tmp;
                      			j++;
                			sum = 0;
            		    }
            		    sum += parseFloat(data[z][1]);
        		    }
        		var tmp = [];
        		tmp[0] = Date.UTC(year,month,1);
        		tmp[1] = sum;
        		plotlist[i].plot.data[j] = tmp;
        		// kWh for the current day
        		var today = new Date();
        		if (today.getMonth() == month) plotlist[i].plot.data[j][1] += parseFloat(get_feedvalue(plotlist[i].id));
        		else {
        		    var kwhtoday = [];
        		    kwhtoday[0] = new Date().setHours(0,0,0,0);
        		    kwhtoday[1] = get_feedvalue(plotlist[i].id);
        		    plotlist[i].plot.data.push(kwhtoday);
        		} 
    	    }
    	    // histogram with kWh summed up for each year
    	    else if (mode == "total") {
        		plotlist[i].plot.data = [];
        		var data = get_feed_data(plotlist[i].id, start, end, end-start);
        		var d = new Date();
        		var year = 0, lyear;
        		var sum = 0, j = 0;
    	    	for (var z in data) {
        		    lyear = year;
        		    d.setTime(data[z][0]);
        		    year = d.getFullYear();
        		    if (year!=lyear && z!=0) {
            			var tmp = [];
            			tmp[0] = Date.UTC(lyear,0,1);
            			tmp[1] = sum;
            			plotlist[i].plot.data[j] = tmp;
                  			j++;
            			sum = 0;
        		    }
        		    sum += parseFloat(data[z][1]);
    		    }
        		var tmp = [];
        		tmp[0] = Date.UTC(year,0,1);
        		tmp[1] = sum;
        		plotlist[i].plot.data[j] = tmp;
        		// kWh for the current day
        		var today = new Date();
        		if (today.getFullYear() == year) plotlist[i].plot.data[j][1] += parseFloat(get_feedvalue(plotlist[i].id));
        		else {
        		    var kwhtoday = [];
        		    kwhtoday[0] = new Date().setHours(0,0,0,0);
        		    kwhtoday[1] = get_feedvalue(plotlist[i].id);
        		    plotlist[i].plot.data.push(kwhtoday);
        		}
	       }
        }
    }
}

// get data to be plotted
// experimental and unfinished version of the function above, but to be run asynchronously
/*function async_get_plotdata_mode(plotlist, start, end, mode) {
    for(var i in plotlist) {
        if (timeWindowChanged)
        {
            plotlist[i].plot.data = null;
        }
        if (!plotlist[i].plot.data)
        {
            var feedQuery = "&id="+plotlist[i].id+"&start="+start+"&end="+end+"&dp="+400;
            var valueQuery = "&id="+plotlist[i].id;
            // power (day)
            if (mode == "day") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,  
                    dataType: 'json',
                    success: function(datain) { plotlist[i].plot.data = datain; }
                    });
            }
            // histogram with kWh/day (month)
            else if (mode == "month") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,  
                    dataType: 'json',
                    success: function(datain) { 
                        plotlist[i].plot.data = datain;
                        if(null != plotlist[i].plot.data && ( (plotlist[i].plot.data) instanceof Array )){// added the array check to avoid error Thomas
                            // kWh for the current day
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                               
                            $.ajax({
                            url: path+'feed/value.json',
                            data: valueQuery,  
                            dataType: 'json',
                            success: function(dayData){
                                kwhtoday[1] = parseInt(dayData);
                                plotlist[i].plot.data.push(kwhtoday);
                                 }
                            });
                        }
                     }
                    });
            }
            // histogram with kWh summed up for each month
            else if (mode == "year") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,  
                    dataType: 'json',
                    success: function(datain) {
                       plotlist[i].plot.data = [];
                       var d = new Date();
                       var month = 0, year = 0, lmonth, lyear;
                       var sum = 0, j = 0;
                       for (var z in datain) {
                            lmonth = month;
                            lyear = year;
                            d.setTime(datain[z][0]);
                            month = d.getMonth(); year = d.getFullYear();
                            if (month!=lmonth && z!=0) {
                                var tmp = [];
                                tmp[0] = Date.UTC(lyear,lmonth,1);
                                tmp[1] = sum;
                                plotlist[i].plot.data[j] = tmp;
                                    j++;
                                sum = 0;
                            }
                            sum += parseFloat(datain[z][1]);
                        }
                        var tmp = [];
                        tmp[0] = Date.UTC(year,month,1);
                        tmp[1] = sum;
                        plotlist[i].plot.data[j] = tmp;
                        // kWh for the current month
                        var today = new Date();
                        if (today.getMonth() == month){
                            $.ajax({
                                url: path+'feed/value.json',
                                data: valueQuery,  
                                dataType: 'json',
                                success: function(dayData){
                                    plotlist[i].plot.data[j][1] += parseFloat(dayData);
                                     }
                                });                             
                        }
                        else {
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                            $.ajax({
                                url: path+'feed/value.json',
                                data: valueQuery,  
                                dataType: 'json',
                                success: function(dayData){
                                        kwhtoday[1] =  parseFloat(dayData);
                                        plotlist[i].plot.data.push(kwhtoday);
                                     }
                                }); 
                        } 
                     }// end of years ajax success
                    });// end of years ajax
            }
            // histogram with kWh summed up for each year
            else if (mode == "total") {
                  $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,  
                    dataType: 'json',
                    success: function(datain) {
                      plotlist[i].plot.data = [];
                      var d = new Date();
                      var year = 0, lyear;
                      var sum = 0, j = 0;
                       for (var z in datain) {
                            lyear = year;
                            d.setTime(datain[z][0]);
                            year = d.getFullYear();
                            if (year!=lyear && z!=0) {
                                var tmp = [];
                                tmp[0] = Date.UTC(lyear,0,1);
                                tmp[1] = sum;
                                plotlist[i].plot.data[j] = tmp;
                                    j++;
                                sum = 0;
                            }
                            sum += parseFloat(datain[z][1]);
                        }
                        var tmp = [];
                        tmp[0] = Date.UTC(year,0,1);
                        tmp[1] = sum;
                        plotlist[i].plot.data[j] = tmp;
                         // kWh for the current year
                        var today = new Date();
                        if (today.getFullYear() == year){ 
                              $.ajax({
                                url: path+'feed/value.json',
                                data: valueQuery,  
                                dataType: 'json',
                                success: function(dayData){
                                    plotlist[i].plot.data[j][1] += parseFloat(dayData);
                                     }
                                });          
                        }
                        else {
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                            $.ajax({
                                url: path+'feed/value.json',
                                data: valueQuery,  
                                dataType: 'json',
                                success: function(dayData){
                                        kwhtoday[1] =  parseFloat(dayData);
                                        plotlist[i].plot.data.push(kwhtoday);
                                     }
                                }); 
                        }
                     }// end of years ajax success
                    });// end of years ajax
           }// end of if (mode == "total") {
        }
    }
} */

// to be callled to plot the consumption and generation (but only if not toggling)
                      // TODO: review this plotting as I may have some problems due to incomplete data from the other flots 
function plotWholeCG(plotlist,placeholder,options,start,end,showxaxis){
    var flotdata = [];
    options.xaxis["min"] = start;
    options.xaxis["max"] = end;
    //if (!showxaxis) options.xaxis.tickFormatter = function (val, axis) { return [] };
    if(plotlist[0].plot.data) flotdata.push(plotlist[0].plot);
    if(plotlist[1].plot.data)flotdata.push(plotlist[1].plot);
    if(plotlist[2].plot.data) flotdata.push(plotlist[2].plot);
    $.plot(placeholder, flotdata, options);
}


// The plotting of the consumption graph will always be called through this function    (get_plotdata_and_plot) 
// if one the graphs is toggled, it will be called from the first if inside the fuction
// otherwise, it will call the  plotWholeCG

function get_plotdata_and_plot(plotlist, start, end, mode,placeholder, showxaxis, on1, on2) {
        // check if time has changed
        if (timeWindowChanged == 0){
            var flotdata = [];
            if (on1) {
                flotdata.push(plotlist[0].plot);
            }
            if (on2) {
                flotdata.push(plotlist[1].plot);
            }
            flotdata.push(plotlist[2].plot);
            var options = optionsHash[mode];
            options.xaxis["min"] = start;
            options.xaxis["max"] = end;
            //if (!showxaxis) options.xaxis.tickFormatter = function (val, axis) { return [] };
            $.plot(placeholder, flotdata, options); 
            return;// if the time has not changed, we plot it and leave
        }else{
          // null all the data and then plot in the next for
          for(var i in plotlist)
            plotlist[i].plot.data = null;
          timeWindowChanged = 0;
        }

    for(var i in plotlist) {
        if (!plotlist[i].plot.data){
            var feedQuery = "&id="+plotlist[i].id+"&start="+start+"&end="+end+"&dp="+400;
            var valueQuery = "&id="+plotlist[i].id;
            // power (day)
            if (mode == "day") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,
                    context: {index:i, plotlist: plotlist},  
                    dataType: 'json',
                    success: function(datain) { 
                      this.plotlist[this.index].plot.data = datain;
                      plotWholeCG(this.plotlist,placeholder,optionsHash["day"],start,end,showxaxis);
                      }
                    });
            }
            // histogram with kWh/day (month)
            else if (mode == "month") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,
                    context: {index:i, plotlist: plotlist, valueQuery:valueQuery},  
                    dataType: 'json',
                    success: function(datain) {
                        var refPlotlist = this.plotlist;
                        var refIndex = this.index; 
                        refPlotlist[refIndex].plot.data = datain;
                        if(null != refPlotlist[refIndex].plot.data && ( (refPlotlist[refIndex].plot.data) instanceof Array )){// added the array check to avoid error Thomas
                            // kWh for the current day
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                               
                            $.ajax({
                            url: path+'feed/value.json',
                            data: this.valueQuery,  
                            dataType: 'json',
                            context: {index:refIndex, plotlist: refPlotlist,kwhtoday:kwhtoday},
                            success: function(dayData){
                                this.kwhtoday[1] = parseFloat(dayData);
                                this.plotlist[this.index].plot.data.push(this.kwhtoday);
                                plotWholeCG(this.plotlist,placeholder,optionsHash["month"],start,end,showxaxis);
                                 }
                            });
                        }
                     }
                    });
            }
            // histogram with kWh summed up for each month
            else if (mode == "year") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,
                    context: {index:i, plotlist: plotlist, valueQuery:valueQuery},   
                    dataType: 'json',
                    success: function(datain) {
                       var refPlotlist = this.plotlist;
                       var refIndex = this.index; 
                       refPlotlist[refIndex].plot.data = [];
                       var d = new Date();
                       var month = 0, year = 0, lmonth, lyear;
                       var sum = 0, j = 0;
                       for (var z in datain) {
                            lmonth = month;
                            lyear = year;
                            d.setTime(datain[z][0]);
                            month = d.getMonth(); year = d.getFullYear();
                            if (month!=lmonth && z!=0) {
                                var tmp = [];
                                tmp[0] = Date.UTC(lyear,lmonth,1);
                                tmp[1] = sum;
                                refPlotlist[refIndex].plot.data[j] = tmp;
                                    j++;
                                sum = 0;
                            }
                            sum += parseFloat(datain[z][1]);
                        }
                        var tmp = [];
                        tmp[0] = Date.UTC(year,month,1);
                        tmp[1] = sum;
                        refPlotlist[refIndex].plot.data[j] = tmp;
                        // kWh for the current month
                        var today = new Date();
                        if (today.getMonth() == month){
                            $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,
                                context: {index:refIndex, plotlist: refPlotlist},  
                                dataType: 'json',
                                success: function(dayData){
                                    this.plotlist[this.index].plot.data[j][1] += parseFloat(dayData);
                                    plotWholeCG(this.plotlist,placeholder,optionsHash["year"],start,end,showxaxis);
                                     }
                                });                             
                        }
                        else {
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                            $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,  
                                dataType: 'json',
                                context: {index:refIndex, plotlist: refPlotlist,kwhtoday:kwhtoday},
                                success: function(dayData){
                                        this.kwhtoday[1] =  parseFloat(dayData);
                                        this.plotlist[this.index].plot.data.push(this.kwhtoday);
                                        plotWholeCG(this.plotlist,placeholder,optionsHash["year"],start,end,showxaxis);
                                     }
                                }); 
                        } 
                     }// end of years ajax success
                    });// end of years ajax
            }
            // histogram with kWh summed up for each year
            else if (mode == "total") {
                  $.ajax({
                    url: path+'feed/data.json',
                    context: {index:i, plotlist: plotlist, valueQuery:valueQuery},
                    data: feedQuery,  
                    dataType: 'json',
                    success: function(datain) {
                      var refPlotlist = this.plotlist;
                      var refIndex = this.index; 
                      refPlotlist[refIndex].plot.data = [];
                      var d = new Date();
                      var year = 0, lyear;
                      var sum = 0, j = 0;
                       for (var z in datain) {
                            lyear = year;
                            d.setTime(datain[z][0]);
                            year = d.getFullYear();
                            if (year!=lyear && z!=0) {
                                var tmp = [];
                                tmp[0] = Date.UTC(lyear,0,1);
                                tmp[1] = sum;
                                refPlotlist[refIndex].plot.data[j] = tmp;
                                    j++;
                                sum = 0;
                            }
                            sum += parseFloat(datain[z][1]);
                        }
                        var tmp = [];
                        tmp[0] = Date.UTC(year,0,1);
                        tmp[1] = sum;
                        refPlotlist[refIndex].plot.data[j] = tmp;
                         // kWh for the current year
                        var today = new Date();
                        if (today.getFullYear() == year){ 
                              $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,  
                                dataType: 'json',
                                context: {index:refIndex, plotlist: refPlotlist},
                                success: function(dayData){
                                    if(null == this.plotlist[this.index].plot.data){
                                      var tmp = [];
                                      tmp[0] = Date.UTC(year,0,1);
                                      tmp[1] = 0;
                                      this.plotlist[this.index].plot.data[0] = tmp;
                                    }
                                    else {
                                      this.plotlist[this.index].plot.data[j][1] += parseFloat(dayData);
                                    }
                                    plotWholeCG(this.plotlist,placeholder,optionsHash["total"],start,end,showxaxis);
                                }
                            });          
                        }
                        else {
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                            $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,  
                                dataType: 'json',
                                context: {index:refIndex, plotlist: refPlotlist,kwhtoday:kwhtoday},
                                success: function(dayData){
									this.kwhtoday[1] =  parseFloat(dayData);
									this.plotlist[this.index].plot.data.push(this.kwhtoday);
									plotWholeCG(this.plotlist,placeholder,optionsHash["total"],start,end,showxaxis);
                                }
                            }); 
                        }
                     }// end of years ajax success
                    });// end of years ajax
           }// end of if (mode == "total") {
        }
    }
}

// ideally, we should merge this function with  get_plotdata_and_plot but doing a different plotting depending on
// each other
// however be aware that the Wh graph (month) of devices needs the data to be multiplied by 1000
function devices_get_plotdata_and_plot(choiceContainer, plotlist, start, end, yaxislabel, placeholder, mode, nr_devices) {

        // check if time has changed
        if (timeWindowChanged == 0)
        {
            plotAccordingToChoices(choiceContainer, plotlist, start, end, yaxislabel, placeholder, mode, nr_devices); 
            return;// if the time has not changed, we plot it and leave
        }else{
          // null all the data and then plot in the next for
          for(var i in plotlist)
            plotlist[i].plot.data = null;
          timeWindowChanged = 0;
        }

    for(var i in plotlist) {

        if (!plotlist[i].plot.data)
        {
            //timeWindowChanged = 0;
            var feedQuery = "&id="+plotlist[i].id+"&start="+start+"&end="+end+"&dp="+400;
            var valueQuery = "&id="+plotlist[i].id;
            // power (day)
            if (mode == "day") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,
                    context: {index:i, plotlist: plotlist},  
                    dataType: 'json',
                    success: function(datain) { 
                      this.plotlist[this.index].plot.data = datain;
                      plotAccordingToChoices(choiceContainer, this.plotlist, start, end, yaxislabel, placeholder, mode, nr_devices); 
                      }
                    });
            }
            // histogram with kWh/day (month)
            else if (mode == "month") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,
                    context: {index:i, plotlist: plotlist, valueQuery:valueQuery},  
                    dataType: 'json',
                    success: function(datain) {
                        var refPlotlist = this.plotlist;
                        var refIndex = this.index; 
                        refPlotlist[refIndex].plot.data = datain;
                        if(null != refPlotlist[refIndex].plot.data && ( (refPlotlist[refIndex].plot.data) instanceof Array )){// added the array check to avoid error Thomas
                            // kWh for the current day
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                               
                            $.ajax({
                            url: path+'feed/value.json',
                            data: this.valueQuery,  
                            dataType: 'json',
                            context: {index:refIndex, plotlist: refPlotlist,kwhtoday:kwhtoday},
                            success: function(dayData){
                                this.kwhtoday[1] = parseFloat(dayData);
                                this.plotlist[this.index].plot.data.push(this.kwhtoday);
                                // for the monthly graphs of the devices, we multiply the value for 1000 not anylonger
                                // scalarMultiplyY(this.plotlist[this.index].plot.data,1000)
                                plotAccordingToChoices(choiceContainer, this.plotlist, start, end, yaxislabel, placeholder, mode, nr_devices);
                                 }
                            });
                        }
                     }
                    });
            }
            // histogram with kWh summed up for each month
            else if (mode == "year") {
                    $.ajax({
                    url: path+'feed/data.json',
                    data: feedQuery,
                    context: {index:i, plotlist: plotlist, valueQuery:valueQuery},   
                    dataType: 'json',
                    success: function(datain) {
                       var refPlotlist = this.plotlist;
                       var refIndex = this.index; 
                       refPlotlist[refIndex].plot.data = [];
                       var d = new Date();
                       var month = 0, year = 0, lmonth, lyear;
                       var sum = 0, j = 0;
                       for (var z in datain) {
                            lmonth = month;
                            lyear = year;
                            d.setTime(datain[z][0]);
                            month = d.getMonth(); year = d.getFullYear();
                            if (month!=lmonth && z!=0) {
                                var tmp = [];
                                tmp[0] = Date.UTC(lyear,lmonth,1);
                                tmp[1] = sum;
                                refPlotlist[refIndex].plot.data[j] = tmp;
                                    j++;
                                sum = 0;
                            }
                            sum += parseFloat(datain[z][1]);
                        }
                        var tmp = [];
                        tmp[0] = Date.UTC(year,month,1);
                        tmp[1] = sum;
                        refPlotlist[refIndex].plot.data[j] = tmp;
                        // kWh for the current month
                        var today = new Date();
                        if (today.getMonth() == month){
                            $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,
                                context: {index:refIndex, plotlist: refPlotlist},  
                                dataType: 'json',
                                success: function(dayData){
                                    this.plotlist[this.index].plot.data[j][1] += parseFloat(dayData);
                                    plotAccordingToChoices(choiceContainer, this.plotlist, start, end, yaxislabel, placeholder, mode, nr_devices);
                                     }
                                });                             
                        }
                        else {
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                            $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,  
                                dataType: 'json',
                                context: {index:refIndex, plotlist: refPlotlist,kwhtoday:kwhtoday},
                                success: function(dayData){
                                        this.kwhtoday[1] =  parseFloat(dayData);
                                        this.plotlist[this.index].plot.data.push(this.kwhtoday);
                                        plotAccordingToChoices(choiceContainer, this.plotlist, start, end, yaxislabel, placeholder, mode, nr_devices);
                                     }
                                }); 
                        } 
                     }// end of years ajax success
                    });// end of years ajax
            }
            // histogram with kWh summed up for each year
            else if (mode == "total") {
                  $.ajax({
                    url: path+'feed/data.json',
                    context: {index:i, plotlist: plotlist, valueQuery:valueQuery},
                    data: feedQuery,  
                    dataType: 'json',
                    success: function(datain) {
                      var refPlotlist = this.plotlist;
                      var refIndex = this.index; 
                      refPlotlist[refIndex].plot.data = [];
                      var d = new Date();
                      var year = 0, lyear;
                      var sum = 0, j = 0;
                       for (var z in datain) {
                            lyear = year;
                            d.setTime(datain[z][0]);
                            year = d.getFullYear();
                            if (year!=lyear && z!=0) {
                                var tmp = [];
                                tmp[0] = Date.UTC(lyear,0,1);
                                tmp[1] = sum;
                                refPlotlist[refIndex].plot.data[j] = tmp;
                                    j++;
                                sum = 0;
                            }
                            sum += parseFloat(datain[z][1]);
                        }
                        var tmp = [];
                        tmp[0] = Date.UTC(year,0,1);
                        tmp[1] = sum;
                        refPlotlist[refIndex].plot.data[j] = tmp;
                         // kWh for the current year
                        var today = new Date();
                        if (today.getFullYear() == year){ 
                              $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,  
                                dataType: 'json',
                                context: {index:refIndex, plotlist: refPlotlist},
                                success: function(dayData){
                                    if(null == this.plotlist[this.index].plot.data){
                                      var tmp = [];
                                      tmp[0] = Date.UTC(year,0,1);
                                      tmp[1] = 0;
                                      this.plotlist[this.index].plot.data[0] = tmp;
                                    }
                                    else {
                                      this.plotlist[this.index].plot.data[j][1] += parseFloat(dayData);
                                    }
                                    plotAccordingToChoices(choiceContainer, this.plotlist, start, end, yaxislabel, placeholder, mode, nr_devices);
                                     }
                                });          
                        }
                        else {
                            var kwhtoday = [];
                            kwhtoday[0] = new Date().setHours(0,0,0,0);
                            $.ajax({
                                url: path+'feed/value.json',
                                data: this.valueQuery,  
                                dataType: 'json',
                                context: {index:refIndex, plotlist: refPlotlist,kwhtoday:kwhtoday},
                                success: function(dayData){
                                        this.kwhtoday[1] =  parseFloat(dayData);
                                        this.plotlist[this.index].plot.data.push(this.kwhtoday);
                                        plotAccordingToChoices(choiceContainer, this.plotlist, start, end, yaxislabel, placeholder, mode, nr_devices);
                                     }
                                }); 
                        }
                     }// end of years ajax success
                    });// end of years ajax
           }// end of if (mode == "total") {
        }
    }
}





// define devices plot
function define_plot(plotdata, start, end, yaxislabel, placeholder, mode, nr_devices) {
    var options = {
        grid: { show: true, hoverable: false, clickable: false, color: "#fff", backgroundColor: "#1192d3", tickColor: "#fff"  },
        xaxis: { show: true, mode: "time", timezone: "browser", min: start, max: end, minTickSize: [1, "hour"], timeformat: "%H", font: {color: "#fff"} },
        yaxis: {
	    show: true,
            axisLabel: yaxislabel,
            axisLabelFontSizePixels: 8,
            axisLabelFontFamily: "Verdana, Arial",
            axisLabelPadding: 5,
	    min: 0, 
		font: {color: "#fff"} 
        },
        legend: false,
	lines: { show: true },
	bars: { show: false, fill: true, align: "center", order: 1 }
    };

    if (mode == "month") {
	options.lines.show = false;
	options.bars.show = true;
	options.bars.barWidth = (12*3600*1000)/nr_devices;
	options.xaxis.minTickSize = [1, "day"];
	options.xaxis.timeformat = "%e";
    }
    else if (mode == "year") {
	options.lines.show = false;
	options.bars.show = true;
	options.bars.barWidth = (15*24*3600*1000)/nr_devices;
	options.xaxis.minTickSize = [1, "month"];
	options.xaxis.timeformat = "%b";
    }
    else if (mode == "total") {
	options.lines.show = false;
	options.bars.show = true;
	options.bars.barWidth = (3600*1000*24*30*6)/nr_devices
	options.xaxis.minTickSize = [1, "year"];
	options.xaxis.timeformat = "%Y";
    }

    var plot = $.plot(placeholder, plotdata, options);
    return plot;
}


// plot devices that are ticked  
function plotAccordingToChoices(choiceContainer, plotlist, start, end, yaxislabel, placeholder, mode, nr_devices) {
    var plotdata = [];
    $(choiceContainer).find("input:checked").each(function () {
        var key = $(this).attr("name");
        if (key && plotlist[key].plot) {
            plotdata.push(plotlist[key].plot);
        }
    });

    if (plotdata.length >= 0) {
        var plot = define_plot(plotdata, start, end, yaxislabel, placeholder, mode, nr_devices);
	plot.draw();
    }
    timeWindowChanged = 0;
}

// LEGACY: plot stacked and line/bar graphs (consumption/generation plot)
/*
function plot_mode(plotlist, placeholder, showxaxis, on1, on2, mode, start, end) {
    
    var options;

    if (mode == "day") {
	get_plotdata_mode(plotlist, start, end, "day");
        options = {
            grid: { show: true, hoverable: false, clickable: false, font: {color: "#fff"} },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "hour"], timeformat: "%H", min: start, max: end, labelHeight:15, reserveSpace: true, font: {color: "#fff"} },
            yaxis: { min: 0 , font: {color: "#fff"}},
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: true}
    	};
    }
    else if (mode == "month") {
	get_plotdata_mode(plotlist, start, end, "month");
	options = {
            grid: { show: true, hoverable: false, clickable: false },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "day"], timeformat: "%e", min: start, max: end, labelHeight:15, reserveSpace: true },
            yaxis: { min: 0 },
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: false },
	    bars: { show: true, align: "center", fill: false, barWidth: 3600*1000*12} 
    	};
    }
    else if (mode == "year") {
	get_plotdata_mode(plotlist, start, end, "year");
	options = {
            grid: { show: true, hoverable: false, clickable: false },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "month"], timeformat: "%b", min: start, max: end, labelHeight:15, reserveSpace: true },
            yaxis: { min: 0 },
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: false },
	    bars: { show: true, align: "center", fill: false, barWidth: 3600*1000*24*15} 
    	};
    }
    else if (mode == "total") {
	get_plotdata_mode(plotlist, start, end, "total");
  	options = {
            grid: { show: true, hoverable: false, clickable: false },
            xaxis: { show: true, mode: "time", timezone: "browser", minTickSize: [1, "year"], timeformat: "%Y", min: start, max: end, labelHeight:15, reserveSpace: true },
            yaxis: { min: 0 },
            yaxes: [{show: true, labelWidth:50}, {position: "right", show: false, labelWidth:25, reserveSpace: true}],
            legend: false,
            lines: { show: false },
	    bars: { show: true, align: "center", fill: false, barWidth: 3600*1000*24*30*6} 
    	};
    }

    if (!showxaxis) options.xaxis.tickFormatter = function (val, axis) { return [] };

    var plotdata = [];
    if (on1) {
        plotdata.push(plotlist[0].plot);
    }
    if (on2) {
        plotdata.push(plotlist[1].plot);
    }
    plotdata.push(plotlist[2].plot);

    $.plot(placeholder, plotdata, options);     
    
    timeWindowChanged = 0;
}
*/

// hard-code color indices to prevent them from shifting as devices are ticked/unticked
function hardcode_color(plotlist) {
    var i = 0;
    $.each(plotlist, function (key, val) {
        val.plot.color = colors[i];
        ++i;
    });
}

// hard-code colors (consumption/generation plot)
function hardcode_color_consumption(plotlist) {
    plotlist[0].plot.color = "#FF0000"; //red
    plotlist[1].plot.color = "#088A08"; // green
    plotlist[2].plot.color = "#424242"; // black
}

function hardcode_color_generation(plotlist) {
    plotlist[0].plot.color = "#088A08"; // green
    plotlist[1].plot.color =  "#FFFF00"; // yellow
    plotlist[2].plot.color = "#424242"; // black
}

// on click functions for click on legend (consumption/generation plot)
function onConsumptionClick(plotlist, start, end, placeholder, showxaxis, td1, td2, td3, mode) {
    consumption_on = !consumption_on;
    $(td1).toggleClass("off-cell");
    if (!consumption_on) {
	$(td2).addClass("off-cell");
	extenergysupply_on = false;
	$(td3).addClass("off-cell");
	intpowersupply_on = false;
    }
    else {
	$(td2).removeClass("off-cell");
	extenergysupply_on = true;
	$(td3).removeClass("off-cell");
	intpowersupply_on = true;
    }
    //plot_mode(plotlist, placeholder, showxaxis, extenergysupply_on, intpowersupply_on, mode, start, end);
     get_plotdata_and_plot(plotlist, start, end, mode,placeholder, showxaxis, extenergysupply_on, intpowersupply_on);
}

function onExtEnergySupplyClick(plotlist, start, end, placeholder, showxaxis, td1, td2, mode) {
    extenergysupply_on = !extenergysupply_on;
    $(td2).toggleClass("off-cell");
    consumption_on = extenergysupply_on && intpowersupply_on;
    if (consumption_on) { $(td1).removeClass("off-cell"); } else { if (intpowersupply_on) $(td1).addClass("off-cell"); }
    //plot_mode(plotlist, placeholder, showxaxis, extenergysupply_on, intpowersupply_on, mode, start, end);
    get_plotdata_and_plot(plotlist, start, end, mode,placeholder, showxaxis, extenergysupply_on, intpowersupply_on);
}

function onIntPowerSupplyClick(plotlist, start, end, placeholder, showxaxis, td1, td3, mode) {
    intpowersupply_on = !intpowersupply_on;
    $(td3).toggleClass("off-cell");
    consumption_on = extenergysupply_on && intpowersupply_on;
    if (consumption_on) { $(td1).removeClass("off-cell"); } else { if (extenergysupply_on) $(td1).addClass("off-cell"); }
    //plot_mode(plotlist, placeholder, showxaxis, extenergysupply_on, intpowersupply_on, mode, start, end);
    get_plotdata_and_plot(plotlist, start, end, mode,placeholder, showxaxis, extenergysupply_on, intpowersupply_on);
}

function onGenerationClick(plotlist, start, end, placeholder, showxaxis, td1, td2, td3, mode) {
    generation_on = !generation_on;
    $(td1).toggleClass("off-cell");
    if (!generation_on) {
	$(td2).addClass("off-cell");
	selfconsumption_on = false;
	$(td3).addClass("off-cell");
	gridfeedin_on = false;
    }
    else {
	$(td2).removeClass("off-cell");
	selfconsumption_on = true;
	$(td3).removeClass("off-cell");
	gridfeedin_on = true;
    }
    //plot_mode(plotlist, placeholder, showxaxis, selfconsumption_on, gridfeedin_on, mode, start, end);
    get_plotdata_and_plot(plotlist, start, end, mode,placeholder, showxaxis, selfconsumption_on, gridfeedin_on);
}

function onSelfConsumptionClick(plotlist, start, end, placeholder, showxaxis, td1, td2, mode) {
    selfconsumption_on = !selfconsumption_on;
    $(td2).toggleClass("off-cell");
    generation_on = selfconsumption_on && gridfeedin_on;
    if (generation_on) { $(td1).removeClass("off-cell"); } else { if (gridfeedin_on) $(td1).addClass("off-cell"); }
    //plot_mode(plotlist, placeholder, showxaxis, selfconsumption_on, gridfeedin_on, mode, start, end);
    get_plotdata_and_plot(plotlist, start, end, mode,placeholder, showxaxis, selfconsumption_on, gridfeedin_on);
}

function onGridFeedInClick(plotlist, start, end, placeholder, showxaxis, td1, td3, mode) {
    gridfeedin_on = !gridfeedin_on;
    $(td3).toggleClass("off-cell");
    generation_on = selfconsumption_on && gridfeedin_on;
    if (generation_on) { $(td1).removeClass("off-cell"); } else { if (selfconsumption_on) $(td1).addClass("off-cell"); }
    //plot_mode(plotlist, placeholder, showxaxis, selfconsumption_on, gridfeedin_on, mode, start, end);
    get_plotdata_and_plot(plotlist, start, end, mode,placeholder, showxaxis, selfconsumption_on, gridfeedin_on);
}