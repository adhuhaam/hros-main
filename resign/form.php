<!DOCTYPE html>
<html style="width: 100%; height: 100%;">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta charset="utf-8" />
    <title></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/formviewer.css">
    <script src="assets/formviewer.js" type="text/javascript"></script>
    <script src="assets/formvuapi.js" type="text/javascript"></script>
    <script type="text/javascript">
(function() {
"use strict";

/**
 * Shorthand helper function to getElementById
 * @param id
 * @returns {Element}
 */
var d = function (id) {
    return document.getElementById(id);
};

var ClassHelper = (function() {
    return {
        addClass: function(ele, name) {
            var classes = ele.className.length !== 0 ? ele.className.split(" ") : [];
            var index = classes.indexOf(name);
            if (index === -1) {
                classes.push(name);
                ele.className = classes.join(" ");
            }
        },

        removeClass: function(ele, name) {
            var classes = ele.className.length !== 0 ? ele.className.split(" ") : [];
            var index = classes.indexOf(name);
            if (index !== -1) {
                classes.splice(index, 1);
            }
            ele.className = classes.join(" ");
        }
    };
})();

var Button = {};

FormViewer.on('ready', function(data) {
    // Grab buttons
    Button.zoomIn = d('btnZoomIn');
    Button.zoomOut = d('btnZoomOut');

    if (Button.zoomIn) {
        Button.zoomIn.onclick = function(e) { FormViewer.zoomIn(); e.preventDefault(); };
    }
    if (Button.zoomOut) {
        Button.zoomOut.onclick = function(e) { FormViewer.zoomOut(); e.preventDefault(); };
    }

    document.title = data.title ? data.title : data.fileName;
    var pageLabels = data.pageLabels;
    var btnPage = d('btnPage');
    if (btnPage != null) {
        btnPage.innerHTML = pageLabels.length ? pageLabels[data.page - 1] : data.page;
        btnPage.title = data.page + " of " + data.pagecount;

        FormViewer.on('pagechange', function(data) {
            d('btnPage').innerHTML = pageLabels.length ? pageLabels[data.page - 1] : data.page;
            d('btnPage').title = data.page + " of " + data.pagecount;
        });
    }

    if (idrform.app) {
        idrform.app.execFunc = idrform.app.execMenuItem;
        idrform.app.execMenuItem = function (str) {
            switch (str.toUpperCase()) {
                case "FIRSTPAGE":
                    idrform.app.activeDocs[0].pageNum = 0;
                    FormViewer.goToPage(1);
                    break;
                case "LASTPAGE":
                    idrform.app.activeDocs[0].pageNum = FormViewer.config.pagecount - 1;
                    FormViewer.goToPage(FormViewer.config.pagecount);
                    break;
                case "NEXTPAGE":
                    idrform.app.activeDocs[0].pageNum++;
                    FormViewer.next();
                    break;
                case "PREVPAGE":
                    idrform.app.activeDocs[0].pageNum--;
                    FormViewer.prev();
                    break;
                default:
                    idrform.app.execFunc(str);
                    break;
            }
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.target != null) {
            switch (e.target.constructor) {
                case HTMLInputElement:
                case HTMLTextAreaElement:
                case HTMLVideoElement:
                case HTMLAudioElement:
                case HTMLSelectElement:
                    return;
                default:
                    break;
            }
        }
        switch (e.keyCode) {
            case 33: // Page Up
                FormViewer.prev();
                e.preventDefault();
                break;
            case 34: // Page Down
                FormViewer.next();
                e.preventDefault();
                break;
            case 37: // Left Arrow
                data.isR2L ? FormViewer.next() : FormViewer.prev();
                e.preventDefault();
                break;
            case 39: // Right Arrow
                data.isR2L ? FormViewer.prev() : FormViewer.next();
                e.preventDefault();
                break;
            case 36: // Home
                FormViewer.goToPage(1);
                e.preventDefault();
                break;
            case 35: // End
                FormViewer.goToPage(data.pagecount);
                e.preventDefault();
                break;
        }
    });
});

window.addEventListener("beforeprint", function(event) {
    FormViewer.setZoom(FormViewer.ZOOM_AUTO);
});

})();
</script>
<style type="text/css">
.btn{border:0 none; height:30px; padding:0; width:30px; background-color:transparent; display:inline-block; margin:7px 5px 0; vertical-align:top; cursor:pointer; color:#fff;}
.btn:hover{background-color:#0e1319; color:#eddbd9; border-radius:5px;}
.page{box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);}
#formviewer{bottom:0; left:0; right:0; position:absolute; top:40px; background:#191f2f none repeat scroll 0 0;}
body{padding:0px; margin:0px; background-color:#191f2f;}
#FDFXFA_Menu{text-align:center; width:100%; z-index:9999; color:white;background-color:#707784; position:fixed; font-size:32px; margin:0px; opacity:0.8; top:0px; min-width:300px; min-height: 40px;}
#FDFXFA_Menu a{cursor:pointer; border-radius:5px; padding:5px; font-family: IDRSymbols; margin:5px 10px 5px 10px;}
#FDFXFA_PageLabel{padding-left:5px;font-size:20px}
#FDFXFA_PageCount{padding-right:5px;font-size:20px}
#FDFXFA_Menu a:hover{background-color:#0e1319; color:#eddbd9;}
#FDFXFA_PageLabel{min-width:20px;display:inline-block;}
#FDFXFA_Processing{width:100%; height:100%; z-index:10000; position:fixed; background-color:black; opacity:0.8; color:white; top:0px;text-align: center; font-size:300px; font-family:IDRSymbols;}
#FDFXFA_Processing span{top:50%;left:50%;margin:-50px 0 0 -50px}
#FDFXFA_FormType,#FDFXFA_Form,#FDFXFA_PDFName,#FDFXFA_PDFID,#FDFXFA_FormSubmitURL{display:none;}
.editable_combo {position:relative;}
.editable_combo select {position:absolute; top:0px; left:0px; font-size: inherit; border:none; height:100%; width:100%; margin:0;background-color: transparent}
.editable_combo input {position:absolute; top:0px; left:0px; font-size: inherit; height:calc(100% - 2px); width:calc(100% - 25px); padding:1px 4px; border:none;}
.editable_combo select:focus, .select-editable input:focus {outline:none;}

@media print {
#FDFXFA_Menu,#FDFXFA_Menu a,#FDFXFA_Menu label,#FDFXFA_Menu button{display:none}
#formviewer{overflow: visible}
div.page{box-shadow: none;}
}
</style>
</head>
<body style="margin: 0;" onload='idrform.init()'>
<script type="text/javascript" src="js/formvuacroform.js"></script>
<script type="text/javascript" src="js/EmbeddedScript.js"></script>
<div id='FDFXFA_Processing'>&#xF010;</div>
<div id='FDFXFA_FormType'>AcroForm</div>
<div id='FDFXFA_PDFName'>Resignation Request - Male' V1.1.pdf</div>
<div id='FDFXFA_PDFID'>27E0FBA2E6F1D344BA45D98B0FCCB061</div>
<div id='FDFXFA_Menu'><a title='Submit Form' onclick="FormViewer.handleFormSubmission('', 'formdata')">&#xF018;</a><a title='Go To FirstPage' onclick="idrform.app.execMenuItem('FirstPage')">&#xF01C;</a><a title='Go To PrevPage' onclick="idrform.app.execMenuItem('PrevPage')">&#xF01D;</a><label id='FDFXFA_PageLabel'><span id="btnPage">1</span></label><label id='FDFXFA_PageCount'>/ <span>1</span></label><a title='Go To NextPage' onclick="idrform.app.execMenuItem('NextPage')">&#xF01E;</a><a title='Go To LastPage' onclick="idrform.app.execMenuItem('LastPage')">&#xF01F;</a><a title='Save As Editable PDF' onclick="idrform.app.execMenuItem('SaveAs')">&#xF01A;</a><button id="btnZoomOut" title="Zoom Out" class="btn"><i class="fa fa-minus fa-lg" aria-hidden="true"></i></button><button id="btnZoomIn" title="Zoom In" class="btn"><i class="fa fa-plus fa-lg" aria-hidden="true"></i></button></div>
<div id="formviewer">
<div></div>
<div id="overlay"></div>
<form>
<div id="contentContainer">
<div id="page1" style="width: 909px; height: 1286px; margin-top:20px;" class="page">
<div class="page-inner" style="width: 909px; height: 1286px;">

<div id="p1" class="pageArea" style="overflow: hidden; position: relative; width: 909px; height: 1286px; margin-top:auto; margin-left:auto; margin-right:auto; background-color: white;">
<script>
//global variables that can be used by ALL the functions on this page.
let is64;
let inputs;     // A list of all the inputs on the page
let tabOrder;   // A list of all the inputs with tab order, ordered by tab index
const states = ['On.png', 'Off.png', 'DownOn.png', 'DownOff.png', 'RollOn.png', 'RollOff.png'];
const states64 = ['imageOn', 'imageOff', 'imageDownOn', 'imageDownOff', 'imageRollOn', 'imageRollOff'];

function setImage(input, state) {
    if (inputs[input].getAttribute('images').charAt(state) === '1') {
        document.getElementById(inputs[input].getAttribute('id') + "_img").src = getSrc(input, state);
    }
}

function getSrc(input, state) {
    let src;
    if (is64) {
        src = inputs[input].getAttribute(states64[state]);
    } else {
        src = inputs[input].getAttribute('imageName') + states[state];
    }
    return src;
}

/**
 * Replace checkboxes and radiobuttons with their APImages
 * @param isBase64 Whether the APImages are encoded in base64
 */
function replaceChecks(isBase64) {

    is64 = isBase64;
    // Get all the input fields on the page
    inputs = [...document.getElementsByTagName('input')];
    // Create a sorted list of inputs for tab ordering
    tabOrder = [...document.querySelectorAll("[tabindex]")].filter(input => input.tabIndex !== -1)
        .sort((a, b) => a.tabIndex - b.tabIndex);

    //cycle through the input fields
    for (let i=0; i<inputs.length; i++) {
        if (!inputs[i].hasAttribute('images')) continue;

        //check if the input is a checkbox or radio button
        if (inputs[i].getAttribute('class') !== 'idr-hidden' && inputs[i].getAttribute('data-image-added') !== 'true'
            && (inputs[i].getAttribute('type') === 'checkbox' || inputs[i].getAttribute('type') === 'radio')) {

            //create a new image
            let img = document.createElement('img');

            //check if the checkbox is checked
            if (inputs[i].checked) {
                if (inputs[i].getAttribute('images').charAt(0) === '1')
                    img.src = getSrc(i, 0);
            } else {
                if (inputs[i].getAttribute('images').charAt(1) === '1')
                    img.src = getSrc(i, 1);
            }

            //set image ID
            img.id = inputs[i].getAttribute('id') + "_img";
            // Copy Tab index
            img.tabIndex = inputs[i].tabIndex;

            //set action associations
            let imageIndex = i;
            img.addEventListener("click", () => checkClick(imageIndex));
            img.addEventListener("mousedown", () => checkDown(imageIndex));
            img.addEventListener("mouseover", () => checkOver(imageIndex));
            img.addEventListener("mouseup", () => checkRelease(imageIndex));
            img.addEventListener("mouseout", () => checkRelease(imageIndex));
            img.addEventListener("focus", () => checkFocus(imageIndex))
            img.addEventListener("blur", () => checkBlur(imageIndex))

            img.style.position = "absolute";
            let style = window.getComputedStyle(inputs[i]);
            img.style.top = style.top;
            img.style.left = style.left;
            img.style.width = style.width;
            img.style.height = style.height;
            img.style.zIndex = style.zIndex;

            //place image in front of the checkbox
            inputs[i].parentNode.insertBefore(img, inputs[i]);
            inputs[i].setAttribute('data-image-added','true');
            inputs[i].setAttribute('data-image-index', i.toString());

            //hide the checkbox
            inputs[i].style.display='none';

            // Specific handling for checkbox
            if (inputs[i].type === 'checkbox') {
                img.addEventListener("keydown", event => {
                    // Need to capture keydown or it will scroll the page
                    if (event.code === "Space") {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    }
                });

                img.addEventListener("keyup", event => {
                    if (event.isComposing) return;
                    if (event.code === "Space") {
                        checkSpace(imageIndex);
                    }
                })
            } else if (inputs[i].type === "radio") {

                // Handle navigation
                img.addEventListener("keydown", event => {
                    if (["ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown"].includes(event.code)) {
                        event.preventDefault();
                        event.stopPropagation();
                        handleRadioArrow(event.code, i);
                        return false;
                    } else if (event.code === "Tab") {
                        event.preventDefault();
                        event.stopPropagation();
                        handleRadioTab(event.shiftKey, i);
                        return false;
                    }
                })
            }
        }
    }
}

/**
 * Handle when a radio button is navigated using the arrow keys
 * @param code {("ArrowUp"|"ArrowDown"|"ArrowLeft"|"ArrowRight")} The code for the key used to navigate
 * @param i {Number}The index of the radiobutton in the inputs array
 */
function handleRadioArrow(code, i) {
    const options = [...document.querySelectorAll(`input[data-field-name="${inputs[i].dataset.fieldName}"]`)];


    // Get the index of the currently selected checkbox
    const selected = inputs[i];
    let index = selected ? options.indexOf(selected) : 0;

    if (["ArrowLeft", "ArrowUp"].includes(code)) {
        // Get the previous index, wrapping around if necessary
        index = index === 0 ? options.length - 1 : index - 1;
    } else {
        // Get the next index, wrapping around if necessary
        index = (index + 1) % options.length;
    }

    const input = options[index];
    const imageIndex = parseInt(input.dataset.imageIndex);
    input.checked = true;
    focus(input);
    input.dispatchEvent(new Event("change"));

    deselectSiblingRadio(imageIndex);
    refreshApImage(imageIndex);
}

/**
 * Handle when a radiobutton tries to go to the next or previous form field with tab
 * @param back {Boolean} Whether to go to the previous element
 * @param i {Number} The index of the radiobutton in the inputs array
 */
function handleRadioTab(back, i) {
    let index = tabOrder.indexOf(inputs[i]);

    // A count is used to ensure that if there is only one radio button group in the list then it will not be an
    // infinite loop
    let count = 0;
    while (count++ < tabOrder.length
            && (tabOrder[index].dataset.fieldName === inputs[i].dataset.fieldName
            || tabOrder[index].readOnly || tabOrder[index].disabled)) {
        if (!back) {
            index = (index + 1) % tabOrder.length;
        } else {
            index = (index - 1);
            if (index < 0) index = tabOrder.length - 1;
        }
    }

    focus(tabOrder[index]);
}

/**
 * Focus the element at the given index of the Inputs array
 * <br>
 * This ensures that the AP Image is selected if available, and the input is selected otherwise
 * @param i {Number | Element}The index of the element in the inputs array or the input itself
 */
function focus(i) {
    const input = typeof i === "number" ? inputs[i] : i;
    let element;
    if (input.dataset.imageAdded === "true") element = document.getElementById(input.id + "_img");
    else element = input;

    element.focus({focusVisible: true});
}

/**
 * A utility to deselect all the siblings of the input at the given index
 * @param i {Number} The index of the input of who's siblings are to be disabled
 */
function deselectSiblingRadio(i) {
    if (inputs[i].getAttribute('name') !== null) {
        for (let index = 0; index < inputs.length; index++) {
            if (index !== i && inputs[index].getAttribute('name') === inputs[i].getAttribute('name')) {
                inputs[index].checked = false;
                setImage(index, 1);
            }
        }
    }
}

/**
 * Refresh the AP Image of the given input based on its value
 *
 * Intended to be used externally to update the ap image after a change
 * @param i {Number} The index of the checkbox/radiobutton
 */
function refreshApImage(i)  {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        setImage(i, 0);
    } else {
        setImage(i, 1);
    }
}

/**
 * Handle clicking on a checkbox/radiobutton
 * <br>
 * This is the one of the mouse operations that actually changes the checkbox/radiobutton status
 * @param i {Number} The index of the checkbox/radiobutton
 */
function checkClick(i) {
    if (!inputs[i].hasAttribute('images')) return;

    if (inputs[i].checked) {
        if (inputs[i].getAttribute('type') === 'radio' && inputs[i].dataset.flagNotoggletooff === "true") {
            inputs[i].dispatchEvent(new Event('click'));
            return;
        } else {
            inputs[i].checked = false;
            setImage(i, 1);
        }
    } else {
        inputs[i].checked = true;

        setImage(i, 0);

        deselectSiblingRadio(i);
    }

    /*
     * Both checkboxes and radio buttons fire the change and input events
     * https://html.spec.whatwg.org/multipage/input.html#concept-input-apply
     */
    inputs[i].dispatchEvent(new Event('change'));
    inputs[i].dispatchEvent(new Event('input'));

    inputs[i].dispatchEvent(new Event('click'));
}

/**
 * Handle when the space bar is pressed whilst a checkbox is targeted
 * <br>
 * This is only for checkboxes, so there's no radiobutton specific logic included
 * <br>
 * Changes the checkbox status and set the replacement image
 * @param i {Number} The index of the checkbox/radiobutton
 */
function checkSpace(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        inputs[i].checked = false;
        setImage(i, 1);
    } else {
        inputs[i].checked = true;
        setImage(i, 0);
    }

    /*
     * Both checkboxes and radio buttons fire the change and input events
     * https://html.spec.whatwg.org/multipage/input.html#concept-input-apply
     */
    inputs[i].dispatchEvent(new Event('change'));
    inputs[i].dispatchEvent(new Event('input'));

    inputs[i].dispatchEvent(new Event('keyup'));
}

/**
 * Handle when a checkbox/radiobutton is released (mouseup/mouseout)
 * @param i {Number} The index of the checkbox/radiobutton
 */
function checkRelease(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        setImage(i, 0);
    } else {
        setImage(i, 1);
    }
    inputs[i].dispatchEvent(new Event('mouseup'));
}

/**
 * Handle when a checkbox/radiobutton is pressed (mousedown)
 * @param i {Number} The index of the checkbox/radiobutton
 */
function checkDown(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        setImage(i, 2);
    } else {
        setImage(i, 3);
    }
    inputs[i].dispatchEvent(new Event('mousedown'));
}

/**
 * Handle when a mouse hovers over a checkbox/radiobutton
 * @param i {Number} The index of the checkbox/radiobutton
 */
function checkOver(i) {
    if (!inputs[i].hasAttribute('images')) return;
    if (inputs[i].checked) {
        setImage(i, 4);
    } else {
        setImage(i, 5);
    }
    inputs[i].dispatchEvent(new Event('mouseover'));
}

/**
 * Handle when the AP image is focused
 * @param i {Number} The index of the checkbox/radiobutton
 */
function checkFocus(i) {
    if (!inputs[i].hasAttribute('images')) return;

    inputs[i].dispatchEvent(new Event('focus'));
}

/**
 * Handle when the AP image loses focus
 * @param i {Number} The index of the checkbox/radiobutton
 */
function checkBlur(i) {
    if (!inputs[i].hasAttribute('images')) return;

    inputs[i].dispatchEvent(new Event('blur'));
}
</script>
<style class="shared-css" type="text/css" >
.t {
	transform-origin: bottom left;
	z-index: 2;
	position: absolute;
	white-space: pre;
	overflow: visible;
	line-height: 1.5;
}
.text-container {
	white-space: pre;
}
@supports (-webkit-touch-callout: none) {
	.text-container {
		white-space: normal;
	}
}
</style>
<style type="text/css" >

#t1{left:68px;bottom:1091px;letter-spacing:0.08px;}
#t2{left:562px;bottom:1091px;letter-spacing:0.06px;}
#t3{left:68px;bottom:1055px;letter-spacing:0.1px;}
#t4{left:519px;bottom:1055px;letter-spacing:0.12px;word-spacing:0.02px;}
#t5{left:68px;bottom:1020px;letter-spacing:0.14px;}
#t6{left:569px;bottom:1020px;letter-spacing:0.15px;}
#t7{left:68px;bottom:988px;letter-spacing:0.2px;word-spacing:-0.06px;}
#t8{left:192px;bottom:988px;letter-spacing:0.07px;word-spacing:0.08px;}
#t9{left:521px;bottom:988px;letter-spacing:0.11px;word-spacing:0.03px;}
#ta{left:68px;bottom:953px;letter-spacing:0.12px;word-spacing:0.02px;}
#tb{left:69px;bottom:918px;letter-spacing:-0.04px;word-spacing:-0.02px;}
#tc{left:69px;bottom:751px;letter-spacing:-0.15px;}
#td{left:371px;bottom:751px;letter-spacing:-0.05px;}
#te{left:69px;bottom:718px;letter-spacing:-0.03px;word-spacing:-0.03px;}
#tf{left:69px;bottom:517px;letter-spacing:-0.07px;word-spacing:0.01px;}
#tg{left:366px;bottom:517px;letter-spacing:0.03px;}
#th{left:464px;bottom:517px;letter-spacing:-0.06px;}
#ti{left:69px;bottom:485px;letter-spacing:-0.04px;word-spacing:-0.02px;}
#tj{left:71px;bottom:306px;letter-spacing:-0.03px;}
#tk{left:120px;bottom:306px;letter-spacing:-0.03px;}
#tl{left:366px;bottom:304px;letter-spacing:-0.07px;word-spacing:5.83px;}
#tm{left:69px;bottom:251px;letter-spacing:-0.04px;word-spacing:-0.04px;}
#tn{left:162px;bottom:219px;letter-spacing:-0.05px;word-spacing:0.03px;}
#to{left:502px;bottom:220px;letter-spacing:-0.04px;word-spacing:-0.08px;}
#tp{left:69px;bottom:62px;letter-spacing:-0.12px;word-spacing:0.05px;}
#tq{left:319px;bottom:62px;letter-spacing:-0.1px;word-spacing:0.03px;}
#tr{left:69px;bottom:40px;letter-spacing:-0.02px;word-spacing:-0.04px;}
#ts{left:319px;bottom:40px;letter-spacing:-0.06px;}
#tt{left:665px;bottom:40px;letter-spacing:-0.05px;word-spacing:-0.01px;}
#tu{left:716px;bottom:751px;letter-spacing:-0.09px;}
#tv{left:716px;bottom:517px;letter-spacing:-0.09px;}
#tw{left:718px;bottom:305px;letter-spacing:-0.09px;}
#tx{left:472px;bottom:1190px;letter-spacing:0.13px;}
#ty{left:656px;bottom:1167px;letter-spacing:0.23px;word-spacing:0.01px;}
#tz{left:677px;bottom:62px;letter-spacing:-0.09px;word-spacing:0.03px;}

.s0{font-size:15px;font-family:TimesNewRomanPS-BoldMT_9r;color:#000;}
.s1{font-size:17px;font-family:TimesNewRomanPS-BoldMT_9r;color:#000;}
.s2{font-size:17px;font-family:TimesNewRomanPSMT_9t;color:#000;}
.s3{font-size:17px;font-family:TimesNewRomanPSMT_9f;color:#000;}
.s4{font-size:15px;font-family:Poppins-SemiBold_9m;color:#000;-webkit-text-stroke:0.4px #000;text-stroke:0.4px #000;}
.s5{font-size:15px;font-family:Poppins-Light_9p;color:#000;-webkit-text-stroke:0.4px #000;text-stroke:0.4px #000;}
#form1_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 124px;	top: 171px;	width: 62px;	height: 18px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 15px Arial, Helvetica, sans-serif;}
#form2_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 610px;	top: 171px;	width: 250px;	height: 20px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 16px Arial, Helvetica, sans-serif;}
#form3_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 159px;	top: 208px;	width: 154px;	height: 18px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 15px Arial, Helvetica, sans-serif;}
#form4_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 610px;	top: 208px;	width: 250px;	height: 20px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 16px Arial, Helvetica, sans-serif;}
#form5_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 159px;	top: 243px;	width: 154px;	height: 18px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 15px Arial, Helvetica, sans-serif;}
#form6_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 610px;	top: 243px;	width: 250px;	height: 20px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 16px Arial, Helvetica, sans-serif;}
#form7_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 610px;	top: 276px;	width: 250px;	height: 19px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 15px Arial, Helvetica, sans-serif;}
#form8_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 159px;	top: 309px;	width: 701px;	height: 21px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 18px Arial, Helvetica, sans-serif;}
#form9_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 393px;	width: 792px;	height: 24px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 9px Arial, Helvetica, sans-serif;}
#form10_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 420px;	width: 792px;	height: 24px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 19px Arial, Helvetica, sans-serif;}
#form11_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 449px;	width: 792px;	height: 21px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 18px Arial, Helvetica, sans-serif;}
#form12_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 596px;	width: 792px;	height: 27px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 22px Arial, Helvetica, sans-serif;}
#form13_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 628px;	width: 792px;	height: 27px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 22px Arial, Helvetica, sans-serif;}
#form14_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 658px;	width: 792px;	height: 29px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 24px Arial, Helvetica, sans-serif;}
#form15_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 833px;	width: 792px;	height: 27px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 22px Arial, Helvetica, sans-serif;}
#form16_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 863px;	width: 792px;	height: 32px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 25px Arial, Helvetica, sans-serif;}
#form17_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 67px;	top: 900px;	width: 792px;	height: 29px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 24px Arial, Helvetica, sans-serif;}
#form18_1{	z-index: 2;	border-style: none;	padding: 0px;	position: absolute;	left: 121px;	top: 273px;	width: 36px;	height: 23px;	color: rgb(0,0,0);	text-align: left;	background-color: rgb(255,255,255);	font: normal 18px Wingdings, 'Zapf Dingbats';}
#form19_1{	z-index: 2;	border-style: none;	padding: 0px;	position: absolute;	left: 281px;	top: 273px;	width: 34px;	height: 23px;	color: rgb(0,0,0);	text-align: left;	background-color: rgb(255,255,255);	font: normal 18px Wingdings, 'Zapf Dingbats';}
#form20_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 122px;	top: 504px;	width: 228px;	height: 28px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 18px Arial, Helvetica, sans-serif;}
#form21_1{	z-index: 2;	padding: 0px;	position: absolute;	left: 455px;	top: 506px;	width: 228px;	height: 26px;	color: rgb(0,0,0);	text-align: left;	background: transparent;	border: none;	font: normal 18px Arial, Helvetica, sans-serif;}

</style>
<style id="fonts1" type="text/css" >

@font-face {
	font-family: Poppins-Light_9p;
	src: url("fonts/Poppins-Light_9p.woff") format("woff");
}

@font-face {
	font-family: Poppins-SemiBold_9m;
	src: url("fonts/Poppins-SemiBold_9m.woff") format("woff");
}

@font-face {
	font-family: TimesNewRomanPS-BoldMT_9r;
	src: url("fonts/TimesNewRomanPS-BoldMT_9r.woff") format("woff");
}

@font-face {
	font-family: TimesNewRomanPSMT_9f;
	src: url("fonts/TimesNewRomanPSMT_9f.woff") format("woff");
}

@font-face {
	font-family: TimesNewRomanPSMT_9t;
	src: url("fonts/TimesNewRomanPSMT_9t.woff") format("woff");
}

</style>
<div id="pg1Overlay" style="width:100%; height:100%; position:absolute; z-index:1; background-color:rgba(0,0,0,0); -webkit-user-select: none;"></div>
<div id="pg1" style="-webkit-user-select: none;"><object width="909" height="1286" data="1/1.svg" type="image/svg+xml" id="pdf1" style="width:909px; height:1286px; z-index: 0;"></object></div>
<div class="text-container"><span id="t1" class="t s0">ID: </span><span id="t2" class="t s0">Name: </span>
<span id="t3" class="t s0">Department: </span><span id="t4" class="t s0">Designation : </span>
<span id="t5" class="t s0">Date of Join: </span><span id="t6" class="t s0">Date: </span>
<span id="t7" class="t s0">Male : </span><span id="t8" class="t s0">Hulhumale : </span><span id="t9" class="t s0">Destination : </span>
<span id="ta" class="t s0">Reason : </span>
<span id="tb" class="t s1">Employee Statement </span>
<span id="tc" class="t s2">Name: </span><span id="td" class="t s2">Designation: </span>
<span id="te" class="t s1">HOD Statement / Opinion </span>
<span id="tf" class="t s2">Name: Jayaprakash </span><span id="tg" class="t s2">Designation: </span><span id="th" class="t s3">Project Manager </span>
<span id="ti" class="t s1">HR Department Statement / Opinion </span>
<span id="tj" class="t s2">Name: </span><span id="tk" class="t s3">N.B. Rajanayaka </span>
<span id="tl" class="t s2">Designation: HRM </span>
<span id="tm" class="t s1">Final Decision by the Management </span>
<span id="tn" class="t s2">Accept Resignation </span><span id="to" class="t s2">Talk to employee to reverse decision </span>
<span id="tp" class="t s2">Adam Rakheem </span><span id="tq" class="t s2">Mohamed Nazim </span>
<span id="tr" class="t s1">Director, Projects </span><span id="ts" class="t s1">Chairman </span><span id="tt" class="t s1">Managing Director </span>
<span id="tu" class="t s2">Sign: </span>
<span id="tv" class="t s2">Sign: </span>
<span id="tw" class="t s2">Sign: </span>
<span id="tx" class="t s4">RASHEED CARPENTRY AND CONSTRUCTIONS PVT LTD </span>
<span id="ty" class="t s5">RESIGNATION REQUEST FORM </span>
<span id="tz" class="t s2">Ibrahim Rasheed </span></div>
<input id="form1_1" type="text" tabindex="1" value="4943" data-objref="382 0 R" title="ID" data-field-name="ID"/>
<input id="form2_1" type="text" tabindex="2" value="DURGA KUMAR JAISWAL" data-objref="383 0 R" title="Name" data-field-name="Name"/>
<input id="form3_1" type="text" tabindex="3" value="PROJECTS" data-objref="384 0 R" title="Department" data-field-name="Department"/>
<input id="form4_1" type="text" tabindex="4" value="PAINTER" data-objref="385 0 R" title="Designation" data-field-name="Designation"/>
<input id="form5_1" type="text" tabindex="5" value="11/Jul/2024" data-objref="386 0 R" title="Date of Join" data-field-name="Date of Join"/>
<input id="form6_1" type="text" tabindex="6" value="17-FEB-2024" data-objref="387 0 R" title="Date" data-field-name="Date"/>
<input id="form7_1" type="text" tabindex="7" value="DELHI ( INDIA )" data-objref="388 0 R" title="Destination" data-field-name="Destination"/>
<input id="form8_1" type="text" tabindex="8" value="HEALTH ISSUE" data-objref="389 0 R" title="Reason" data-field-name="Reason"/>
<input id="form9_1" type="text" tabindex="9" value="&quot;Due to health reasons, I would like to resign from my position as a painter in order to receive medical treatment in my home country.&quot;" data-objref="390 0 R" title="Employee Statement_Row_1" data-field-name="Employee StatementRow1"/>
<input id="form10_1" type="text" tabindex="10" value="" data-objref="391 0 R" title="Employee Statement_Row_2" data-field-name="Employee StatementRow2"/>
<input id="form11_1" type="text" tabindex="11" value="" data-objref="394 0 R" title="Employee Statement_Row_3" data-field-name="Employee StatementRow3"/>
<input id="form12_1" type="text" tabindex="12" value="" data-objref="395 0 R" title="HOD Statement / Opinion_Row_1" data-field-name="HOD Statement  OpinionRow1"/>
<input id="form13_1" type="text" tabindex="13" value="" data-objref="396 0 R" title="HOD Statement / Opinion_Row_2" data-field-name="HOD Statement  OpinionRow2"/>
<input id="form14_1" type="text" tabindex="14" value="" data-objref="397 0 R" title="HOD Statement / Opinion_Row_3" data-field-name="HOD Statement  OpinionRow3"/>
<input id="form15_1" type="text" tabindex="15" value="" data-objref="398 0 R" title="HR Department Statement / Opinion_Row_1" data-field-name="HR Department Statement  OpinionRow1"/>
<input id="form16_1" type="text" tabindex="16" value="" data-objref="399 0 R" title="HR Department Statement / Opinion_Row_2" data-field-name="HR Department Statement  OpinionRow2"/>
<input id="form17_1" type="text" tabindex="17" value="" data-objref="400 0 R" title="HR Department Statement / Opinion_Row_3" data-field-name="HR Department Statement  OpinionRow3"/>
<input id="form18_1" type="checkbox" tabindex="18" value="Yes" data-objref="401 0 R" data-field-name="Check Box1" imageName="1/form/401 0 R" images="110100" checked="checked"/>
<input id="form19_1" type="checkbox" tabindex="19" data-objref="403 0 R" data-field-name="Check Box2" value="Yes" imageName="1/form/403 0 R" images="110100"/>
<input id="form20_1" type="text" tabindex="20" value="DURGA KUMAR JAISWAL" data-objref="404 0 R" data-field-name="Text3"/>
<input id="form21_1" type="text" tabindex="21" value="PAINTER" data-objref="405 0 R" data-field-name="Text4"/>


<!-- call to setup Radio and Checkboxes as images, without this call images dont work for them -->
<script type="text/javascript">replaceChecks(false);</script>

</div>

</div>
</div>
</div>
</form>
</div>
<div id='FDFXFA_PDFDump' style='display:none;'> 
<script src="config.js" type="text/javascript"></script>
<script type="text/javascript">FormViewer.setup();</script>


</body>
</html>
