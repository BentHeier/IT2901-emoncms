//Extremely simple function to alter the CoSSMic Score tree image to match the score given by the scoring function.
//value should range from 0 - 100
window.selectTree = function(value){
	document.getElementById('cossmictree').src="../../images/tree/pine-tree-percent-"+value+".png";
}