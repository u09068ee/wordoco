/*if(window.location.pathname.length > 1){
			var title = window.location.pathname.substr(1);
			document.title = title + ' definition';
}*/
jQuery(document).ready(function($){
	var initialpop = true;
	var typeList = ["Adjective","Noun","Verb","Adverb","Pronoun","Article","Determiner","Preposition"];
	var myResultsList = new Array();
	
	if(window.location.pathname.length > 1){
		if($('.search').length){
			$('.search').val(unescape(window.location.pathname.substr(1)).replace(/_+/g,' '));
			$('#result').animate({opacity: 0});
			executeSearch(unescape(window.location.pathname.substr(1)).toLowerCase().replace(/_+/g,' '));
			// checkFavoriteButton(unescape(window.location.pathname.substr(1)).replace(/_+/g,' '));
			// checkWhoElseLiked(unescape(window.location.pathname.substr(1)).replace(/_+/g,' '));
			$('#result').animate({opacity: 1},100);
			rebindClick();
		}
	} else {
		
	}
	$('#foo').submit(function(e){
		
		$('#result').css('opacity',0);
		$('#word-likes-tab-header').css('opacity', 0);
		//$('#result').animate({opacity: 0},1000);

		//window.location.href=$('#foo .search').val().toLowerCase();
		//This is where we update the address bar with the 'url' parameter
		// .trim().replace(/ +/g,'-')
		window.history.pushState('', document.title, $('#foo .search').val().toLowerCase().trim().replace(/ +/g,'_'));

		//This stops the browser from actually following the link
		e.preventDefault();
		executeSearch(($('#foo .search').val()).toLowerCase().replace(/_+/g,' '));
		return false;
	});
	function checkFavoriteButton(word){
		url = document.location.origin+'/services/posthandler.php';
		
		var mydata = { word : word, mywlaction: 'check', ajax:1 };
		$.post(url,mydata, function(data){
				loadingImg = $('.mywl-img');
				if(data == 'favorite'){
					$('.mywl-link').addClass('favorited');
					loadingImg.hide();
				} else {
					$('.mywl-link').removeClass('favorited');
					loadingImg.hide();
				}
		});
	}
	
	function checkWhoElseLiked(word){
		url = document.location.origin+'/services/posthandler.php';
		
		var mydata = { word : word, mywlaction: 'whoElseLiked', ajax:1 };
		log = $('#word-likes-tab');
		log.html('');
		$.post(url,mydata, function(data){
				log = $('#word-likes-tab');
				log.html('');
				if(data != 'none'){
					$('.notes').show();
					$('#word-likes-tab-header').text('Likes');
					$('#word-likes-tab-header').animate({opacity:1},100);
					log.html(data);
					jQuery("time.timeago").timeago();
				} else {
					$('.notes').hide();
					$('#word-likes-tab-header').text('');
					$('#word-likes-tab-header').animate({opacity:0},100);
					log.html('');
				}
		});
	}
	
	function executeSearch(word){
			initialpop =false;
			var title = word;
			//document.title = title + ' definition';
			//$('#foo .search').val(title.replace(/_/g," "));
			//http://en.wiktionary.org/w/api.php?format=jsonfm&action=query&titles=Homo_sapiens&prop=revisions&rvprop=content
			$.getJSON("https://en.wiktionary.org/w/api.php?format=json&action=query&titles=" + title + "&prop=revisions&rvprop=content&callback=?", function(data) {
				//$('body').append('<br/>' + data);
				//alert('test');
				var mydata = data['query']['pages'];
				for (first in mydata) break;

				//alert(first);
				
				mydata = mydata[first]['revisions'];
				for (first in mydata) break;
				if(mydata!==undefined){
					mydata = mydata[first]['*'];
					resultSelect(mydata);
				} else {
					//noresultfound
					noSearchResult();
				}
			});
			
			checkFavoriteButton(word);
			checkWhoElseLiked(word);
		
	}
	function resultSelect(x10){
		myResultsList = new Array();
		$('#result').html('');
		var newResult = x10;
		var m_result;
		var m_temp;
		var result = x10;
		var myIndex = -1;
		//var counter = 0;
		//while(myIndex < 0 && counter < typeList.length){
		m_result = x10.match(/==English==[\s\S]*?----/g);
		if(m_result == null){
			m_result = x10.match(/==English==[\s\S]*/g);
		}
		if(m_result != null){
			x10 = m_result[0];
			for(var i = 0; i < typeList.length; i++){
				var re = new RegExp("={3,4}" + typeList[i] + "={3,4}","g");
				myIndex = x10.search(re);
				//myconsole(x10.match(re));
				if(myIndex >= 0){
				    var x101 = x10;
					x101 = x101.substr(myIndex);
					
					//go to the first #
					x101 = x101.substr(x101.indexOf('#') + 1);
					
					//get the first definition
					x101 = x101.substr(0, x101.indexOf('\n'));
					myResultsList.push(x101);
					x101 = wordoReplaceLinks(x101);
					if(x101.replace(' ','') != ''){
						$('#result').append('<div class="definition"><div class="deftypewrapper"><div class="deftype">' + typeList[i] + '</div></div>' + x101 + '</div>');
						
						$('.mywl-link').show();
					}
				}
				//counter++;
			}
			if(myResultsList.length == 1){
				var myres = myResultsList[0];
				myres = myres.replace(/.*{{(.*?)\|(.*?)}}/,'$2');
				//myconsole('myres3: ' + myres);
				if(myres.match(/\[/) == null && myres.match(/\s/) == null ){
					window.history.pushState(myres.toLowerCase(), document.title, myres.toLowerCase().trim().replace(/ +/g,'_'));
					//myconsole(myres);
					executeSearch(myres.replace(/_+/g,' '));
				}
			}
		} else{
			//myconsole('is null');
		}

		rebindClick();
	}
	
	$(window).bind('popstate', function(event) {
		if(!initialpop){
			if(window.location.pathname.length > 1){
				var currentpage = window.location.pathname.substr(1);
				executeSearch(currentpage.toLowerCase().replace(/_+/g,' '));
				$('#foo .search').val(currentpage);
			}
		}
		initialpop=false;
	});
	function myconsole(logObj){
		try{
			console.log(logObj);
		} catch(e){
		
		}
	}
	function noSearchResult(){
		$('#foo').effect( "bounce", {direction:'right'} );
		$('.mywl-link').hide();
	}
	
	function wordoReplaceLinks(data){
		//remove the {{}} stuff
		data = data.replace(/\{\{.*?\}\}/g,'');
		
		//replace all anchors
		data = data.replace(/\[\[.*?([A-z ]+?)\]\]/g,'[[$1]]');
		
		//try to replace all spaces
		
		var links = data.match(/\[\[([A-z ]+?)\]\]/g);
		if(links!=null){
			$.each(links,function(index,value){
				var value = value.replace(/[\[\]]/g,'');
				value = value.replace(/ /g,'-');
				data = data.replace(/\[\[([A-z ]+?)\]\]/,value);
			});
			
		}
		//replace the rest of the words
		data = wordoReplaceRegularWords(data);
		return data;
	}
	
	function wordoReplaceRegularWords(data){
		var allWords = data.match(/[\w-]+/g);
		var uniquestring = Math.random().toString(36).substr(2);
		data = data.replace(/[\w-]+/g,uniquestring);
		if(allWords!=null){
			//var newData = '';
			$.each(allWords,function(index,value){
				var value = value.replace(/-/g,' ');
				value = value.replace(/(.*)/,'<div class="a">$1</div>');
				var regex = new RegExp(uniquestring);
				data = data.replace(regex,value);
			});
			//newData = newData.replace(/\/div><div/g,'/div> <div');
			//data = newData;
		}
		
		return data;
	}
	
	function rebindClick(){
		//alert('i try');
		$('div.a').off('click');
		$('div.a').click(function(event){
			var linktext = $(this).text();
			$('#foo .search').val(linktext);
			$('#result').animate({opacity: 0},100,function(){
				//window.location.href = '/' + linktext;
				window.history.pushState(linktext.toLowerCase(), document.title, linktext.toLowerCase().trim().replace(/ +/g,'_'));
				executeSearch(linktext.toLowerCase().replace(/_+/g,' '));
				
			});
			event.preventDefault();
		});
		$('#result').animate({opacity: 1},200);
	}
	
});