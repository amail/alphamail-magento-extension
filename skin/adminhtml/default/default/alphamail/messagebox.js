/* Message Box for AM Dashboard */

MessageBox.prototype.id;
MessageBox.prototype.title;
MessageBox.prototype.content;
MessageBox.prototype.layer;
MessageBox.prototype.msgbox;
MessageBox.prototype.buttons = [];

function MessageBox(id, prop) {
	this.id = id;
	this.title = prop.title;
	this.content = prop.content;
	this.buttons = prop.buttons;
}

MessageBox.prototype.setTitle = function(value) {
	this.title = value;
}

MessageBox.prototype.setContent = function(value) {
	this.content = value;
}

MessageBox.prototype.addButton = function(btn) {
	this.buttons.push(btn);
}

MessageBox.prototype.hide = function() {
	this.msgbox.fadeOut(300, function() {
		$(this).remove();
	});
	this.layer.fadeOut(300, function() {
		$(this).remove();
	});
}

MessageBox.prototype.show = function(object) {
	var box = this;

	this.msgbox = 
	$('<div id="msgbox_'+this.id+'" class="alert">').html(
		'<div class="widget">'+
			'<div class="w_header wcolor_gray"><div class="w_header_text">'+this.title+'</div></div>'+
			'<div class="w_menu"></div>'+
			'<div class="w_main">'+
				'<div class="w_main_inner">'+
					'<div class="w_main_inner2">'+
						this.content+
					'</div>'+
				'</div>'+
				'<div class="w_buttonarea">'+
					'<div id="'+this.id+'_buttonarea" class="w_buttonarea_inner"></div>'+
				'</div>'+
			'</div>'+
		'</div>'
	).insertBefore(object);

    this.msgbox.css("position","fixed");
    this.msgbox.css("top", (($(window).height() - this.msgbox.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.msgbox.css("left", (($(window).width() - this.msgbox.outerWidth()) / 2) + $(window).scrollLeft() + "px");

	this.layer = $('<div id="msgbox_layer_'+this.id+'" class="locker" style="display: none;"></div>').insertBefore(object);
	this.layer.click(function() {
		box.hide();
	});
	
	for (var i = 0; i < this.buttons.length; i++) {
		var btn = $('<input type="button" class="btn-silver" border="0" value="'+this.buttons[i].text+'" />');
		
		if (this.buttons[i].color == "green") {
			btn.addClass("btn-silver_done");
		}
		
		btn.click(this.buttons[i].fn);
		this.msgbox.find("#"+this.id+"_buttonarea").append(btn);
	}
	
	//$(this.msgbox).offset({"top": (object.parents("body").height()/2)-($(this.msgbox).height()/2), "left": (object.parents("body").width()/2)-($(this.msgbox).width()/2)});
	$(this.msgbox).fadeIn(300);
	$(this.layer).fadeIn(300);
}
