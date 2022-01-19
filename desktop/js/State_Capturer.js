
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


/* Permet la réorganisation des commandes dans l'équipement */
$("#table_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});
$("#table_state_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});


/* Fonction permettant l'affichage des commandes dans l'équipement */
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
     var _cmd = {configuration: {}};
   }
   if (!isset(_cmd.configuration)) {
     _cmd.configuration = {};
   }
   if(!isset(_cmd.configuration.cmdType)){
    _cmd.configuration.cmdType = 'default';
  }
   
   if(_cmd.configuration.cmdType=='default' || _cmd.configuration.cmdType=='updateState'){   // ########################################### les commandes par défaut
      var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
      tr += '<td style="width:60px;">';
      tr += '<span class="cmdAttr" data-l1key="id"></span>';
      tr += '<input class="cmdAttr" data-l1key="configuration" data-l2key="cmdType" value="'+init(_cmd.configuration.cmdType)+'" style="display:none;"/>';
      tr += '</td>';

      tr += '<td style="min-width:300px;width:350px;">';
      tr += '<div class="row">';
      tr += '<div class="col-xs-7">';
      tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom de la commande}}" '+(_cmd.configuration.cmdType == 'updateState'? 'disabled':'')+' >';
      tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display : none;margin-top : 5px;" title="{{Commande information liée}}">';
      tr += '<option value="">{{Aucune}}</option>';
      tr += '</select>';
      tr += '</div>';
      tr += '<div class="col-xs-5">';
      tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> {{Icône}}</a>';
      tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
      tr += '</div>';
      tr += '</div>';
      tr += '</td>';
      tr += '<td>';
      tr += '<span class="type" type="' + init(_cmd.type) + '">' +  init(_cmd.type) + '</span>/';
      tr += '<span class="" subType="' + init(_cmd.subType) + '">' + init(_cmd.subType) + '</span>';
      tr += '</td>';
      tr += '<td style="min-width:80px;width:350px;">';
      tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label>';
      tr += '</td>';
      tr += '<td style="min-width:80px;width:200px;">';
      if (is_numeric(_cmd.id)) {
          tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
          if(_cmd.configuration.cmdType=='default')tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>';
      }
      tr += '<td><i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
      tr += '</tr>';

     if(_cmd.configuration.cmdType=='default'){
   		$('#table_cmd tbody').append(tr);
     	var tr = $('#table_cmd tbody tr').last();
     }else{
       	$('#table_update_cmd tbody').append(tr);
     	var tr = $('#table_update_cmd tbody tr').last();
     }
  }else if(_cmd.configuration.cmdType=='equip'){ // ###########################################  les équipement à capturer
      var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
      tr += '<td style="width:60px;">';
      tr += '<span class="cmdAttr" data-l1key="id"></span>';
      tr += '<input class="cmdAttr" data-l1key="configuration" data-l2key="cmdType" value="'+init(_cmd.configuration.cmdType)+'" style="display:none;"/>';
      tr += '<input class="cmdAttr form-control input-sm type" data-l1key="type" value="info" style="display:none;"/>';
      tr += '<span class="subType" subType="string" value="string"  style="display:none;"></span>';
      tr += '</td>';
      tr += '<td style="min-width:100px;width:150px;">';
      tr += '<div class="row col-xs-12">';
      tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom de la commande}}">';
      tr += '</div>';
      // commande de léquiepment
      tr += '<td style="width:460px;">';
      tr += '<div class="input-group" >';
      tr += '<input class="cmdAttr form-control CS-cmd-el" data-l1key="configuration" data-l2key="equip"/>';
      tr += '<span class="input-group-btn">';
      tr += '<button type="button" class="btn btn-default cursor listCmdActionMessage tooltips cmdSendSel" title="{{Rechercher un equipement}}" data-input="sendCmd"><i class="fas fa-list-alt"></i></button>';
      tr += '</span>';
      tr += '</div>';
    
      tr += '</td>';
      tr += '<td>';
      tr += '<label class="checkbox-inline" style="display:none;"><input type="checkbox" class="cmdAttr" data-l1key="isVisible"/>{{Afficher}}</label>';
      if (is_numeric(_cmd.id)) {
     tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
   }
      tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
      tr += '</tr>';

      $('#table_equip_cmd tbody').append(tr);
      var tr = $('#table_equip_cmd tbody tr').last();



    
  }else if(_cmd.configuration.cmdType=='state'){ // ########################################### les etats 
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
      tr += '<td style="width:60px;">';
      tr += '<span class="cmdAttr" data-l1key="id"></span>';
      tr += '<input class="cmdAttr" data-l1key="configuration" data-l2key="cmdType" value="'+init(_cmd.configuration.cmdType)+'" style="display:none;"/>';
      tr += '<input class="cmdAttr form-control input-sm type" data-l1key="type" value="action" style="display:none;"/>';
      tr += '<span class="subType" subType="string" value="string"  style="display:none;"></span>';
      tr += '</td>';
      tr += '<td style="min-width:100px;width:350px;">';
      tr += '<div class="row col-xs-8">';
      tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom de la commande}}">';
      tr += '</div>';
       tr += '<div class="col-xs-5">';
      tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> {{Icône}}</a>';
      tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
      tr += '</div>';
      // commande de léquiepment
      tr += '<td class="col-xs-7">';
      tr += '<span class="input-group-btn SC_button">';
      tr += '<button type="button" class="btn btn-default cursor tooltips stateUpdateBTN" title="{{Mettre à jour état}}" data-input="update_state" '+(!is_numeric(_cmd.id)?'disabled':'')+'><i class="fas fa-pencil-alt"></i> {{Mettre à jour état}}</button>';
      tr += '<button type="button" class="btn btn-default cursor tooltips stateConfBTN" title="{{afficher la configuration}}" data-input="update_state" '+(!is_numeric(_cmd.id)?'disabled':'')+'><i class="fas fa-cogs"></i> {{afficher Config}}</button>';
      tr += '<button type="button" class="btn btn-warning cursor tooltips delConfBTN" title="{{Supprimer la configuration}}" data-input="delete_state" '+(!is_numeric(_cmd.id)?'disabled':'')+'><i class="fas fa-cogs"></i> {{Supprimer Config}}</button>';
      tr += '</span>';
      tr += '</div>';
      tr += '</td>';
      tr += '<td class="col-xs-1">';
      tr += '<label class="checkbox-inline" ><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label>';
      tr += '</td>';
      tr += '<td class="col-xs-2">';
      if (is_numeric(_cmd.id)) {
             tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
             tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>';
        }
      tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
      tr += '</td>';
      tr += '</tr>';

      $('#table_state_cmd tbody').append(tr);
      var tr = $('#table_state_cmd  tbody tr').last();


  }
   




   jeedom.eqLogic.builSelectCmd({
     id:  $('.eqLogicAttr[data-l1key=id]').value(),
     filter: {type: 'info'},
     error: function (error) {
       $('#div_alert').showAlert({message: error.message, level: 'danger'});
     },
     success: function (result) {
       tr.find('.cmdAttr[data-l1key=value]').append(result);
       tr.setValues(_cmd, '.cmdAttr');
       jeedom.cmd.changeType(tr, init(_cmd.subType));
     }
   });
 }


 /*              GESTION Ajout Equipement       */

$('.cmdAction[data-action=addEquip]').on('click', function() {
  var _cmd = {configuration: {cmdType:'equip'}};
 addCmdToTable(_cmd)
 modifyWithoutSave = true
});

 /*              GESTION Ajout Etat       */
$('.cmdAction[data-action=addState]').on('click', function() {
  var _cmd = {configuration: {cmdType:'state'}};
 addCmdToTable(_cmd)
 modifyWithoutSave = true
});

/*              GESTION Selection Equipement       */
$('#table_equip_cmd').on('click','.cmdSendSel', function () {
        var el = $(this);
        jeedom.eqLogic.getSelectModal({cmd:{type:'action'}}, function(result) {
        var equip = el.closest('div').find('.cmdAttr[data-l1key=configuration][data-l2key=equip]');
        equip.val('');
        equip.atCaret('insert', result.human);
      });
    });
/*----------------> Fin GESTION Selection Equipement       */


      /*              Gestion des boutons de configuration            
--------------------------------------------------------------------- */

 $('#table_state_cmd').on('click','.stateUpdateBTN', function () {
        var confirmAct = confirm('{{Ceci mettra à jour la configuration précédente, qui sera écrasée}}');
        if(!confirmAct)return false;


        var el = $(this);
        var id = el.closest('tr').find('.cmdAttr[data-l1key=id]').value();
        console.log('Create State id : '+id);
        createState(id, "update_state");
    });
 $('#table_state_cmd').on('click','.stateConfBTN', function () {
        var el = $(this);
        var id = el.closest('tr').find('.cmdAttr[data-l1key=id]').value();
        console.log('Configuration State id : '+id);
        // ouvertude de la modale pour Configuration
       $('#md_modal').dialog({title: "Capture Configuration"}).load('index.php?v=d&plugin=State_Capturer&modal=modal.State_Capturer&id=' +id).dialog('open');

    });
 $('#table_state_cmd').on('click','.delConfBTN', function () {
        var confirmAct = confirm('{{Attention, ceci supprimera définitivement la configuration}}');
        if(!confirmAct)return false;
        var el = $(this);
        var id = el.closest('tr').find('.cmdAttr[data-l1key=id]').value();
        console.log('Create State id : '+id);
        createState(id, "delete_state");
    });

/*               APPEL Ajax pour update et createion de l'état         
--------------------------------------------------------------------------- */
function createState(cmdIdAct, actionCmd){

    

  $.ajax({
    type: "POST", 
    url: "plugins/State_Capturer/core/ajax/State_Capturer.ajax.php", 
    data: {
        action: actionCmd,
        cmdId:cmdIdAct
    },
    dataType: 'json',
    error: function (request, status, error) {
        handleAjaxError(request, status, error);
    },
    success: function (data) { // si l'appel a bien fonctionné
        if (init(data.state) != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }

        //console.log(data.result);
        $('#div_alert').showAlert({message: "Action ("+actionCmd+") Réussie", level: 'success'});

        
    }
  });
}