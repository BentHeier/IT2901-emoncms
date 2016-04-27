
var margin = {top: 2, right: 2, bottom: 48, left: 28},
	width = 370 - margin.left - margin.right,
	height = 270 - margin.top - margin.bottom;

var x = d3.scale.ordinal()
	.rangeRoundBands([0, width], .1);
	
var y = d3.scale.linear()
	.range([height, 0]);
	
var xAxis = d3.svg.axis()
	.scale(x)
	.orient("bottom");
	
var yAxis = d3.svg.axis()
	.scale(y)
	.orient("left")
	.ticks(10, "kWh");
	
var chart = d3.select(".outerChart")
	.attr("width", width + margin.left + margin.right)
	.attr("height", height + margin.top + margin.bottom)
	.append("g")
	.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
d3.tsv("values.tsv", type, function(error, values){
	x.domain(values.map(function(d) {return d.name; }));
	y.domain([0, d3.max(values, function(d) {return d.value; })]);
	
	chart.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")", "rotate(-90)")
		.call(xAxis);
		
	chart.append("g")
		.attr("class", "y axis")
		.call(yAxis)
		.append("text")
		.attr("transform", "rotate(-90)")
		.attr("y", 6)
		.attr("dy", ".71em")
		.style("text-anchor", "end")
		.text("kWh");
		
	chart.selectAll(".bar")
		.data(values)
			.enter().append("rect")
		.attr("class", "bar")
		.attr("x", function(d) {return x(d.name); })
		.attr("width", x.rangeBand())
		.attr("y", function(d) {return y(d.value); })
		.attr("height", function(d) {return height - y(d.value); });
});
	
function type(d){
	d.value = +d.value;
	return d;
}