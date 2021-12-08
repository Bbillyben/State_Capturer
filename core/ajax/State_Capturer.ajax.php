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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    require_once dirname(__FILE__) . '/../class/State_Capturer.class.php';

    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    
  /* Fonction permettant l'envoi de l'entête 'Content-Type: application/json'
    En V3 : indiquer l'argument 'true' pour contrôler le token d'accès Jeedom
    En V4 : autoriser l'exécution d'une méthode 'action' en GET en indiquant le(s) nom(s) de(s) action(s) dans un tableau en argument
  */  
    ajax::init();
    log::add('State_Capturer','debug','╔═══ #################### AJAX action required :'.init('action'));

    if (init('action') == 'update_state') { 
      //send file
      log::add('State_Capturer', 'debug','╠════ call Create State for id:'.init('cmdId'));
      $result=State_Capturer::updateState(init('cmdId'));
      ajax::success($result);
    }
    if (init('action') == 'delete_state') { 
      //send file
      log::add('State_Capturer', 'debug','╠════ call Delete State for id:'.init('cmdId'));
      $result=State_Capturer::delete_state_configuration(init('cmdId'));
      ajax::success($result);
    }
     if (init('action') == 'save_capture') { 
      State_Capturer::save_capture(init('cmdId'), init('data'));
      ajax::success();
    }


    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}

