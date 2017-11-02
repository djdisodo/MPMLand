<?php
namespace mpm;

/*
* 이 코드는 SOLOLand(Nukkit) 에서 가져왔으며
* PS가 java에서 php로 번역하였음을 알려드립니다.
*/

use pocketmine\level\generator\Generator;
use pocketmine\block\Block;
use pocketmine\block\Stone as BlockStone;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\generic\BaseFullChunk;
use pocketmine\math\Vector3;
use pocketmine\utils\{Random, Config};
use mpm\IsLandMain as Main;

class FieldGenerator extends Generator{

	//public static TYPE_GRID_LAND = 11;

	/** @var ChunkManager */
	private $level;

	private $options = [];
	private $floorLevel;

	private $preset = "1;7,4x1,3x3;3;road(block=1:6 width=5 depth=5),land(width=32 depth=32 border=43 block=2)";
	private $version = 1;

	private $flatBlocksId = [Block::BEDROCK, Block::STONE, Block::STONE, Block::STONE, Block::STONE, Block::DIRT, Block::DIRT, Block::DIRT];
	private $flatBlocksDamage = [0, 0, 0, 0, 0, 0, 0, 0];
	private $roadBlockId = Block::STONE;
	private $roadBlockDamage = BlockStone::POLISHED_ANDESITE;
	private $roadWidth = 5;
	private $roadDepth = 5;

	private $landBlockId= 2;
	private $landBlockDamage = 0;
	private $landWidth = 32;
	private $landDepth = 32;
	private $landBorderBlockId= 168; //Block::DOUBLE_SLAB; //..?
	private $landBorderBlockDamage = 0;

	public function getChunkManager() : ChunkManager{
		return $level;
	}
	public function getSettings() : array{
		return $this->options;
	}
	public function getName() : string{
		return "field";
	}
	public function init(ChunkManager $level, Random $random){
		$this->level = $level;
		$this->random = $random;
	}

	public function __construct(array $options = []){

	}
	public function getLandWidth() : int{
		return $this->landWidth;
	}
	public function getLandDepth() : int{
		return $this->landDepth;
	}
	public function getRoadWidth() : int{
		return $this->roadWidth;
	}
	public function getRoadDepth() : int{
		return $this->roadDepth;
	}
	public function generateChunk(int $chunkX, int $chunkZ){
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		if($chunkX >= 0 && $chunkZ >= 0){
			for($x = 0; $x <= 15; $x++){
				for($z = 0; $z <= 15; $z++){
					//	$chunk->setBiomeColor($x,$z,133,188,86);
					for($i = 0; $i < count($this->flatBlocksId); $i++){
						$chunk->setBlock($x, $i, $z, $this->flatBlocksId[$i], $this->flatBlocksDamage[$i]);
					}
					$calcRed = $this->calcGen($chunkX * 16 + $x, $chunkZ * 16 + $z);
					$chunk->setBlock($x, count($this->flatBlocksId), $z, $calcRed[0], $calcRed[1]);
				}
			}
		}
		// 6,9,6(첫 땅 생성 좌표)
		// 43, 9, 6(위로 갈시), 6, 9, 43(오른쪽으로 갈시)
		// 37 - 7 = 30
		//땅은 30 * 30 길 너비는 7
		$this->level->setChunk($chunkX, $chunkZ, $chunk);
	}
	private function calcGen(int $worldX, int $worldZ){
		$landBlock = [$this->landBlockId, $this->landBlockDamage];
		$roadBlock = [$this->roadBlockId, $this->roadBlockDamage];
		$landBorder = [$this->landBorderBlockId, $this->landBorderBlockDamage];

		if($worldX == 0 || $worldZ == 0){
			return $landBorder;
		}
		$gridlandx = $worldX % ($this->landWidth + $this->roadWidth);
		$gridlandz = $worldZ % ($this->landDepth + $this->roadDepth);

		if($gridlandx >= ($this->roadWidth + 2) && $gridlandz >= ($this->roadDepth + 2)){
			return $landBlock;
		}
		if($gridlandx >= ($this->roadWidth + 1) && $gridlandz >= ($this->roadDepth + 1)){
			return $landBorder;
		}
		if($gridlandx >= 1 && $gridlandz >= 1){
			return $roadBlock;
		}

		if($gridlandx == 0 && $gridlandz >= ($this->roadDepth + 1)){
			return $landBorder;
		}
		if($gridlandz == 0 && $gridlandx >= ($this->roadWidth + 1)){
			return $landBorder;
		}
		if($gridlandx == 0 && $gridlandz == 0){
			return $landBorder;
		}
		return $roadBlock;
	}
	public function populateChunk(int $chunkX, int $chunkZ){
	}
	public function getSpawn() : Vector3{
		return new Vector3(128, $this->floorLevel, 128);
	}
	public function registerLand($x1, $z1, $x2, $z2, $level){
		$main = new Main();
		$num = $main->c->get('flast');
		$main->c->__unset('flast');
		$main->c->set('flast', $num + 1);
		$main->c->get('land')[$num] = [
		'fpos' => [
		'x' => $x1,
		'z' => $z1
		],
		'lpos' => [
		'x' => $x2,
		'z' => $z2
		],
		'level' => $level,
		'share' => []
		];
	}
}
?>
