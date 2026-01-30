<?php
/*
Plugin Name: WOMPI - El Salvador
Plugin URI: https://github.com/wompisv/wocommerce-wompi-sv-plugin
Description: Plugin WooCommerce para integrar la pasarela de pago Wompi El Salvador
Version: 2.1.0
Author: WOMPI-El Salvador 
Author URI: https://wompi.sv
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * --------------------------------------------------------------------------
 * 1. Installation Safety Guard (Fase 0 - Priority)
 * --------------------------------------------------------------------------
 */

// Activation Hook: Validar entorno antes de permitir la activación
register_activation_hook( __FILE__, 'wompi_safe_activation' );
function wompi_safe_activation() {
    // Verificar PHP version (8.2 recomendado, 7.4 min referencial)
    if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 'Este plugin requiere PHP 7.4 o superior.' );
    }

    // Verificar WooCommerce presencia (solo si está activo)
    // Nota: is_plugin_active no siempre está disponible en este contexto sin incluir plugin.php,
    // por lo que confiamos en class_exists('WooCommerce') si se carga después, pero para activación
    // segura es mejor advertir en admin notice si falta.
}

// Safe Loading Logic
add_action( 'plugins_loaded', 'wompi_init_gateway', 0 );
function wompi_init_gateway() {
    // 1. Verificar Dependencias Críticas
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        return; // Fallback silencioso: Si no hay WC, no cargamos el Gateway para no romper.
    }

    // 2. Cargar Clase del Gateway
    if ( file_exists( plugin_dir_path( __FILE__ ) . 'paywompi-gateway.php' ) ) {
        require_once 'paywompi-gateway.php';
        
        // Registrar Gateway
        add_filter( 'woocommerce_payment_gateways', 'wompi_register_gateway' );
    }

    // 3. Self-Healing (Auto-Curación) de Configuración
    // Protege contra arrays corruptos en la base de datos que causan fatal errors en init_settings
    $settings_key = 'woocommerce_wompi_payment_settings';
    $current_settings = get_option( $settings_key );
    
    // Si la opción existe pero NO es un array (data corrupta), reiniciarla a un estado seguro
    if ( false !== $current_settings && ! is_array( $current_settings ) ) {
        update_option( $settings_key, array( 'enabled' => 'no' ) );
        // Opcional: Loguear este evento si tuviéramos logger activo
    }
}

/**
 * Registrar la clase del Gateway en WooCommerce
 */
function wompi_register_gateway( $methods ) {
    $methods[] = 'Pay_Wompi_Gateway';
    return $methods;
}

/**
 * --------------------------------------------------------------------------
 * 2. Blocks Compatibility (Legacy Code Preserved & Cleaned)
 * --------------------------------------------------------------------------
 */

// Hook para compatibilidad con Checkout Blocks
add_action( 'before_woocommerce_init', 'wompi_declare_cart_checkout_blocks_compatibility' );
function wompi_declare_cart_checkout_blocks_compatibility() {
    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
    }
}

// Registro del Payment Method Type para Blocks
add_action( 'woocommerce_blocks_loaded', 'wompi_register_blocks_payment_method' );
function wompi_register_blocks_payment_method() {
    if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
        return;
    }

    $block_class_path = plugin_dir_path( __FILE__ ) . 'class-block.php';
    if ( file_exists( $block_class_path ) ) {
        require_once $block_class_path;
        
        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
                $payment_method_registry->register( new Pay_Wompi_Blocks );
            }
        );
    }
}