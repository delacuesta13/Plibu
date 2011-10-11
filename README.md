# PLIBU

La **Pl**ataforma de **I**nscripciones de **B**ienestar **U**niversitario es una *aplicaci&oacute;n web* que ampl&iacute;a el funcionamiento del Sistema de Informaci&oacute;n
[Sigabu](https://github.com/delacuesta13/Sigabu) <sup>1</sup>. Por tal raz&oacute;n, *Plibu* es una aplicaci&oacute;n dependiente de la gesti&oacute;n de la informaci&oacute;n 
que se realiza a trav&eacute;s de dicho sistema.

Esta *aplicaci&oacute;n web* permite que la *comunidad universitaria* conozca la oferta de actividades de un determinado per&iacute;odo acad&eacute;mico, de tal forma que, las personas que conforman dicha comunidad podr&aacute;n inscribirse en las diferentes actividades. *Plibu*, a diferencia de *Sigabu*, no est&aacute; delimitada a un determinado grupo de personas; sin embargo,
para poder inscribirse en alguna actividad, gestionar el perfil (seg&uacute;n la clasificaci&oacute;n de la *comunidad universitaria*) y revisar las inscripciones en un determinado per&iacute;odo, el usuario que interact&uacute;a con la aplicaci&oacute;n deber&aacute; identificarse en &eacute;sta, raz&oacute;n por la cual, el proceso de identificaci&oacute;n ser&aacute; exitoso si los datos de la persona existen en la Base de Datos que se gestiona a trav&eacute;s del sistema *Sigabu*.

## Requerimientos

* PHP 5.3.5 o superior.
* MySQL 5.1.54 o superior.
* Apache 2.2.17 o superior.

## Instalaci&oacute;n

Antes de iniciar la instalaci&oacute;n de esta aplicaci&oacute;n, recuerde que debe haber instalado el sistema *Sigabu*.

1. Haber instalado el sistema *Sigabu*, y la base de datos que se incluye en el respositorio de dicho sistema.
2. Ubicar el directorio ***plibu*** en el directorio ra&iacute;z del servidor web <sup>2</sup>.
3. Configurar la plataforma.

**Nota:** Aseg&uacute;rese que est&eacute; habilitado el m&oacute;dolu `mod_rewrite` de Apache. 

## Configuraci&oacute;n

Una vez realizados los pasos indicados de la instalaci&oacute;n, configure la *aplicaci&oacute;n* seg&uacute;n los par&aacute;metros que ha definido para cada una de las tecnolog&iacute;as
que componen el servidor web. Para configurar el Sistema, s&oacute;lo tiene que editar el fichero `config/config.php`.

A continuaci&oacute;n se explican las variables de configuraci&oacute;n de la plataforma, sus posibles valores y su significado dentro del mismo.

* DEVELOPMENT\_ENVIRONMENT
	* tipo: `boolean`.
	* valores: `true | false`
	* explicaci&oacute;n: defina como `true` si usar&aacute; la aplicaci&oacute;n web en ambiente de desarrollo. Ello significa que se 
	notificar&aacute;n todos los errores encontrados en compilaci&oacute;n. En caso de definir como `false`, los errores no ser&aacute;n notificados, 
	sino que se guardar&aacute;n en un log de errores, en el fichero `tmp/logs/error.log`.
* DB\_NAME
	* tipo: `string`.
	* explicaci&oacute;n: nombre de la base de datos con la cual trabajar&aacute; la aplicaci&oacute;n. El nombre de la BD deber&aacute; ser el mismo
	que ha definido en la configuraci&oacute;n `sigabu/config/config.php` del sistema *Sigabu*.
	Recuerde que ambos sistemas vienen pre-definidos para trabajar con la base de datos `sigabu`.
* DB\_HOST, DB\_USER, DB\_PASSWORD
	* tipo: `string`.
	* explicaci&oacute;n: nombre del host, de usuario y password para establecer conexi&oacute;n con MySQL.
* BASE\_PATH
	* tipo: `string`.
	* explicaci&oacute;n: URL que apunta al directorio `plibu`.
* PAGINATE\_LIMIT:
	* tipo: `int`.
	* explicaci&oacute;n: n&uacute;mero l&iacute;mite de registros que se mostrar&aacute;n al paginar.
* INSCRIPCIONES\_CRUCEHRS:
	* tipo: `boolean`.
	* valores: `true | false`.
	* explicaci&oacute;n: en el fichero `config/config.php` est&aacute; comentada la explicaci&oacute;n de esta variable.

## Recomendaciones

*Plibu* est&aacute; implementada con el conjunto de tecnolog&iacute;as [HTML5](http://www.w3.org/html/logo/). Para su implementaci&oacute;n se utiliz&oacute; el *framework*
[Twitter](http://twitter.com/twitter) [Bootstrap](http://twitter.github.com/bootstrap/).

1. La aplicaci&oacute;n est&aacute; lista para trabajar en la mayor&iacute;a de navegadores web **modernos** <sup>3</sup>.
2. Resoluci&oacute;n de pantalla superior a **1000\*800** pixeles.

## Seguimiento a bugs

Si encontraste un bug, por favor crea un tema aqu&iacute; en GitHub.

[Crear tema!](https://github.com/delacuesta13/Plibu/issues)

## Contribuir

* Si&eacute;ntete libre de hacer un ***fork*** a este repositorio.
* Env&iacute;a una solicitud de ***pull***.

---

### Versionamiento

El sistema de informaci&oacute;n *Sigabu* y la aplicaci&oacute;n web *Plibu*, deber&aacute;n ser mantenidos a trav&eacute;s de las directrices de Versionamiento Sem&aacute;ntico.

Las versiones ser&aacute;n numeradas bajo el siguiente formato:

`<major>.<minor>.<patch>`

Para m&aacute;s informaci&oacute;n, por favor visita [http://semver.org/](http://semver.org/).

## Desarrollo del sistema

El desarrollo del Sistema de Informaci&oacute;n se concibi&oacute; bajo el enfoque de separar &eacute;ste en dos partes: **Front-end** y **Back-end**.

**Back-end** es la interfaz del sistema en la cual administrar, por completo, las funcionalidades implementadas y el comportamiento del mismo. Esta interfaz
est&aacute; delimitada para ser usada por el staff de Bienestar Universitario, desde el Jefe del departamento hasta los monitores de las actividades ofertadas
por Bienestar. [*Sigabu*](https://github.com/delacuesta13/Sigabu) es la denominaci&oacute;n que se la ha dado a esta interfaz.

**Front-end** es la interfaz del sistema abierta a toda la *Comunidad Universitaria*, en la cual se muestran las actividades programadas en un determinado per&iacute;odo;
ampliando la informaci&oacute;n para cada actividad, as&iacute; como los horarios que se definieron para &eacute;sta. Adem&aacute;s de consultar las actividades, la *comunidad*
tiene la posibilidad de inscribirse en &eacute;stas. **Plibu** es la denominaci&oacute;n que se le ha dado a esta interfaz.

---

### Importante

Por favor **no** iniciar sesi&oacute;n en las interfaces **Sigabu** y **Plibu** al mismo tiempo, usando un mismo *navegador web*. 
Se recomienda inicar sesi&oacute;n en una sola interfaz, y finalizada la misma, iniciar sesi&oacute;n en la otra interfaz.

## Acerca de  

El *Sistema de Informaci&oacute;n para los procesos de Inscripci&oacute;n, Control de Asistencia y Gesti&oacute;n de Actividades de las &aacute;reas de
Recreaci&oacute;n y Deportes y Art&iacute;stica y Cultural del departamento de Bienestar Universitario de la Universidad Cooperativa de Colombia, sede Cali*, 
es un proyecto de desarrollo de *software*, por medio del cual, optar al t&iacute;tulo de **Ingeniero de Sistemas** de la universidad mencionada.

---

### Autor

Jhon Adri&aacute;n Cer&oacute;n Guzm&aacute;n <[jadrian.ceron@gmail.com](mailto:jadrian.ceron@gmail.com)>.

## Copyright y licencia

Copyright &copy; 2011 Jhon Adri&aacute;n Cer&oacute;n Guzm&aacute;n.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

---
1. Antes de continuar la lectura de este documento, y en procura de un adecuado entendimiento, es necesario haber le&iacute;do el documento 
[README](https://github.com/delacuesta13/Sigabu/edit/master/README.md) del Sistema de Informaci&oacute;n [Sigabu](https://github.com/delacuesta13/Sigabu).
2. Por lo general (y sin ser una regla), el directorio ra&iacute;z de un servidor web es ***www*** o ***htdocs***.
3. Navegadores modernos como Chrome, Safari, Internet Explorer 7+, Firefox 4+ y Opera 11.
