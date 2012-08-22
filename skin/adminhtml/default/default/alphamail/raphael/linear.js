function graph_linear(labels, ds, holderid, clickev) {
	function getAnchors(p1x, p1y, p2x, p2y, p3x, p3y) {
		return {
			x1: p2x,
			y1: p2y,
			x2: p2x,
			y2: p2y
		};
	}
   
	/* draw */
	var width = $("#"+holderid).width(),
	height = 200,
	leftgutter = 0,
	bottomgutter = 40,
	topgutter = 5,
	color = ["#c3f63a", "#f7914e", "#aac7e1", "#a71aeb"] /*color = ["#1ac6eb", "#eb511a", "#89eb1a", "#a71aeb"]*/,
	r = Raphael(holderid, width, height),
	txt = {font: '9px Sans, Arial, Verdana', fill: "#000"},
	txt1 = {font: '9px Sans, Arial, Verdana', fill: "#000", "text-anchor": "start"},
	txt2 = {font: '10px Sans, Arial, Verdana', fill: "#000", "text-anchor": "start"},
	X = (width - leftgutter) / labels.length;
	
	/* find max */
	var max = 10;
	
	for (var i = 0; i < ds.length; i++) {
		max = Math.max.apply(Math, ds[i]) > max ? Math.max.apply(Math, ds[i]) : max;
	}	
		
	var Y = (height - bottomgutter - topgutter) / max;

	var label = r.set(),
	is_label_visible = false,
	leave_timer,
	blanket = r.set();

	label.push(r.circle(0, 15, 4).attr("fill", "#c3f63a").hide());
	label.push(r.text(15, 15, "4503 "+dict["sent"]).attr(txt1).attr({fill: "#fff"}).hide());
		
	label.push(r.circle(0, 25, 4).attr("fill", "#f7914e").hide());
	label.push(r.text(15, 25, "4503 "+dict["bounces"]).attr(txt1).attr({fill: "#fff"}).hide());

	label.push(r.circle(0, 35, 4).attr("fill", "#aac7e1").hide());
	label.push(r.text(15, 35, "4503 "+dict["opens"]).attr(txt1).attr({fill: "#fff"}).hide());
	
	label.push(r.circle(0, 45, 4).attr("fill", "#a71aeb").hide());
	label.push(r.text(15, 45, "4503 "+dict["clicks"]).attr(txt1).attr({fill: "#fff"}).hide());
	
	label.push(r.text(0, 0, "Thu 12 Oct").attr(txt2).attr({fill: "#fff", "font-weight": "bold"}).hide());
	
	var frame = r.popup(100, 200, label, "right").attr({fill: "#000", stroke: "none", "fill-opacity": .95}).hide();

	var dots = [];
	var graph = [];
	var lbls = [];
	
	for (var i = 0; i < ds.length; i++) {
		dots[i] = [];
	
    		graph[i] = graph_data(ds, color[i], i);
    		
    		graph[i].bgpp = graph[i].bgpp.concat([graph[i].x, graph[i].y, graph[i].x, graph[i].y, "L", graph[i].x, height - bottomgutter, "z"]);
    		graph[i].bgp.attr({path: graph[i].bgpp});
    		
    		graph[i].p = graph[i].p.concat([graph[i].x, graph[i].y, graph[i].x, graph[i].y]);
		graph[i].path.attr({path: graph[i].p});
    	}
    
	frame.toFront();
	
	for (var i = 0; i < label.length; i++) {
		label[i].toFront();
	}
	
	blanket.toFront();
	
	/* draw columns */
	var w = dots[0][1].x - dots[0][0].x;
	
	tmp = r.path().attr({path: ["M", 0.5, topgutter+0.5, "H", width+0.5], stroke: "#dedede","stroke-width": 1, "vector-effect": "non-scaling-stroke"}).toBack();
	tmp = r.path().attr({path: ["M", 0.5, topgutter+(height-bottomgutter)/2+0.5, "H", width+0.5], stroke: "#dedede","stroke-width": 1, "vector-effect": "non-scaling-stroke"}).toBack();
	tmp = r.path().attr({path: ["M", 0.5, height-bottomgutter+2.5, "H", width+0.5], stroke: "#444444","stroke-width": 2, "vector-effect": "non-scaling-stroke"}).toFront();

	tmp = r.text(0.5, topgutter+10, max).attr(txt1);
	tmp = r.text(0.5, topgutter+(height-bottomgutter)/2+10.5, Math.round(max/2)).attr(txt1);

	for (var i = 0; i < dots[0].length; i++) {
		if (labels[i][0] != "") {
			tmp = r.path().attr({path: ["M", dots[0][i].x+0.5, topgutter+0.5, "V", height-bottomgutter+10+0.5], stroke: "#dedede","stroke-width": 1, "vector-effect": "non-scaling-stroke"}).toBack();
		}
	}
	for (var i = 0; i < dots[0].length; i++) {
		if (labels[i][2] == 0) {
			var line = r.rect(dots[0][i].x+0.5, topgutter+0.5, w+1.5, height-bottomgutter).attr({fill: "#f4f4f4", stroke: "none"}).toBack();
		}
	}

	function graph_data(ds, color, data_idx) {
		var data = ds[data_idx];
 		var path = r.path().attr({stroke: color, "stroke-width": 3, "stroke-linejoin": "round"});
 		var bgp = r.path().attr({stroke: "none", opacity: .1, fill: color});
 		var p, bgpp;
 		
		for (var i = 0, ii = labels.length; i < ii; i++) {
			var y = Math.round(height - bottomgutter - Y * data[i]),
			x = Math.round(leftgutter + X * (i + .5));

			if (!i) {
				p = ["M", x, y, "C", x, y];
				bgpp = ["M", leftgutter + X * .5, height - bottomgutter, "L", x, y, "C", x, y];
			}
			
			if (i && i < ii - 1) {
				var Y0 = Math.round(height - bottomgutter - Y * data[i - 1]),
				X0 = Math.round(leftgutter + X * (i - .5)),
				Y2 = Math.round(height - bottomgutter - Y * data[i + 1]),
				X2 = Math.round(leftgutter + X * (i + 1.5));
			
				var a = getAnchors(X0, Y0, x, y, X2, Y2);
				p = p.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
				bgpp = bgpp.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
			}

			var dot = r.circle(x, y, 0).attr({fill: color, stroke: "#fff", "stroke-width": 1, r: 3});
			dots[data_idx].push({"dot": dot, "x": x, "y": y});
			
			if (data_idx == 0) {
				txt.fontWeight = 500;
			
				var t = r.text(x, height - 16, labels[i][0]).attr(txt).toBack();
				blanket.push(r.rect(leftgutter + X * i, 0, X, height - bottomgutter).attr({stroke: "none", fill: "#fff", opacity: 0}));
				
				lbls.push(t);
				var rect = blanket[blanket.length - 1];

				if (clickev != undefined) {
					rect.attr({cursor: "pointer"});
				}

				(function (x, y, index, lbl, dot, txt) {
					var timer, i = 0;
				
					if (clickev != undefined) {
						rect.click(function() {
							clickev(labels[index][3], labels[index][1]);
						});
					}
					
					rect.hover(function () {
						clearTimeout(leave_timer);
						var side = "right";
						var padding = 12;
				
						label[1].attr({text: ds[0][index]+" "+dict["sent"]});
						label[3].attr({text: ds[1][index]+" "+dict["bounces"]});
						label[5].attr({text: ds[2][index]+" "+dict["opens"]});
						label[7].attr({text: ds[3][index]+" "+dict["clicks"]});
						label[8].attr({text: labels[index][1]});
				
						if (x + frame.getBBox().width > width) {
							side = "left";
							padding = -12;
						}
				
						var dot_y = height / 2;
						var ppp = r.popup(x + padding, dot_y, label, side, 1);
					
						frame.show().stop().animate({path: ppp.path}, 200 * is_label_visible);
						
						for (var i = 0; i < label.length; i++) {
							label[i].show().stop().animateWith(frame, {translation: [ppp.dx, ppp.dy]}, 200 * is_label_visible);
						}

						for (var e = 0; e < dots.length; e++) {
							dots[e][index].dot.animate({"r": 5}, 500, "bounce");
							dots[e][index].dot.attr("stroke-width", 2);
						}
						
						lbls[index].attr({"font-weight": 800, "font-size": "12px"});
						
						is_label_visible = true;
					}, function () {
						for (var e = 0; e < dots.length; e++) {
							dots[e][index].dot.animate({"r": 3}, 500, "bounce");
							dots[e][index].dot.attr("stroke-width", 1);
						}
						
						lbls[index].attr({"font-weight": 500, "font-size": "9px"});
						
						leave_timer = setTimeout(function () {
							frame.hide();
							for (var i = 0; i < label.length; i++) {
								label[i].hide();
							}
							is_label_visible = false;
						}, 1);
					});
				})(x, y, i, labels[i], dot, t);
			}
		}
		
		return {"x": x, "y": y, "p": p, "path": path, "bgp": bgp, "bgpp": bgpp};
    	}
}
