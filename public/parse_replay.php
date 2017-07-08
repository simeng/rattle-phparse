<?php

require_once("../vendor/autoload.php");

use Symfony\Component\Dotenv\Dotenv;

function parseRattletrap($header) {
	$out = [];

	$props = $header['properties']['value'];

	$out['id'] = $props['Id']['value']['str_property'];
	$out['name'] = $props['ReplayName']['value']['str_property'];
	$out['keyframe_delay'] = $props['KeyframeDelay']['value']['float_property'];
	$out['map_name'] = $props['MapName']['value']['name_property'];
	$out['match_type'] = $props['MatchType']['value']['name_property'];
	$out['num_frames'] = $props['NumFrames']['value']['int_property'];
	$out['duration'] = $props['NumFrames']['value']['int_property'] / $props['RecordFPS']['value']['float_property'];
	$out['date'] = preg_replace('/([0-9]{2})-([0-9]{2})-([0-9]{2})$/', '$1:$2:$3', $props['Date']['value']['str_property']);
	$out['team'] = [
		'size' => $props['TeamSize']['value']['int_property'],
		'score' => [
			$props['Team0Score']['value']['int_property'],
			$props['Team1Score']['value']['int_property']
		]
	];

	$out['recorded_by'] = [
		'player' => [
			'name' => $props['PlayerName']['value']['str_property'],
			'team' => isset($props['PrimaryPlayerTeam']['value']['int_property']) ? $props['PrimaryPlayerTeam']['value']['int_property']  : null,
		],
	];
	$out['record_fps'] = $props['RecordFPS']['value']['float_property'];

	$out['stats'] = [];
	foreach ($props['PlayerStats']['value']['array_property'] as $v) {
		$stats = [
			'player' => [
				'name' => $v['value']['Name']['value']['str_property'],
				'team' => $v['value']['Team']['value']['int_property'],
				'is_bot' => $v['value']['bBot']['value']['bool_property'] === 1,
				'online' => [
					'id' => $v['value']['OnlineID']['value']['q_word_property'],
					'platform' => join(" > ", $v['value']['Platform']['value']['byte_property']),
				]
			],
			'assists' => $v['value']['Assists']['value']['int_property'],
			'goals' => $v['value']['Goals']['value']['int_property'],
			'saves' => $v['value']['Saves']['value']['int_property'],
			'shots' => $v['value']['Shots']['value']['int_property'],
			'score' => $v['value']['Score']['value']['int_property'],
		];
		$out['stats'][] = $stats;
	}

	$out['events'] = [];

	foreach ($props['Goals']['value']['array_property'] as $k => $v) {
		$goal = [
			'player' => [
				'name' => $v['value']['PlayerName']['value']['str_property'],
				'team' => $v['value']['PlayerTeam']['value']['int_property']
			],
			'frame' => $v['value']['frame']['value']['int_property'],
			'seconds' => $v['value']['frame']['value']['int_property'] / $props['RecordFPS']['value']['float_property']
		];

		$out['events']['goals'][] = $goal;
	}

	$out['version'] = [
		'engine' => $header['engine_version'],
		'licensee' => $header['licensee_version'],
		'replay' => $props['ReplayVersion']['value']['int_property'],
		'game' => $props['GameVersion']['value']['int_property'],
		'build' => [
			'name' => $props['BuildVersion']['value']['str_property'],
			'id' => $props['BuildID']['value']['int_property']
		]
	];

	return $out;
}

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

if (isset($_FILES['file'])) {
	$file = $_FILES['file']['tmp_name'];
}
elseif ($_POST['type'] == 'rocketleaguereplays') {
	$id = strtoupper(str_replace("-", "", $_POST['id']));
	if (strlen($id) !== 32) {
		exit();
	}
	$data = file_get_contents(getenv('ROCKETLEAGUEREPLAYS_UPLOAD_URI') . "{$id}.replay");
	$file = tempnam("/tmp", "rlr");
	file_put_contents($file, $data);
}
$cmd = getenv('RATTLETRAP_BINARY') . " decode \"{$file}\" | jq .header";

$fp = popen($cmd, "r");
$json = "";
while (!feof($fp)) {
	$data = fread($fp, 16384);
	$json .= $data;
}
unlink($file);

$data = json_decode($json, true);
$out = parseRattletrap($data);


header('Content-Type: application/json');
print json_encode($out);

