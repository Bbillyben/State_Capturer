# State Capturer plugin para Jeedom


<p align="center">
  <img width="100" src="/plugin_info/State_Capturer_icon.png">
</p>

Este plugin te permite <b>capturar diferentes estados</b>, de diferentes equipos y llamar a estos estados después. 

Para ello, basta con que se instale en un estado de equipo Capturar el equipo que desea "fotografiar", situarlo en el estado deseado e iniciar la captura. Esta captura se puede volver a cargar. Puedes configurar varias capturas en un solo dispositivo. 

Cada captura guarda los valores de todos los comandos de información del equipo capturado, y busca los comandos asociados. Estos comandos serán llamados automáticamente para actualizar el equipo. 
Puede editar cada captura para definir los comandos que deben actualizarse, cambiar el estado retenido y los comandos que controlan el cambio de valores. Si no hay ningún comando asociado, el plugin intentará utilizar el comando `event` para actualizar la información.

# Configuración
  
  1. Activar el plugin
  
  2. Configuraciones: ninguna configuración
  
  3. crear un primer equipo


 # Equipamiento
 
 ### Parámetros generales      
 
 * __Nombre del equipo__ 
 * __Objeto padre__ 
 * __Categoría__ 
 * __Opciones__
 
# Ficha de comandos

  <p align="center">
  <img width="100" src="/plugin_info/COMMAND.PNG">
</p>
  
 Se crean cinco comandos con el equipo: 
* __Last State__ : Referencia al id del comando "state" que fue llamado por última vez
* __Last State Name__ : Referencia al nombre del comando "state" que fue llamado por última vez
* __Cargar el último estado__ : permite actualizar con el último estado llamado

* __Cargar el siguiente estado__ : se actualiza con el siguiente estado de `Last State`. Los estados se visualizan por orden en la pestaña "Estados".
* __Cargar estado anterior__: se actualiza con el estado anterior de `Último estado'. Los estados se visualizan en orden inverso en la pestaña "Estados".

Una tabla secundaria contiene los comandos utilizados para actualizar los estados sobre la marcha. 
Estos comandos se crean automáticamente cuando se guardan los estados. 
Su nombre se genera automáticamente y no se puede modificar, y no se muestran por defecto en el widget.
Estos comandos no pueden ser llamados directamente en la configuración del plugin, pero están disponibles con el equipo, especialmente en los escenarios.

:aviso: No se pedirá confirmación al llamar a estos comandos, y el estado anterior se sobrescribirá permanentemente.


 # Pestaña de Equipos a Capturar
 
  <p align="center">
  <img width="100%" src="/docs/img/capture.PNG">
</p>


Haga clic en `Agregar equipo` para añadir un comando a la lista, luego en el comando creado, rellene la columna `Equipo` con el nombre del equipo. Puede abrir la ventana de selección para elegir el equipo. 

¡Eso es todo por esta parte!

# Ficha de los Estados
   <p align="center">
  <img width="100%" src="/docs/img/etats.PNG">
</p>


Aquí es donde se capturan los informes. 

Haga clic en "Añadir un informe", introduzca el nombre del informe y guárdelo. 
> :advertencia: ¡No podrás capturar un estado sin que se haya guardado el comando!

A continuación, hay tres botones disponibles: 

* __Update State__ : permite crear o actualizar una captura de estado
* __Show Config__ : abre una ventana con los detalles de la configuración, que le permitirá modificar la captura
* __Borrar Config__ : borra la captura del comando.

> Sistemáticamente recibirá una advertencia que le informará de que el estado anterior se sobrescribirá, ¡la manipulación es irreversible!

Una vez guardado, sitúe el equipo en el estado que desea capturar y pulse "Actualizar estado". 
A continuación, tienes un comando que puedes llamar para restaurar ese estado.

La captura contiene el valor de todos los comandos de información de los dispositivos, el tipo de información y los comandos asociados a ese comando de información si los hay.
A continuación, puede modificar la captura mediante el botón "Mostrar configuración".

## Ventana de configuración de la captura

  <p align="center">
  <img width="100%" src="/docs/img/config_1.PNG">
</p>

Aquí se muestra la información guardada de cada equipo, el valor del estado y los comandos asociados. El tipo de comando afecta a cómo se actualizará la información.
Se detectan automáticamente los comandos de encendido/apagado, apertura/cierre que actualizan la información del tipo binario, en función de su nombre. 

Puedes cambiar aquí: 
* __On__: permite especificar si esta información debe actualizarse cuando se carga el informe
* __Status__: aquí está el valor del estado de la captura, que se puede modificar.
* __Force UpdateForzar una actualización__ : para forzar la actualización del estado, aunque sea igual al capturado La lista desplegable está contextualizada y permite elegir el tipo de comando. La lista desplegable se contextualiza en función de la elección del comando (ver más abajo). Puede elegir "on/off" para los comandos de información "binarios", y "message-body/message-title" para los comandos de acción de tipo mensaje (que actualizan el comando info/status)
* _Comandos__ : Son los comandos encontrados por el plugin relacionados con la información a actualizar. Puede seleccionar otro comando, utilizando el botón para abrir la ventana de selección de comandos, o añadir uno nuevo, utilizando el botón `Añadir comando`.


> Sólo los comandos "binarios" (tipo on/off) son duplicados, los comandos "color", "numérico/deslizante" y "mensaje" son únicos. Si hay más de uno, sólo se utilizará el primero. 

> No se admiten comandos de lista, la información se actualizará llamando al método `event` de la información. 
> Para los comandos de tipo `mensaje`, el valor del estado se pasará en el título y el cuerpo del mensaje.

Una vez realizados los cambios, pulse `Save` para guardar la captura.

Nota: La información se actualizará para cada equipo en el orden en que aparece en esta tabla, puede cambiar el orden arrastrando y soltando.
Esto puede ser un problema, por ejemplo, para las cintas de leds. 

Normalmente, las cintas led de Lidl tienen un parámetro de color y otro de temperatura de color. Si cambia el valor de la temperatura después del color, no tendrá la visualización del color, sino del blanco a temperatura. 

=> Asegúrese de desactivar la temperatura si quiere un color y desactivar el color si quiere una "temperatura" y ambos para el apagado.



por ejemplo, para asegurar el apagado (ya que la temperatura y el color se informan después del estado de encendido/apagado)
  <p align="center">
  <img width="100%" src="/docs/img/capture_2.PNG">
</p>

