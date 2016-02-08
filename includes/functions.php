<?php

/*
 * Wordo theme functions
 */
$result;
/*
 * search for a word if requested in url
 */
if(strlen($_SERVER['REQUEST_URI']) > 1){
	$word = substr($_SERVER['REQUEST_URI'],1);
	wordo_search_word($word);
}

function wordo_search_word($word){
	//$array = array("foo", "bar", "hallo", "world");
	$typelist = array("Adjective","Noun","Verb","Adverb","Pronoun","Article","Determiner","Preposition");
	
	//perform the get-request to wicktionary
	$json = file_get_contents("http://en.wiktionary.org/w/api.php?format=json&action=query&titles=$word&prop=revisions&rvprop=content");
	
	//decode the json-result
	$decoded = json_decode($json,true);
	
	//temp
	//var_dump($decoded);
	
	//get the specific text-data with the definitions
	if(!is_null($decoded['query']['pages'])){
		$data = array_shift($decoded['query']['pages']);//[0]['revisions'][0]['*'];
		$data = $data['revisions'][0]['*'];
		
		//filter the english part
		preg_match_all('/==English==[\s\S]*/',$data,$matches);
		$english = $matches[0][0];
		
		
		foreach($typelist as $type){
			//filter the noun part
			preg_match_all('/={3,4}' . $type . '={3,4}[\s\S]*/',$english,$matches2);
			if($matches2[0][0] != null){
				//filter the first definition
				preg_match('/#.*/',$matches2[0][0],$matches3);
				$matches3[0] = substr($matches3[0],2);
				//echo wordo_replace_links($matches3[0]);
				
				//use GLOBALS to set the value of a global variable in a function
				$definition = wordo_replace_links($matches3[0]);
				if(str_replace(' ','',$definition) != ''){
					$GLOBALS['result'] .= '<div class="definition"><div class="deftypewrapper"><div class="deftype">' . $type . '</div></div>' . $definition . '</div>';
				}
			}
		}
	}
}

function wordo_replace_links($data){
	//remove the {{}} stuff
	$mypattern='/\{\{.*?\}\}/';
	$myreplacement='';
	$data = preg_replace($mypattern,$myreplacement,$data);


	//replace all anchors
	$mypattern='/\[\[.*?([A-z ]+?)\]\]/';
	$myreplacement='[[$1]]';
	$data = preg_replace($mypattern,$myreplacement,$data);
	//try to replace all spaces
	preg_match_all('/\[\[([A-z ]+?)\]\]/',$data,$links);
	//var_dump($links);
	if(!empty($links)){
		for($i = 0;$i <  count($links[1]);$i++){
			$val = $links[1][$i];
			$val = str_replace(' ','-',$val);
			$data = preg_replace('/\[\[([A-z ]+?)\]\]/',$val,$data,1);
		}
	}
	
	$data = wordo_replace_regular_words($data);
	
	return $data;
}
function wordo_replace_regular_words($data){
	preg_match_all('/[\w-]+/',$data,$allWords);
	$uniquestring = md5(uniqid(rand(), true));
	$data = preg_replace('/[\w-]+/',$uniquestring,$data);
	if(!empty($allWords)){
		for($i = 0;$i <  count($allWords[0]);$i++){
			$val = $allWords[0][$i];
			$val = str_replace('-',' ',$val);
			$val = preg_replace('/(.*)/','<div class="a">$1</div>',$val);
			$data = preg_replace('/' . $uniquestring . '/',$val,$data,1);;
		}
	}
	return $data;
}
function wordo_get_result(){
	global $result;
	return $result;
}
function wordo_get_word(){
	$word ='';
	if(strlen($_SERVER['REQUEST_URI']) > 1){
		$word = substr($_SERVER['REQUEST_URI'],1);
	}
	return $word;
}


/*
 * Enqueue styles for front-end.
 *
 */
function wordo_styles() {
	// Loads our main stylesheet.
	// wp_enqueue_style( 'wordo-style', get_stylesheet_uri() );
}
// add_action( 'wp_enqueue_scripts', 'wordo_styles' );

/*
 * Enqueue JQuery for front-end.
 *
 */
function wordo_jquery() {
	// wp_enqueue_script("jquery");
}
// add_action('wp_enqueue_scripts','wordo_jquery');

/*
 * Hide the admin bar css and bump css from front end
 *
 */

function wordo_hide_admin_bar_from_front_end(){
  if (is_blog_admin()) {
    return true;
  }
  //remove_action( 'wp_head', '_admin_bar_bump_cb' );
  return false;
}
// add_filter( 'show_admin_bar', 'wordo_hide_admin_bar_from_front_end' );

/*
 * Take care of the page title on front end
 */

function wordo_title(){
	$title = 'Wordo: the clean dictionary';
	return $title;
}
// add_filter('wp_title','wordo_title',10,2);