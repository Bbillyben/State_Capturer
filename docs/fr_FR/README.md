# State Capturer plugin pour Jeedom

<p align="center">
  <img width="100" src="/plugin_info/State_Capturer_icon.png">
</p>


Ce plugin permet de <b>capturer différents états</b>, de différents équipements et d'appeler ces états par la suite. 

Pour cela, il suffit de paramétrer dans un équipement State Capturer les équipements que vous souhaitez "photographier", de les positionner dans l'état souhaité, et de lancer la capture. Cette capture pourra alors être rechargée. Vous pouvez paramétrer plusieurs captures dans un seul équipement. 

Chaque capture conserve les valeurs de toutes les commandes info des équipements capturés, et recherche les commandes associées. Ces commandes seront alors automatiquement appelées pour mettre à jour l'équipement. 
Vous pouvez éditer chaque capture pour définir quelles sont les commandes à mettre à jour, modifier l'état conservé, ainsi que les commandes qui contrôlent la modification des valeurs. Si aucune commande n'est associée, le plugin essaiera de passer par la commande `event` pour mettre à jour l'information.



# Configuration
  
  1. activer le plugin
  
  2. Configurations :  pas de configuration
  
  3. créer un premier équipement


 # Equipement
 
 ### Paramètres généraux      
 
 * __Nom de l'équipement__ 
 * __Objet parent__ 
 * __Catégorie__ 
 * __Options__
 
 
  # Onglet Commandes
  
 Cinq commandes sont créées avec l'équipement : 
* __Dernier Etat__ : Référence l'id de la commande "etat" qui a été appelée en dernier
* __Nom Dernier Etat__ : Référence le nom de la commande "etat" qui a été appelée en dernier
* __Charger Dernier Etat__ : permet de mettre à jour avec le dernier état appelé

* __Charger Prochain Etat__ : permet de mettre à jour avec l'état suivant de `Dernier Etat`. Les états sont parcourus dans l'ordre d'affichage sur l'onglet `Etats`.
* __Charger Etat Précédent__ : permet de mettre à jour avec l'état précédent de `Dernier Etat`. Les états sont parcourus dans l'ordre inverse d'affichage sur l'onglet `Etats`.


 # Onglet Equipement à capturer

 <p align="center">
  <img width="100%" src="/docs/img/capture.PNG">
</p>


Dans cet onglet, vous paramétrez les équipements dont vous souhaitez capturer les états.


Cliquez sur `Ajouter un équipement` pour ajouter une commande à la liste, puis dans la commande créée, renseignez dans la colonne `equipement` le nom de l'équipement. Vous pouvez ouvrir la fenêtre de sélection pour choisir l'équipement. 

C'est tout pour cette partie!

 # Onglet Etats
 
  <p align="center">
  <img width="100%" src="/docs/img/etats.PNG">
</p>

C'est ici que vous allez capturer les états. 

Cliquez sur `Ajouter un Etat`, renseignez le nom de l'état, puis sauvegardez. 
> :warning: Vous ne pourrez pas capturer un état sans que la commande soit sauvegardée !

Trois boutons sont alors disponibles : 

* __Mettre à jour l'Etat__ : permet de créer ou mettre à jour la capture d'un état
* __Afficher Config__ : ouvre une fenêtre avec le détail de la configuration, qui vous permettra de modifier la capture
* __Supprimer Config__ : supprime la capture de la commande.


> Vous aurez systématiquement un avertissement vous informant que l'état précédent sera écrasé, la manipulation est irréversible !

Une fois sauvegardée, positionnez les équipements dans l'état que vous souhaitez capturer, puis cliquez sur `Mettre à jour l'Etat`. 
Vous avez alors une commande que vous pouvez appeler pour restaurer cet état !

La capture contient la valeur de toutes les commandes info des équipements, du type d'information et des commandes associées à cette commande information le cas échéant.
Vous pouvez alors modifier la capture grâce au bouton `Afficher Config`

## Fenêtre de configuration de la capture

  <p align="center">
  <img width="100%" src="/docs/img/config_1.PNG">
</p>

Ici, sont affichées les informations sauvegardées pour chaque équipement, la valeur de l'état, ainsi que les commandes associées. Le type de la commande influe sur la façon dont seront mises à jour les informations.
Les commandes on/off, Allumer/Eteindre, Ouvrir/Fermer, qui mettent à jour les informations type binaire sont automatiquement détectées, selon leur nom. 

Vous pouvez modifier ici : 
* __Activer__ : permet de spécifier si cette information doit être mise à jour lors du chargement de l'état
* __Etat__ : ici est renseignée la valeur de l'état capture, que vous pouvez modifier.
* __Force Update__ : pour forcer la mise à jour de l'état, même si celui-ci est égal à celui capturé.
* __Type de commande__ : Vous permet de choisir quel type de commande. La liste déroulante est contextualisée en fonction du choix de la commande (voir ci-dessous). Vous pourrez choisir "on/off" pour les commandes information "binaire", et "message-corps/message-titre" pour les commandes action de type message (qui mettent à jour la commande info/etat)
* __Commandes__ : Ce sont les commandes qu'a trouvé le plugin liées à l'information à mettre à jour. Vous pouvez sélectionner une autre commande, avec le bouton vous permettant d'ouvrir la fenêtre de sélection des commandes, en ajouter une nouvelle, via le bouton `Ajout Commande`.
> Seules les commandes `binaires` (type on/off) sont en double, les commande de type `couleur`, `numérique/slider` et `message` sont uniques. Si il y en a plusieurs, seule la première sera utilisée. 

> Les commandes de type liste ne sont pas prises en charge, l'information sera mise à jour par l'appel à la méthode `event` de l'information. 
> Pour les commandes de type `message`, la valeur de l'état sera transmise dans le titre et le corps du message.

Une fois les modifications faites, appuyer sur `Save` pour sauvegarder la capture.

Note : Les informations seront mises à jour pour chaque équipement dans l'ordre où elles apparaissent dans ce tableau, vous pouvez modifier l'ordre par un glissé déposé.
Cela peut poser problème par exemple pour les rubans led. 

Typiquement, les rubans led de la marque Lidl ont un paramètre couleur et un paramètre température de couleur. Si vous modifiez la valeur de la température après la couleur, vous n'aurez pas l'affichage de la couleur, mais le blanc à température. 

=> Il faut veiller à désactiver la température si on veut une couleur et à désactiver la couleur si on veut une "température" et les deux pour le off.



ex pour assurer le off (comme la température et la couleur sont renseignées après l'état allumé/éteint)
  <p align="center">
  <img width="100%" src="/docs/img/capture_2.PNG">
</p>
