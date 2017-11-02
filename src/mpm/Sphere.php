<?php

namespace mpm;

use mpm\Sphere;

class Sphere {
	
	public static function getElements(int $cx, int $cy, int $cz, int $radius) : array {
		$minX = $cx - $radius;
		$minY = $cy - $radius;
		$minZ = $cz - $radius;
		$maxX = $cx + $radius;
		$maxY = $cy + $radius;
		$maxZ = $cz + $radius;
		
		$ret = [];
		
		for($x = $minX; $x <= $maxX; $x++){
			for($y = $minY; $y <= $maxY; $y++){
				for($z = $minZ; $z <= $maxZ; $z++){
					$diff = Sphere::getDiff($x, $y, $z, $cx, $cy, $cz);
					if($diff < $radius){
						$el = [];
						$el[0] = $x;
						$el[1] = $y;
						$el[2] = $z;
						$ret[count($ret)] = $el;
					}
				}
			}
		}
		return $ret;
	}
	
	public static function getDiff(int $sx, int $sy, int $sz, int $ex, int $ey, int $ez){
		$xzDiff = sqrt(pow(abs($sx - $ex), 2) + pow(abs($sz - $ez), 2));
		return sqrt(pow($xzDiff, 2) + pow(abs($sy - $sz), 2));
	}
}

?>