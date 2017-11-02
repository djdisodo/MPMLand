<?php
namespace mpm;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use onebone\economyapi\EconomyAPI;
use pocketmine\level\generator\Generator;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\block\{BlockPlaceEvent, BlockBreakEvent};
use pocketmine\event\entity\EntitySpawnEvent;

use mpm\IsLandGenerator as LandGenerator;
use mpm\FieldGenerator;

/* Author : PS88
 *
 * This php file is modified by GoldBigDragon (OverTook).
 */

class IsLandMain extends PluginBase implements Listener{

    public $prefix = "§l§f[§bMPMLand§f]";
	public $c, $s;
  //private $nis = [];


      public function loadConfig(){
        @mkdir($this->getDataFolder());
        if(!file_exists($this->getDataFolder() . 'data.json')) {
	          $this->c = [
	              'island' => [],
	              'islast' => 0,
	              'land' => [],
	              'llast' => 0
	          ];
	} else {
    	  	$this->c = json_decode(file_get_contents($this->getDataFolder() . 'settings.yml'), true);
    	  }  

          file_put_contents($this->getDataFolder() . 'data.json', json_encode($this->c));
          if(!file_exists($this->getDataFolder() . 'settings.yml')) {
	          $this->s = [
	              'island' => [
	                'prize' => 20000,
	                'istype' => 'water',
	                'make' => true,
	                'pvp' => true,
	                'max' => 3
	              ],
	              'field' => [
	                'prize' => 20000,
	                'pvp' => true,
	                'make' => true,
	                'max' => 3
	              ]
	          ];
    	  } else {
    	  	$this->s = yaml_parse(file_get_contents($this->getDataFolder() . 'settings.yml'));
    	  }
    	  file_put_contents($this->getDataFolder() . 'settings.yml', yaml_emit($this->s));

        /*  if( $this->c->__isset('flast')){
          $this->c->set('flast', "0");
        }

         if( $this->c->__isset('islast')){
         $this->c->set('islast', "0");
       }*/
    /*  while (true) {
      if(! $this->c->__isset('islast')){
				$this->c->set('islast', 0);
			}
			$num = $this->c->get('islast');
			$this->c->get('island')[$num] = [
				'share' => [],
				'welcomeM' => "섬".$num."번입니다. 가격 : 20000원",
        'pos' => 103 + $num * 200
			];
			$this->c->__unset('islast');
			$this->c->set('islast', $num + 1);
    }*/
    }
    public function saveConfig() {
    	file_put_contents($this->getDataFolder() . 'data.json', json_encode($this->c));
    	return file_put_contents($this->getDataFolder() . 'settings.yml', yaml_emit($this->s));
    }
    public function onEnable(){
    	$this->loadConfig();
    	
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        // Island Name "Land"
      //  $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), 1);

    if($this->s['island']['make']){
		Generator::addGenerator(LandGenerator::class, "island");
		$gener = Generator::getGenerator("island");

		if(!($this->getServer()->loadLevel("island"))){
			@mkdir($this->getServer()->getDataPath() . "/" . "worlds" . "/" . "island");
			$options = [];
			$this->getServer()->generateLevel("island", 0, $gener, $options);
			$this->getLogger()->info("섬 생성 완료.");
		}
		$this->getLogger()->info("섬 로드 완료.");
    }
  if($this->s['field']['make']){
    Generator::addGenerator(FieldGenerator::class, "field");
    $gener = Generator::getGenerator("field");

    if(!($this->getServer()->loadLevel("field"))){
      @mkdir($this->getServer()->getDataPath() . "/" . "worlds" . "/" . "field");
      $options = [];
      $this->getServer()->generateLevel("field", 0, $gener, $options);
      $this->getLogger()->info("땅 생성 완료.");
    }
    $this->getLogger()->info("땅 로드 완료.");
    }
  }

    public function onCommand(CommandSender $pl, Command $cmd, String $label, array $args) : bool{
      if(! $pl instanceof Player){
        $this->getLogger()->info($this->prefix."서버에서만 사용가능합니다.");
        return true;
      }
      $pr = $this->prefix;
      switch($cmd->getName()){
        case '섬': {
          if(! isset($args[0])){
            $pl->sendMessage($pr." /섬 구매 §o§8- 섬을 구매합니다.");
            $pl->sendMessage($pr." /섬 양도 [플레이어] §o§8- 섬을 [플레이어] 에게 양도합니다.");
            $pl->sendMessage($pr." /섬 이동 [번호] §o§8- [번호] 섬으로 갑니다.");
            $pl->sendMessage($pr." /섬 공유 [플레이어] §o§8- 이섬을 [플레이어]에게 공유 시킵니다.");
            $pl->sendMessage($pr." /섬 공유해제 [플레이어] §o§8- 이섬 공유자인 [플레이어]를 섬에서 공유해제시킵니다.");
            return true;
          }
          switch($args[0]){
            case '구매': {
              if(EconomyAPI::getInstance()->myMoney($pl->getName()) < $this->s['island'] ['prize']){
                $pl->sendMessage($pr."돈이 부족합니다. 섬 가격 : ".$this->s['island'] ['prize']);
                return true;
              }
              if(count($this->getPlIslands($pl->getName())) >= $this->s['island'] ['max']){
                $pl->sendMessage($pr. "당신의 섬 개수가 이미 제한 개수만큼 채워졌습니다."); return true;
              }
              $this->setIsland($this->c['islast'], $pl);
            } break;
           case '양도': {
             if(! isset($args[1])){$pl->sendMessage($pr."/섬 양도 [플레이어]"); return true;}
             if($this->nowIsland($pl) == false or $this->c['island'] [$this->nowIsland($pl)] ['owner'] !== strtolower($pl->getName())){$pl->sendMessage($pr."당신은 섬에 있지 않거나 당신의 섬이 아닌곳에 있습니다."); return true;}
             $this->setIsland($this->nowIsland($pl), $this->getServer()->getPlayer($args[1]));
           } break;
           case '이동': {
             if(! isset($args[1])){$pl->sendMessage($pr."/섬 이동 [번호]"); return true;}
             $this->warpIsland($args[1], $pl);
           } break;
           case '공유': {
             if(! isset($args[1])){$pl->sendMessage($pr."/섬 공유 [플레이어]"); return true;}
             if($this->nowIsland($pl) == false or $this->c['island'] [$this->nowIsland($pl)] ['owner'] !== strtolower($pl->getName())){$pl->sendMessage($pr."당신은 섬에 있지 않거나 당신의 섬이 아닌곳에 있습니다."); return true;}
             $this->shareIsland($this->nowIsland($pl), $this->getServer()->getPlayer($args[1]));
           } break;
           case '공유해제': {
             if(! isset($args[1])){$pl->sendMessage($pr."/섬 공유해제 [플레이어]"); return true;}
             if($this->nowIsland($pl) == false or $this->c['island'] [$this->nowIsland($pl)] ['owner'] !== strtolower($pl->getName())){$pl->sendMessage($pr."당신은 섬에 있지 않거나 당신의 섬이 아닌곳에 있습니다."); return true;}
             $this->outIsland($this->nowIsland($pl), $this->getServer()->getPlayer($args[1]));
           } break;
           default: {
             $pl->sendMessage($pr." /섬 구매 §o§8- 섬을 구매합니다.");
             $pl->sendMessage($pr." /섬 양도 [플레이어] §o§8- 섬을 [플레이어] 에게 양도합니다.");
             $pl->sendMessage($pr." /섬 이동 [번호] §o§8- [번호] 섬으로 갑니다.");
             $pl->sendMessage($pr." /섬 공유 [플레이어] §o§8- 이섬을 [플레이어]에게 공유 시킵니다.");
             $pl->sendMessage($pr." /섬 공유해제 [플레이어] §o§8- 이섬 공유자인 [플레이어]를 섬에서 공유해제시킵니다.");
           } break;
          }
        } break;
        case '땅': {
          $pl->sendMessage("준비중..");
          #현재 연구 중입니다..
        }
      } return true;
    }

    /**EventListning Point*/
    public function blockBreak(BlockBreakEvent $ev){
      $pl = $ev->getPlayer();
      $num = $this->nowIsland($pl);
      if($pl->isOp() or $this->c['island'] [$this->nowIsland($pl)] ['owner'] == strtolower($pl->getName()) or isset($this->c['island'] [$this->nowIsland($pl)] ['share'] [strtolower($pl->getName())])){
      	return true;
      }elseif($pl->getLevel()->getName() == 'island'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
    }

    public function blockPlace(BlockPlaceEvent $ev){
      $pl = $ev->getPlayer();
      //$num = $this->nowIsland($pl);
      if($pl->isOp() or $this->c['island'] [$this->nowIsland($pl)] ['owner'] == strtolower($pl->getName()) or isset($this->c['island'] [$this->nowIsland($pl)] ['share'] [strtolower($pl->getName())])){
      }elseif($pl->getLevel()->getName() == 'island'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
    }

    /** 다른 곳에서 사용할 섬 메소드들*/
    public function setIsland(int $num, Player $owner){
    	$this->loadConfig();
      if(isset($this->c['island'] [$num] ['owner'])){
        unset($this->c['island'] [$num] ['owner']);
      }else{
        $this->c['islast']++;
      }
      $this->c['island'] [$num] ['owner'] = strtolower($owner->getName());
      $this->saveConfig();
      $owner->sendMessage($this->prefix."섬 ".$num."을 가지셨습니다!"); return true;
    }
    public function shareIsland(int $num, Player $share){
      $this->loadConfig();
      $this->c['island'][$num]['share'][] = strtolower($share->getName());
      $this->c['island'][$num]['share'] = array_unique($this->c['island'][$num]['share']);
      $this->saveConfig();
      $share->sendMessage($this->prefix."섬 ".$num."번을 공유 받았습니다."); return true;
    }
    public function outIsland(int $num, Player $outed){
    	 $this->loadConfig();
      for($i = 0; $i >= count($this->c['island'] [$num] ['share']); $i++){
        if(! $this->c['island'] [$num] ['share'][$i] == strtolower($outed->getName())) continue;
        unset($this->c['island'] [$num] ['share'][$i]);
         $this->saveConfig();
        $outed->sendMessage($this->prefix."당신은 섬".$num."번에서 퇴출당하셨습니다.");
        break;
      } return true;
    }
    public function warpIsland(int $num, Player $player){
      $player->teleport(new Position($num * 200 + 103, 13, 297, $this->getServer()->getLevelByName('island')));
      $player->sendMessage($this->prefix."섬".$num."번으로 이동하셨습니다."); return true;
    }
    public function getPlIslands(string $pname) : array{
    	$pname = strtolower($pname);
      $d = [];
      for ($i=0; $i >= $this->c['islast'] ; $i++) {
        if(! isset($this->c['island'] [$i] ['owner'])) continue;
        if(! $this->c['island'] [$i] ['owner'] == $pname) continue;
        $d[] = $i;
      }
      return $d;
    }
    public function nowIsland(Player $player){
      if($player->getLevel()->getName() !== 'island') return false;
      for ($i=0; $i >= $this->c['islast'] ; $i++) {
        # code...
        if($player->distance(new Vector3(103 + $i * 200, 12, 297)) > 200) continue;
        return $i;
        break;
      }
    }

    /** 다른 곳에서 사용할 땅 메소드들*/

    #Comming Soon..
    }
    /*class Task extends PluginTask{
      function onRun(int $currentTick){
      /*  $this->c = new Config($this->getOwner()->getDataFolder().'data.json', Config::JSON, [
            'island' => [],
            'land' => []
        ]);*//*
        for($i = 0; ! isset($this->getOwner()->c['island'][$i]); $i++){}
          $num = $i;
          $this->getOwner()->c['island'] [$num] = [
            'share' => [],
            'pos' => 103 + $num * 200,
            'welcomeM' => "섬".$num."번에 오신것을 환영합니다."
          ];
      }
    }*/
