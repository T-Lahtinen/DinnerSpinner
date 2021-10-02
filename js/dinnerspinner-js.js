$(document).ready(function(){

// Check the selection box cookie when page loads.
	checkSelectionCookie();

// Uncheck all checked checkboxes on page load.
	if ($(".restaurant-selection").prop("checked", true)) {
		$(".restaurant-selection").prop("checked", false);
	}

//Create the cookie functions.
	function setCookie(cname, cvalue, exyears) {
		const d = new Date();
		d.setTime(d.getTime() + (exyears*365*24*60*60*1000));
		let expires = "expires="+ d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
		let name = cname + "=";
		let ca = document.cookie.split(';');
		for(let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
		  	}
		}
		return "";
	}

	function checkSelectionCookie() {
		let selection = getCookie("selection_box");
		if (selection == "foods") {
			$(".restaurants").hide();
			$(".foods").show();
		} else {
			$(".restaurants").show();
			$(".foods").hide();
		}
	}

// Set different variables.
	var deg = rand(0, 360);
	var slowDownRand = 0;
	var ctx = canvas.getContext('2d');
	var width = canvas.width;
	var center = width/2;
	var isStopped = false;
	var lock = false;
	var ai = '';
	var winner = '';
	var link = document.getElementById("style");

// Animation frame and it's request id.
	var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
	var cancelAnimationFrame = window.cancelAnimationFrame || window.mozCancelAnimationFrame;
	var myReqID;

// Wheel functions.
	function rand(min, max) {
		return Math.random() * (max - min) + min;
	}

	function deg2rad(deg) {
		return deg * Math.PI/180;
	}

	function drawSlice(deg, color, sliceDeg) {
		ctx.beginPath();
		ctx.fillStyle = color;
		ctx.moveTo(center, center);
		ctx.arc(center, center, width/2, deg2rad(deg), deg2rad(deg+sliceDeg));
		ctx.lineTo(center, center);
		ctx.fill();
	}

	function drawText(deg, text) {
		ctx.save();
		ctx.translate(center, center);
		ctx.rotate(deg2rad(deg));
		ctx.textAlign = "right";
		ctx.fillStyle = "#fff";
		ctx.font = 'bold 22px sans-serif';
		ctx.fillText(text, 280, 10);
		ctx.restore();
	}

	function drawImg(label, color, slices, sliceDeg) {
		ctx.clearRect(0, 0, width, width);
		for (var i=0; i<slices; i++) {
			drawSlice(deg, color[i], sliceDeg);
			drawText(deg+sliceDeg/2, label[i]);
			deg += sliceDeg;
		}
	}

	function anim(label, color, slices, sliceDeg, speed) {

		deg += speed;
		deg %= 360;

// Increment speed.
		if (!isStopped && speed<3) {
			speed = speed+1 * 0.1;
		}
// Decrement Speed.
		if (isStopped) {
			if (!lock) {
				lock = true;
				slowDownRand = rand(0.994, 0.998);
			}
			speed = speed > 0.2 ? speed*=slowDownRand : 0;
		}
// Stopped!
		if (lock && !speed) {
// Winner !!!
			$("body.main").addClass("winner");

// deg 2 Array index.
			ai = Math.floor(((360 - deg - 90) % 360) / sliceDeg);
// Fix negative index.
			ai = (slices+ai)%slices;
			winner = label[ai];
// Append winner info to ui.
			$("#info-box-wrapper, .selection-boxes, .wheel-bottom").addClass("hide");
			return $(".winner-box").append("Onneksi olkoon <span class='winner'>"+ winner +"</span>!");
		}
// Draw a slice.
		drawImg(label, color, slices, sliceDeg, speed);

// Animation frame.
		myReqID = requestAnimationFrame(function() {
			anim(label, color, slices, sliceDeg, speed)
		});
	};

// Stop the wheel once the button is clicked.
	document.getElementById("spin").addEventListener("mousedown", function(){
		isStopped = true;
	}, false);

// Toggle which checkbox selection should be shown.	
	$(".selection-toggle").click(function(){
		$(".selection-box-wrapper").toggle();

// Set a correct cookie value and update notice text.
		if ($(".restaurants").css("display") == "none") {
			setCookie("selection_box", "foods", 1000);
			$('.notice-text').text(' vaihtoehtoa');
		} else {
			setCookie("selection_box", "restaurants", 1000);
			$('.notice-text').text(' ravintolaa');
		}
// Also uncheck all checked checkboxes.
		if ($(".restaurant-selection").prop("checked", true)) {
			$(".restaurant-selection").prop("checked", false);
		}
	});

// Hide the stop button one it's been clicked.
	$("#spin").click(function(){
		$(".style-switch").hide();
		$("#spin").css({transition: "3s", opacity: "0"});
	});

// Insert checked restautants in to the wheel once one is clicked.
	$("input.restaurant-selection, .selection-toggle").on("click", function(event) {

		var checkedRestaurantIDs = [];
		var label = [];
		var color = [];

// Loop through chosen restaurants.
		$(".restaurant-selection").each(function( index ) {
// If checked add to array.
			if ($( this ).prop('checked')) {
// Push input value, which is restaurants ID.
				checkedRestaurantIDs.push($( this ).val())
			}
		});

// Loop through restaurants json.
		var json_selected = window.json_selected;
		$.each(json_selected, function( index, value ) {

// If ID = json checked restaurant ID --> use that data.
			if (checkedRestaurantIDs.indexOf(value['ID']) !== -1) {
				label.push(value['Name']);
				color.push(value['Color']);
			}
		});

// Stop the previous animation.
		cancelAnimationFrame(myReqID);

// Create new animation.
		anim(label, color, label.length, 360/label.length, 0);
	});

// Show/hide spinner stop button if conditions are met.
	var currently_selected = '';

	$(".restaurant-selection, .selection-toggle").click(function(){
		currently_selected = $('input.restaurant-selection:checked').length;
// Check if we have less than 2 items selected, in which case hide the stop button and inform the user.
		if (currently_selected < 2) {
			$("#spin").hide();
			$(".notice-message").show();
		  } else {
			$("#spin").show();
			$(".notice-message").hide();
		};
// Remove gif bg if we have at least 1 thing selected.
		if (currently_selected > 0) {
			$("#wheel").removeClass("wheel-bg");
			$("#wheel").addClass("wheel-pointer");
		} else {
			$("#wheel").addClass("wheel-bg");
			$("#wheel").removeClass("wheel-pointer");
		};
	});

// Style change.
	$(".style-toggle").click(function() {

		if ($(link).attr("href") == "css/dinnerspinner-style.css") {
			link.setAttribute("href", "css/dinnerspinner-dark-style.css");
			setCookie("style_css", "dark", 1000);
		} else if ($(link).attr("href") == "css/dinnerspinner-dark-style.css") {
			link.setAttribute("href", "css/dinnerspinner-style.css");
			setCookie("style_css", "dinnerspinner", 1000);
		}			
	});

// Hover color for spin icon.
	$("#spin").mouseenter(function() {
		$(".spin-icon img").css("filter", "invert(49%) sepia(99%) saturate(1205%) hue-rotate(152deg) brightness(95%) contrast(101%)");
	}).mouseleave(function() {
		$(".spin-icon img").css("filter", "invert(44%) sepia(92%) saturate(5903%) hue-rotate(177deg) brightness(96%) contrast(101%)");
	});

// By default hide "about" box on smaller screen sizes.
	if($(window).width() < 1100){
		$("#info-box .about-text").hide()
		$("#info-box .show-more").show();
// Make it so the text can be toggled shown or hidden
		$("#info-box").click(function() {
			$("#info-box .about-text").slideToggle();
			$("#info-box .show-more, #info-box .show-less").toggle();
		});
	}

});