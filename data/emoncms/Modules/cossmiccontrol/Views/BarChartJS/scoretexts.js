function getSharingScoreText(score){
	var result = +score;	
	if(result == 0){
		return "This is a horrible result!<br>"+
		"In order to increase this score, you have to make sure that you are producing more then you consume.<br>"+
		"One way to do that is to invest in more PV panel.";
	}
	if(result > 0 && score < 11){
		return "This needs to be improved!<br>"+
		"Make sure that you lower your consumption in order to share your green energy.";
	}
	if(result > 10 && score < 41){
		return "The score is way to low.<br>"+
		"Make sure that you lower your consumption in order to share your green energy.";
	}
	if(result > 40 && score < 61){
		return "Your score is good!<br>"+
		"To improve, reduce your consumption even more!";
	}
	if(result > 60 && score <81){
		return "Wow! This is an impressive score!<br>"+
		"Your community should be proud to have you in their midst.";
	}
	if(result > 80 && score <100){
		return "You are so green that the color green should be named after yourself!";
	}
	if(result == 100){
		return "THE PERFECT SCORE! You just saved the planet!";
	}
}

function getPvUsageScoreText(score){

	var result = +score;	
	if(result == 0){
		return "This is a horrible result! Are you sure you plugged in the PV?";
	}
	if(result > 0 && score < 11){
		return "This needs to be improved!<br>"+
		"The easiest way to do this is to reduce the consumption in your house,<br>"+
		"or make sure that your tasks are run in the best possible moment.";
	}
	if(result > 10 && score < 41){
		return "Your Sun Score could be higher.<br>"+
		"Try to program your dishwasher and washing machine to be run <br>"+
		"during the day.";
	}
	if(result > 40 && score < 61){
		return "Your score is good!<br>"+
		"To improve, make sure that the tasks are run at a good time.";
	}
	if(result > 60 && score <81){
		return "Wow! This is an impressive score!<br>"+
		"Try to optimize the timing of the tasks even more to increase your score!";
	}
	if(result > 80 && score <100){
		return "You are so green that the color green should be named after yourself!<br>"+
		"Can you make it to 100?";
	}
	if(result == 100){
		return "THE PERFECT SCORE! You just saved the planet!";
	}
}

function getGridUsageScoreText(score){
	var result = +score;	
	if(result == 0){
		return "This is a horrible result!<br>"+
		"Did you remember to plug in your PV?";
	}
	if(result > 0 && score < 11){
		return "This needs to be improved!<br>"+
		"In order to improve this score, you have to use less power from the grid.<br>"+
		"Make sure you lower your consumption.";
	}
	if(result > 10 && score < 41){
		return "The score is way to low.<br>"+
		"In order to improve this score, you have to use less power from the grid.<br>"+
		"Make sure you lower your consumption.";
	}
	if(result > 40 && score < 61){
		return "Your score is good!<br>"+
		"In order to improve this score, you have to use less power from the grid.<br>"+
		"Make sure you lower your consumption.";
	}
	if(result > 60 && score <81){
		return "Wow! This is an impressive score!<br>"+
		"In order to improve this score, you have to use less power from the grid.<br>"+
		"Make sure you lower your consumption.";
	}
	if(result > 80 && score <100){
		return "You are so green that the color green should be named after yourself!";
	}
	if(result == 100){
		return "THE PERFECT SCORE! You just saved the planet!";
	}
}

function getSchedulingScoreText(score){
	var result = +score;	
	if(result == 0){
		return "This is a horrible result!<br>"+
		"Go to scheduler and schedule you appliances!";
	}
	if(result > 0 && score < 11){
		return "This needs to be improved!<br>"
		"It is very easy to increase this score.<br>"+
		"Go to <b>scheduler</b> and schedule your appliances for some easy points.";
	}
	if(result > 10 && score < 41){
		return "The score is way to low.<br>"+
		"It is very easy to increase this score.<br>"+
		"Go to <b>scheduler</b> and schedule your appliances for some easy points.";
	}
	if(result > 40 && score < 61){
		return "Your score is good!<br>"+
		"It is very easy to increase this score.<br>"+
		"Go to <b>scheduler</b> and schedule your appliances for some easy points.";
	}
	if(result > 60 && score <81){
		return "Wow! This is an impressive score!<br>"+
		"You are doing great. Make sure that you schedule the remaining appliances to increase the score even more.";
	}
	if(result > 80 && score <100){
		return "You are so green that the color green should be named after yourself!<br>"+
		"Can you make it to 100? You know what to do.";
	}
	if(result == 100){
		return "THE PERFECT SCORE! You just saved the planet!";
	}

}