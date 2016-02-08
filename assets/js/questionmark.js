jQuery(document).ready(function($){
	$(".questionmark").hover(
		function () {
			$(".definition").text("Wordo is the clean dictionary. We are devoted to bringing users the best experience. Help spread the word. it's users to bring all the definitions in a friendly way.");
			$(".word").text("wordo");
		},
		function () {
			$(".definition").text("The class of industrial wage earners who, possessing neither capital nor production means, must earn their living by selling their labor.");
			$(".word").text("proletariat");
		}
	);
});