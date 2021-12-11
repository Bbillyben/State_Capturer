<?php


/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/State_Capturer.class.php';

class State_CapturerCmd extends cmd {

    public static function load_next_state($eqL, $dir){
        // dernière Commande
            
        $lastCMD = $eqL->getCmd(null, 'lastState');
        $lastCMDId=$lastCMD->execCmd();

         $allCmds = $eqL->getCmd('action');
         $cmdArr=array();
        // référencement des valeurs
        foreach($allCmds as $cmdCol){
            if($cmdCol->getConfiguration('cmdType') != "state")continue;
            $cmdArr[]=$cmdCol->getId();
        }
        log::add('State_Capturer', 'debug', '╠═══   liste id cmd ;'.json_encode($cmdArr));
        $id=array_search(strval($lastCMDId), $cmdArr);
         log::add('State_Capturer', 'debug', '╠═══   index dernier id('.$lastCMDId.'):'.$id);
        if($id===false){
            self::load_state($cmdArr[0]);
            return;
        }
        $idTarget=($id+$dir)%count($cmdArr);
        if($idTarget<0)$idTarget=count($cmdArr)-1;
        log::add('State_Capturer', 'debug', '╠═══  target id :'.$idTarget);
        self::load_state($cmdArr[$idTarget]);

    }
   
    public static function load_state($cmdId){
        log::add('State_Capturer', 'debug', '╠═══════════════════════════════════════════    Load state pour id :'.$cmdId);
        // récup du fihier de conf
        $stateConf = State_Capturer::get_state_configuration($cmdId);
        if(count($stateConf)==0){
            log::add('State_Capturer', 'error', '####   NO state Captured for id : '.$cmdId);
            return false;
        }
        foreach($stateConf as $elId=>$elDef){// boucle sur les entrée du tableau de conf => les equipement à mettre à jour
            $eqL = eqLogic::byId($elId);
            if (!is_object($eqL)) {
                 log::add('State_Capturer', 'error', '####   Equipement capturé non trouvé - id : '.$elId);
                 continue;
            }
             log::add('State_Capturer', 'debug', '╠════════════════    Mise à jour de :'.$eqL->getHumanName());
             foreach($elDef as $cmdIdState=>$stateDef){
                  if($stateDef['activated']==false)continue;
                  $cmd=cmd::byId($cmdIdState);
                  if (!is_object($cmd)) {
                        log::add('State_Capturer', 'error', '####   Commande Info capturée non trouvé - id : '.$elId);
                        continue;
                    }
                log::add('State_Capturer', 'debug', '╟┄┄┄┄┄┄┄    commande mise à jour :'.$cmd->getHumanName());
                self::updateCmd($cmd,$stateDef);

             }
        }

        // mise à jour du dernier état
        $cmdState=cmd::byId($cmdId);
        if (!is_object($cmdState)) {
                 log::add('State_Capturer', 'error', '####   Commande state non trouvée : '.$cmdId);
                 return;
            }
        $eqL = $cmdState->getEqLogic();
         if (!is_object($eqL)) {
                 log::add('State_Capturer', 'error', '####   Equipement state non trouvée : ');
                 return;
            }
            $ctCMD = $eqL->getCmd(null, 'lastState');
            if (is_object($ctCMD)) {
                 log::add('State_Capturer', 'debug',  '╟┄┄    update last state :');
                 $ctCMD->event($cmdId);
            }else{
                log::add('State_Capturer', 'error',  'commande last state non trouvée');
            }
            // nom du dernier state
            $ctCMD = $eqL->getCmd(null, 'lastStateName');
            if (is_object($ctCMD)) {
                 log::add('State_Capturer', 'debug',  '╟┄┄    update last state :');
                 $ctCMD->event($cmdState->getName());
            }else{
                log::add('State_Capturer', 'error',  'commande last state name non trouvée');
            }
    }

    private static function updateCmd($cmd,$stateDef){
        $loadState = $stateDef['state'];

        // si commande binaire, on cherche le on/off
        if ($stateDef['type']=='binary') {
	         $cmdEffName=$stateDef['state']==1?'on':'off';
                if(array_key_exists($cmdEffName,$stateDef['cmd'])){// si on a le on ou le off
                   $cmdEff = cmd::byId($stateDef['cmd'][$cmdEffName]);
                    if (!is_object($cmdEff)) {
                        log::add('State_Capturer', 'error', '####   Commande load capturée non trouvé - id : '.$stateDef['cmd'][$cmdEffName]);
                        return false;
                    }
                    log::add('State_Capturer', 'debug', '╟┄┄    update by command binary :'.$cmdEff->getHumanName());
                    $cmdEff->execCmd();
                }else{
                    log::add('State_Capturer', 'debug', '╟┄┄    update by event :'.$cmd->getHumanName());
                    $cmd->event($loadState);
                }
                return true;
        }
        if(count($stateDef['cmd'])==0){
             log::add('State_Capturer', 'debug', '╟┄┄    update by event :'.$cmd->getHumanName());
             $cmd->event($loadState);
             return true;
        }
       
        $typeCmd=current(array_keys($stateDef['cmd']));
        $cmdEffId=$stateDef['cmd'][$typeCmd];
        $cmdEff = cmd::byId($cmdEffId);
        if (!is_object($cmdEff)) {
            log::add('State_Capturer', 'error', '####   Commande load capturée non trouvé - id : '.$stateDef['cmd'][$cmdEffName]);
            return false;
        }
        switch ($typeCmd) {
	        case 'color':
            case 'slider':
                $option=array($typeCmd=>$loadState);
		        break;
            case 'message':
                $option=array('message'=>$loadState, 'title'=>$loadState);
		        break;
	        default:
                log::add('State_Capturer', 'debug', '### cmd type :'.$typeCmd.' is not supported => essai en event');
		        break;
        }
        if(is_array($option)){
             log::add('State_Capturer', 'debug', '╟┄┄    update by cmd '.$typeCmd.', options :'.json_encode($option));
            $cmdEff->execCmd($option);
        }else{
            log::add('State_Capturer', 'debug', '╟┄┄    update by event');
            $cmd->event($loadState);
        }
        

        
    }
  // Exécution d'une commande  
     public function execute($_options = array()) {
      log::add('State_Capturer','debug', "╔═══════════════════════ execute CMD : ".$this->getId()." | ".$this->getHumanName().", logical id : ".$this->getLogicalId() ."  options : ".json_encode($_options));
      log::add('State_Capturer','debug', '╠════ Eq logic '.$this->getEqLogic()->getHumanName());

        if($this->getConfiguration('cmdType')=='state'){
            self::load_state($this->getId());
        }
        if($this->getLogicalId()=='loadLastState'){
            self::load_state($this->getEqLogic()->getCmd(null, 'lastState')->execCmd());
        }
        if($this->getLogicalId()=='loadNextState'){
            self::load_next_state($this->getEqLogic(),1);
        }
         if($this->getLogicalId()=='loadPrevState'){
            self::load_next_state($this->getEqLogic(),-1);
        }


        
     }

    /*     * **********************Getteur Setteur*************************** */

    // gestion du remove pour supprimer le fichier
    public function remove() {
            if($this->getConfiguration('cmdType')=='state')State_Capturer::delete_state_configuration($this->getId());
            parent::remove();
    }
}


