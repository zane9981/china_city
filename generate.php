<?php

$mysqli = new mysqli("localhost", "root", "admin", "city");

function get_zone($mysqli, $name) {
		$sql = "SELECT num FROM zone WHERE address = '".$name."'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$result->free();

		return $row['num'];
}

function get_xy($mysqli,$name) {
		$sql = "SELECT longitude AS x, latitude AS y FROM test_data WHERE city = '".$name."' AND county = '".$name."'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$result->free();

		return $row;
}

$filename = "./city.json";
$json_string = file_get_contents($filename);
$objs = json_decode($json_string,true);

foreach ($objs as $sheng => $v1){
		foreach($v1 as $shi => $v2 ) {
				foreach($v2 as $k3 => $xian) {
						if("省直辖行政单位" == $shi) {
								$shi = $xian; 
						}
						if("省直辖县级行政单位" == $shi) {
								$shi = $xian; 
						}
						$zone = get_zone($mysqli,$shi);
						if(!$zone) {
								echo "error get_zone:$shi\n";
								file_put_contents("./debug.txt", "error get_zone:$shi \n", FILE_APPEND);
						}
						$xy = get_xy($mysqli,$shi);
						if(!is_array($xy)) {
								echo "error get_xy:$shi \n";
								file_put_contents("./debug.txt", "error get_xy:$shi \n", FILE_APPEND);
						}
						/*
            if($sheng === $shi) {
								$zone2city[$zone] = $sheng;
						} else {
								$zone2city[$zone] = $sheng . $shi;
						}
						*/
						$zone2city[$zone] = rtrim($shi,'');
            $zone2province[$zone] = rtrim($sheng,'');
			
						$city2zone[$sheng][$shi] = $zone;
						$zone2xy[$zone]['x'] = $xy['x'];
						$zone2xy[$zone]['y'] = $xy['y'];
				}
		}
}

file_put_contents("./zone2city.json", json_encode($zone2city,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents("./zone2province.json", json_encode($zone2province,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents("./city2zone.json", json_encode($city2zone,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
file_put_contents("./zone2xy.json", json_encode($zone2xy,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
$mysqli->close();

