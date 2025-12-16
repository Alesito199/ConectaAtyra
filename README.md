# 🌐 ConectaAtyrá - Red Social Profesional

![Logo ConectaAtyrá](uploads/logoConectaAtyra.png)

**ConectaAtyrá** es una plataforma de red social diseñada para conectar profesionales, compartir publicaciones, gestionar eventos y facilitar la comunicación mediante mensajería directa.

**Aclaracion**: Este sistema fue desarrollado por mí como **programador/desarrollador profesional contratado** para proporcionar la implementación técnica de un proyecto de tesis del área de Ingeniería Informática. La **la defensa de la tesis pertenecen exclusivamente a la estudiante** que contrató mis servicios de desarrollo. Mi rol fue estrictamente técnico: análisis, diseño, programación y documentación del software.

---

## 📋 Tabla de Contenidos

- [Características](#características)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Uso](#uso)
- [Panel de Administración](#panel-de-administración)
- [Capturas de Pantalla](#capturas-de-pantalla)
- [Contribuciones](#contribuciones)
- [Licencia](#licencia)
- [Contacto](#contacto)

---

## ✨ Características

### 👥 Para Usuarios
- **Registro e Inicio de Sesión**: Sistema de autenticación seguro con contraseñas encriptadas
- **Perfiles Personalizados**: Edición de perfil con foto, información personal y experiencia laboral
- **Publicaciones**: Crear, editar y eliminar publicaciones con imágenes
- **Mensajería Directa**: Chat en tiempo real entre usuarios
- **Eventos**: Crear y visualizar eventos profesionales (sujetos a aprobación administrativa)
- **Búsqueda de Usuarios**: Filtrar profesionales por nombre, ciudad o profesión
- **Timeline de Experiencia**: Visualización cronológica de experiencia laboral

### 🔐 Para Administradores
- **Dashboard con Estadísticas**: Gráficos de usuarios, publicaciones y mensajes
- **Gestión de Usuarios**: Visualizar, editar estado y eliminar usuarios
- **Moderación de Eventos**: Aprobar o rechazar eventos creados por usuarios
- **Monitoreo de Mensajes**: Supervisar conversaciones en la plataforma
- **Gestión de Consultas**: Revisar solicitudes de soporte de usuarios

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 7.4+** - Lenguaje del lado del servidor

### Frontend
- **HTML5 & CSS3**
- **Tailwind CSS** - Framework de utilidades CSS
- **JavaScript (Vanilla)** - Interactividad del cliente
- **Chart.js** - Visualización de datos estadísticos
- **SweetAlert2** - Alertas personalizadas

### Servidor
- **Apache** - Servidor web
- **WAMP/XAMPP** - Entorno de desarrollo (opcional)

---

## 💻 Requisitos del Sistema

- PHP >= 7.4
- Apache con mod_rewrite habilitado
- Servidor web (WAMP/XAMPP recomendado)

---

## 📦 Instalación y Configuración

Este proyecto fue desarrollado y entregado como sistema completo para una tesis académica.

### 1. Clonar o descargar el repositorio
```bash
git clone https://github.com/tu-usuario/conecta-atyra.git
cd conecta-atyra
```

### 2. Verificar estructura de carpetas
El proyecto incluye las carpetas necesarias para uploads:
```
uploads/perfiles/
uploads/documentos/
uploads/eventos/
main/uploads/perfiles/
main/uploads/documentos/
main/uploads/eventos/
main/uploads/publicaciones/
```

Asegúrate de que tengan permisos de escritura en tu servidor.

### 3. Iniciar el servidor
Con WAMP/XAMPP activo, accede a:
```
http://localhost/ConectaAtyrá/index.php
```

---

## ⚙️ Configuración

### Configuración de Uploads
Las carpetas de uploads permiten almacenar:
- **perfiles/**: Fotos de perfil de usuarios
- **documentos/**: Archivos adjuntos (futuro)
- **eventos/**: Imágenes de eventos
- **publicaciones/**: Imágenes de publicaciones

### Configuración de Email (Futuro)
Para notificaciones por email, edita [config/funciones/funciones.php](config/funciones/funciones.php) y configura SMTP.

---

## 📁 Estructura del Proyecto

```
ConectaAtyra/
├── admin/                      # Panel de administración
│   ├── menuAdmin.php          # Dashboard con estadísticas
│   ├── adminUsuarios.php      # Gestión de usuarios
│   ├── adminEventos.php       # Moderación de eventos
│   ├── adminMensajes.php      # Monitoreo de mensajes
│   ├── adminConsultas.php     # Gestión de consultas
│   └── proceso/               # Procesadores backend del admin
│
├── config/                     # Configuración
│   └── funciones/             # Funciones auxiliares
│       ├── funciones.php      # Funciones generales
│       └── logout.php         # Cerrar sesión
│
├── css/                        # Estilos
│   └── styles.css             # Estilos personalizados
│
├── main/                       # Módulos principales
│   ├── menu.php               # Página principal con publicaciones
│   ├── perfil.php             # Editar perfil propio
│   ├── perfilMuestra.php      # Ver perfil de otros usuarios
│   ├── publicacion.php        # Vista detallada de publicaciones
│   ├── mensajes.php           # Lista de conversaciones
│   ├── chat.php               # Chat individual
│   ├── eventos.php            # Gestión de eventos
│   ├── guardar_consulta.php   # Formulario de soporte
│   ├── deshabilitar_cuenta.php # Desactivar cuenta
│   ├── procesos/              # Procesadores backend
│   │   ├── publicar.php       # Crear publicación
│   │   ├── editar_publicacion.php
│   │   ├── eliminar_publicacion.php
│   │   ├── filtrarPublicaciones.php
│   │   ├── enviarMensaje.php
│   │   ├── obtenerMensajes.php
│   │   ├── obtenerConversaciones.php
│   │   ├── editarMensaje.php
│   │   ├── eliminarMensaje.php
│   │   └── crearEvento.php
│   │
│   └── uploads/               # Archivos subidos por usuarios
│       ├── perfiles/
│       ├── documentos/
│       ├── eventos/
│       └── publicaciones/
│
├── uploads/                    # Archivos generales
│   ├── perfiles/
│   ├── documentos/
│   ├── eventos/
│   └── logoConectaAtyra.png   # Logo del proyecto
│
├── index.php                   # Página de login
├── register.php                # Página de registro
├── verificar_datos.php         # Recuperación de cuenta
├── crea.php                    # Creador de admin (eliminar después)
├── package.json                # Dependencias Node.js
├── tailwind.config.js          # Configuración Tailwind CSS
├── .gitignore                  # Archivos ignorados por Git
└── README.md                   # Este archivo
```

---

## 🚀 Uso

### Para Usuarios

#### 1. Registro
Accede a [register.php](register.php) y completa el formulario con:
- Nombre completo
- Email
- Contraseña
- Teléfono
- Ciudad
- Profesión

#### 2. Inicio de Sesión
Usa tu email y contraseña en [index.php](index.php)

#### 3. Crear Publicaciones
En el menú principal, haz clic en "Crear Publicación":
- Escribe el contenido
- Opcionalmente adjunta una imagen
- Publica

#### 4. Enviar Mensajes
- Busca un usuario en la barra de búsqueda
- Haz clic en su perfil
- Haz clic en "Enviar Mensaje"
- Escribe y envía tu mensaje

#### 5. Crear Eventos
En la sección "Eventos":
- Haz clic en "Crear Evento"
- Completa: nombre, descripción, fecha, hora, ubicación
- Adjunta una imagen
- Los eventos quedan "Pendientes" hasta aprobación del admin

### Para Administradores

#### Iniciar Sesión como Admin
Usa las credenciales creadas con `crea.php`:
- **Email**: admin@example.com
- **Contraseña**: admin123

#### Dashboard
Visualiza estadísticas en tiempo real:
- Usuarios registrados por fecha
- Publicaciones creadas por fecha
- Mensajes enviados por fecha

#### Gestión de Eventos
- Ver eventos "Pendientes"
- Aprobar o rechazar eventos
- Los eventos "Aceptados" se muestran a todos los usuarios
- Los eventos pasados automáticamente cambian a "Finalizado"

#### Gestión de Usuarios
- Ver lista completa de usuarios
- Activar/Desactivar cuentas
- Ver gráficos de registros

---

## 🔐 Panel de Administración

El panel de administración incluye:

### 📊 Dashboard (menuAdmin.php)
- Gráficos de líneas con datos históricos
- Conteo de usuarios activos
- Conteo de publicaciones totales
- Conteo de mensajes enviados

### 👥 Gestión de Usuarios (adminUsuarios.php)
- Tabla con todos los usuarios
- Gráfico de barras de usuarios por ciudad
- Gráfico de líneas de registros por fecha
- Acciones: Activar/Desactivar/Eliminar

### 🎉 Moderación de Eventos (adminEventos.php)
- Filtros por estado (Pendiente, Aceptado, Rechazado, Finalizado)
- Modal con detalles completos del evento
- Botones de aprobación/rechazo

### 💬 Monitoreo de Mensajes (adminMensajes.php)
- Tabla de todos los mensajes
- Información de emisor y receptor
- Fecha y estado de lectura

### 📋 Gestión de Consultas (adminConsultas.php)
- Ver consultas de soporte de usuarios
- Marcar como resueltas


## 📧 Contacto

**Desarrollador/Programador**: Alejandro Aquino  
**Rol**: Desarrollo profesional del sistema (implementación técnica)  
**Tipo de proyecto**: Sistema web desarrollado como servicio profesional para tesis académica  
**Email**: [alexs199.ale@gmail.com]  
**GitHub**: [@Alesito199](https://github.com/Alesito199)

**Nota**: Este proyecto fue desarrollado profesionalmente como parte de mis servicios de programación. La tesis académica y su defensa corresponden a la estudiante de Ingeniería Informática que contrató el desarrollo.

---

## 🙏 Agradecimientos

- A la estudiante que contrató mis servicios profesionales y confió en mi trabajo
- A los usuarios beta que probaron la plataforma
- A la comunidad de desarrolladores PHP
- A todos los que contribuyeron al éxito técnico de este proyecto

---

## 📌 Notas Importantes


1. **Futuras Mejoras Recomendadas**:
   - Sistema de notificaciones push
   - Recuperación de contraseña por email
   - Verificación de email al registrarse
   - Sistema de comentarios en publicaciones
   - Reacciones (like, love, etc.)
   - Búsqueda avanzada con filtros
   - Exportar datos del usuario (GDPR)

---

## 🔄 Changelog

### Versión 1.0.0 (2024)
- ✅ Sistema de autenticación completo
- ✅ CRUD de publicaciones
- ✅ Mensajería directa en tiempo real
- ✅ Gestión de eventos con aprobación
- ✅ Panel de administración con estadísticas
- ✅ Perfiles de usuario con experiencia laboral
- ✅ Búsqueda y filtrado de usuarios

---

