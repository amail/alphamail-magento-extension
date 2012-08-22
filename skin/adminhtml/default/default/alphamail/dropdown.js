function dropdown(select, id, get_data) {	
	$('<div id="'+id+'list"></div>').insertBefore(select);
	$("#"+id+"list").addClass("dropdown_list");
	$("#"+id+"list").width(158); //select.width()
	$("#"+id+"list").position(select.position());	
	$("#"+id+"list").css({"margin-top": select.height()+5});
	$("#"+id+"list").hide();
	
	$("#"+select.attr("id")+" option").each(function() {
		var value = this.value;

		var item = document.createElement("div");
		item.className = "dropdown_listitem";
		item.id = id+"_item_"+value;
		
		var spark = document.createElement("div");
		spark.className = "dropdown_sparkline";
		spark.id = id+"_sparkline_"+value;
		
		var text = document.createElement("div");
		text.className = "dropdown_text";
		text.id = id+"_text_"+value;
		text.innerHTML = this.text;
		
		var loader = document.createElement("img");
		loader.src = "img/spark-loader.gif";
		loader.alt = this.text;
		
		spark.appendChild(loader);
		item.appendChild(spark);
		item.appendChild(text);
		
		$(item).click(function() {
			select.val(value);
			select.change();
		});
		
		$(item).mouseover(function() {
			$(".dropdown_selected").removeClass("dropdown_selected");
		});
		
		$("#"+id+"list").append(item);
	});

	$("#"+id+"_item_"+select.val()).addClass("dropdown_selected");

	var layer = document.createElement("div");
	layer.className = "dropdown_layer";
	layer.id = id+"_layer";
	
	$(layer).width(select.width()+2);
	$(layer).height(select.height()+2);
	$(layer).position(select.position());
	$(layer).css({"margin-top": -(select.height()+4)});

	select.parent().append(layer);

	$("#"+id+"_layer").click(function() {
		this.blur;
		
		if ($("#"+id+"list:visible").length > 0) {
			$(document).click();
		}
		else {
			$(document).click();
			$("#"+id+"list").show();

			$("#"+select.attr("id")+" option").each(function() {	
				get_data(this.value, id+"_sparkline_"+this.value);
			});
		
			document.onclick = function() {
				document.onclick = function() {
					$("#"+id+"list").hide();
					document.onclick = null;
				}
			}
		}
	});
}

function dropdown_remove(id) {
	$("#"+id+"list").remove();
	$("#"+id+"_layer").remove();
	document.onclick = null;
}
