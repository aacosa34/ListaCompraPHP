# Lista de la Compra en PHP

Para instalar twig localmente es necesario ejecutar los siguientes comandos:

	$ curl -s http://getcomposer.org/installer | php
	$ nvim composer.json
	$ php composer.phar install

Donde el archivo llamado `composer.json` contiene lo siguiente:
	
	{
	"require":{
	"twig/twig":"^3.0"
	}
	}

NOTA: Las vistas en vez de almacenarlas en el directorio `view` las almaceno en templates porque asi lo requiere twig.
