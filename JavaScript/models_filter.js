function formSubmit(full){
var form = document.getElementById(formName='formFilter'+full);
form.action = document.location;
form.submit();
return true;
}
/*
function formReset(full){
var models = document.getElementById('filterModel'+full);
var brands = document.getElementById('filterBrand'+full);
var operators = document.getElementById('filterOperator');
var form = document.getElementById('formFilter');

models.options[0].selected = true;
brands.options[0].selected = true;
operators.options[0].selected = true;
form.action = document.location;
form.submit();
return true;
}
*/
var brandoptions = Array();
var brandopt = {};

function b(v,i,full){
brandopt = document.createElement("OPTION");
brandopt.value = v;
//???brandopt.innerHtml = i;
brandopt.text = i;
//brandoptions.push(brandopt);
document.getElementById('filterBrand').options.add(brandopt);
}
/* Не забыть включить в документ brands.js */

function loadModels(brandID){
var href = 'jsmodels/models.' + brandID + '.js';
//Далее код _подгрузки_ позаимствован из:
// * Subsys_JsHttpRequest_Js: JavaScript DHTML data loader.
// * (C) 2005 Dmitry Koterov, http://forum.dklab.ru/users/DmitryKoterov/
var span = null;
// Oh shit! Damned stupid fucked Opera 7.23 does not allow to create SCRIPT
// element over createElement (in HEAD or BODY section or in nested SPAN -
// no matter): it is created deadly, and does not respons on href assignment.
// So - always create SPAN.
span = document.body.appendChild(document.createElement("SPAN"));
span.style.display = 'none';
span.innerHTML = 'Text for stupid IE.<s'+'cript></' + 'script>';
var s = span.getElementsByTagName("script")[0];
s.language = "JavaScript";
	if (s.setAttribute) s.setAttribute('src', href); else s.src = href;
}

function updateModel(brandID, full, modelSelected){
	if (brandID == '') return true;
var models = document.getElementById('filterModel'+full);//full вроде для разных выборов на странице

	for (var i = models.options.length; i >= 0; i--){
	models.remove(i);
	}

	//Gодгрузка какраз
	if ( !options[brandID] || options[brandID].length == 0){
	models.options.add(options[-1][0]);
	return loadModels(brandID);
	}

	for (var i = 0; i < options[brandID].length; i++){
	models.options.add(options[brandID][i]);
		if (modelSelected && modelSelected > 0 && models.options[i].value == modelSelected) models.options[i].selected = true;
	}
}

var options = Array();
var opt = {};

function o(ob,ov,ot){
	if ( !options[ob] || options[ob].length == 0) options[ob] = Array();
opt = document.createElement("OPTION");
//opt.resx = '128';Вроде работает
//opt.resy = '96';
//	opt.id = ob;
opt.value = ov;
//opt.innerHtml = ot;//??
opt.text = ot;
options[ob].push(opt);
}

function models_load_complete (brandID){
updateModel(brandID, '');
}

//o("8",1152,"162");
o(-1,"","Подождите, загружаю список моделей");