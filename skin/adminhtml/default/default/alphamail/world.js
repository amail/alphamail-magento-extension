/* the world (map) */

world.prototype.adjustOffset = 248;
world.prototype.targetHeight = 1650;
world.prototype.paper;
world.prototype.map = {};
world.prototype.mc;
world.prototype.map_img;

function world(map_container) {
	this.mc = map_container;
	this.map_img = $("#"+map_container.attr("id")+"_img");
}

world.prototype.init = function() {
	/* init the map */
	this.map.scale = this.map_img.width() / 3600;
	
	this.mc.width(this.map_img.width());
	this.mc.height(this.map_img.width() * 0.5);
	
	/* create the paper canvas for Raphael */
	this.paper = Raphael("map", this.mc.width(), this.mc.height());
	this.paper.clear();
	this.paper.rect(0, 0, this.mc.width(), this.mc.height(), 10).attr({"fill": "none", "stroke": "none"});
};

world.prototype.addDot = function(p, color) {
	/* add a dot to the map */
	
	/* Miller cylindrical projection */
	/* TODO:  The latitude is scaled by a factor of 4/5, projected according to Mercator, 
		  and then the result is multiplied by 5/4 to retain scale along the equator.
		  http://en.wikipedia.org/wiki/Miller_cylindrical_projection */
		  
	/* calculate coordinates on the screen */
	var latitude = (-p.lat + 90) * 10.0 * this.map.scale;
	var longitude = (p.lng + 180) * 10.0 * this.map.scale;
	
	var circle = this.paper.circle(longitude, latitude-12, 5);

	circle.attr({
		"opacity": 1, 
		"fill": color, 
		"stroke-width": 3,
		"stroke": color,
		"stroke-opacity": 0.3
	});
	
	circle.animate({
		"fill": color, 
		"stroke": color, 
		"stroke-width": 11, 
		"opacity": 0, 
		"stroke-opacity": 0
	}, 2500, function() {
		circle.remove();
	});
}

