<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>Contrast grid for all PSU brand colors</title>
</head>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
<style>
	body {
		font-family: 'Open Sans', sans-serif;
		background: #ffffff;
		color: #333333;
	}
	td, tr {
		padding:.5em;
		margin:0;
		border:0;
		vertical-align:top;
	}
	.example {
		border-radius: .25em;
	}
	.ratio {
		font-size:.8em;
	}
	.report {
		padding: .125em .25em;
	}
	.report--pass {
		background: darkgreen;
		color: white;
	}
	.report--fail {
		background: darkred;
		color: white;
	}
	.report--inactive {
		display:none;
	}
	.body--js-on .report__label {
		position:absolute;
		left:-9999px;
		display:inline-block;
	}
</style>
<script>
	var domReady = function(callback) {
    	document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
	};
	domReady(function() {
		var body = document.getElementById('body');
		var form = document.getElementById('form');
		var levelElement = document.getElementById('level');
		var sizeElement = document.getElementById('size');
		var reports = document.getElementsByClassName('report');
		var updateDisplay = function() {
			Array.from(reports).forEach((el) => {
			    el.classList.add('report--inactive');
			});
			var toShow = document.getElementsByClassName('report--' + levelElement.value + '-' + sizeElement.value);
			Array.from(toShow).forEach((el) => {
			    el.classList.remove('report--inactive');
			});
		};
		
		body.classList.add('body--js-on');
    	updateDisplay();
    	levelElement.addEventListener('change',function(){
    		updateDisplay();
		});
    	sizeElement.addEventListener('change',function(){
    		updateDisplay();
		});
	});
</script>
<body id="body">
<?php

// calculates the luminosity of an given RGB color
// the color code must be in the format of RRGGBB
// the luminosity equations are from the WCAG 2 requirements
// http://www.w3.org/TR/WCAG20/#relativeluminancedef

function calculateLuminosity($color) {

    $r = hexdec(substr($color, 0, 2)) / 255; // red value
    $g = hexdec(substr($color, 2, 2)) / 255; // green value
    $b = hexdec(substr($color, 4, 2)) / 255; // blue value
    if ($r <= 0.03928) {
        $r = $r / 12.92;
    } else {
        $r = pow((($r + 0.055) / 1.055), 2.4);
    }

    if ($g <= 0.03928) {
        $g = $g / 12.92;
    } else {
        $g = pow((($g + 0.055) / 1.055), 2.4);
    }

    if ($b <= 0.03928) {
        $b = $b / 12.92;
    } else {
        $b = pow((($b + 0.055) / 1.055), 2.4);
    }

    $luminosity = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    return $luminosity;
}

// calculates the luminosity ratio of two colors
// the luminosity ratio equations are from the WCAG 2 requirements
// http://www.w3.org/TR/WCAG20/#contrast-ratiodef

function calculateLuminosityRatio($color1, $color2) {
    $l1 = calculateLuminosity($color1);
    $l2 = calculateLuminosity($color2);

    if ($l1 > $l2) {
        $ratio = (($l1 + 0.05) / ($l2 + 0.05));
    } else {
        $ratio = (($l2 + 0.05) / ($l1 + 0.05));
    }
    return $ratio;
}

// returns an array with the results of the color contrast analysis
// it returns akey for each level (AA and AAA, both for normal and large or bold text)
// it also returns the calculated contrast ratio
// the ratio levels are from the WCAG 2 requirements
// http://www.w3.org/TR/WCAG20/#visual-audio-contrast (1.4.3)
// http://www.w3.org/TR/WCAG20/#larger-scaledef

function evaluateColorContrast($color1, $color2) {
    $ratio = calculateLuminosityRatio($color1, $color2);

    $colorEvaluation["levelAANormal"] = ($ratio >= 4.5 ? 'pass' : 'fail');
    $colorEvaluation["levelAALarge"] = ($ratio >= 3 ? 'pass' : 'fail');
    $colorEvaluation["levelAAMediumBold"] = ($ratio >= 3 ? 'pass' : 'fail');
    $colorEvaluation["levelAAGraphic"] = ($ratio >= 3 ? 'pass' : 'fail');
    $colorEvaluation["levelAAANormal"] = ($ratio >= 7 ? 'pass' : 'fail');
    $colorEvaluation["levelAAALarge"] = ($ratio >= 4.5 ? 'pass' : 'fail');
    $colorEvaluation["levelAAAMediumBold"] = ($ratio >= 4.5 ? 'pass' : 'fail');
    $colorEvaluation["levelAAAGraphic"] = ($ratio >= 3 ? 'pass' : 'fail');
    $colorEvaluation["ratio"] = $ratio;

    return $colorEvaluation;
}

$colors = array(
	'White Out' => 'FFFFFF',
	'Nittany Navy' => '001E44',
	'Beaver Blue' => '1E407C',
	'Pennsylvania Sky' => '009CDE',
	'Limestone' => 'A2AAAD',
	'Slate' => '314D64',
	'Creek' => '3EA39E',
	'Penn\'s Forest' => '4A7729',
	'Old Coaly' => '444444',
	'Land Grant' => '6A3028',
	'Lion\'s Roar' => 'BF8226',
	'Lion Shrine' => 'B88965',
	'Stately Atherton' => 'AC8DCE',
	'Pugh Blue' => '96BEE6',
	'Original \'87' => 'BC204B',
	'Bright Keystone' => 'FFD100',
	'Invent Orange' => 'E98300',
	'Dawn of Discovery' => 'F2665E',
	'Perpetual Wonder' => '491D70',
	'Green Opportunity' => '008755',
	'Future\'s Calling' => '99CC00',
	'Endless Potential' => '000321',
);
?>
	<form action="?" id="form">
		<label>WCAG 2.1 Level: <select name="level" id="level">
			<option value="aa" selected>AA</option>
			<option value="aaa">AAA</option>
		</select></label>
		<label>Type of element: <select name="size" id="size">
			<option value="normal" selected>Normal Text (17px and smaller)</option>
			<option value="large">Large Text (18px and larger)</option>
			<option value="mediumbold">Medium Bold Text (bold, 14px and larger)</option>
			<option value="graphic">Meaningful graphic element</option>
		</select></label>
		<input type="submit" value="Apply Changes">
	</form>
<?php
echo '<table>';
echo '<tr>';
echo '<td></td>';
foreach($colors as $name => $hex) {
	echo '<th>';
	echo $name . '<br>' . $hex;
	echo '</th>';
}
echo '</tr>';
foreach($colors as $name1 => $hex1) {
echo '<tr>';
	echo '<th>';
	echo $name1 . '<br>' . $hex1;
	echo '</th>';
	foreach($colors as $name2 => $hex2) {
		echo '<td>';
		echo '<div class="example" style="color:#'.$hex1.';background:#'.$hex2.'">Text</div>';
		echo '<div class="ratio">'.round(calculateLuminosityRatio($hex1, $hex2), 2).'</div>';
		$report_data = evaluateColorContrast($hex1, $hex2);
		echo '<div class="report report--'.$report_data["levelAANormal"].' report--aa-normal"><span class="report__label">AA/Normal: </span>'.($report_data["levelAANormal"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '<div class="report report--'.$report_data["levelAALarge"].' report--aa-mediumbold"><span class="report__label">AA/Medium Bold: </span>'.($report_data["levelAAMediumBold"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '<div class="report report--'.$report_data["levelAALarge"].' report--aa-large"><span class="report__label">AA/Large: </span>'.($report_data["levelAALarge"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '<div class="report report--'.$report_data["levelAAGraphic"].' report--aa-graphic"><span class="report__label">AA/Graphical Elements: </span>'.($report_data["levelAAGraphic"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '<div class="report report--'.$report_data["levelAAANormal"].' report--aaa-normal"><span class="report__label">AAA/Normal: </span>'.($report_data["levelAAANormal"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '<div class="report report--'.$report_data["levelAALarge"].' report--aaa-mediumbold"><span class="report__label">AAA/Medium Bold: </span>'.($report_data["levelAAAMediumBold"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '<div class="report report--'.$report_data["levelAAALarge"].' report--aaa-large"><span class="report__label">AAA/Large: </span>'.($report_data["levelAAALarge"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '<div class="report report--'.$report_data["levelAAAGraphic"].' report--aaa-graphic"><span class="report__label">AAA/Graphical Elements: </span>'.($report_data["levelAAAGraphic"] == 'pass' ? 'OK' : 'Avoid').'</div>';
		echo '</td>';
	}
echo '</tr>';
}
?>
</body>
</html>
