<?php


// This is the secret key for API authentication. You configured it in the settings menu of the license manager plugin.
define('AJAXY_SECRET_KEY', '56d4131a60f6c8.69447567'); //Rename this constant name so it is specific to your plugin or theme.

// This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
define('AJAXY_LICENSE_SERVER_URL', 'http://www.ajaxy.org'); //Rename this constant name so it is specific to your plugin or theme.

// This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
define('AJAXY_ITEM_REFERENCE', 'CF7-REPEATER'); //Rename this constant name so it is specific to your plugin or theme.

class AJAXY_CF7_Repeater_License{
    public $lic;
    public $server = AJAXY_LICENSE_SERVER_URL;
    public $api_key = AJAXY_SECRET_KEY;
    private $wp_option  = '_cf7_repeater_license_2';
    private $product_id = 'CF7-REPEATER';
    public $err;
  
    public function __construct(){
      add_action('admin_menu', array(&$this, 'license_menu'));
    }
    public function check($lic = false){
        if($this->is_licensed())
            $this->lic = get_option($this->wp_option);
        else
            $this->lic = $lic;
    }
    /**
     * check for current product if licensed
     * @return boolean 
     */
    public function is_licensed(){
      $lic = get_option($this->wp_option);
      if(!empty( $lic ))
          return true;
      return false;
    }

  
    public function license_menu() {
      add_submenu_page('wpcf7', 'Contact Form 7 Repeater', 'Repeater', 'manage_options', 'wpcf7-repeater', array($this, 'license_page'));
    }
    public function license_page() {
      echo '<div class="wrap">';
      echo '<h2>License</h2>';

      /*** License activate button was clicked ***/
      if (isset($_REQUEST['activate_license'])) {
          $license_key = $_REQUEST['license_key'];
          // Send query to the license manager server
          $this->check($license_key);
          if($this->active()){
              echo 'You license Activated successfuly';
          }else{
              echo $lic->err;
          }

      }
      if($this->is_licensed()){
          echo 'Thank You Phurchasing!';
      }else{
          ?>
          <form action="" method="post">
              <table class="form-table">
                  <tr>
                      <th style="width:100px;"><label for="license_key">License Key</label></th>
                      <td ><input class="regular-text" type="text" id="license_key" name="license_key"  value="<?php echo get_option('license_key'); ?>" ></td>
                  </tr>
              </table>
              <p class="submit">
                  <input type="submit" name="activate_license" value="Activate" class="button-primary" />
              </p>
          </form>
          <?php
      }


      echo '</div>';
    }

    /**
     * send query to server and try to active lisence
     * @return boolean
     */
    public function active(){
        $url = AJAXY_LICENSE_SERVER_URL . '/?secret_key=' . AJAXY_SECRET_KEY . '&slm_action=slm_activate&license_key=' . $this->lic . '&registered_domain=' . get_bloginfo('url').'&item_reference='.$this->product_id;
        $response = wp_remote_get($url, array('timeout' => 20, 'sslverify' => false));

        if(is_array($response)){
            $json = $response['body']; // use the content
            $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', utf8_encode($json));
            $license_data = json_decode($json);
        }
        if($license_data->result == 'success'){
            update_option( $this->wp_option, $this->lic );
            return true;
        }else{
            $this->err = $license_data->message;
            return false;
        }
    }

    /**
     * send query to server and try to deactive lisence
     * @return boolean
     */
    public function deactive(){

    }

}
global $AJAXY_CF7_Repeater_License;
$AJAXY_CF7_Repeater_License = new AJAXY_CF7_Repeater_License();