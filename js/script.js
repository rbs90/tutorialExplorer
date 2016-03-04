function loadVideo(vid){
		//open section:
		$("#course").accordion( "option", "active",  $(".section").index(vid.parent()));

		$(".currentVideo").removeClass("currentVideo");
		vid.addClass("currentVideo");
		$("#video_src").attr("src", vid.attr("file"));
		$("#videoArea").load();
		$("#videoArea")[0].play();
		Cookies.set('tut_' + tut_hash, $(".video").index($(".currentVideo")[0]), { expires: 2147483647 });
}

window.onload = function(){
	var params={};window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(str,key,value){params[key] = value;});

	if(!params["tut"]) {
		$("#courses").accordion({
			heightStyle: "content"
		});

		$(".tut_progress").each(function(){
			var curr = $(this).attr("curr");
			var all = $(this).attr("count");
			$(this).progressbar({
				value: curr/all * 100
			});
		});
		return;
	}


	$("#course").accordion({
		heightStyle: "content"
	});

	//ignore clicks on Downloads
	$(".ui-accordion-header .sectionDownload").click(function(e) { e.stopPropagation(); });


	tut_hash = calcMD5($("#title").text());

	//load first vid:
	cookieVid = Cookies.get('tut_' + tut_hash);
	if(cookieVid)
		loadVideo($(".video").eq(cookieVid));
	else
		loadVideo($(".video").eq(0));

	$("#progressbar").progressbar({
  		value: 100
    	});

	$("#videoArea").bind("ended", function() {
		$("#progressbar").fadeIn();

		var remaining = 10;

		function countDown(){
			if (remaining > 0) {
				$("#progressbar .label").text("N\u00e4chstes Video in " + remaining + "s...");
				$("#progressbar").progressbar('value', (remaining-1)/10 * 100 + 0.0001); //animation to next secound, +0.0001 => no animation when setting to 0
				remaining = remaining-1;
			} else {
				clearInterval(interval);
				$("#progressbar").fadeOut();
				$("#progressbar").progressbar('value', 100);
				var next_vid = $(".video").eq($(".video").index($(".currentVideo")[0]) + 1);
				loadVideo(next_vid);
			}
		}


		var interval = setInterval(countDown, 1000);

	});

	$(".video").click(function(){
		loadVideo($(this));
	});
}
