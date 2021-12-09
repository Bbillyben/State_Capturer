# State Capturer plugin pour Jeedom

<p align="center">
  <img width="100" src="/plugin_info/State_Capturer_icon.png">
</p>

Ce plugin permet de <b>capturer différents états</b>, de différents équipements et d'appeler ces état par la suite. 

Pour cela il suffit de paramétrer dans un équipement State Capturer les équipement que vous souhaitez "photographier", de les positionner dans l'état souhaité, et de lancer la capture. Cette capture pourra alors être rechargé. Vous pouvez paramétrer plusieurs captures dans un seul équipement. 

Chaque capture conserve les valeurs de toutes les commandes info des équipements capturés, et recherche les commandes associées. Ces commandes seront alors automatiquement appelées pour mettre à jour l'équipement. 
Vous pouvez éditer chaque capture pour définir quelles sont les commandes à mettre à jour, modifié l'état conservé, ainsi que les commandes qui contrôlent la modification des valeurs. Si aucune commande n'est associée, le plugin essaira de passer par la commande `event` pour mettre à jour l'information.


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
  
 Quatre commandes sont créées avec l'équipement : 
* __Dernier Etat__ : Référence la commande "etat" qui a été appelée en dernier
* __Charger Dernier Etat__ : permet de mettre à jour avec le dernier état appelé
* __Charger Prochain Etat__ : permet de mettre à jour avec l'état suivant de `Dernier Etat`. Les états sont parcouru dans l'ordre d'affichage sur l'onglet `Etats`.
* __Charger Etat Précédent__ : permet de mettre à jour avec l'état précédent de `Dernier Etat`. Les états sont parcouru dans l'ordre inverse d'affichage sur l'onglet `Etats`.

 # Onglet Equipement à capturer

 <p align="center">
  <img width="100%" src="/docs/img/capture.PNG">
</p>

Dans cet onglet vous paramétrez les équipements dont vous souhaitez capturer les états.

Cliquez sur `Ajouter un équipement` pour ajouter une commande à la liste, puis dans la commande créée, renseignez dans la colonne `equipement` le nom de l'équipement. Vous pouvez ouvrir la fenêtre de sélection pour choisir l'équipement. 

C'est tout pour cette partie!

 # Onglet Etats
 
  <p align="center">
  <img width="100%" src="/docs/img/etats.PNG">
</p>

C'est ici que vous allez capturer les états. 
Cliquez sur `Ajouter un Etat`, renseignez le nom de l'état, puis sauvegardez. 
> :warning: Vous ne pourrez pas capturer un état sans que la commande soit sauvegardeé!.

Trois boutons sont alors disponibles : 
* __Mettre à jour l'Etat__ : permet de créer ou mettre à jour la capture d'un état
* __Afficher Config__ : ouvre une fenêtre avec le détail de la configuration, qui vous permettra de modifier la capture
* __Supprimer Config__ : supprime la capture de la commande.

> Vous aurez systématiquement un avertissement vous informant que l'état précédent sera écrasé, la manipulation est irréversible!

Une fois sauvegardée, positionnez les équipements dans l'état que vous souhaitez capturer, puis cliquez sur `Mettre à jour l'Etat`. 
Vous avez alors une commande que vous pouvez appeler pour restaure cet état!

La capture contient la valeur de toutes les commande info des équipements, du type d'information et des commandes associées à cette commande information le cas échéant.
Vous pouvez alors modifier la capture grace au bouton `Afficher Config`

## Fenetre de configuration de la capture
  <p align="center">
  <img width="100%" src="/docs/img/config_1.PNG">
</p>

Ici, sont affichées les informations sauvegardées pour chaque équipement, la valeur de l'état, ainsi que les commandes associées. Le type de la commande influe sur la façon dont seront mise à jour les informations.
Les commandes on/off, Allumer/Eteindre, Ouvrir/Fermer, qui mettent à jour les informations type binaire sont automatiquement détectées, selon leur nom. 

Vous pouvez mofidier ici : 
* __Activer__ : permet de spécifier si cette information doit être mise à jour lors du chargement de l'état
* __Etat__ : ici est renseigné la valeur de l'état capture, que vous pouvez modifier.
* __Commandes__ : Ce sont les commandes qu'a trouvé le plugin liées à l'information à mettre à jour. Vous pouvez selectionner une autre commande, avec le bouton vous permettant d'ouvrir la fenêtre de selection des commande, en ajouter une nouvelle, via le bouton `Ajout Commande`.
> Seule les commandes `binaires` (type on/off) sont en double, les commande de type `couleur`, `numérique/slider` et `message` sont unique. Si il y en a plusieurs, seule la première sera utilisée. 

> Les commandes de type liste ne sont pas prises en charge, l'information sera mise à jour par l'appel à la méthode `event` de l'information. 
> Pour les commandes de type `message`, la valeur de l'état sera transmise dans le titre et le corps du message.

Une fois les modifications faites, appuyer sur `Save` pour sauvegarder la capture.

Note : Les informations seront mises à jour pour chaque équipement dans l'ordre ou elle apparaissent dans ce tableau (non modifiable pour le moment).
Cela peux poser problème par exemple pour les ruban led. 

Typiquement, les ruban led de la marque Lidl ont un paramètre couleur et un paramètre température de couleur. Si vous modifiez la valeur de la température après la couleur, vous n'aurez pas l'affichage de la couleur, mais le blanc à température. 

=> Il faut veiller à désactiver la température si on veux une couleur et à déactiver la couleur si on veux une "température" et les deux pour le off.


ex pour assurer le off (comme la température et la couleur sont renseignées après l'état allumé/éteint)
  <p align="center">
  <img width="100%" src="/docs/img/capture_2.PNG">
</p>
