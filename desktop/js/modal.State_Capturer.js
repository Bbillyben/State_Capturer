
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

$(".table_modal").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});

/* Permet la réorganisation des commandes dans l'équipement */

$('.table_modal').on('click','.cmdSendDelete', function () {
        var el = $(this);
        var equip = el.closest('.cmdContainer');
        equip.remove();

});

// modif des commandes 
// selectiond e la commande 
$('.table_modal').on('click','.cmdSendSel', function () {
        var el = $(this);
        jeedom.cmd.getSelectModal({cmd:{type:'action'}}, function(result) {
        var equip = el.closest('div').find('.cmdInfoAttr[data-l1key=cmd_act]');
        equip.val('');
        equip.atCaret('insert', result.human);
        makeDestinationSelection(el,result); 
      });
    });

function makeDestinationSelection(el, result){
  var typeSel = result.cmd.subType;
  var elType = el.closest('tr');
  var cmCont = el.closest('.cmdContainer');
  var selInp = cmCont.find('.cmdInfoAttr[data-l1key=cmd_type]');
  // le type de l'info à mettre à jour
  var ty = elType.find('.cmdInfoAttr[data-l1key=type]').text();
  if(ty == 'binary'){
    selInp.prop('disabled', false);
    selInp.find('.SC-message').hide();
    selInp.find('.SC-other').hide();
    selInp.find('.SC-binary').show();

    // un on ou un off //on|allum|start|ouvrir|open|encende // |\[.*allum.*\]$|\[.*start.*\]$|\[.*ouvrir.*\]$|\[.*open.*\]$|\[.*encende.*\]$
    var onReg = new RegExp(/\[[^\]]*on.*\]\#|\[[^\]]*allum.*\]\#|\[[^\]]*start.*\]\#|\[[^\]]*ouvrir.*\]\#|\[[^\]]*open.*\]\#|\[[^\]]*encende.*\]\#/, 'gim');
    if( onReg.test(result.human) ){
        //selInp.find('[value=off]').prop('selected', false);
        selInp.find('[value=on]').prop('selected', true);
        
    }else{
        //selInp.find('[value=on]').prop('selected', false);
        selInp.find('[value=off]').prop('selected', true);
    }

    return;
  }
  console.log('type info : '+ ty +'  / type cmd : ' + typeSel);
  // on est dans les autre cas
  switch (typeSel) {
    case 'slider':
    case 'color':
    case 'select':
        selInp.prop('disabled', true);
        selInp.find('.SC-message').hide();
        selInp.find('.SC-other').show();
        selInp.find('.SC-binary').hide();
        selInp.find('[value='+typeSel+']').prop('selected', true);
      break;
    case 'message':
         selInp.prop('disabled', false);
        selInp.find('.SC-message').show();
        selInp.find('.SC-other').hide();
        selInp.find('.SC-binary').hide();
        selInp.find('[value=titre]').prop('selected', true);
    case null:
      break;
  }

};


    //ajout de la commande
$('.table_modal').on('click','.addCmd', function () {
        var el = $(this);
        var equip = el.closest('tr').find('.cmdRowCont');
        var tr = "";
  
  								tr +=  '<div class="cmdContainer">';
                                tr +=  '<div class="col-xs-3" >';
                              		tr +=  '<select class="cmdInfoAttr form-control SC-cmd-el" data-l1key="cmd_type" disabled>';
										tr +=  '<option value="on" class="SC-binary" >{{On}}</option>';
                              			tr +=  '<option value="off" class="SC-binary" >{{Off}}</option>';
                              			tr +=  '<option value="titre" class="SC-message" >{{Message-titre}}</option>';
                              			tr +=  '<option value="message" class="SC-message" >{{Message-corps}}</option>';
                              			tr +=  '<option value="slider" class="SC-other" >{{Slider}}</option>';
                              			tr +=  '<option value="color"  class="SC-other" >{{Color}}</option>';
                              			tr +=  '<option value="select"  class="SC-other" >{{select}}</option>';
                              		tr +=  '</select>';
                              
                              	tr +=  '</div><div class="input-group" >';
                                    tr +=  '<input class="cmdInfoAttr form-control SC-cmd-el" data-l1key="cmd_act" />';
                                    tr +=  '<span class="input-group-btn">';
                                    tr +=  '<button type="button" class="btn btn-default cursor listCmdActionMessage tooltips cmdSendSel" title="{{Rechercher un equipement}}" data-input="sendCmd"><i class="fas fa-list-alt"></i></button>';

                                    tr +=  '<button type="button" class="btn btn-default cursor listCmdActionMessage tooltips cmdSendDelete" title="{{Supprimer une commande}}" data-input="delCmd"><i class="fas fa-times"></i></button>';
                                    tr +=  '</span>';
                                tr +=  '</div>';
                              tr +=  '</div>';	
  
        equip.append(tr);

});


// fonction de sauvegarde
$('#bt_save_conf').on('click',function(){
  $( ".table_modal" ).sortable( "refreshPositions" );
    var form_data = {};
    console.log('start process');
    $('.eqLogicSCmodal').each(function(index){
        var cmdId= $(this).find('.cmdId[data-l1key=cmdId]').text()
        console.log( index + ": " +  cmdId);
        var cmdArr={};
        cmdArr['index']=index;
        // les info
      $(this).parent().find('#table_modal_SC_'+cmdId).find('tr').each(function(index2){
      
            // on est dans la table_modal_SC
            var infoCmdId=$(this).find('.cmdInfoAttr[data-l1key=id]').text();
        	console.log('id processed : '+infoCmdId);
            if(infoCmdId=='')return;
            var cmdInfoArr={};
        	cmdInfoArr['index']=index2;
            cmdInfoArr['activated']=$(this).find('.cmdInfoAttr[data-l1key=isActivated]').value();
            cmdInfoArr['state']=$(this).find('.cmdInfoAttr[data-l1key=state]').value();
            cmdInfoArr['type']=$(this).find('.cmdInfoAttr[data-l1key=type]').text();
            cmdInfoArr['force_update']=$(this).find('.cmdInfoAttr[data-l1key=isForced]').value();
            cmdInfoArr['cmd']={};
            // pour les commandes
            $(this).find('.cmdContainer').each(function(index, elCont){
                var elType = $(this).find('.cmdInfoAttr[data-l1key=cmd_type]').value();
                var cmdName = $(this).find('.cmdInfoAttr[data-l1key=cmd_act]').value();
                if(cmdName=="")return;
                cmdInfoArr['cmd'][elType]=cmdName;
            });

            cmdArr[infoCmdId]=cmdInfoArr
        });




        form_data[cmdId]=cmdArr;
    });

    console.log(JSON.stringify(form_data));
    var stateId=$('#cmdIdState').text();
     console.log('id state à sauver : '+stateId);
  
    $.ajax({
        type: "POST", 
        url: "plugins/State_Capturer/core/ajax/State_Capturer.ajax.php", 
        data: {
            action:'save_capture',
            cmdId:stateId,
            data:form_data
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
            $('#alerte_message_ca').showAlert({message: data.result, level: 'danger'});
            return;
        }
        $('#div_alert').showAlert({message: '{{Réussie}}', level: 'success'});
        $('#md_modal').dialog('close');

        
        }
      });
      

})