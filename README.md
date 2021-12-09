# State Capturer plugin pour Jeedom

<p align="center">
  <img width="100" src="/plugin_info/State_Capturer_icon.png">
</p>

Ce plugin permet de <b>capturer différents états</b>, de différents équipements et d'appeler ces état par la suite. 

Pour cela il suffit de paramétrer dans un équipement `State Capturer` les équipements que vous souhaitez "photographier", de les positionner dans l'état souhaité, et de lancer la capture. Cette capture pourra alors être rechargée. Vous pouvez paramétrer plusieurs captures dans un seul équipement. 

Chaque capture conserve les valeurs de toutes les commandes info des équipements capturés, et recherche les commandes associées. Ces commandes seront alors automatiquement appelées pour mettre à jour l'équipement. 
Vous pouvez éditer chaque capture pour définir quelles sont les commandes à mettre à jour, modifier l'état conservé, ainsi que les commandes qui contrôlent la modification des valeurs. Si aucune commande n'est associée, le plugin essaira de passer par la commande `event` pour mettre à jour l'information.



Documentation :
  * fr : https://github.com/Bbillyben/State_Capturer/tree/master/docs/fr_FR



Forum/community : 
  * general : https://community.jeedom.com/
  * sujet plugin : -tbd-
 
 
