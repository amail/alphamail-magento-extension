function pieChart(cx, cy, r, values, labels, holder, bounce) {
    var height = 265 + labels.length*(16+20);
    var paper = Raphael(holder, 380, height),
        rad = Math.PI / 180,
        chart = paper.set();
        
    $("#"+holder).height(height);
        
    function sector(cx, cy, r, startAngle, endAngle, params) {
	if (startAngle == 0 && endAngle == 360) {
		startAngle = 0.01;
	}

	var x1 = cx + r * Math.cos(-startAngle * rad),
	    x2 = cx + r * Math.cos(-endAngle * rad),
	    y1 = cy + r * Math.sin(-startAngle * rad),
	    y2 = cy + r * Math.sin(-endAngle * rad);
	
	if (bounce)
		params.scale =  [0.1, 0.1, cx, cy];
	
	var p = paper.path(["M", cx, cy, "L", x1, y1, "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2, "z"]).attr(params);
	
	if (bounce)
		p.animate({scale: [1, 1, cx, cy]}, 800, "bounce");

	return p;
    }
    
    var colors = [
    			["#f7914e", "#d86429"], 
    			["#aac7e1", "#7faed3"], 
    			["#ffe080", "#faae16"], 
    			["#d2e39e", "#c1d87b"],
    			["#7395d6", "#3c6bc7"],
    			["#73d674", "#3cc73e"],
    			["#d6cf73", "#c7bc3c"],
    			["#d68673", "#c7583c"],
    			["#ad73d6", "#8d3cc7"],
    			["#73bfd6", "#3ba6c6"],
    		];
    
    if (values.length == 0) {
    	colors = [["#dedede", "#aaaaaa"]];
    	labels = [{"lbl":dict["no_data_display"], "count": ""}];
    	values = [100];
    }
    
    var angle = 0,
        total = 0,
        start = 0,
        process = function (j) {
            var value = values[j],
                angleplus = 360 * value / total,
                popangle = angle + (angleplus / 2),
                color = colors[j][0],
                ms = 500,
                delta = 30,
                bcolor = colors[j][1],
                p = sector(cx, cy, r, angle, angle + angleplus, {gradient: "90-" + bcolor + "-" + color, stroke: "none", "stroke-width": 0}),
                txt = paper.text(cx + (r - delta) * Math.cos(-popangle * rad), cy + (r - delta) * Math.sin(-popangle * rad), value+"%").attr({fill: "#fff", stroke: "none", opacity: 0, "font-family": 'Sans, Arial, Verdana', "font-size": "14px"});
           
           	var height = 16+20;
           	
         	paper.rect(cx-r, 250+(j*height)-5, 10, 10).attr({stroke: "none", fill: bcolor});
         	var legend = paper.text((cx-r)+15, 250+(j*height), labels[j].lbl).attr({
         		fill: "#444444", 
         		stroke: "none", 
         		"font-family": 'Sans, Arial, Verdana', 
         		"font-size": "12px", 
         		"text-anchor": "start"
         	});
         	
         	if (labels[j].count != "") {
		 	var sub_text = paper.text((cx-r)+15, 250+(j*height+16), labels[j].count + " ("+values[j]+"%)").attr({
		 		fill: "#444444", 
		 		stroke: "none", 
		 		"font-family": 'Sans, Arial, Verdana', 
		 		"font-size": "11px", 
		 		"text-anchor": "start"
		 	});
		 }
            p.mouseover(function () {
                p.animate({scale: [1.1, 1.1, cx, cy]}, ms, "bounce");
                txt.animate({opacity: 1}, ms, "bounce");
                legend.attr({"font-weight": "bold"});
            }).mouseout(function () {
                p.animate({scale: [1, 1, cx, cy]}, ms, "bounce");
                txt.animate({opacity: 0}, ms);
                legend.attr({"font-weight": "normal"});
            });
            angle += angleplus;
            chart.push(p);
            chart.push(txt);
            start += .1;
        };
    for (var i = 0, ii = values.length; i < ii; i++) {
        total += values[i];
    }
    for (var i = 0; i < ii; i++) {
        process(i);
    }
    return chart;
};
