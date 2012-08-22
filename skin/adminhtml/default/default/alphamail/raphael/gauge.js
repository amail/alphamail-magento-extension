function gauge(min_angle, max_angle) {
	var r = Raphael("holder", 700, 300)

	min_angle = min_angle || 0;
	max_angle = max_angle || 360;
	
	var bg_center = [172, 172],
	    center = [67, 13];	

	r.image("img/gauge.png", 0, 0, 346, 345)

	var pointer = {
		center: center,
		elem: r.image("img/gauge-pointer.png", 0, 0, 84, 27).attr({
			x: bg_center[0]-center[0],
			y: bg_center[1]-center[1],
			rotation: min_angle + " " + bg_center[0] + " " + bg_center[1]
		})
    	};
	
	this.set = function(percent, duration) {
		pointer.elem.animate({rotation: percent + " " + bg_center[0] + " " + bg_center[1]}, duration, "elastic");
	};
	
	return this;
}
