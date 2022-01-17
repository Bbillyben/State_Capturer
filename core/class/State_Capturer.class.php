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
require_once __DIR__  . '/State_CapturerCmd.class.php';
require_once __DIR__  . '/../../../../core/php/utils.inc.php';

class State_Capturer extends eqLogic {

     const STATE_PATH=__DIR__  . '/../../data/';
     const STATE_PREFIX="STATE_";
     const DEFAULT_CMD_CONF=array("activated"=>true,"force_update"=>false, "state"=>null,"type"=>"string", "cmd"=>array());
     const REG_ON='/on|allum|start|ouvrir|open|encender/i';
     const REG_OFF='/off|etein|stop|fermer|close|desactivar/i';
    
    public static function updateState($cmdId){
        log::add(__CLASS__, 'debug', '╠═══════════════    Demande de creation etat pour id :'.$cmdId);
        $cmd=cmd::byId($cmdId); 
        if (!is_object($cmd)) {
             throw new Exception('{{Commande non trouvée}}');
        }
        log::add(__CLASS__, 'debug', '╟┄┄┄┄┄┄┄    commande trouvée :'.$cmd->getHumanName());
        $eqL = eqLogic::byId($cmd->getEqLogic_id());
        if (!is_object($eqL)) {
             throw new Exception('{{Equipement non trouvé}}');
        }
        log::add(__CLASS__, 'debug', '╟┄┄┄┄┄┄┄    equipement trouvé :'.$eqL->getHumanName());
        return $eqL->update_state($cmdId);
    }


    // ---------------------------------------------------------  gestion fichier de configuration
    public static function get_state_configuration($cmdId){
        log::add(__CLASS__, 'debug', '╠════════    récupération fichier pour :'.$cmdId);
        $filePath = self::STATE_PATH.self::STATE_PREFIX.$cmdId.".json";
        log::add(__CLASS__, 'debug', '╟┄┄    filepath  :'.$filePath);
        // test file existe
        if(!file_exists($filePath)){
            log::add(__CLASS__, 'debug', '╟┄┄    Fichier non trouvé');
            return array();
        }
        log::add(__CLASS__, 'debug', '╟┄┄    Définition Etat trouvé');
        $str = file_get_contents($filePath);
        $json = json_decode($str, true);
        return $json;
        
    }
     public static function save_state_configuration($cmdId, $conf){
         log::add(__CLASS__, 'debug', '╠════════    sauvegarde du fichier pour :'.$cmdId);
         $filePath = self::STATE_PATH.self::STATE_PREFIX.$cmdId.".json";
         log::add(__CLASS__, 'debug', '╟┄┄    filepath  :'.$filePath);
         $res=file_put_contents($filePath, json_encode($conf));
         if($res==false)throw new Exception('{{Erreur ecriture fichier de capture}}'." : ".$cmdId); 
         return $res;
     }
     public static function delete_state_configuration($cmdId){
        log::add(__CLASS__, 'debug', '╠════════    Suppression du fichier pour :'.$cmdId);
        $filePath = self::STATE_PATH.self::STATE_PREFIX.$cmdId.".json";
        if(!file_exists($filePath))return true;
        $res = unlink($filePath);
        return $res;
     }
     // ---------------------------------------------------------  gestion du sav du json à partir de la config 
     public static function save_capture($cmdId, $conf){
            log::add(__CLASS__, 'debug', '╠════════    Sauvegarde du fichier à partir de la conf :'.$cmdId);
            log::add(__CLASS__, 'debug', '╠════════    datas'.(is_array($conf)?1:0).' :'.json_encode($conf));
            // mise à jour des commandes-> on les recoit sous format name
            $fullArray=array();
            uasort($conf, function ($a, $b) {return intval($a['index']) - intval($b['index']);});


            foreach($conf as $id=>&$eqlConf){
                unset($eqlConf['index']);
              	uasort($eqlConf, function ($a, $b) {return intval($a['index']) - intval($b['index']);});
                foreach($eqlConf as &$cmdInfoConf){
                  // on lance le tri
                 	unset($cmdInfoConf['index']);
                    $cmdArr=array();
                    if(!array_key_exists('cmd' ,$cmdInfoConf))$cmdInfoConf['cmd']=array();
                    foreach($cmdInfoConf['cmd'] as $cmdType=>&$cmdName){
                        $cmdCol=cmd::byString($cmdName);
                        if(!is_object($cmdCol)){
                            throw new Exception('{{Commande action non trouvée}}'." : ".$cmdName);
                        }
                        $id=$cmdCol->getId();   
                        $cmdArr[$cmdType]=$id;
                      
                    }
                     log::add(__CLASS__, 'debug', '╠════════    cmd arra :'.json_encode($cmdArr));
                     unset($cmdInfoConf['cmd']);
                    $cmdInfoConf['cmd']=$cmdArr;
                }

            }
            log::add(__CLASS__, 'debug', '╠════════    datas saved :'.json_encode($conf));
            // sauvegarde du fichier
        return self::save_state_configuration($cmdId, $conf);

     }
     // ----------------------------------------------------- Generationd e la configuration pour une commande à capturer ----------------- //
    public static function generate_conf($eqL, $conf){
        
        //log::add(__CLASS__, 'debug', '╟┄┄┄┄┄┄┄┄┄┄┄    Capture de '.$eqL->getHumanName());

        $allCmds = $eqL->getCmd('info');
        // référencement des valeurs
        foreach($allCmds as $cmdCol){
            log::add(__CLASS__, 'debug', '╟┄┄┄┄┄┄┄   cmd info '.$cmdCol->getHumanName());
            $id = $cmdCol->getID();
            if(!array_key_exists($id ,$conf))$conf[$id]=self::DEFAULT_CMD_CONF;
            if($conf[$id]['activated']==false){
                log::add(__CLASS__, 'debug', '╟┄┄commande désactivée ');
                continue;
            }
            // mise à jour de la valeur
            $conf[$id]['state']=$cmdCol->execCmd();
            $conf[$id]['type']=$cmdCol->getSubType();
        }
        // référencement des commandes si non replies
        $allCmds = $eqL->getCmd('action');
        foreach($allCmds as $cmdCol){
            log::add(__CLASS__, 'debug', '╟┄┄┄┄┄┄┄   cmd action '.$cmdCol->getHumanName());
            $id = $cmdCol->getID();
            $idRel = $cmdCol->getValue();// la commande liée
             if(is_null($idRel) || $idRel =="")continue;// si pas de cmd liée
             if(!array_key_exists($idRel ,$conf))continue;// si la cmd info liée non référencée
             if($conf[$idRel]['activated']==false)continue;//si la capture est désactivée
             if(array_key_exists($id, $conf[$idRel]['cmd']))continue;// si la conf existe déjà, on modifie par la modale
             
             $cmdType = $cmdCol->getSubType();
             $cmdName = $cmdCol->getName();
             $cmdName = sanitizeAccent($cmdName); //strtr( $str, self::unwanted_array);// on enlève les accents
            log::add(__CLASS__,'debug','###################  test commande :'.$cmdName.' ('.$cmdType.')  |  '.preg_match(self::REG_ON, $cmdName));
             if($cmdType!='other' ){
                $conf[$idRel]['cmd'][$cmdType]=$id;
             }else if(preg_match(self::REG_ON, $cmdName)){
                $conf[$idRel]['cmd']["on"]=$id;
                    
             }
             else if(preg_match(self::REG_OFF, $cmdName)){
                $conf[$idRel]['cmd']["off"]=$id;
                    
             }

        }
        return $conf;
    }


    // **************************************************************** méthode d'instance *************************************
    // --------------------- Mise à jour de l'état'
    public function update_state($cmdId){
        log::add(__CLASS__, 'debug', '╠════════════════    Création etat pour id :'.$cmdId);
        // récupération de l'array à partir du fichier si existe'
        $stateConf = self::get_state_configuration($cmdId);
        // récupération de la liste des équipements à capturer
        $allCmds = $this->getCmd('info');
        foreach($allCmds as $cmdCol){
            if($cmdCol->getConfiguration('cmdType') != "equip")continue;
            // récupération de l'quipement cible
            $eqCibleId = str_replace(array('#', 'eqLogic'),array('',''),$cmdCol->getConfiguration('equip'));
            $eqCible = eqLogic::byId($eqCibleId);
        
            if (!is_object($eqCible)) {
                    throw new Exception('{{Equipement à capturer non trouvé}}'." : ".$eqLId);
            }

            log::add(__CLASS__, 'debug', '╟┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄    Capture de l\'équipement '.$cmdCol->getHumanName());
            if(!array_key_exists($eqCibleId,$stateConf)){
             $stateConf[$eqCibleId]=array();
            }
            $stateConf[$eqCibleId]=self::generate_conf($eqCible,$stateConf[$eqCibleId]);
         }

        // sauvegarde du fichier
        return self::save_state_configuration($cmdId, $stateConf);
    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
    // création des commandes
    // dernier état
    $ctCMD = $this->getCmd(null, 'lastState');
      if (!is_object($ctCMD)) {
          $ctCMD = new State_CapturerCmd();
          $ctCMD->setLogicalId('lastState');
          $ctCMD->setIsVisible(1);
          $ctCMD->setName(__('Dernier Etat', __FILE__));
          $ctCMD->setType('info');
          $ctCMD->setSubType('string');
      }
      
      $ctCMD->setType('info');
      $ctCMD->setSubType('string');
      $ctCMD->setEqLogic_id($this->getId());
      $ctCMD->save();

      // dernier état nom de la Commande
      $ctCMD = $this->getCmd(null, 'lastStateName');
      if (!is_object($ctCMD)) {
          $ctCMD = new State_CapturerCmd();
          $ctCMD->setLogicalId('lastStateName');
          $ctCMD->setIsVisible(1);
          $ctCMD->setName(__('Nom Dernier Etat', __FILE__));
          $ctCMD->setType('info');
          $ctCMD->setSubType('string');
      }
      
      $ctCMD->setType('info');
      $ctCMD->setSubType('string');
      $ctCMD->setEqLogic_id($this->getId());
      $ctCMD->save();
      // cahrge rle derier etat
      $ctCMD = $this->getCmd(null, 'loadLastState');
      if (!is_object($ctCMD)) {
          $ctCMD = new State_CapturerCmd();
          $ctCMD->setLogicalId('loadLastState');
          $ctCMD->setIsVisible(1);
          $ctCMD->setName(__('Charger Dernier Etat', __FILE__));
          $ctCMD->setType('action');
          $ctCMD->setSubType('other');
      }
      // charger l'état suivant
      $ctCMD->setEqLogic_id($this->getId());
      $ctCMD->save();

      $ctCMD = $this->getCmd(null, 'loadNextState');
      if (!is_object($ctCMD)) {
          $ctCMD = new State_CapturerCmd();
          $ctCMD->setLogicalId('loadNextState');
          $ctCMD->setIsVisible(1);
          $ctCMD->setName(__('Charger Prochain Etat', __FILE__));
          $ctCMD->setType('action');
          $ctCMD->setSubType('other');
      }
   
      $ctCMD->setEqLogic_id($this->getId());
      $ctCMD->save();

      $ctCMD = $this->getCmd(null, 'loadPrevState');
      if (!is_object($ctCMD)) {
          $ctCMD = new State_CapturerCmd();
          $ctCMD->setLogicalId('loadPrevState');
          $ctCMD->setIsVisible(1);
          $ctCMD->setName(__('Charger Etat Précédent', __FILE__));
          $ctCMD->setType('action');
          $ctCMD->setSubType('other');
      }
   
      $ctCMD->setEqLogic_id($this->getId());
      $ctCMD->save();
        
    }

 // Fonction exécutée automatiquement avant la suppression de l'équipement 
    public function preRemove() {
        
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement 
    public function postRemove() {
        
    }
  
  // fonction pour la gestion des commande de mise à jour d'un état
  public function check_update_cmd($stateId, $name){
    $ctCMD = $this->getCmd(null, 'update_'.$stateId);
      if (!is_object($ctCMD)) {
          $ctCMD = new State_CapturerCmd();
          $ctCMD->setLogicalId('update_'.$stateId);
          $ctCMD->setIsVisible(0);
          $ctCMD->setConfiguration('cmdType', 'updateState');
          $ctCMD->setType('action');
          $ctCMD->setSubType('other');
          
          $ctCMD->setEqLogic_id($this->getId());
        
      }
   $ctCMD->setValue($stateId);
   $ctCMD->setName(__('Mise à jour '.$name, __FILE__));
      
      $ctCMD->save();
    	    
  }
  public function check_delete_cmd($stateId){
    $ctCMD = $this->getCmd(null, 'update_'.$stateId);
    if (is_object($ctCMD)) $ctCMD->remove();
  }

    
}