# State Capturer plugin for Jeedom

<p align="center">
  <img width="100" src="/plugin_info/State_Capturer_icon.png">
</p>

This plugin allows you to <b>capture different states</b>, from different devices and call these states afterwards. 

To do this, simply set up in an equipment State Capture the equipment you want to "photograph", position them in the desired state, and launch the capture. This capture can then be reloaded. You can set up multiple captures in a single device. 

Each capture keeps the values of all the info commands of the captured equipment, and searches for the associated commands. These commands will then be automatically called to update the device. 
You can edit each capture to define which commands to update, to change the retained state, and which commands control the change of values. If no command is associated, the plugin will try to use the `event` command to update the information.

# Configuration
  
  1. Activate the plugin
  
  2. Configurations: no configuration
  
  3. create a first equipment
  
  # Equipment
 
 ### General parameters      
 
 * __Equipment Name__ 
 * __Parent object__ 
 * __Category__ 
 * __Options__
 
 # Commands tab
  
 Five commands are created with the equipment: 
* __Last State__ : Reference the id of the "state" command that was last called
* __Last State Name__ : Reference the name of the "state" command that was called last
* __Load Last State__ : allows to update with the last called state

* __Load Next State__ : allows to update with the next state of `Last State`. The states are browsed in order of display on the `States` tab.
* __Load Previous State__: updates with the previous state of `Last State'. The states are scrolled through in reverse order of display on the `States' tab.

 # Equipment to Capture tab.

  <p align="center">
  <img width="100%" src="/docs/img/capture.PNG">
</p>

Click `Add Equipment` to add a command to the list, then in the command you create, fill in the `equipment` column with the equipment name. You can open the selection window to choose the equipment. 

That's all for this part!

 # States tab

  <p align="center">
  <img width="100%" src="/docs/img/etats.PNG">
</p>

This is where you will capture the states. 

Click `Add a State`, fill in the name of the state, then save. 
> :warning: You will not be able to capture a state without the command being saved!

* __Update State__ : allows you to create or update a state capture
* __Show Config__ : opens a window with the details of the configuration, which will allow you to modify the capture
* __Delete Config__ : deletes the capture from the command.


> You will systematically get a warning informing you that the previous state will be overwritten, the manipulation is irreversible!

Once saved, position the equipment in the state you wish to capture, then click on `Update State`. 
You then have a command that you can call to restore that state!

The capture contains the value of all the devices info commands, the type of information and the commands associated with that info command if any.
You can then modify the capture with the `Show Config` button

## Capture Configuration Window

  <p align="center">
  <img width="100%" src="/docs/img/config_1.PNG">
</p>

Here, the saved information for each device, the status value, and the associated commands are displayed. The type of the command affects how the information will be updated.
On/off, On/Off, Open/Close commands that update binary type information are automatically detected, depending on their name. 


You can modify here: 
* __Activate__ : allows you to specify whether this information should be updated when the report is loaded
* __Status__ : this is where the value of the captured report is entered, which you can modify.
* __Force Update__ : to force the update of the state, even if it is equal to the captured one
* __Command Type__ : Allows you to choose which type of command. The drop-down list is contextualized according to the choice of the command (see below). You can choose "on/off" for "binary" information commands, and "message-body/message-title" for message-type action commands (which update the info/status command)
* __Commands__ : These are the commands found by the plugin related to the information to update. You can select another command, with the button allowing you to open the command selection window, add a new one, via the `Add Command` button.
> Only `binary' commands (on/off type) are duplicates, `color', `numeric/slider' and `message' commands are unique. If there is more than one, only the first one will be used. 

> List commands are not supported, the information will be updated by calling the `event` method of the information. 
> For `message` type commands, the status value will be passed in the title and body of the message.

Once the changes have been made, press `Save` to save the capture.

Note: The information will be updated for each piece of equipment in the order it appears in this table, you can change the order by dragging and dropping.
This can be a problem for example for led ribbons. 

Typically, Lidl led ribbons have a color parameter and a color temperature parameter. If you change the temperature value after the color, you will not have the color display, but the white at temperature. 

=> You have to be careful to disable the temperature if you want a color and to disable the color if you want a "temperature" and both for the off.

ex to ensure the off (as the temperature and color are informed after the state on / off)
  <p align="center">
  <img width="100%" src="/docs/img/capture_2.PNG">
</p>
