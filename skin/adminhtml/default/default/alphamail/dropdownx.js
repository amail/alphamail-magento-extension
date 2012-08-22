function dropdownx(select, id, ico) {	
	/* create the dropdown list */
	$('<div id="'+id+'list"></div>').insertBefore(select);
	$("#"+id+"list").addClass("dropdownx_list");
	$("#"+id+"list").width(300); //select.width()
	$("#"+id+"list").position(select.position());	
	$("#"+id+"list").css({"margin-top": select.height()+16});
	$("#"+id+"list").hide();
	
	/* create the select button */
	var new_select = $("<div>");
	new_select.addClass("dropdownx_box");

	var item = $("<div>");
	item.addClass("dropdownx_listitem");
	item.attr("id", id+"_item_selected");
	
	var icon = $("<div>");
	icon.addClass("dropdownx_icon");
	icon.attr("id", id+"_icon_selected");
	icon.css("background-image", "url('"+ico+"')");
	
	var boxtext = $("<div>");
	boxtext.addClass("dropdownx_text");
	boxtext.attr("id", id+"_text_selected");
	boxtext.html(select.find("option:selected").text());
	
	var open = $("<div>");
	open.addClass("dropdownx_box_icon");
	open.attr("id", id+"_open_selected");
	
	item.append(icon);
	item.append(boxtext);
	item.append(open);
	new_select.append(item);

	/* replace the old button with the new */
	select.parent().append(new_select);
	select.hide();
	
	/* create list items */
	var height = 0;
	$("#"+select.attr("id")+" option").each(function() {
		var value = this.value;

		var item = $("<div>");
		item.addClass("dropdownx_listitem");
		item.attr("id", id+"_item_"+value);
		
		var icon = $("<div>");
		icon.addClass("dropdownx_icon");
		icon.attr("id", id+"_icon_"+value);
		icon.css("background-image", "url('"+ico+"')");
		
		var text = $("<div>");
		text.addClass("dropdownx_text");
		text.attr("id", id+"_text_"+value);
		text.html(this.text);
		
		item.append(icon);
		item.append(text);
		
		/* click event */
		item.click(function() {
			select.val(value);
			select.change();
		});
		
		/* hover effect */
		item.mouseover(function() {
			$(".dropdownx_selected").removeClass("dropdownx_selected");
		});
		
		$("#"+id+"list").append(item);
		height += 30;
	});

	/* on change */
	select.change(function () {
		boxtext.html(select.find("option:selected").text());
	});

	/* set height */
	height = height > 210 ? 210 : height;
	$("#"+id+"list").css("height", height);
	
	/* set selected */
	$("#"+id+"_item_"+select.val()).addClass("dropdownx_selected");

	/* open the list on click */
	item.click(function() {
		this.blur;
		
		if ($("#"+id+"list:visible").length > 0) {
			$(document).click();
		}
		else {
			$(document).click();
			$("#"+id+"list").show();

			document.onclick = function() {
				document.onclick = function() {
					$("#"+id+"list").hide();
					document.onclick = null;
				}
			}
		}
	});
}

function dropdownx_remove(id) {
	$("#"+id+"list").remove();
	$("#"+id+"_layer").remove();
	document.onclick = null;
}
