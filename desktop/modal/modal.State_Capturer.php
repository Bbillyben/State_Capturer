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
require_once __DIR__  . '/../../core/class/State_Capturer.class.php';
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/../../core/class/State_CapturerCmd.class.php';
require_once __DIR__  . '/../../../../core/php/utils.inc.php';

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

    $id = init('id');
    $stateCmd = cmd::byId($id);
    if (!is_object($stateCmd)) {
        throw new Exception('{{Commande non trouvée}}');
    }
    $conf=State_Capturer::get_state_configuration($id); 


    echo '<div id="" class="col-xs-12 eqLogic">';
    
        echo '<div class="form-group">';
            echo '<label  class="col-xs-2 label_ca label_ca-blue">{{Etat}} :</label>';
            echo '<div class="col-xs-1">';
                echo '<span id="cmdIdState" type="text" >'.$id.'</span>';
            echo '</div>';
            echo '<div class="col-xs-5">';
                echo '<span id="cmdName" type="text" size="50" >'.$stateCmd->getHumanName().'</span>';
            echo '</div>';
// la divp pour les alertes
            echo '<div id="alerte_message_ca" class="jqAlert alert-danger" style="width: 100%;display:none;">no alerte</div>';
// les boutons        
        echo '</div>';
        echo '<a class="btn btn-danger" id="bt_save_conf"><i class="fas fa-download"></i>{{Save}}</a>';
        echo '</div>';
        foreach($conf as $eqCaptId=>$eqCaptDef){
            $eq=eqLogic::byId($eqCaptId);
            if (!is_object($stateCmd)) {
                log::add('State_Capturer','error', '## Modal : Command non trouvée, id :'.$eqCaptId);
                continue;
            }
            echo '<div id="" class="col-xs-12 eqLogicSCmodal">';
            //echo '<form class="form-ca-opt">';
                echo '<div class="form-group">';
                    echo '<label  class="col-xs-2 label_ca label_ca-yellow">{{Commande}} :</label>';
                    echo '<div class="col-xs-1">';
                        echo '<span class="cmdId" type="text" data-l1key="cmdId" >'.$eqCaptId.'</span>';
                    echo '</div>';
                    echo '<div class="col-xs-5">';
                        echo '<span id="cmdName" type="text" size="50" ><b>'.$eq->getHumanName().'</b></span>';
                    echo '</div>';  
                echo '</div>';
            echo '<label  class=" label_ca"></label>';
            echo '<div id="table_modal_SC_'.$eqCaptId.'"  class="table-responsive">';
					echo '<table id="table_cmd_"'.$eqCaptId.' class="table table-bordered table-condensed ui-sortable table_modal" data-l1key="'.$eqCaptId.'">';
						echo '<thead>';
							echo '<tr>';
                                echo '<th>{{Activation}}</th>';
								echo '<th>{{Id}}</th>';
								echo '<th>{{Nom}}</th>';
								echo '<th>{{Type}}</th>';
                                echo '<th>{{Etat}}</th>';
                                echo '<th>{{Force Update}}</th>';
								echo '<th>{{Commande}}</th>';
								echo '<th>{{Action}}</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
                       
                        foreach($eqCaptDef as $cmdCapId => $def){
                            $def=array_replace_recursive(State_Capturer::DEFAULT_CMD_CONF, $def);
                            $cmdCap=cmd::byId($cmdCapId);
                            if (!is_object($cmdCap)) {
                                 log::add('State_Capturer','error', '## Modal : Command Capturée non trouvée, id :'.$cmdCapId);
                                continue;
                            }
                            echo '<tr class="cmd" data-cmd_id="' . $cmdCapId . '">';
                            //activation
                            echo '<td class="SC_CB">';
                            echo '<input type="checkbox" class="cmdInfoAttr" data-l1key="isActivated" '.($def['activated']==true?'checked':'').'/>';
                            echo '</td>';
                            //id
                            echo '<td style="width:60px;">';
                            echo '<span class="cmdInfoAttr SC-input-cont" data-l1key="id">'.$cmdCapId.'</span>';
                            echo '</td>';
                            //nom
                            echo '<td>';
                            echo '<div class="row SC-input-cont">';
                            echo $cmdCap->getHumanName();
                            echo '</div>';
                            echo '</td>';
                            //Type
                            echo '<td>';
                            echo '<div class="row col-xs-8 cmdInfoAttr SC-input-cont" data-l1key="type">';
                            echo $def['type'];
                            echo '</div>';
                            echo '</td>';
                            // etat capturé
                            echo '<td>';
                            echo '<div class="row col-xs-5">';
                            echo '<input class="cmdInfoAttr input-sm SC-input-state" data-l1key="state" placeholder="{{Nom de la commande}}" value="'.$def['state'].'">';
                            echo '</div>';
                            echo '</td>';
                            // Force Update
                            echo '<td  class="SC_CB">';
                            echo '<input type="checkbox" class="cmdInfoAttr" data-l1key="isForced" '.($def['force_update']==true?'checked':'').'/>';
                            echo '</td>';
                            // les commandes.
                            echo '<td class="col-xs-4 cmdRowCont">';
                            foreach($def['cmd'] as $ty => $actId){
                                 $cmdAct=cmd::byId($actId);
                                if (!is_object($cmdAct)) {
                                     log::add('State_Capturer','error', '## Modal : Command action non trouvée, id :'.$actId);
                                    continue;
                                }
                              	echo '<div class="cmdContainer">';
                                echo '<div class="col-xs-3" >';
                              	// les case contextuelle
                              // choix
                              $disa = ($ty ==  "slider" || $ty == "color" || $ty == "select")?true:false;
                              $isBin= ($def['type'] == 'binary')?true:false;
                              $noDisp = 'style="display:none"';
                              		// le select
                              		echo '<select class="cmdInfoAttr form-control SC-cmd-el" 
                                    											data-l1key="cmd_type" '.($disa?'disabled':'').'>';
										echo '<option value="on" class="SC-binary" '.($ty=='on'?'selected':'').' '.($isBin?'':$noDisp).'>{{On}}</option>';
                              			echo '<option value="off" class="SC-binary" '.($ty=='off'?'selected':'').' '.($isBin?'':$noDisp).'>{{Off}}</option>';
                              			echo '<option value="titre" class="SC-message" '.($ty=='titre'?'selected':'').' '.($isBin?$noDisp:'').'>{{Message-titre}}</option>';
                              			echo '<option value="message" class="SC-message" '.($ty=='message'?'selected':'').' '.($isBin?$noDisp:'').'>{{Message-corps}}</option>';
                              			echo '<option value="slider" class="SC-other" '.($ty=='slider'?'selected':'').' '.($disa?'':$noDisp).'>{{Slider}}</option>';
                              			echo '<option value="color"  class="SC-other" '.($ty=='color'?'selected':'').' '.($disa?'':$noDisp).'>{{Color}}</option>';
                              			echo '<option value="select"  class="SC-other" '.($ty=='select'?'selected':'').' '.($disa?'':$noDisp).'>{{select}}</option>';
                              		echo '</select>';
                              
                              	echo '</div><div class="input-group" >';
                                    echo '<input class="cmdInfoAttr form-control SC-cmd-el" data-l1key="cmd_act" value="#'.$cmdAct->getHumanName().'#"/>';
                                    echo '<span class="input-group-btn">';
                                    echo '<button type="button" class="btn btn-default cursor listCmdActionMessage tooltips cmdSendSel" title="{{Rechercher un equipement}}" data-input="sendCmd"><i class="fas fa-list-alt"></i></button>';

                                    echo '<button type="button" class="btn btn-default cursor listCmdActionMessage tooltips cmdSendDelete" title="{{Supprimer une commande}}" data-input="delCmd"><i class="fas fa-times"></i></button>';
                                    echo '</span>';
                                echo '</div>';
                              echo '</div></br>';
                            }
                            echo '</td>';
                            // les commandes
                           echo '<td >';
                           echo '<span class="input-group-btn SC_button">';
                           echo '<button type="button" class="btn btn-default cursor tooltips addCmd" title="{{Ajouter une commande}}" data-input="add_cmd" ><i class="fas fa-plus-circle"></i></button>';
                         echo '</span>';
                           echo '</div>';
                            echo '</td>';
                            echo '</tr>';



                        }


						echo '</tbody>';
					echo '</table>';
				echo '</div>';
                echo '</div>';
            echo '</div>';
        }
        // les configurations
        include_file('desktop', 'modal.State_Capturer', 'js', 'State_Capturer');
        include_file('desktop', 'State_Capturer_modal', 'css', 'State_Capturer');


?>