<?php

class Rimplenet_Admin_Template_Settings{
    public $admin_post_page_type, $viewed_url, $post_id;
    
    public function __construct() {
        
        $this->viewed_url = $_SERVER['REQUEST_URI'];
        $this->admin_post_page_type = sanitize_text_field($_GET["rimplenettransaction_type"]);
        $this->post_id = sanitize_text_field($_GET['post']);
        
        add_action('init',  array($this,'required_admin_functions_loaded'));
        //save meta value with save post hook when Template Settings is POSTED
        add_action('save_post_rimplates',  array($this,'save_settings'), 10,3 );
        add_action('save_rp_color_settings',  array($this,'save_rp_color_settings'), 10,4 );
        
    }
    
    function required_admin_functions_loaded() {
       if(empty($this->admin_post_page_type)){
          
          //Register Rimplenet Template Settings Meta Box
          add_action('add_meta_boxes',  array($this,'rimplates_template_register_meta_box'));
        
        }
    }
    
    function rimplates_template_register_meta_box() {
        
        add_meta_box( 'rimplates-admin-basic-settings-meta-box', esc_html__( 'Templates Settings', 'rimplates' ),   array($this,'rimplates_admin_basic_settings_meta_box_callback'), 'rimplates', 'normal', 'high' );
        add_meta_box( 'rimplates-admin-template-details-meta-box', esc_html__( 'Shortcode / Template Details', 'rimplates' ),   array($this,'rimplates_template_details_meta_box_callback'), 'rimplates', 'side', 'high' );  
        
        add_meta_box( 'rimplates-admin-template-color-meta-box', esc_html__( 'Color Settings', 'rimplates' ),   array($this,'rimplates_template_color_meta_box_callback'), 'rimplates', 'side', 'high' );  
        
    }
    
    function rimplates_admin_basic_settings_meta_box_callback( $meta_id ) {
        
       include_once plugin_dir_path( dirname( __FILE__ ) ) . '/admin/partials/metabox-template-settings.php';
    
     }
    
    //Color Settings Metabox Function
    function rimplates_template_color_meta_box_callback($meta_id) {
        
        $post_id = $meta_id->ID;
        $template_id = $post_id;
        $shortcode  = "[rimplates-template id=$template_post_id]";
        
        $rp_primary_color_bg = get_post_meta($template_id, 'rp_primary_color_bg', true);
        if(empty($rp_primary_color_bg)){ $rp_primary_color_bg = "#4463dc"; }
        $rp_primary_color_text = get_post_meta($template_id, 'rp_primary_color_text', true);
        if(empty($rp_primary_color_text)){ $rp_primary_color_text = "#ffffff"; }
        
        $rp_secondary_color_bg = get_post_meta($template_id, 'rp_secondary_color_bg', true);
        if(empty($rp_secondary_color_bg)){ $rp_secondary_color_bg = "#212529"; }
        $rp_secondary_color_text = get_post_meta($template_id, 'rp_secondary_color_text', true);
        if(empty($rp_secondary_color_text)){ $rp_secondary_color_text = "#ffffff"; }
        
        
        
        ?>
        
         <!-- Primary Colors -->
         <div id="rimplates-primary-colors" class="rp-colors">
            <p>
                <strong>Primary Colors</strong>
                <span class="dashicons dashicons-editor-help rimplates-admin-tooltip" title="Primary color is used on the sidebar & some other places"></span>
            </p>
            
            <label for="rp_primary_color_bg">Background</label>
            <input type="color" id="rp_primary_color_bg" name="rp_primary_color_bg" value="<?php echo $rp_primary_color_bg; ?>">
            
            <label for="rp_primary_color_text">Text</label>
            <input type="color" id="rp_primary_color_text" name="rp_primary_color_text" value="<?php echo $rp_primary_color_text; ?>">
            
         </div>
         
         <!-- Secondary Colors -->
         <div id="rimplates-secondary-colors" class="rp-colors">
            <p>
                <strong>Secondary Colors</strong> 
                <span class="dashicons dashicons-editor-help rimplates-admin-tooltip" title="Secondary color is used in footer & collapse button"></span>
            </p>
            
            <label for="rp_secondary_color_bg">Background</label>
            <input type="color" id="rp_secondary_color_bg" name="rp_secondary_color_bg" value="<?php echo $rp_secondary_color_bg; ?>">
            
            <label for="rp_secondary_color_text">Text</label>
            <input type="color" id="rp_secondary_color_text" name="rp_secondary_color_text" value="<?php echo $rp_secondary_color_text; ?>">
            
         </div>
        
        <?php
          //Hook for displaying rimplates color settings
          do_action("rp_color_settings",$template_id);
        
     }
     
    function rimplates_template_details_meta_box_callback($meta_id) {
        
        $post_id = $meta_id->ID;
        $template_post_id = $post_id;
        $shortcode  = "[rimplates-template id=$template_post_id]";
        if(!empty($this->post_id)){
            echo "<p style='color:red;'><code class='rimplenet_click_to_copy'>$shortcode</code></p>";
        }
        else{
            echo "<p style='color:red;'>Shortcode for displaying user balance will appear here after publish</p>";
        }
    
        $linked_rimplate_page_id = get_post_meta($template_post_id, 'linked_rimplate_page_id', true);

        if(!empty($linked_rimplate_page_id)){
            echo '<a href="'.get_permalink($linked_rimplate_page_id).'" class="button button-primary button-large" target="_blank">View Rimplate Page</a>' ;
        }else{
            ?>
            <input name="create_rimplate_page" id="create_rimplate_page" type="checkbox" value="yes" class="regular-text" style="max-width: 25px; "checked ><label for="create_rimplate_page"> 
                     Tick to create Rimplate Page 
                     <span class="dashicons dashicons-editor-help rimplenet-admin-tooltip" title="If ticked, a page will be automatifally created with the shortcode, it saves you the stress of manually creating a page and inserting the shortcode"></span>
                 </label>
         <?php
        }
    
     }
    
    
    //Save Color Settings Hook
    function save_settings($post_id, $post, $update){
      $template_id = $post_id;
      
      $rimplates_template = sanitize_text_field($_POST['rimplates_template']);
      do_action("save_rp_color_settings", $template_id, $rimplates_template, $post, $update); 
      

      if(!empty($rimplates_template)){ 
        //$WALLET_CAT_NAME = 'RIMPLENET WALLETS';
        //wp_set_object_terms($post_id, $WALLET_CAT_NAME, 'rimplenettransaction_type');
        
        $template = sanitize_text_field( $_POST['rimplates_template'] );
        $create_rimplate_page = sanitize_text_field( $_POST['create_rimplate_page'] );
        $rimplates_small_title = sanitize_text_field( $_POST['rimplates_small_title'] );
        $rimplates_sidebar_menu = sanitize_text_field( $_POST['rimplates_sidebar_menu'] );
        $rimplates_default_post = sanitize_text_field( $_POST['rimplates_default_post'] );
        $rimplates_template_header_text = sanitize_text_field( $_POST['rimplates_template_header_text'] );     
        
        $rimplates_template_footer_text = wp_kses_post( $_POST['rimplates_template_footer_text'] );
     
        if($create_rimplate_page==true){
              $page_content = "[rimplates-template id=$post_id]";
              $args_page = array(
                    'post_title' => $post->post_title,
                    'post_content' => $page_content,
                    'post_status' => 'publish',
                    'post_type' => "page",
                    'page_template'  => 'template-blank-page.php'
                   ) ;  
            $linked_rimplate_page_id = wp_insert_post($args_page);
        }
        else{
            $linked_rimplate_page_id = get_post_meta($post_id, 'linked_rimplate_page_id', true);      
        }
        
        $metas = array( 
              'template' => $template,
              'linked_rimplate_page_id' => $linked_rimplate_page_id,
              'title' => $rimplates_template_header_text,
              'small_title' => $rimplates_small_title,
              'sidebar_menu' => $rimplates_sidebar_menu,
              'rimplates_default_post' => $rimplates_default_post,
              'footer_copyright_text' => $rimplates_template_footer_text,
             );
            
        foreach ($metas as $key => $value) {
          update_post_meta($post_id, $key, $value);
         }
        
       }
    }
    
    //Save Color Settings Function
    function save_rp_color_settings($template_id, $rimplates_template, $post, $update){
        
      if(!empty($rimplates_template)){
         
        $rp_primary_color_bg = sanitize_text_field( $_POST['rp_primary_color_bg'] );
        $rp_primary_color_text = sanitize_text_field( $_POST['rp_primary_color_text'] );
        
        $rp_secondary_color_bg = sanitize_text_field( $_POST['rp_secondary_color_bg'] );
        $rp_secondary_color_text = sanitize_text_field( $_POST['rp_secondary_color_text'] ); 
        
        $metas = array( 
              'rp_primary_color_bg' => $rp_primary_color_bg,
              'rp_primary_color_text' => $rp_primary_color_text,
              'rp_secondary_color_bg' => $rp_secondary_color_bg,
              'rp_secondary_color_text' => $rp_secondary_color_text,
             );
            
        foreach ($metas as $key => $value) {
          update_post_meta($template_id, $key, $value);
         }
        
        
      }
      
    }
    
  
        
}


$Rimplenet_Admin_Template_Settings = new Rimplenet_Admin_Template_Settings();