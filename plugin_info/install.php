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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
  function template_install() {

  }

// Fonction exécutée automatiquement après la mise à jour du plugin
function State_Capturer_update() {
	
log::add('State_Capturer','debug','=============  mise à jour des equipements suite à update plugin =============');


 foreach (eqLogic::byType('State_Capturer', true) as $eqLogic) {
	log::add('State_Capturer','debug', 'mise à jour de '.$eqLogic->getHumanName());
	$eqLogic->save();
	 $allCmds = $eqLogic->getCmd('info');
        // référencement des valeurs
        foreach($allCmds as $cmdCol){
			log::add('State_Capturer','debug', 'mise à jour de '.$cmdCol->getHumanName());
			$cmdCol->save();
			if($cmdCol->getConfiguration('cmdType')=='state'){
				
				
			}
        }
 }

}

// Fonction exécutée automatiquement après la suppression du plugin
  function template_remove() {

  }

?>
