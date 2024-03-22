<?php
if (!defined('ABSPATH')) die('-1');

if (!class_exists("WD_ASL_Maintenance_Handler")) {
    /**
     * Class WD_ASL_Maintenance_Handler
     *
     * Maintenance page ajax handler
     *
     * @class         WD_ASL_Maintenance_Handler
     * @version       1.0
     * @package       AjaxSearchLite/Classes/Ajax
     * @category      Class
     * @author        Ernest Marcinko
     */
    class WD_ASL_Maintenance_Handler extends WD_ASL_Handler_Abstract {

        /**
         * This function handles the index table ajax requests
         */
        public function handle() {
            if (
                current_user_can( 'administrator' )
             ) {
                if (ASL_DEMO) {
                    print "Maintenance !!!ASL_MAINT_START!!!";
                    print_r(json_encode(array(
                        'status'    => 0,
                        'action'    => '',
                        'msg'       => 'Not allowed in demo mode!'
                    )));
                    print "!!!ASL_MAINT_STOP!!!";
                    die();
                }
    
                $status = 0;
                $msg = 'Missing POST information, please try again!';
                $action = 'none';
                if ( isset($_POST, $_POST['data']) ) {
                    if (is_array($_POST['data']))
                        $data = $_POST['data'];
                    else
                        parse_str($_POST['data'], $data);
                    if ( isset($data['asl_reset_nonce']) ) {
                        $nonce = 'asl_reset_nonce';
                    } else if ( isset($data['asl_wipe_nonce']) ) {
                        $nonce = 'asl_wipe_nonce';
                    }
                    if ( isset($data[$nonce]) &&
                        wp_verify_nonce( $data[$nonce], $nonce )
                    ) {
                        if ( $nonce == 'asl_reset_nonce' ) { // Reset
                            wd_asl()->init->pluginReset();
                            $status = 1;
                            $action = 'refresh';
                            $msg = 'The plugin data was successfully reset!';
                        } else {                             // Wipe
                            wd_asl()->init->pluginWipe();
                            $status = 1;
                            $action = 'redirect';
                            $msg = 'All plugin data was successfully wiped, you will be redirected in 5 seconds!';
                        }
                    } else {
                        $msg = 'Missing or invalid NONCE, please <strong>reload this page</strong> and try again!';
                    }
                }
                $ret = array(
                    'status'    => $status,
                    'action'    => $action,
                    'msg'       => $msg
                );
                print "Maintenance !!!ASL_MAINT_START!!!";
                print_r(json_encode($ret));
                print "!!!ASL_MAINT_STOP!!!";
            }
            die();
        }

        // ------------------------------------------------------------
        //   ---------------- SINGLETON SPECIFIC --------------------
        // ------------------------------------------------------------
        /**
         * Static instance storage
         *
         * @var self
         */
        protected static $_instance;

        public static function getInstance() {
            if ( ! ( self::$_instance instanceof self ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
    }
}