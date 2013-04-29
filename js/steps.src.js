/**
 * Update datetime picker element
 * Used for static & dynamic added elements (when clone)
 */
var steps = {
	update : function() {
		var $ = jQuery;
		$(".stepsbox").each(function(n) {
			var box = $(this).find(".twostepcombo");
			var origname = box.attr("name");
			if (origname.indexOf('[') > 0) {
				origname = origname.substring(0, origname.indexOf('[')) + "[" + n + "]";
			} else {
				origname = origname + "[" + n + "]";
			}
			box.attr("name", origname);
		});
		$(".stepsbox").last().step2box();
		console.log("update is on");
		//	stepsboxLast = $(".stepsbox").last();
		//	stepsboxLast.children(".select2-container").remove();
		//	stepsboxLast.children(".step2.section_step_hkm").addClass("hidden");
		//	stepsboxLast.step2box();
		//this.ini2();
	},
	ini2 : function() {
		var s = this, $ = jQuery, data_obj = [], fieldID;
		$(".stepsbox").each(function(n) {
			var box = $(this).find(".twostepcombo");
			var origname = box.attr("name");
			box.attr("name", s.rename2Array(origname, n));
			$(this).step2box();
		});
	},
	rename2Array : function(str, n) {
		var output = '';
		if (str.indexOf('[') > 0) {
			output = str.substring(0, str.indexOf('[')) + "[" + n + "]";
		} else {
			output = str + "[" + n + "]";
		}
		return output;
	}
}
jQuery.fn.step2box = step2stepfunction;
function step2stepfunction() {
	var $ = jQuery;
	var self = this;
	//self.find(".select2-container.combobox").remove();
	var $step1 = self.find('.step1.combobox:not(.select2-container)');
	var fieldID = $step1.attr('id');
	/*$.data(this, "save", {
	internal_data : window["twosteps_" + fieldID],
	options : window["twosteps_options_" + fieldID],
	storedObj : {},
	inputval_current : {},
	basic_id : $step1.attr('id')
	});
	*/
	//$step2 =

	//$(".step1.combobox").parent().children(".select2-container").remove();
	//console.log($input.get([0]));

	//console.log("get data from: "+fieldID );
	if (fieldID.substring(0, 5) == "s2id_") {
		fieldID = fieldID.substring(5);
	}
	//alert("break 68");
	//console.log("get data from: "+fieldID);
	var selectableData = window["twosteps_" + fieldID];
	var options = window["twosteps_options_" + fieldID];
	var storedObj = {};
	//==directly read the data from field
	var data = self.find('.twostepcombo').val();
	var $step2 = self.find(".step2.combobox:not(.select2-container)");
	self.find(".combobox:not(.select2-container)").removeAttr("name");
	//==directly read the data from field
	if (data != "") {
		console.log("running storedObj");
		storedObj = JSON.parse(data);
	}else{
		self.find(".select2-container.combobox").remove();
	}
	console.log(options);
	//console.log(selectableData);
	//alert("break 77");
	console.log('trigger s1');
	$step1.select2({
		showSearchBox : false,
		placeholder : options.step1,
		minimumResultsForSearch : -1
	}).on('change', function() {
		//alert("break 83");
		console.log('trigger change s1');
		var key1 = $(this).val();
		var labelstep2 = $(this).nextMatchNaive(".step2.section_step_hkm");
		labelstep2.removeClass("hidden");
		storedObj["key_1"] = key1;
		$(this).parent().find(".twostepcombo").val(JSON.stringify(storedObj));
		//trigger
		console.log('trigger s2');
		var s2 = $step2.select2({
			placeholder : options.step2,
			data : selectableData[key1],
			initSelection : function(element, callback) {
				//using underscore
				console.log("storedObj 51");
				//storedObj["key_2"] = key2;
				//if (storedObj.hasOwnProperty("key_2")) {
				console.log(selectableData);
				//=========================
				var current_obj = _.select(selectableData[key1], function(obj) {
					return obj.id == element.val()
				});
				callback(current_obj[0]);
				//}
			}
		}).on('change', function() {
			//trigger the second step
			var key2 = $(this).val();
			//storedObj["key_2"] = key2;
			storedObj["key_2"] = key2;
			$(this).parent().find(".twostepcombo").val(JSON.stringify(storedObj));
			console.log("level key  " + key2);
		});
	});

	//============================
	if (data != "") {
		if (storedObj.hasOwnProperty("key_1")) {
			console.log("yes there is a property called key_1 in the object interna_data_meta");
			console.log(storedObj.key_1);
			console.log("set data into interna_data_meta");
			var stepup = $step1.select2("val", storedObj.key_1).trigger('change');
			var s2 = self.find(".step2.combobox.select2-container");
			if (storedObj.hasOwnProperty("key_2") && s2.size() > 0) {
				if (storedObj.key_2 != "") {
					console.log("dump val for the second select2 and trigger");
					s2.select2("val", storedObj.key_2).trigger('change');
				}
			}
		}
	} else {
		$step1.trigger('change');
	}
}

// Implementation for "naive next loop" function
jQuery.fn.nextMatchNaive = nextMatchNaive;
function nextMatchNaive(selector) {
	var match;
	match = this.next();
	while (match.length > 0 && !match.is(selector)) {
		match = match.next();
	}
	return match;
}

jQuery.fn.input_save_data = input_field_data;
function input_field_data(objdata) {
	this.val(JSON.stringify(objdata));
}


jQuery(document).ready(function($) {
	console.log("steps.ini2 135");
	steps.ini2();
});
