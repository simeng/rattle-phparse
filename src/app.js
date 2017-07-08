// yeah
import Dropzone from 'dropzone';
import '../node_modules/dropzone/dist/min/basic.min.css';
import '../node_modules/dropzone/dist/min/dropzone.min.css';

Dropzone.autoDiscover = false;
var isUploading = false;
function uploading() {
	if (!isUploading) {
		document.querySelector(".upload").style.opacity = .3;
		isUploading = true;
		return true;
	}
	else {
		return false;
	}
}
function uploadDone() {
	document.querySelector(".upload").style.opacity = 1;
	isUploading = false;
}
function createDataRecursive($c, key, value) {
	var $dt = document.createElement("dt");
	$dt.innerText = key;
	$c.appendChild($dt);

	if (typeof(value) == 'object') {
		var $dd = document.createElement("dd");
		for (var i in value) {
			if (typeof i !== 'string')
				continue;
			createDataRecursive($dd, i, value[i]);
		}
	}
	else {
		var $dd = document.createElement("dd");
		$dd.innerText = value;
	}
	$c.appendChild($dd);
	return $c;
}
function uploadFromUrlCb(evt) {
	var url = evt.target.value;
	var foundUrl = url.match(/rocketleaguereplays.com\/replays\/([^\/]+)/);
	if (foundUrl) {
		var id = foundUrl[1];
		var req = new XMLHttpRequest();
		if (uploading()) {
			req.open('POST', 'parse_replay.php');
			req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			req.send('type=rocketleaguereplays&id=' + encodeURIComponent(id));
			req.addEventListener('readystatechange', function () {
				if (req.readyState === XMLHttpRequest.DONE) {
					uploadDone();
					var data = JSON.parse(req.responseText);
					addResult(data);
				}
			});
		}
	}
}
function addResult(response) {
	var $data = document.querySelector(".result .data");

	var $replay = document.createElement("div");
	for (var k in response) {
		if (typeof k !== 'string')
			continue;
		var value = response[k];

		createDataRecursive($replay, k, value);
	}

	$data.insertBefore($replay, $data.childNodes[0])
}
window.addEventListener('load', function (evt) {
	var dropzone = new Dropzone(".dropzone", {
		url: 'parse_replay.php',
		maxFilesize: 10,
		accept: function (file, done) {
			if (!file.name.match(/\.replay$/)) {
				done("Replay files end with .replay");
				return false;
			}
			else {
				uploading();
				done();
				return true;
			}
		},
		'success': function (d, response) {
			uploadDone();
			addResult(response);
		}
	});

	var input = document.querySelector("#rocketleague-replays");
	input.addEventListener("keyup", uploadFromUrlCb);
});
