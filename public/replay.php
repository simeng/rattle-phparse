<!doctype html>
<head>
	<style>
		body {
			background: #333;
		}
		.content {
			display: flex;
			flex-flow: column;
			align-items: center;
		}
		label {
			display: block;
		}
		input {
			width: 100%;
		}
		.upload {
			padding: 10px;
			width: 50%;
			background: #ccc;
		}
		.result {
			padding: 10px;
			background: #eee;
			width: 50%;
		}
		.dropzone {
			border: 1px solid silver;
			min-height: 200px;
		}
		.dropzone.dz-drag-hover {
			background: yellow;
		}
		dd {
			margin-bottom: 5px;
		}
		dt {
			font-weight: bold;
			padding: 3px;
		}
	</style>
</head>
<body>
	<div class="content">
		<div class="upload">
			<h3>Paste url to replay</h3>
			<label>URL to replay on rocketleaguereplays.com</label>
			<input placeholder="https://www.rocketleaguereplays.com/replays/2b8e298b-4a28-6289-3d21-f6ab250dd151/" type="text" id="rocketleague-replays">

			<h3>Drop replay</h3>
			<form class="dropzone">
			</form>
		</div>
		<div class="result">
			<h4>Data</h4>
			<div class="data"></div>
		</div>
	</div>

	<script src="build/bundle.js" async></script>
</body>
