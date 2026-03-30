<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ServiciosSeeder extends Seeder
{

    public function run(): void
    {
        $now = Carbon::now();

        $slaIds = DB::table('slas')->pluck('id', 'codigo');

        $catalogo = [

            // ────────────────────────────────────────────────────────────────
            // 1. ADQUISICIONES
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Adquisiciones',
                    'descripcion' => 'Servicios relacionados con con compras de equipos tecnológicos.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Especificaciones Técnicas',
                        'descripcion' => '',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Elaboración de Especificaciones Técnicas',
                                'descripcion' => '',
                            ],
                            [
                                'nombre' => 'Verificación de especificaciones Técnicas',
                                'descripcion' => '',
                            ],
                        ],
                    ],

                ],
            ],
            [
                'categoria' => [
                    'nombre' => 'Estación de Trabajo',
                    'descripcion' => 'Servicios relacionados con computadoras, impresoras, escaneres y otros dispositivos conectados a computadoras.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Gestión de Computadoras',
                        'descripcion' => 'Matenimiento y reparación de computadoras.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Diagnóstico y reparación de hardware',
                                'descripcion' => 'Si tu equipo presenta fallas físicas como pantalla dañada, teclado que no responde, lentitud extrema u otros problemas de hardware, solicita aquí una revisión técnica para su reparación.',
                            ],
                            [
                                'nombre' => 'Reparación  de hardware',
                                'descripcion' => 'Cuando tu equipo ha sido evaluado por TI y se determinó que no tiene reparación viable, solicita su sustitución por un equipo en condiciones adecuadas para continuar con tus labores.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Gestión de Software',
                        'descripcion' => '¿Necesitas un programa específico para tu trabajo o tienes uno que ya no utilizas? Este servicio cubre la instalación, actualización y desinstalación de aplicaciones en tu equipo institucional, siempre dentro del catálogo de software autorizado por la municipalidad.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Instalación de software autorizado',
                                'descripcion' => 'Solicita la instalación de un programa o aplicación que necesitas para realizar tus funciones. El software debe estar dentro del listado de aplicaciones aprobadas por la institución.',
                            ],
                            [
                                'nombre' => 'Actualización de software existente',
                                'descripcion' => 'Si tienes una aplicación desactualizada que está generando errores o incompatibilidades, solicita aquí su actualización a la versión más reciente disponible.',
                            ],
                            [
                                'nombre' => 'Desinstalación de aplicación',
                                'descripcion' => 'Solicita la eliminación de un programa que ya no utilizas o que fue instalado por error, para liberar espacio y mantener tu equipo en óptimas condiciones.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Gestión de Periféricos',
                        'descripcion' => '¿Tu impresora no imprime, el escáner no es reconocido por tu equipo o necesitas configurar un dispositivo nuevo? Este servicio cubre la instalación y configuración de impresoras, escáneres y equipos multifunción en tu estación de trabajo.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Instalación de impresora o escáner',
                                'descripcion' => 'Solicita la configuración de una impresora o escáner nuevo en tu equipo. Indica el modelo del dispositivo y si es de uso personal o compartido con otras personas del área.',
                            ],
                            [
                                'nombre' => 'Reconfiguración de periférico existente',
                                'descripcion' => 'Si tu impresora o escáner dejó de funcionar correctamente tras una actualización o cambio de equipo, solicita su reconfiguración para restablecer el funcionamiento normal.',
                            ],
                            [
                                'nombre' => 'Reporte de falla en periférico',
                                'descripcion' => 'Reporta aquí cualquier falla en tu impresora, escáner u otro dispositivo periférico. Un técnico de TI evaluará el problema y coordinará la reparación o reemplazo según corresponda.',
                            ],
                        ],
                    ]


                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 2. COMUNICACIÓN
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Comunicación',
                    'descripcion' => 'Engloba los servicios que te permiten comunicarte con tus compañeros y otras entidades: correo institucional, videollamadas, teléfono interno y la intranet municipal. Si tienes problemas para enviar correos, unirte a una reunión virtual o comunicarte por teléfono IP, aquí encuentras el servicio que necesitas.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Correo Institucional',
                        'descripcion' => 'Tu correo institucional es tu canal oficial de comunicación dentro y fuera de la municipalidad. Desde aquí puedes solicitar la creación de tu cuenta, recuperar acceso si olvidaste tu contraseña, o gestionar la baja de una cuenta cuando un colaborador deja la institución.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Creación de cuenta de correo institucional',
                                'descripcion' => 'Solicita la creación de una cuenta de correo electrónico con dominio institucional. Indica el nombre completo del colaborador, área a la que pertenece y el tipo de acceso que requiere.',
                            ],
                            [
                                'nombre' => 'Baja o desactivación de cuenta de correo',
                                'descripcion' => 'Cuando un colaborador se retira de la institución o cambia de función, solicita aquí la desactivación de su cuenta de correo para proteger la información institucional.',
                            ],
                            [
                                'nombre' => 'Recuperación de correos eliminados',
                                'descripcion' => 'Si eliminaste por error correos importantes, solicita su recuperación. Ten en cuenta que la restauración depende del tiempo transcurrido desde la eliminación y las políticas de respaldo vigentes.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Videoconferencia',
                        'descripcion' => '¿Necesitas participar en reuniones virtuales con otras áreas, entidades del Estado o proveedores? Este servicio te proporciona acceso a las plataformas de videoconferencia institucionales y soporte técnico para que tus reuniones se realicen sin inconvenientes.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Habilitación de acceso a plataforma de videoconferencia',
                                'descripcion' => 'Solicita el acceso o la licencia necesaria para usar la plataforma de videoconferencia institucional (Zoom, Teams u otra). Indica si necesitas solo participar en reuniones o también organizarlas.',
                            ],
                            [
                                'nombre' => 'Soporte técnico durante sesión',
                                'descripcion' => 'Si tienes problemas de audio, video o conexión durante una reunión virtual importante, solicita asistencia técnica inmediata. Indica la fecha y hora de la reunión para coordinar el apoyo oportuno.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Telefonía IP (VoIP)',
                        'descripcion' => 'El sistema de telefonía IP te permite comunicarte con cualquier área de la municipalidad desde tu equipo o teléfono IP sin costo adicional. Solicita aquí la asignación de tu número interno o reporta problemas con tu línea.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Asignación de interno telefónico',
                                'descripcion' => 'Solicita la asignación de un número de interno para comunicación telefónica dentro de la municipalidad. Indica tu nombre, área y si requieres teléfono físico o softphone en tu equipo.',
                            ],
                            [
                                'nombre' => 'Configuración de dispositivo VoIP',
                                'descripcion' => 'Si tienes un teléfono IP que no está configurado, dejó de funcionar o fue reasignado a otro usuario, solicita aquí su configuración para restablecer las comunicaciones.',
                            ],
                            [
                                'nombre' => 'Reporte de falla en línea IP',
                                'descripcion' => 'Reporta aquí si tu teléfono IP no tiene tono, no puede realizar o recibir llamadas, o presenta cualquier otro problema que interrumpa la comunicación interna de tu área.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Intranet Institucional',
                        'descripcion' => 'La intranet es el portal interno de la municipalidad donde encontrarás noticias institucionales, documentos, formularios y herramientas para el trabajo diario. Solicita acceso o reporta problemas de navegación desde aquí.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Habilitación de acceso a la intranet',
                                'descripcion' => 'Solicita el acceso al portal de intranet institucional para consultar información, descargar documentos y usar las herramientas disponibles para el personal de la municipalidad.',
                            ],
                            [
                                'nombre' => 'Reporte de falla o contenido desactualizado',
                                'descripcion' => 'Si la intranet no carga correctamente, encuentras errores al navegar o detectas información desactualizada que debe corregirse, repórtalo aquí para que el equipo de TI lo atienda.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 3. CONECTIVIDAD
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Conectividad',
                    'descripcion' => 'Si no tienes internet, no puedes conectarte a la red de la municipalidad o necesitas acceso remoto seguro desde fuera de la institución, este es el lugar indicado. Gestionamos todos los servicios de conexión para que puedas trabajar sin interrupciones.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Acceso a Internet',
                        'descripcion' => 'El acceso a internet es indispensable para el trabajo diario. Si no tienes conexión, la velocidad es muy lenta o la señal es intermitente, solicita aquí la habilitación o el restablecimiento de tu servicio de internet.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Habilitación de acceso a internet',
                                'descripcion' => 'Solicita la activación del acceso a internet en tu equipo o estación de trabajo. Generalmente se requiere para equipos nuevos o reubicados en una nueva área.',
                            ],
                            [
                                'nombre' => 'Restablecimiento de conexión a internet',
                                'descripcion' => 'Si tu conexión a internet dejó de funcionar de forma repentina, solicita aquí el restablecimiento del servicio. Indica desde cuándo presentas el problema y si afecta solo a tu equipo o a toda el área.',
                            ],
                            [
                                'nombre' => 'Reporte de lentitud o intermitencia',
                                'descripcion' => 'Si tu conexión a internet es muy lenta o se corta con frecuencia afectando tu productividad, repórtalo aquí para que TI evalúe y corrija el problema en tu punto de red.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Red Institucional (LAN/WiFi)',
                        'descripcion' => 'La red institucional conecta todos los equipos y sistemas de la municipalidad. Si no puedes acceder a recursos compartidos, carpetas de red o simplemente tu equipo no reconoce la red, solicita aquí el apoyo técnico necesario.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Conexión a red LAN',
                                'descripcion' => 'Solicita la configuración de tu equipo para conectarse a la red cableada institucional. Necesario para equipos nuevos, reubicados o que perdieron la conexión a la red local.',
                            ],
                            [
                                'nombre' => 'Acceso a red WiFi institucional',
                                'descripcion' => 'Solicita las credenciales o la configuración necesaria para conectar tu equipo o dispositivo a la red inalámbrica institucional. Indica el dispositivo y el área donde lo utilizarás.',
                            ],
                            [
                                'nombre' => 'Reporte de falla de conectividad',
                                'descripcion' => 'Si tu equipo no se conecta a la red, no accede a carpetas compartidas o pierde la conexión frecuentemente, repórtalo aquí. Indica si el problema es solo en tu equipo o afecta a varios compañeros del área.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Acceso Remoto (VPN)',
                        'descripcion' => '¿Necesitas trabajar desde casa u otro lugar fuera de la municipalidad y acceder a los sistemas internos de forma segura? La VPN es la solución. Solicita aquí su configuración y resuelve cualquier problema de conexión remota.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Configuración de cliente VPN',
                                'descripcion' => 'Solicita la instalación y configuración de la VPN institucional en tu equipo para poder acceder de forma segura a los sistemas y recursos de la municipalidad desde fuera de las instalaciones.',
                            ],
                            [
                                'nombre' => 'Reporte de falla en conexión VPN',
                                'descripcion' => 'Si tu VPN no conecta, se desconecta sola o no te permite acceder a los recursos internos, repórtalo aquí. Indica el mensaje de error que aparece en pantalla para agilizar el diagnóstico.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 4. ACCESO E IDENTIDAD
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Acceso e Identidad',
                    'descripcion' => 'Gestiona todo lo relacionado con tus credenciales de acceso y permisos en los sistemas de la municipalidad. Aquí puedes solicitar la creación de tu cuenta, restablecer tu contraseña, ampliar permisos o dar de baja cuentas de colaboradores que ya no laboran en la institución.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Gestión de Cuentas de Usuario',
                        'descripcion' => 'Tu cuenta de usuario es la llave de acceso a todos los sistemas institucionales. Desde aquí puedes gestionar la creación de cuentas para personal nuevo, modificar datos o permisos cuando cambian tus funciones, y solicitar el restablecimiento de contraseñas olvidadas.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Creación de cuenta para personal nuevo',
                                'descripcion' => 'Solicita la creación de una cuenta de acceso a los sistemas institucionales para un colaborador que se incorpora a la municipalidad. Adjunta el documento de designación o contrato para agilizar el proceso.',
                            ],
                            [
                                'nombre' => 'Modificación de datos o permisos de cuenta',
                                'descripcion' => 'Si cambiaste de área, cargo o funciones y necesitas ajustar los permisos de tu cuenta para acceder a nuevos sistemas o dejar de acceder a otros, solicita aquí la actualización correspondiente.',
                            ],
                            [
                                'nombre' => 'Baja o desactivación de cuenta',
                                'descripcion' => 'Solicita la desactivación de la cuenta de un colaborador que se retiró de la institución, fue trasladado o cuyo contrato finalizó. Es importante gestionar esto oportunamente para proteger la seguridad de la información.',
                            ],
                            [
                                'nombre' => 'Restablecimiento de contraseña',
                                'descripcion' => 'Si olvidaste tu contraseña o tu cuenta fue bloqueada por intentos fallidos de acceso, solicita aquí el restablecimiento. Un técnico de TI te proporcionará una contraseña temporal de forma segura.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Control de Acceso a Sistemas',
                        'descripcion' => 'Cada sistema institucional tiene módulos y funciones a los que solo pueden acceder personas autorizadas según su cargo. Si necesitas acceder a un sistema nuevo, ampliar tus permisos para realizar tus funciones o revocar accesos que ya no corresponden, gestiona tu solicitud aquí.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Solicitud de acceso a sistema o módulo',
                                'descripcion' => 'Solicita permiso de acceso a un sistema o módulo específico que necesitas para cumplir con tus funciones. Tu jefe inmediato deberá aprobar la solicitud antes de que TI proceda con la habilitación.',
                            ],
                            [
                                'nombre' => 'Ampliación de permisos en sistema existente',
                                'descripcion' => 'Si ya tienes acceso a un sistema pero necesitas permisos adicionales para realizar nuevas tareas (como registrar, aprobar o generar reportes), solicita aquí la ampliación de tu perfil de acceso.',
                            ],
                            [
                                'nombre' => 'Revocación de acceso a sistema',
                                'descripcion' => 'Solicita la eliminación de los permisos de acceso de un colaborador a uno o más sistemas, ya sea por cambio de funciones, traslado de área o salida de la institución.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 5. ALMACENAMIENTO Y ARCHIVOS
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Almacenamiento y Archivos',
                    'descripcion' => 'Administra el espacio de almacenamiento de tu área en los servidores de la municipalidad. Aquí puedes solicitar carpetas compartidas para trabajo colaborativo, ampliar tu cuota de almacenamiento y recuperar archivos que se eliminaron por error.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Almacenamiento Compartido',
                        'descripcion' => 'Las carpetas compartidas en la red permiten que todo el equipo acceda, edite y comparta documentos de forma organizada y segura. Solicita la creación de carpetas para tu área, ajusta los permisos de acceso o amplía el espacio disponible cuando lo necesites.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Asignación de carpeta compartida',
                                'descripcion' => 'Solicita la creación de una carpeta en el servidor de archivos para uso compartido entre los miembros de tu área. Indica quiénes deben tener acceso y qué tipo de permisos requieren (solo lectura o lectura y escritura).',
                            ],
                            [
                                'nombre' => 'Ampliación de cuota de almacenamiento',
                                'descripcion' => 'Si el espacio asignado en el servidor está por agotarse y necesitas más capacidad para almacenar los archivos de tu área, solicita aquí la ampliación. Indica el tamaño adicional aproximado que requieres.',
                            ],
                            [
                                'nombre' => 'Gestión de permisos en carpeta compartida',
                                'descripcion' => 'Solicita agregar o quitar usuarios de una carpeta compartida, o cambiar el nivel de acceso de los miembros actuales. Útil cuando hay cambios en el equipo de trabajo.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Respaldo y Recuperación',
                        'descripcion' => 'Los archivos institucionales se respaldan periódicamente para prevenir la pérdida de información. Si eliminaste por error un documento importante o una carpeta, o si un archivo se dañó, solicita su recuperación desde las copias de seguridad disponibles.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Recuperación de archivo o carpeta eliminada',
                                'descripcion' => 'Si eliminaste por error un archivo o carpeta del servidor, solicita su recuperación lo antes posible. La posibilidad de restauración depende del tiempo transcurrido y del ciclo de respaldo. Indica la ruta exacta y la fecha aproximada de eliminación.',
                            ],
                            [
                                'nombre' => 'Restauración desde punto de respaldo específico',
                                'descripcion' => 'Si necesitas recuperar una versión anterior de un archivo o restaurar el estado de una carpeta a una fecha específica, solicita aquí la restauración desde el punto de respaldo que corresponda.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 6. APLICACIONES INSTITUCIONALES
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Aplicaciones Institucionales',
                    'descripcion' => 'Aquí gestionas el acceso a los sistemas propios de la municipalidad y a los aplicativos del Ministerio de Economía y Finanzas que usas en tu trabajo diario, como el SIGA, SIAF, sistema de trámite documentario, planillas y recaudación tributaria.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Sistema SIGA',
                        'descripcion' => 'El SIGA (Sistema Integrado de Gestión Administrativa) del MEF es la herramienta oficial para la gestión de compras, almacén, patrimonio y otros procesos administrativos. Solicita aquí el acceso, la asignación de perfil o reporta cualquier problema técnico con el sistema.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Creación de usuario en SIGA',
                                'descripcion' => 'Solicita la creación de tu usuario en el SIGA para poder operar en los módulos que corresponden a tus funciones. Adjunta la autorización de tu jefe inmediato indicando los módulos a los que debes tener acceso.',
                            ],
                            [
                                'nombre' => 'Asignación de perfil o módulo en SIGA',
                                'descripcion' => 'Si ya tienes usuario en el SIGA pero necesitas acceder a un módulo adicional (logística, patrimonio, tesorería, etc.) o te asignaron nuevas funciones, solicita aquí la actualización de tu perfil.',
                            ],
                            [
                                'nombre' => 'Reporte de error o falla en SIGA',
                                'descripcion' => 'Reporta aquí cualquier error, mensaje inesperado o funcionamiento incorrecto que encuentres al usar el SIGA. Describe detalladamente el problema e indica en qué módulo ocurre para agilizar su atención.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Sistema SIAF',
                        'descripcion' => 'El SIAF (Sistema Integrado de Administración Financiera) del MEF es el sistema oficial para el registro y control de las operaciones presupuestales, financieras y de tesorería de la municipalidad. Gestiona aquí tu acceso y reporta cualquier incidencia.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Creación de usuario en SIAF',
                                'descripcion' => 'Solicita la creación de tu usuario en el SIAF para operar en los módulos presupuestales o financieros según tus funciones. Requiere autorización previa del área de finanzas o contabilidad.',
                            ],
                            [
                                'nombre' => 'Asignación de perfil o módulo en SIAF',
                                'descripcion' => 'Solicita el acceso a un módulo adicional del SIAF o la actualización de tu perfil cuando tus responsabilidades dentro del área de administración financiera cambian.',
                            ],
                            [
                                'nombre' => 'Reporte de error o falla en SIAF',
                                'descripcion' => 'Si encuentras errores al registrar operaciones, generar reportes o cualquier otro proceso en el SIAF, repórtalo aquí con el mayor detalle posible para una atención oportuna.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Trámite Documentario',
                        'descripcion' => 'El sistema de trámite documentario permite registrar, derivar y hacer seguimiento de los documentos oficiales que circulan en la municipalidad. Solicita aquí tu acceso al sistema o reporta cualquier inconveniente que afecte la gestión de documentos.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Creación o habilitación de cuenta',
                                'descripcion' => 'Solicita la creación o reactivación de tu cuenta en el sistema de trámite documentario para poder recibir, derivar y hacer seguimiento a los expedientes y documentos oficiales de la municipalidad.',
                            ],
                            [
                                'nombre' => 'Capacitación básica en el sistema',
                                'descripcion' => 'Si eres nuevo en el sistema de trámite documentario o necesitas reforzar su uso, solicita una sesión de capacitación. Un especialista de TI te guiará en el registro y seguimiento de documentos.',
                            ],
                            [
                                'nombre' => 'Reporte de falla o error',
                                'descripcion' => 'Si el sistema no carga, presenta errores al registrar un documento o no permite derivar expedientes, repórtalo aquí indicando el tipo de operación que intentabas realizar.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Planillas y RRHH',
                        'descripcion' => 'El sistema de planillas y recursos humanos gestiona la información del personal de la municipalidad: contratos, asistencia, vacaciones, boletas de pago y más. Solicita aquí el acceso según tu rol en el área de recursos humanos o administración.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Creación o habilitación de cuenta',
                                'descripcion' => 'Solicita el acceso al sistema de planillas y RRHH para realizar las funciones que corresponden a tu cargo, como registro de personal, control de asistencia o generación de boletas de pago.',
                            ],
                            [
                                'nombre' => 'Asignación de perfil según rol',
                                'descripcion' => 'Solicita la configuración de tu perfil en el sistema de RRHH de acuerdo con tus funciones específicas: operador, supervisor, consulta u otro. Cada perfil tiene acceso a diferentes módulos del sistema.',
                            ],
                            [
                                'nombre' => 'Reporte de falla o error',
                                'descripcion' => 'Reporta aquí cualquier error que encuentres en el sistema de planillas o RRHH, como problemas al generar boletas, registrar asistencia o consultar información del personal.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Recaudación Tributaria',
                        'descripcion' => 'El sistema de recaudación tributaria es la herramienta con la que el área de rentas gestiona los tributos municipales: predial, arbitrios, licencias y otros. Solicita aquí el acceso o reporta cualquier problema que afecte la atención a los contribuyentes.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Creación o habilitación de cuenta',
                                'descripcion' => 'Solicita el acceso al sistema de recaudación tributaria para operar en las funciones de tu cargo dentro del área de rentas o administración tributaria de la municipalidad.',
                            ],
                            [
                                'nombre' => 'Asignación de perfil según rol',
                                'descripcion' => 'Solicita la asignación del perfil adecuado en el sistema tributario según tus responsabilidades: cajero, fiscalizador, supervisor u otro. Esto define las operaciones que podrás realizar.',
                            ],
                            [
                                'nombre' => 'Reporte de falla o error',
                                'descripcion' => 'Si el sistema tributario presenta errores al registrar pagos, consultar deudas o emitir recibos, repórtalo aquí de inmediato ya que estas fallas pueden afectar directamente la atención a los contribuyentes.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 7. DIGITALIZACIÓN Y DOCUMENTO ELECTRÓNICO
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Digitalización y Doc. Electrónico',
                    'descripcion' => 'Apoya la transformación digital de los procesos documentales de la municipalidad. Aquí puedes solicitar la digitalización de documentos físicos, gestionar tu firma digital para firmar documentos de forma electrónica y obtener asistencia en el manejo del expediente electrónico.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Digitalización de Documentos',
                        'descripcion' => '¿Tienes documentos físicos que necesitas convertir a formato digital para archivarlos o compartirlos? Este servicio cubre el escaneo y conversión de documentos en papel a archivos digitales con los estándares de calidad requeridos por la institución.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Digitalización de documento físico',
                                'descripcion' => 'Solicita el escaneo y conversión a formato digital de documentos físicos como resoluciones, contratos, informes u otro tipo de documentación. Indica la cantidad aproximada de páginas y el nivel de urgencia.',
                            ],
                            [
                                'nombre' => 'Conversión de formato de archivo digital',
                                'descripcion' => 'Si tienes un archivo en un formato que no es compatible con los sistemas institucionales (por ejemplo, necesitas pasar de Word a PDF firmado o de imagen a PDF), solicita aquí la conversión.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Firma Digital Institucional',
                        'descripcion' => 'La firma digital tiene la misma validez legal que la firma manuscrita y te permite firmar documentos electrónicos de forma segura y oficial. Si estás autorizado para firmar documentos en nombre de la municipalidad, solicita aquí tu certificado de firma digital.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Emisión de certificado de firma digital',
                                'descripcion' => 'Solicita la emisión de tu certificado de firma digital institucional. Este proceso requiere la validación de tu identidad y la autorización de tu jefe. Indica el cargo que ejerces y el tipo de documentos que necesitas firmar.',
                            ],
                            [
                                'nombre' => 'Renovación de certificado vencido',
                                'descripcion' => 'Si tu certificado de firma digital está próximo a vencer o ya expiró y no puedes firmar documentos, solicita aquí su renovación con anticipación para evitar interrupciones en tus labores.',
                            ],
                            [
                                'nombre' => 'Soporte en uso de firma digital',
                                'descripcion' => 'Si tienes dificultades para usar tu firma digital, no sabes cómo aplicarla en un documento o recibes errores al intentar firmar, solicita aquí la asistencia de un especialista de TI.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Expediente Electrónico',
                        'descripcion' => 'El expediente electrónico reemplaza al expediente físico y permite gestionar, tramitar y hacer seguimiento de los procesos documentales de forma digital. Si necesitas orientación para crear o dar seguimiento a un expediente, este servicio es para ti.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Asistencia en generación de expediente',
                                'descripcion' => 'Si no sabes cómo crear un expediente electrónico o tienes dudas sobre cómo adjuntar documentos y completar la información requerida, solicita aquí la asistencia de un especialista que te guiará paso a paso.',
                            ],
                            [
                                'nombre' => 'Seguimiento de expediente electrónico',
                                'descripcion' => 'Si un expediente electrónico lleva tiempo sin movimiento, fue derivado incorrectamente o no puedes ubicar su estado actual, solicita aquí el apoyo de TI para rastrearlo y continuar su trámite.',
                            ],
                            [
                                'nombre' => 'Reporte de falla en plataforma',
                                'descripcion' => 'Si la plataforma de expediente electrónico no carga, presenta errores al adjuntar archivos o no permite avanzar con el trámite, repórtalo aquí indicando el número de expediente afectado si lo tienes disponible.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 8. CAPACITACIÓN TI
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Capacitación TI',
                    'descripcion' => 'Accede a los servicios de formación tecnológica que ofrece el área de TI de la municipalidad. Ya sea que necesites mejorar tu manejo de herramientas de oficina, aprender a usar un sistema institucional o recibir la orientación tecnológica de bienvenida, aquí encontrarás el apoyo que necesitas.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Capacitación en Ofimática',
                        'descripcion' => '¿Quieres mejorar tu manejo de Word, Excel, PowerPoint u otras herramientas de oficina? El área de TI ofrece talleres y capacitaciones para que puedas aprovechar al máximo estas herramientas en tu trabajo diario.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Inscripción a taller de ofimática',
                                'descripcion' => 'Inscríbete a uno de los talleres grupales de ofimática organizados por el área de TI. Indica el programa en el que deseas capacitarte (Excel, Word, PowerPoint, etc.) y tu nivel de conocimiento actual.',
                            ],
                            [
                                'nombre' => 'Solicitud de capacitación personalizada',
                                'descripcion' => 'Si necesitas capacitación en un tema específico de ofimática que no está cubierto en los talleres regulares, o prefieres una sesión individual adaptada a tus necesidades, solicítala aquí.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Capacitación en Aplicativos',
                        'descripcion' => 'Los sistemas institucionales como el SIGA, SIAF, trámite documentario y otros pueden ser complejos. El área de TI ofrece capacitaciones para que puedas operarlos con seguridad y eficiencia en el ejercicio de tus funciones.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Inscripción a capacitación de sistema específico',
                                'descripcion' => 'Inscríbete a una capacitación sobre el uso de un sistema institucional específico. Indica el sistema en el que necesitas formación (SIGA, SIAF, trámite documentario, etc.) y los módulos que utilizarás.',
                            ],
                            [
                                'nombre' => 'Solicitud de manual o guía de usuario',
                                'descripcion' => 'Solicita el manual o guía de uso de un sistema o aplicativo institucional. Recibirás documentación actualizada con los pasos para realizar las operaciones más frecuentes de tu cargo.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Inducción TI Personal Nuevo',
                        'descripcion' => 'Si acabas de ingresar a la municipalidad, esta sesión de inducción te presentará todos los recursos tecnológicos disponibles, las políticas de uso que debes conocer y los sistemas a los que tendrás acceso según tu cargo.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Programación de sesión de inducción TI',
                                'descripcion' => 'Solicita la programación de tu sesión de inducción tecnológica. Esta sesión cubre los sistemas que usarás, las políticas de seguridad informática, el correo institucional y las herramientas disponibles para el personal.',
                            ],
                            [
                                'nombre' => 'Entrega de guía de bienvenida tecnológica',
                                'descripcion' => 'Solicita la guía de bienvenida tecnológica, un documento que resume los sistemas, herramientas y buenas prácticas que todo colaborador de la municipalidad debe conocer al iniciar sus funciones.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 9. SEGURIDAD DE LA INFORMACIÓN
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Seguridad de la Información',
                    'descripcion' => 'La seguridad de la información es responsabilidad de todos. Aquí puedes reportar amenazas informáticas, solicitar la instalación de antivirus, gestionar accesos físicos a áreas restringidas de TI y consultar sobre el cumplimiento de las normativas de seguridad vigentes como la Ley 29733 y las directivas de la PCM.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Gestión de Antivirus y Malware',
                        'descripcion' => 'Mantener tu equipo protegido contra virus y software malicioso es fundamental para la seguridad de la información municipal. Si tu equipo no tiene antivirus actualizado, sospechas que fue infectado o detectas un comportamiento extraño, actúa de inmediato usando este servicio.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Instalación o actualización de antivirus',
                                'descripcion' => 'Solicita la instalación del antivirus institucional en tu equipo o la actualización de la versión que tienes instalada. Un equipo desprotegido puede comprometer la seguridad de toda la red de la municipalidad.',
                            ],
                            [
                                'nombre' => 'Análisis y limpieza de equipo infectado',
                                'descripcion' => 'Si tu equipo muestra señales de infección (ventanas emergentes, lentitud inusual, programas que se abren solos, etc.), solicita de inmediato un análisis completo y limpieza por parte del equipo de TI.',
                            ],
                            [
                                'nombre' => 'Reporte de comportamiento sospechoso en equipo',
                                'descripcion' => 'Si notas algo inusual en tu equipo (accesos no autorizados, archivos modificados, correos enviados sin tu conocimiento, etc.), repórtalo aquí. Es mejor reportar y estar seguros que ignorarlo y arriesgar la información institucional.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Control de Acceso Físico a TI',
                        'descripcion' => 'La sala de servidores y los cuartos de equipos son áreas restringidas que requieren autorización especial para ingresar. Si necesitas acceder a estas instalaciones por razones de trabajo, gestiona tu autorización aquí con la debida anticipación.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Solicitud de acceso a sala de servidores',
                                'descripcion' => 'Solicita autorización para ingresar a la sala de servidores u otras áreas restringidas de infraestructura TI. Indica el motivo, la fecha y hora de ingreso y el tiempo estimado de permanencia.',
                            ],
                            [
                                'nombre' => 'Registro de visitante a infraestructura TI',
                                'descripcion' => 'Solicita el registro formal de un visitante externo (proveedor, técnico especializado, auditor) que necesita ingresar a las instalaciones de infraestructura tecnológica de la municipalidad.',
                            ],
                            [
                                'nombre' => 'Reporte de acceso no autorizado',
                                'descripcion' => 'Si detectas o sospechas que alguien accedió a la sala de servidores o áreas de TI sin la autorización correspondiente, repórtalo de inmediato. La seguridad física de la infraestructura es crítica para la continuidad de los servicios.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Gestión de Incidentes de Seguridad',
                        'descripcion' => 'Un incidente de seguridad informática puede tener consecuencias graves para la institución. Si experimentas un ataque, una filtración de datos, accesos indebidos a sistemas o cualquier evento que comprometa la seguridad de la información, repórtalo de inmediato.',
                        'sla'         => 'CRITICA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Reporte de incidente de seguridad informática',
                                'descripcion' => 'Reporta de forma inmediata cualquier evento que comprometa la seguridad de la información: accesos no autorizados, ransomware, phishing, fuga de datos u otros. Cuanta más información proporciones, más rápido podremos contener el incidente.',
                            ],
                            [
                                'nombre' => 'Solicitud de análisis forense básico',
                                'descripcion' => 'Si necesitas determinar cómo ocurrió un incidente de seguridad, qué información fue comprometida o quién realizó determinadas acciones en un sistema, solicita aquí un análisis forense básico del equipo o sistema afectado.',
                            ],
                            [
                                'nombre' => 'Bloqueo urgente de cuenta comprometida',
                                'descripcion' => 'Si sospechas o confirmas que tu cuenta o la de un colaborador fue comprometida (contraseña robada, acceso desde lugar desconocido, etc.), solicita el bloqueo inmediato para evitar daños mayores a la información institucional.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Políticas y Cumplimiento TI',
                        'descripcion' => 'La municipalidad debe cumplir con diversas normativas de seguridad de la información, entre ellas la Ley 29733 de protección de datos personales y las directivas de la PCM. Este servicio te ayuda a entender las políticas de uso de TI y a verificar el cumplimiento en tu área.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Consulta sobre política de uso de TI',
                                'descripcion' => 'Si tienes dudas sobre qué está permitido y qué no en el uso de los recursos tecnológicos de la municipalidad (internet, correo, dispositivos, etc.), realiza tu consulta aquí y recibirás orientación clara y oportuna.',
                            ],
                            [
                                'nombre' => 'Solicitud de capacitación en seguridad informática',
                                'descripcion' => 'Solicita una capacitación para ti o tu área sobre buenas prácticas de seguridad informática: cómo identificar correos de phishing, crear contraseñas seguras, proteger información sensible y cumplir con las normativas vigentes.',
                            ],
                            [
                                'nombre' => 'Revisión de cumplimiento en área específica',
                                'descripcion' => 'Solicita una revisión del estado de cumplimiento de las políticas de seguridad de la información en tu área. Recibirás un informe con observaciones y recomendaciones para mejorar la seguridad en tu entorno de trabajo.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 10. INFRAESTRUCTURA Y SERVIDORES
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Infraestructura y Servidores',
                    'descripcion' => 'Agrupa los servicios de administración y soporte de la infraestructura tecnológica central de la municipalidad: servidores, sistema de respaldo institucional y monitoreo de disponibilidad. Principalmente orientado al personal de TI y responsables de sistemas.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Gestión de Servidores',
                        'descripcion' => 'Los servidores son el corazón de los servicios tecnológicos de la municipalidad. Desde aquí puedes solicitar espacio en servidor para nuevas aplicaciones, programar mantenimientos y reportar caídas o fallas que afecten la disponibilidad de los sistemas.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Solicitud de espacio en servidor',
                                'descripcion' => 'Solicita la asignación de espacio en los servidores institucionales para alojar una nueva aplicación, base de datos o repositorio. Indica el propósito, el espacio requerido y los requerimientos técnicos del servicio.',
                            ],
                            [
                                'nombre' => 'Reinicio o mantenimiento programado de servidor',
                                'descripcion' => 'Solicita el reinicio o el mantenimiento programado de un servidor específico. Esta solicitud debe coordinarse con anticipación para minimizar el impacto en los usuarios que dependen de los servicios alojados.',
                            ],
                            [
                                'nombre' => 'Reporte de falla o caída de servidor',
                                'descripcion' => 'Reporta de inmediato la caída o falla de un servidor que esté afectando la disponibilidad de uno o más servicios. Indica qué servicios están impactados y desde cuándo se presenta el problema.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Gestión de Respaldo Institucional',
                        'descripcion' => 'El sistema de respaldo protege la información crítica de la municipalidad ante pérdidas accidentales, fallas de hardware o incidentes de seguridad. Solicita aquí la programación de respaldos para sistemas específicos y la verificación de su integridad.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Programación de respaldo para sistema específico',
                                'descripcion' => 'Solicita la inclusión de un sistema, base de datos o servidor en el plan de respaldos institucional. Indica la frecuencia requerida, el tipo de respaldo (completo, incremental) y la criticidad de la información.',
                            ],
                            [
                                'nombre' => 'Verificación de integridad de respaldo',
                                'descripcion' => 'Solicita la verificación de que los respaldos de un sistema o base de datos específica se están ejecutando correctamente y que los archivos generados son válidos y restaurables en caso de necesitarlos.',
                            ],
                            [
                                'nombre' => 'Restauración de sistema desde respaldo',
                                'descripcion' => 'Solicita la restauración completa o parcial de un sistema desde su último respaldo disponible. Esta solicitud se activa generalmente ante fallas graves, corrupción de datos o incidentes de seguridad.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Monitoreo de Servicios TI',
                        'descripcion' => 'El monitoreo continuo permite detectar y resolver problemas antes de que afecten a los usuarios. Consulta el estado actual de los servicios, solicita reportes de disponibilidad y configura alertas para ser notificado ante cualquier incidencia.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Consulta de estado de servicio TI',
                                'descripcion' => 'Consulta el estado actual de disponibilidad de un servicio o sistema institucional específico. Útil para verificar si un sistema está operativo antes de escalar un incidente o cuando recibes reportes de usuarios con problemas de acceso.',
                            ],
                            [
                                'nombre' => 'Solicitud de reporte de disponibilidad',
                                'descripcion' => 'Solicita un reporte del histórico de disponibilidad de uno o más servicios TI en un período determinado. Útil para evaluaciones de desempeño, auditorías o justificación de mejoras de infraestructura.',
                            ],
                            [
                                'nombre' => 'Configuración de alerta de monitoreo',
                                'descripcion' => 'Solicita la configuración de una alerta automática para ser notificado cuando un servicio o servidor presente problemas de disponibilidad, alto consumo de recursos o cualquier condición anormal definida previamente.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 11. ATENCIÓN AL CIUDADANO - TI
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Atención al Ciudadano - TI',
                    'descripcion' => 'Agrupa los servicios tecnológicos que la municipalidad pone a disposición de los ciudadanos: portal web, pagos en línea, sistema de turnos y portal de transparencia. Si trabajas en las áreas responsables de estos canales digitales y necesitas actualizarlos o reportar fallas, este es el lugar indicado.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Portal Web Municipal',
                        'descripcion' => 'El portal web es la cara digital de la municipalidad ante los ciudadanos. Mantenerlo actualizado, funcional y con información correcta es esencial para la confianza en la institución. Solicita aquí publicaciones de contenido, correcciones o reporta problemas técnicos.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Publicación de contenido en portal web',
                                'descripcion' => 'Solicita la publicación de noticias, comunicados, ordenanzas, convocatorias u otro contenido oficial en el portal web de la municipalidad. Adjunta el texto, imágenes y cualquier archivo necesario para la publicación.',
                            ],
                            [
                                'nombre' => 'Reporte de error o caída del portal',
                                'descripcion' => 'Si el portal web no carga, muestra errores, tiene enlaces rotos o dejó de estar disponible, repórtalo de inmediato ya que esto afecta directamente la imagen institucional y la atención al ciudadano.',
                            ],
                            [
                                'nombre' => 'Actualización de información institucional',
                                'descripcion' => 'Solicita la actualización de información en el portal web como horarios de atención, directorio de funcionarios, servicios disponibles u otros datos que hayan cambiado y deban reflejarse en la web oficial.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Sistema de Pagos en Línea',
                        'descripcion' => 'El sistema de pagos en línea permite a los ciudadanos pagar sus tributos y servicios municipales desde cualquier lugar sin necesidad de acudir presencialmente. Su correcto funcionamiento es vital para la recaudación y la satisfacción del ciudadano.',
                        'sla'         => 'CRITICA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Reporte de falla en plataforma de pagos',
                                'descripcion' => 'Si los ciudadanos reportan que no pueden completar pagos en línea, o si detectas errores en el sistema de cobro, repórtalo aquí de inmediato para minimizar el impacto en la recaudación y la atención al contribuyente.',
                            ],
                            [
                                'nombre' => 'Solicitud de conciliación de pago electrónico',
                                'descripcion' => 'Si hay discrepancias entre los pagos registrados en el sistema en línea y los que aparecen en el sistema de recaudación, solicita aquí una conciliación para identificar y corregir las diferencias.',
                            ],
                            [
                                'nombre' => 'Configuración de nuevo tipo de pago',
                                'descripcion' => 'Solicita la habilitación de un nuevo concepto de pago en la plataforma en línea (un nuevo tributo, tasa o servicio municipal). Indica la denominación, el monto o la forma de calcularlo y la unidad orgánica responsable.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Sistema de Turnos y Citas',
                        'descripcion' => 'El sistema de turnos y citas permite a los ciudadanos programar su atención en las diferentes ventanillas de la municipalidad, reduciendo tiempos de espera y mejorando la experiencia del usuario.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Habilitación de nuevo servicio en sistema de turnos',
                                'descripcion' => 'Solicita la configuración de un nuevo servicio o ventanilla en el sistema de turnos. Indica el nombre del servicio, el área responsable, el horario de atención y la capacidad de atención diaria.',
                            ],
                            [
                                'nombre' => 'Reporte de falla en sistema de citas',
                                'descripcion' => 'Si el sistema de turnos no está disponible, no permite agendar citas o presenta errores al confirmar una reserva, repórtalo aquí indicando si afecta a todos los servicios o solo a alguno en particular.',
                            ],
                            [
                                'nombre' => 'Modificación de horarios o aforo',
                                'descripcion' => 'Solicita la actualización de los horarios de atención, la capacidad máxima de citas diarias u otros parámetros del sistema de turnos para un servicio o área específica de la municipalidad.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Transparencia y Datos Abiertos',
                        'descripcion' => 'La Ley 27806 obliga a las entidades públicas a publicar información sobre su gestión de forma accesible para todos los ciudadanos. Este servicio apoya la publicación y actualización del portal de transparencia y los conjuntos de datos abiertos de la municipalidad.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Publicación de información de transparencia',
                                'descripcion' => 'Solicita la publicación en el portal de transparencia de información como presupuestos, ejecución del gasto, contratos, planilla de remuneraciones u otros documentos exigidos por la normativa.',
                            ],
                            [
                                'nombre' => 'Actualización de declaraciones y presupuestos',
                                'descripcion' => 'Solicita la actualización de la información presupuestal, las declaraciones juradas de funcionarios u otros datos periódicos que deben mantenerse vigentes en el portal de transparencia.',
                            ],
                            [
                                'nombre' => 'Reporte de error en portal de transparencia',
                                'descripcion' => 'Si detectas información incorrecta, documentos inaccesibles o errores de navegación en el portal de transparencia, repórtalo aquí. Publicar información errónea puede generar observaciones de los órganos de control.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 12. GOBIERNO ELECTRÓNICO E INTEROPERABILIDAD
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Gobierno Electrónico e Interoperabilidad',
                    'descripcion' => 'La municipalidad está integrada con diversas plataformas del Estado peruano para brindar servicios más ágiles y seguros a los ciudadanos. Aquí gestionamos las conexiones con RENIEC para validar identidades, con la PIDE de la PCM para intercambiar datos con otras entidades, y con el portal GOB.PE.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Integración con RENIEC',
                        'descripcion' => 'La integración con RENIEC permite verificar de forma automática la identidad de los ciudadanos durante sus trámites en la municipalidad, reduciendo el fraude y agilizando la atención. Gestiona aquí el acceso al servicio y reporta cualquier falla en la consulta de datos.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Solicitud de acceso al servicio de validación RENIEC',
                                'descripcion' => 'Solicita la habilitación del servicio de validación de identidad con RENIEC para un nuevo sistema o trámite municipal que requiera verificar datos del DNI de los ciudadanos.',
                            ],
                            [
                                'nombre' => 'Reporte de falla en consulta de DNI',
                                'descripcion' => 'Si el sistema no puede consultar o validar datos del DNI a través del servicio de RENIEC, repórtalo aquí indicando el sistema afectado, el mensaje de error y el impacto en la atención al ciudadano.',
                            ],
                            [
                                'nombre' => 'Renovación de convenio de interoperabilidad',
                                'descripcion' => 'Solicita la gestión para la renovación del convenio de interoperabilidad con RENIEC antes de su vencimiento, para evitar interrupciones en los servicios que dependen de esta integración.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Integración con PIDE / PCM',
                        'descripcion' => 'La Plataforma de Interoperabilidad del Estado (PIDE) permite a la municipalidad intercambiar datos con otras entidades públicas sin pedir documentos físicos al ciudadano. Gestiona aquí nuevas integraciones y reporta fallas en los servicios existentes.',
                        'sla'         => 'ALTA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Solicitud de nueva integración con entidad del Estado',
                                'descripcion' => 'Solicita la integración con una nueva entidad del Estado a través de la PIDE para que un trámite municipal pueda consultar o compartir información sin requerir documentos físicos al ciudadano. Indica el trámite, la entidad y los datos a intercambiar.',
                            ],
                            [
                                'nombre' => 'Reporte de falla en servicio PIDE',
                                'descripcion' => 'Si un servicio de interoperabilidad a través de la PIDE deja de funcionar o retorna datos incorrectos, repórtalo aquí indicando el servicio afectado, el sistema que lo consume y el impacto en los trámites ciudadanos.',
                            ],
                            [
                                'nombre' => 'Actualización de credenciales de acceso PIDE',
                                'descripcion' => 'Solicita la renovación o actualización de las credenciales (certificados, tokens, usuarios) necesarias para mantener activas las integraciones de la municipalidad con la plataforma PIDE de la PCM.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Plataforma GOB.PE',
                        'descripcion' => 'GOB.PE es el portal único del Estado peruano donde los ciudadanos pueden encontrar información y servicios de todas las entidades públicas. Mantener actualizados los servicios y la información de la municipalidad en GOB.PE es una obligación institucional.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Actualización de servicios en GOB.PE',
                                'descripcion' => 'Solicita la actualización de la información de uno o más servicios municipales publicados en GOB.PE, como requisitos, costos, plazos, canales de atención u otros datos que hayan cambiado.',
                            ],
                            [
                                'nombre' => 'Reporte de inconsistencia de información',
                                'descripcion' => 'Si detectas que la información de la municipalidad en GOB.PE es incorrecta, está desactualizada o no coincide con lo que se ofrece realmente al ciudadano, repórtalo aquí para su corrección oportuna.',
                            ],
                            [
                                'nombre' => 'Solicitud de nueva ficha de servicio en GOB.PE',
                                'descripcion' => 'Solicita la creación de una nueva ficha informativa en GOB.PE para un servicio o trámite municipal que aún no está publicado en el portal único del Estado. Proporciona toda la información del servicio para agilizar el proceso.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 13. GESTIÓN DE CAMBIOS TI
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Gestión de Cambios TI',
                    'descripcion' => 'Todo cambio en los sistemas o infraestructura tecnológica de la municipalidad debe seguir un proceso formal para minimizar riesgos. Aquí puedes solicitar modificaciones a sistemas existentes o iniciar la evaluación de un nuevo proyecto tecnológico.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Cambios en Sistemas Existentes',
                        'descripcion' => '¿Necesitas una mejora, corrección o ajuste en un sistema que ya está en funcionamiento? Los cambios en producción requieren evaluación previa para evitar interrupciones. Presenta tu solicitud aquí y el equipo de TI la evaluará, planificará y ejecutará de forma controlada.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Solicitud de cambio estándar (bajo riesgo)',
                                'descripcion' => 'Solicita un cambio menor o de rutina en un sistema existente, como la actualización de un parámetro, la corrección de un mensaje o el ajuste de un reporte. Este tipo de cambio no requiere aprobación especial.',
                            ],
                            [
                                'nombre' => 'Solicitud de cambio mayor (requiere aprobación)',
                                'descripcion' => 'Solicita un cambio significativo en un sistema en producción que puede impactar en múltiples usuarios o procesos. Este tipo de cambio requiere la aprobación del responsable del área y del jefe de TI antes de ser implementado.',
                            ],
                            [
                                'nombre' => 'Consulta sobre estado de cambio solicitado',
                                'descripcion' => 'Consulta el estado actual de una solicitud de cambio que ya presentaste: si está en evaluación, pendiente de aprobación, en desarrollo o programada para implementación. Indica el número o nombre del cambio solicitado.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Implementación de Nuevos Sistemas',
                        'descripcion' => '¿Tu área necesita una nueva herramienta tecnológica para mejorar sus procesos? Antes de adquirir o desarrollar cualquier sistema, es importante que TI evalúe los requerimientos técnicos, la integración con los sistemas existentes y la viabilidad del proyecto.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Solicitud de evaluación de nuevo sistema',
                                'descripcion' => 'Solicita que el área de TI evalúe la viabilidad técnica de implementar un nuevo sistema o herramienta en tu área. Describe el problema que quieres resolver y cualquier solución que hayas identificado.',
                            ],
                            [
                                'nombre' => 'Solicitud de proyecto de implementación TI',
                                'descripcion' => 'Una vez aprobada la evaluación, solicita el inicio formal del proyecto de implementación del nuevo sistema. El área de TI coordinará contigo los requerimientos, cronograma, pruebas y puesta en marcha.',
                            ],
                            [
                                'nombre' => 'Seguimiento de proyecto tecnológico en curso',
                                'descripcion' => 'Consulta el estado de avance de un proyecto de implementación tecnológica que ya está en marcha: etapa actual, próximos hitos, inconvenientes identificados o fecha estimada de entrega.',
                            ],
                        ],
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // 14. MESA DE AYUDA GENERAL
            // ────────────────────────────────────────────────────────────────
            [
                'categoria' => [
                    'nombre' => 'Mesa de Ayuda General',
                    'descripcion' => '¿No encuentras tu solicitud en ninguna de las categorías anteriores? No te preocupes. La mesa de ayuda general es el punto de entrada para cualquier consulta, incidente o requerimiento tecnológico que no esté cubierto en el catálogo. Un técnico de TI te orientará y derivará al especialista correcto.',
                ],
                'servicios' => [
                    [
                        'nombre' => 'Soporte Técnico General',
                        'descripcion' => 'Si tienes un problema tecnológico que no sabes cómo clasificar o que no corresponde a ninguna de las categorías del catálogo, repórtalo aquí. El equipo de mesa de ayuda lo evaluará y lo derivará al área técnica correspondiente para su atención.',
                        'sla'         => 'MEDIA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Reporte de incidente técnico no clasificado',
                                'descripcion' => 'Reporta aquí cualquier falla o problema tecnológico que no se ajuste a las categorías del catálogo. Describe el problema con el mayor detalle posible (qué pasó, cuándo, qué equipo o sistema está involucrado) para agilizar su atención.',
                            ],
                            [
                                'nombre' => 'Consulta sobre uso de herramienta tecnológica',
                                'descripcion' => 'Si tienes dudas sobre cómo usar una herramienta, sistema o función tecnológica disponible en la municipalidad y no sabes a qué categoría corresponde, realiza tu consulta aquí y te orientaremos.',
                            ],
                            [
                                'nombre' => 'Escalamiento de solicitud no resuelta',
                                'descripcion' => 'Si tienes una solicitud de TI que lleva más tiempo del esperado sin ser atendida y ya intentaste comunicarte con el área responsable sin éxito, utiliza esta opción para escalarla y obtener una respuesta prioritaria.',
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Consultas y Asesoría TI',
                        'descripcion' => 'El área de TI no solo resuelve problemas, también orienta al personal en el uso eficiente de la tecnología. Si necesitas asesoría para elegir la herramienta adecuada para una tarea, entender cómo funciona algo o conocer las buenas prácticas tecnológicas, estamos aquí para ayudarte.',
                        'sla'         => 'BAJA',
                        'solicitudes' => [
                            [
                                'nombre' => 'Consulta sobre herramienta o sistema',
                                'descripcion' => 'Realiza aquí tu consulta sobre el funcionamiento, las capacidades o las limitaciones de cualquier herramienta o sistema tecnológico de la municipalidad. Te responderemos con información clara y precisa.',
                            ],
                            [
                                'nombre' => 'Asesoría para selección de solución tecnológica',
                                'descripcion' => 'Si tu área necesita resolver un problema y estás evaluando qué herramienta o sistema tecnológico usar, solicita la asesoría del área de TI para tomar la mejor decisión alineada con la infraestructura y las políticas institucionales.',
                            ],
                            [
                                'nombre' => 'Solicitud de buenas prácticas TI',
                                'descripcion' => 'Solicita recomendaciones sobre buenas prácticas en el uso de tecnología: organización de archivos, seguridad de contraseñas, uso eficiente del correo, respaldo de información personal y más.',
                            ],
                        ],
                    ],
                ],
            ],

        ];

        foreach ($catalogo as $item) {
            $categoriaId = DB::table('categorias')->insertGetId([
                'nombre' => $item['categoria']['nombre'],
                'descripcion' => $item['categoria']['descripcion'],
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($item['servicios'] as $servicio) {
                $servicioId = DB::table('servicios')->insertGetId([
                    'nombre'       => $servicio['nombre'],
                    'descripcion'  => $servicio['descripcion'],
                    'categoria_id' => $categoriaId,
                    'sla_id'       => $slaIds[$servicio['sla'] ?? 'MEDIA'] ?? null,
                    'activo'       => true,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                $solicitudesData = array_map(fn($s) => [
                    'nombre'      => $s['nombre'],
                    'descripcion' => $s['descripcion'],
                    'servicio_id' => $servicioId,
                    'activo'      => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ], $servicio['solicitudes']);

                DB::table('solicitudes')->insert($solicitudesData);
            }
        }
    }
}
