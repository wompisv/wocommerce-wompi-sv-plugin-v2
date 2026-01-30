# Wompi El Salvador - WooCommerce Payment Gateway

**Plugin Name:** WOMPI - El Salvador  
**Version:** 2.0.0  
**Owner:** Wompi
**Developer:** Domino Soft 
**License:** Este proyecto es software propietario. Todos los derechos reservados.

## Descripción técnica
Este plugin implementa un Gateway de pagos para WooCommerce que integra la API de Wompi El Salvador. Funciona bajo un modelo de **Redirección**, llevando al usuario a un entorno seguro alojado por Wompi para procesar la transacción y regresando a la tienda mediante validación de hash (Webhooks y Redirección directa).

Técnicamente, extiende la clase `WC_Payment_Gateway` para el backend y utiliza `AbstractPaymentMethodType` junto con React para la integración moderna con WooCommerce Blocks (Checkout Block).

## Características principales
*   **Integración de Pagos:** Procesamiento de tarjetas de crédito y débito.
*   **Soporte Híbrido:** Compatible tanto con el Checkout clásico (Shortcode) como con el nuevo Checkout de Bloques.
*   **Pagos Flexibles:**
    *   Pago en Cuotas (Configurable).
    *   Pago con Puntos (Banco Agrícola).
    *   Pago con Bitcoin.
*   **Integración API:** Conexión automática para obtener tokens de sesión y configuración de comercio.

## Stack Tecnológico
*   **Lenguaje Backend:** PHP 7.4+ (Compatible con 8.x).
*   **Lenguaje Frontend:** JavaScript (ES6), React (para integración con Bloques).
*   **Framework:** WordPress Core / WooCommerce API.
*   **Protocolo de Comunicación:** REST API (JSON) sobre HTTPS.

## Requisitos técnicos, Dependencias, Componentes
*   **WordPress:** Versión 6.2 o superior.
*   **WooCommerce:** Versión 8.0 o superior.
*   **PHP:** Versión 8.2 (Recomendado).
*   **Extensiones PHP:** `curl`, `json`, `openssl`.
*   **Dependencias:** NINGUNA (Zero-Dependency). No utiliza librerías externas ni `vendor` folder.

## Estructura del proyecto
El plugin sigue una estructura plana simplificada:

```
wompi-plugin-wocommerce-main/
├── paywompi.php               # Punto de entrada / Registro de hooks
├── paywompi-gateway.php       # Lógica principal del Gateway (Backend)
├── class-block.php            # Lógica de integración con Blocks
├── checkout.js                # Frontend Script para el Block Checkout
├── assets/                    # Recursos visuales (Logos, Banners)
└── docs/                      # Documentación interna y análisis
```

## Seguridad
*   **Sanitización:** Uso estricto de `sanitize_text_field` y `wp_unslash` en todos los datos de entrada.
*   **Integridad:** Validación de transacciones mediante firma digital **HMAC-SHA256**. El plugin verifica que el hash recibido de Wompi coincida con el generado localmente usando el `Api Secret`.
*   **Comunicación:** Forzado de TLS/SSL en todas las peticiones a la API de Wompi (`https://api.wompi.sv`).

## Instrucciones de instalación
1.  **Descarga:** Obtén el archivo `.zip` del plugin.
2.  **Carga:** En el admin de WordPress, ve a **Plugins > Añadir nuevo > Subir plugin**.
3.  **Activación:** Activa el plugin tras la subida.
4.  **Configuración:**
    *   Ve a **WooCommerce > Ajustes > Pagos > WOMPI - El Salvador**.
    *   Ingresa tus credenciales (`App ID` y `Api Secret`).
    *   Guarda los cambios.

## Licencia
Este proyecto es software propietario. Todos los derechos reservados.
