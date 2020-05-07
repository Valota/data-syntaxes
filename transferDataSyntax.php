<?php

//chdir(__DIR__);

function transferObj($data) {
	$ret = [];
	if ($data['vType'] === 'array') {
		$ret['type'] = 'array';
		$ret['items'] = transferObj($data['elem']);

		if (isset($data['required']) && $data['required']) {
			$ret['required'] = true;
		}

	} else if ($data['vType'] === 'object') {

		$ret['type'] = 'object';

		$ret['properties'] = [];
		$requireds = [];
		foreach ($data as $key => $value) {
			if ($key === 'vType' || $key === 'required') {
				continue;
			}
			$retVal = transferObj($value);

			if (isset($retVal['required'])) {
				if ($retVal['required']) {
					$requireds[] = $key;
				}
				unset($retVal['required']);
			}
			$ret['properties'][$key] = $retVal;

		}
		if (count($requireds)) {
			$ret['required'] = $requireds;
		}

	} else {

		switch ($data['vType']) {

			case 'id':
				$ret['type'] = ['string',
								'integer'];
				$ret['description'] = 'id';
				break;

			case 'url':
				$ret['type'] = 'string';
				$ret['description'] = 'uri to content';
				break;

			case 'text':
				$ret['type'] = 'string';
				break;
			case 'language':
				$ret['type'] = 'string';
				$ret['description'] = 'language';
				break;
			case 'image':
				$ret['type'] = 'string';
				$ret['description'] = 'image';
				break;
			case 'color':
				$ret['type'] = 'string';
				$ret['description'] = 'color';
				break;
			case 'id_string':
				$ret['type'] = 'string';
				$ret['description'] = 'id string';
				break;


			case 'date':
				$ret['type'] = 'integer';
				$ret['description'] = 'date';
				break;
			case 'int':
				$ret['type'] = 'integer';
				break;

			case 'boolean':
				$ret['type'] = ['integer',
								'boolean'];
				break;

			case 'double':
				$ret['type'] = 'number';
				break;


			default:
				echo "Processing missing for " . $data['vType'] . " !!" . PHP_EOL;
				$ret['type'] = 'string';
				break;


		}

		if (isset($data['required']) && $data['required']) {

			$ret['required'] = true;
		}

	}


	return $ret;
}

function convertSyntaxToJSONSchema($id, $name, $input) {
	$ret = ["\$id" => $id,
			"\$schema" => "http://json-schema.org/draft-07/schema#",
			"description" => $name];
	if ($input) {
		$obj = transferObj($input);
		$ret += $obj;
	}


	return $ret;


}

if (!isset($argv[1])) {
	die("Usage transferDataSyntax.php inputfile.json [outputfile.json [version tag]]" . PHP_EOL);
}

$parts = explode('.', $argv[1]);
$file_contents = json_decode(file_get_contents($argv[1]), true);

$output = $parts[0];
$outputFile = $argv[1];
$outputToConsole = true;
if (isset($argv[2])) {
	$output = $argv[2];
	$outputFile = $argv[2] .'.json';
	$outputToConsole = false;
}

$version = "v1.0.0";


if (isset($argv[3])) {
	$version = $argv[3];
}


$url = "https://raw.githubusercontent.com/Valota/data-syntax-{$output}/{$version}/{$outputFile}";
$title = ucwords(str_replace('-', ' ', $output));
$schema = convertSyntaxToJSONSchema($url, $title . ' data syntax for Valotalive', $file_contents);
if (isset($schema['type']) && $schema['type'] === 'array' && isset($schema['required'])) {
	unset($schema['required']);
}
if(!$outputToConsole) {
	file_put_contents($outputFile, json_encode($schema, JSON_PRETTY_PRINT));
} else {
	echo json_encode($schema) . PHP_EOL;
}

