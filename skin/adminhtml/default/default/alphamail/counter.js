function update_counter(id, old_count, new_count, interval) {
	/* update a counter */
	var diff = new_count - old_count;
	diff = diff < 0 ? -diff : diff;

	if (diff > 0) {
		if (old_count == 0) {
			set_counter(id, new_count);
		}
		else {
			var count_delay = 1000 / (diff / interval);
			var j = 0;
	
			if (new_count < old_count) {
				for (var i = old_count; i >= new_count; i--) {
					setTimeout("set_counter('"+id+"', "+i+");", count_delay*j);
					j++;
				}
			} else {
				for (var i = old_count; i <= new_count; i++) {
					setTimeout("set_counter('"+id+"', "+i+");", count_delay*j);
					j++;
				}
			}
		}
	}
}

function set_counter(id, count) {
	/* set a counter */
	var c = $("#"+id);
	var str = ""+count;
	
	/* padding with zeros */
	while (str.length < 5) {
		str = "0" + str;
	}

	/* set number by number */
	for (var i = 0; i < 5; i++) {
		var num = str[i];
		var children = $(c.children()[i]).children();
		
		$(children[0]).attr("class", "num_top num_"+num+"n");
		$(children[1]).attr("class", "num_top num_"+num+"s");
	}
}
