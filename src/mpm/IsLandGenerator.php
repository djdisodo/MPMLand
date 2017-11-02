<?php
namespace mpm;

//use pocketmine\level\generator\Generator;
use mpm\Sphere;
use mpm\IsLandMain as Main;
use pocketmine\math\Vector3;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use pocketmine\level\generator\Generator;

class IsLandGenerator extends Generator {

	/** @var ChunkManager */
	private $level;
	/** @var Random */
	private $random;
	/** @var Main*/
	private $main;

	public function init(ChunkManager $level, Random $random){
		$this->level = $level;
		$this->random = $random;
		$this->main = new Main();
	}

	public function __construct(array $options = []){

	}

	public function getSettings() : array {
		return [];
	}

	public function getName() : string {
		return "island";
	}

	public function generateChunk(int $chunkX, int $chunkZ){
		$chunk = $this->level->getChunk($chunkX, $chunkZ);

		for($x = 0; $x < 16; $x++){
			for($z = 0; $z < 16; $z++){
				if($this->main->s['island'] ['type'] == "water"){
				$chunk->setBlock($x, 0, $z, 7);
				$chunk->setBlock($x, 1, $z, 1);
				$chunk->setBlock($x, 2, $z, 1);
				$chunk->setBlock($x, 3, $z, 1);
				$chunk->setBlock($x, 4, $z, 1);
				$chunk->setBlock($x, 5, $z, 1);
				$chunk->setBlock($x, 6, $z, 1);
				$chunk->setBlock($x, 7, $z, 12);
				$chunk->setBlock($x, 8, $z, 8);
				$chunk->setBlock($x, 9, $z, 8);
			}else{
				$chunk->setBlock($x, 0, $z, 0);
				$chunk->setBlock($x, 1, $z, 0);
				$chunk->setBlock($x, 2, $z, 0);
				$chunk->setBlock($x, 3, $z, 0);
				$chunk->setBlock($x, 4, $z, 0);
				$chunk->setBlock($x, 5, $z, 0);
				$chunk->setBlock($x, 6, $z, 0);
				$chunk->setBlock($x, 7, $z, 0);
				$chunk->setBlock($x, 8, $z, 0);
				$chunk->setBlock($x, 9, $z, 0);
			}
			}
		}
		$num = 0;

		while(true){
			$this->main->c['island'] [$num] = [
				'share' => [],
				'pos' => 103 + $num * 200,
				'welcomeM' => "섬".$num."번에 오신것을 환영합니다."
			];
			$this->main->c['islast']++;
			$num++;
			$worldX = $chunkX * 16;
			$worldZ = $chunkZ * 16;
			if($chunkX < 0 or $chunkZ < 0){
				break;
			}
			$cx = $worldX % 200;
			$cz = $worldZ % 200;
			if($cx <= 100 and 100 <= $cx + 15 and $cz <= 100 and 100 <= $cz + 15){
				$fill = Sphere::getElements(8, 7, 8, 7);
				$high = [];
				for($i = 0; $i < 16; $i++){
					for($ii = 0; $ii < 16; $ii++){
						$high[$i][$ii] = 0;
					}
				}
				foreach($fill as $el){
					$x = $el[0];
					$y = $el[1];
					$z = $el[2];
					if($y < 7){
						continue;
					} else if($y < 10) {
						$chunk->setBlock($x, $y, $z, 1);
					} else if($y < 12) {
						$chunk->setBlock($x, $y, $z, 3);
					} else {
						continue;
					}
					if($high[$x][$z] < $y){
						$high[$x][$z] = $y;
					}

					$chunk->setBlock(10, $high[10][10] +1, 10, 17);
					$chunk->setBlock(10, $high[10][10] +2, 10, 17);
					$chunk->setBlock(10, $high[10][10] +3, 10, 17);
					$chunk->setBlock(10, $high[10][10] +4, 10, 17);
					$chunk->setBlock(10, $high[10][10] +5, 10, 17);
					$chunk->setBlock(10, $high[10][10] +6, 10, 17);

					//Leave

					$chunk->setBlock(8, $high[10][10] +2, 8, 0);
					$chunk->setBlock(9, $high[10][10] +2, 8, 0);
					$chunk->setBlock(10, $high[10][10] +2, 8, 0);
					$chunk->setBlock(11, $high[10][10] +2, 8, 0);
					$chunk->setBlock(12, $high[10][10] +2, 8, 0);
					$chunk->setBlock(8, $high[10][10] +2, 9, 0);
					$chunk->setBlock(9, $high[10][10] +2, 9, 0);
					$chunk->setBlock(10, $high[10][10] +2, 9, 0);
					$chunk->setBlock(11, $high[10][10] +2, 9, 0);
					$chunk->setBlock(12, $high[10][10] +2, 9, 0);
					$chunk->setBlock(8, $high[10][10] +2, 10, 0);
					$chunk->setBlock(9, $high[10][10] +2, 10, 0);
					$chunk->setBlock(11, $high[10][10] +2, 10, 0);
					$chunk->setBlock(12, $high[10][10] +2, 10, 0);
					$chunk->setBlock(8, $high[10][10] +2, 11, 0);
					$chunk->setBlock(9, $high[10][10] +2, 11, 0);
					$chunk->setBlock(10, $high[10][10] +2, 11, 0);
					$chunk->setBlock(11, $high[10][10] +2, 11, 0);
					$chunk->setBlock(12, $high[10][10] +2, 11, 0);
					$chunk->setBlock(8, $high[10][10] +2, 12, 0);
					$chunk->setBlock(9, $high[10][10] +2, 12, 0);
					$chunk->setBlock(10, $high[10][10] +2, 12, 0);
					$chunk->setBlock(11, $high[10][10] +2, 12, 0);
					$chunk->setBlock(12, $high[10][10] +2, 12, 0);
					$chunk->setBlock(9, $high[10][10] +5, 8, 18);
					$chunk->setBlock(10, $high[10][10] +5, 8, 18);
					$chunk->setBlock(11, $high[10][10] +5, 8, 18);
					$chunk->setBlock(12, $high[10][10] +5, 8, 18);
					$chunk->setBlock(8, $high[10][10] +5, 9, 18);
					$chunk->setBlock(9, $high[10][10] +5, 9, 18);
					$chunk->setBlock(10, $high[10][10] +5, 9, 18);
					$chunk->setBlock(11, $high[10][10] +5, 9, 18);
					$chunk->setBlock(12, $high[10][10] +5, 9, 18);
					$chunk->setBlock(8, $high[10][10] +5, 10, 18);
					$chunk->setBlock(9, $high[10][10] +5, 10, 18);
					$chunk->setBlock(11, $high[10][10] +5, 10, 18);
					$chunk->setBlock(12, $high[10][10] +5, 10, 18);
					$chunk->setBlock(8, $high[10][10] +5, 11, 18);
					$chunk->setBlock(9, $high[10][10] +5, 11, 18);
					$chunk->setBlock(10, $high[10][10] +5, 11, 18);
					$chunk->setBlock(11, $high[10][10] +5, 11, 18);
					$chunk->setBlock(12, $high[10][10] +5, 11, 18);
					$chunk->setBlock(8, $high[10][10] +5, 12, 18);
					$chunk->setBlock(9, $high[10][10] +5, 12, 18);
					$chunk->setBlock(10, $high[10][10] +5, 12, 18);
					$chunk->setBlock(11, $high[10][10] +5, 12, 18);
					$chunk->setBlock(9, $high[10][10] +6, 9, 18);
					$chunk->setBlock(10, $high[10][10] +6, 9, 18);
					$chunk->setBlock(11, $high[10][10] +6, 9, 18);
					$chunk->setBlock(9, $high[10][10] +6, 10, 18);
					$chunk->setBlock(11, $high[10][10] +6, 10, 18);
					$chunk->setBlock(9, $high[10][10] +6, 11, 18);
					$chunk->setBlock(10, $high[10][10] +6, 11, 18);
					$chunk->setBlock(11, $high[10][10] +6, 11, 18);
					$chunk->setBlock(10, $high[10][10] +7, 9, 18);
					$chunk->setBlock(9, $high[10][10] +7, 10, 18);
					$chunk->setBlock(10, $high[10][10] +7, 10, 18);
					$chunk->setBlock(11, $high[10][10] +7, 10, 18);
					$chunk->setBlock(10, $high[10][10] +7, 11, 18);

					//Grass

					$chunk->setBlock(3, $high[3][5] +1, 5, 31, 1);
					$chunk->setBlock(4, $high[4][4] +1, 4, 31, 1);
					$chunk->setBlock(5, $high[5][12] +1, 12, 31, 1);
					$chunk->setBlock(6, $high[6][8] +1, 8, 31, 1);
					$chunk->setBlock(7, $high[7][11] +1, 11, 31, 1);
					$chunk->setBlock(8, $high[8][13] +1, 13, 31, 1);
					$chunk->setBlock(9, $high[9][2] +1, 2, 31, 1);
					$chunk->setBlock(11, $high[11][5] +1, 5, 31, 1);
					$chunk->setBlock(13, $high[13][7] +1, 7, 31, 1);

					for($i = 0; $i < 16; $i++){
						for($ii = 0; $ii < 16; $ii++){
							if($high[$i][$ii] < 10){
								continue;
							}
							$chunk->setBlock($i, $high[$i][$ii], $ii, 2);
						}
					}
				}
			}
			break;
		}
		$this->level->setChunk($chunkX, $chunkZ, $chunk);
	}

	public function populateChunk($chunkX, $chunkZ){

	}

	public function getSpawn() : Vector3 {
		return new Vector3(100, 25, 100);
	}
}

?>
