Eventos UMAG
Plugin de WordPress para gestionar y mostrar eventos institucionales de forma simple, flexible.

¿Qué hace?
Eventos UMAG te permite agregar eventos desde el panel de administración y mostrarlos en cualquier página o entrada usando un shortcode. 
Incluye opciones de visualización bastante completas para que el resultado se vea bien sin tener que tocar mucho el CSS.

Requisitos
WordPress 7.0 o superior (no ha sido probado en versiones anteriores)


Instalación
Descargar o clonar este repositorio
Subir la carpeta del plugin a /wp-content/plugins/
Activarlo desde Plugins en el panel de WordPress
Listo — vas a ver el menú Eventos UMAG en el sidebar de administración

Uso básico
Una vez activado, usa el shortcode en cualquier página o entrada:
[eventos_umag]
Eso es todo lo mínimo necesario para que funcione, el shortcode tiene mas opciones en caso de que quieras mostrar personalizado.

Panel de administración
Desde el menú Eventos UMAG vas a encontrar las siguientes secciones:

Todos los eventos — listado y gestión de eventos cargados
Agregar nuevo evento — formulario para crear un evento
Categorías — organiza los eventos por tipo
Ubicaciones — gestionar los lugares asociados a los eventos
Visualización — configurar cómo se ven los eventos en el sitio
Carrusel — ajustes específicos del modo carrusel


Opciones de visualización
Desde la sección Visualización puedes configurar:
Elementos visibles
Cuántas tarjetas se muestran al mismo tiempo, con valores separados para desktop, tablet y celular.

Los eventos se limitan al mes actual.

Modo de presentación

Carrusel — desplazamiento horizontal con controles laterales
Grilla con paginación — layout tradicional en filas y columnas

Vista de tarjetas

Listado — equilibra imagen y texto
Grande — prioriza el impacto visual
Resumido — muestra más eventos en menos espacio (no muestra imagen destacada)

Fondo del contenedor

Transparente
Color sólido (hex RGB, formato #593d80 color institucional UMAG)
Degradado — con opciones de tipo (lineal o circular), transparencia (0–100), y ubicación


Configuración del carrusel
Desde la sección Carrusel poduedes ajustar:
Opción, Descripción, Autoplay e Iniciar automáticamente al cargar la página
Loop infinito
Repite el carrusel de forma continua
Tiempo entre slides En milisegundos (por defecto: 4000ms)
Duración de animación Velocidad del desplazamiento (por defecto: 900ms)
Pausa al pasar el cursor Detiene el carrusel cuando el mouse está encima
Arrastre táctil y mouse Permite navegar arrastrando, útil en móviles y touch

Notas

El plugin fue desarrollado para uso institucional en la Universidad de Magallanes (UMAG), aunque puede adaptarse a otros contextos sin demasiado esfuerzo.
Los eventos cargados se filtran automáticamente al mes en curso.


Licencia
Por definir.
