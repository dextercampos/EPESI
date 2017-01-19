var statusbar_message_t='';
statusbar_message=function(text){
	statusbar_message_t=text;
};
statusbar_fade_count = 0;
statusbar_fade=function(fade_count){
	if (fade_count && statusbar_fade_count!=fade_count) return;
	var seconds = 0.2;
	wait_while_null('jq(\'#Base_StatusBar\').get(0)','jq(\'#Base_StatusBar\').fadeOut();');
	jq('#Base_StatusBar').get(0).onclick = null;
	setTimeout('statusbar_fade_double_check('+statusbar_fade_count+')',seconds*1000+50);
};
statusbar_fade_double_check = function(fade_count) {
	if (fade_count && statusbar_fade_count!=fade_count) jq('#Base_StatusBar').show();
	else jq('#Base_StatusBar').get(0).onclick = Function("if(!Epesi.procOn)statusbar_fade();");
};
updateEpesiIndicatorFunction=function(){
	Epesi.indicator_text='statusbar_text';
	Epesi.indicator='Base_StatusBar';
	statbar = jq('#Base_StatusBar').get(0);
	if (!statbar) {
		setTimeout('updateEpesiIndicatorFunction();',3000);
		return;
	}
	jq('#epesiStatus').hide();
	jq('#main_content').show();
	statbar.onclick = Function("if(!Epesi.procOn)statusbar_fade();");
	statbar.style.display='none';
	Epesi.updateIndicator=function(){
		statbar = jq('#Base_StatusBar').get(0);
		statusbar_fade_count++;
		if(Epesi.procOn){
            jq('#dismiss').hide();
			statbar.style.display='block';
			cache_pause=true;
		}else{
			if(statusbar_message_t!='') {
                jq('#dismiss').show();
				t=jq('#statusbar_text');
				if(t.length>0)t.html(statusbar_message_t);
				statusbar_message('');
				setTimeout('statusbar_fade('+statusbar_fade_count+')',5000);
			}else{
				statusbar_fade();
			};
			cache_pause=false;
		};
	};
};

updateEpesiIndicatorFunction();
