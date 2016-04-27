var chart;
var y;
var x;
var height;
var bartextArray;
function barChart(target, type, data){
	var bartext0 = data[0];
	var bartext1 = data[1];
	var bartext2 = data[2];
	var bartext3 = data[3];
	bartextArray = [bartext0[2],bartext1[2],bartext2[2],bartext3[2]];
	
	var margin = {top: 20, right: 2, bottom: 30, left: 20},
		width = 350 - margin.left - margin.right;
		height = 270 - margin.top - margin.bottom;

	x = d3.scale.ordinal()
		.rangeRoundBands([0, width], .1);
		
	y = d3.scale.linear()
		.range([height, 0]);
		
	var xAxis = d3.svg.axis()
		.scale(x)
		.ticks(0)
		.orient("bottom");
		
	var yAxis = d3.svg.axis()
		.scale(y)
		.orient("left")
		.ticks(10, "kWh");
		
	chart = d3.select(target)
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
		
	x.domain(data.map(function(d) {return d[0]; }));
	y.domain([0, 100]);
	
	//adding text from values that has been passed to xAxis
	chart.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")", "rotate(-90)")
		.attr("fill","#fff")
		.call(xAxis);
	
	var bar = chart.selectAll(".bar")
		.data(data)
			.enter().append("rect")
		.attr("class", "bar")
		.attr("id", function(d, i){ 
			return type+"bar"+i})
		.attr("x", function(d) { return x(d[0]); })
		.attr("width", x.rangeBand())
		.attr("title", function(d,i){
			return bartextArray[i]})
		.attr("y", function(d) {return y(d[1]); })
		.attr("height", function(d) {return height - y(d[1]); })
		.on({
			"mouseover": function(d,i){
				d3.select(this)
				.style("fill","#045B85")
			}
		})
		.on({
			"mouseout": function(d){
				//reset color
				d3.select(this)
				.style("fill","#fff")
			}			
		});

	bartext = chart.selectAll("bar")
		.data(data)
		.enter().append("text")
		.attr("class", "bartext")
		.attr("x", function(d) {return x(d[0])+x.rangeBand()/2;})
     	.attr("y", function(d) {
     		if(d[1] == 0){
     			chart.attr("fill","#fff");
				return y(d[1])-15;
			}
     		return y(d[1])+5;})
      	.attr("dy", ".75em")
      	.attr("text-anchor","middle")
      	.attr("fill",function(d){
      		return d[1] ==0?"#fff":"#1192d3";})
      	.text(function(d) { return d[1]; });
}

function updateBarChart(target, type, data){
	var bartext0 = data[0];
	var bartext1 = data[1];
	var bartext2 = data[2];
	var bartext3 = data[3];
	var bartextArray = [bartext0[2],bartext1[2],bartext2[2],bartext3[2]];

	chart = d3.select(target);
	
	//transition on the bars
	chart.selectAll(".bar")
		.data(data)
		.transition()
			.duration(1000)
			.attr("y", function(d) {return y(d[1]); })
			.attr("height", function(d) {return height - y(d[1]); })
			.attr("title", function(d,i){
				$("#"+type+"bar"+i).data("powertip", function(){
					return bartextArray[i];
				});
				return bartextArray[i]
			});

	//transition on the barlabels
	chart.selectAll("text.bartext")
		.data(data)
		.transition()
			.duration(1000)
			.attr("x", function(d) {return x(d[0])+x.rangeBand()/2;})
     		.attr("y", function(d) {
     			if(d[1] == 0){
     				chart.attr("fill","#fff");
					return y(d[1])-15;}
     			return y(d[1])+5;})
	      	.attr("dy", ".75em")
	      	.attr("text-anchor","middle")
	      	.attr("fill",function(d){
	      		return d[1] ==0?"#fff":"#1192d3";})
	      	.text(function(d) { return d[1]; });	
}

function addTooltips(type){
	$("#"+type+"bar0").powerTip({
                placement: "s",
                mouseOnToPopup:true
            });
	
	$("#"+type+"bar1").powerTip({
           	    placement: "s",
                mouseOnToPopup:true
            });
	$("#"+type+"bar2").powerTip({
                placement: "s",
                mouseOnToPopup:true
            });
	$("#"+type+"bar3").powerTip({
                placement: "s",
                mouseOnToPopup:true
            });
	$("#"+type+"bar4").powerTip({
                placement: "s",
                mouseOnToPopup:true
            });
}

function type(d){
	d[1] = +d[1];
	return d;
}