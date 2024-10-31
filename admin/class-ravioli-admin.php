<?php

class Ravioli_Admin {

	private $plugin_name;

	private $version;

  const DEFAULT_ADMIN_SETTINGS = array(
		'ravioli_display' => 'yes',
    'ravioli_display_mode' => 'popup',
		'ravioli_fee' => 1,
		'ravioli_weight' => 0,
		'ravioli_volume' => 0,
		'ravioli_yes_button_bg_color' => '#fec957',
		'ravioli_yes_button_font_color' => '#0f172a',
    'ravioli_yes_button_color_hover' => '#0f172a',
    'ravioli_yes_button_bg_color_hover' => '#ffe284',
    'ravioli_no_button_bg_color' => '#d3d3d3',
    'ravioli_no_button_font_color' => '#808080',
    'ravioli_no_button_color_hover' => '#808080',
    'ravioli_no_button_bg_color_hover' => '#dcdcdc',
    'ravioli_more_button_font_color' => '#0f172a',
    'ravioli_more_button_color_hover' => '#d3d3d3',
    'ravioli_text_color' => '#0f172a',
    'ravioli_body_text' => 'Erhalte deine Bestellung in einer Mehrweg-Verpackung von Ravioli  ' .
                            'für [PREIS]. ' .
                            'Du hilfst so mit Müll und CO2 zu vermeiden. ',
    'ravioli_teaser_text' => 'Willst du deine Bestellung für [PREIS] in einer Mehrweg-Verpackung erhalten?'
	);

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

  public function load_plugin() {
    if ( is_admin() && get_option( 'Activated_Plugin' ) == 'ravioli-for-woocommerce' ) {

      delete_option( 'Activated_Plugin' );

      // save default options on activation
      // we use add_option instead of update_option, so that if a user already had Ravioli installed
      // earlier, we don't override their previous settings
      $settings = $this->wc_admin_settings();

      // loop through $settings array
      foreach ($settings as $setting) {
        // if the setting has a default value, save it
        if (isset($setting['default'])) {
          add_option( $setting['id'], $setting['default'] );
        }
      }
    }
  }

  public function ravioli_enqueue_styles_and_scripts_admin(){
    // only load scripts if we're in the WooCommerce Ravioli settings
    if( empty( $_GET['page'] ) || ($_GET['page'] !== 'wc-settings' || (isset($_GET['tab']) && $_GET['tab'] !== 'ravioli')) ) { return; }

    wp_enqueue_media();

    wp_enqueue_script( 'admin', plugins_url( 'js/admin.js', __FILE__ ), array(), false, true );

    wp_enqueue_style( 'ravioli_admin_styles', plugins_url( 'css/styles.css', __FILE__ ) );

    wp_localize_script(
      'admin',
      'ravioli_data',
      array(
        "modal_image_default_url" => $this->default_image_url()
      )
    );
  }

  private function default_image_url() {
    return plugins_url( '/public/img/ravioli_checkout_default.jpeg', dirname(__FILE__, 1) );
  }

  // add checkbox to advanced options in edit product
  public function add_exclude_from_ravioli() {
    $args = array(
      'label' => __( 'Von Ravioli ausschließen?', Ravioli::RAVIOLI_TEXT_DOMAIN ),
      'id' => Ravioli::EXCLUDE_RAVIOLI_KEY,
      'desc_tip' => false,
      'description' => __("Falls der Warenkorb nur ausgeschloßene Produkte enthält, wird die Ravioli-Option nicht angezeigt.", Ravioli::RAVIOLI_TEXT_DOMAIN)
    );
    woocommerce_wp_checkbox( $args );
  }

  // save custom advanced options when updating product
  public function action_woocommerce_admin_process_product_object( $product ) {
    $checkbox = isset( $_POST[Ravioli::EXCLUDE_RAVIOLI_KEY] ) ? 'yes' : 'no';
    // Update meta
    $product->update_meta_data( Ravioli::EXCLUDE_RAVIOLI_KEY, $checkbox );
  }

  public function register_menu_items() {
    add_filter( 'woocommerce_settings_tabs_array', 'Ravioli_Admin::add_settings_tab', 50 );
  }
  
  public static function add_settings_tab( $settings_tabs ) {
    $settings_tabs['ravioli'] = __( 'Ravioli', Ravioli::RAVIOLI_TEXT_DOMAIN );
    return $settings_tabs;
  }
  
  public function settings_tab() {
    woocommerce_admin_fields( $this->ravioli_get_settings() );
  }
  
  public function ravioli_get_settings() {
    $settings = $this->wc_admin_settings();
    return apply_filters( 'wc_settings_tab_ravioli_settings', $settings );
  
  }
  
  public function ravioli_update_settings() {
    woocommerce_update_options( $this->ravioli_get_settings() );
  }

  public function ravioli_new_order_column( $columns ) {
    $new_columns = array();
    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;
        
        if ( 'order_status' === $column_name ) {
            $new_columns['ravioli'] = __( 'Ravioli', Ravioli::RAVIOLI_TEXT_DOMAIN );
        }
    }
    return $new_columns;
  }
  
  public function ravioli_populate_column($column, $order_or_order_id) {
    // legacy CPT-based order compatibility
    $order = $order_or_order_id instanceof WC_Order ? $order_or_order_id : wc_get_order( $order_or_order_id );
    
    if ( 'ravioli' === $column ) {
      $ship_with_ravioli = $order->get_meta("ship_with_ravioli");
      if ($ship_with_ravioli == "yes") {
        echo "<mark class='order-status status-processing'>";
        echo "<span>Ja</span>";
        echo "</mark>";
      } else {
        echo "<mark class='order-status status-pending'>";
        echo "<span>Nein</span>";
        echo "</mark>";
      }
    }
  }
  
  public function ravioli_add_order_column_style() {
    $css = '.column-ravioli { width: 6ch !important; }';
    wp_add_inline_style( 'woocommerce_admin_styles', $css );
  }

  private function wc_admin_settings() {
    // these settings are shown in the WooCommerce > Settings > Ravioli tab
    return array(
      'section_constraints_title' => array(
        'name'     => __( 'Ravioli Einstellungen', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type'     => 'title',
        'desc'     => 'Allgemeine Einstellungen',
        'id'       => 'wc_settings_tab_ravioli_section_title'
      ),
      'ravioli_display' => array(
        'name' => __( 'Ravioli Option anzeigen?', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'checkbox',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_display'],
        'desc' => __( 'Die Ravioli-Option auf der Check-out-Seite anzeigen', 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_display'
      ),
      'ravioli_display_mode' => array(
        'name' => __( 'Darstellung', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'select',
        'options' => array(
          'popup' => 'Pop-up',
          'banner' => 'Banner'
        ),
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_display_mode'],
        'desc' => __( 'Die Ravioli-Option als Pop-up oder Banner anzeigen?', 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_display_mode'
      ),
      'ravioli_fee' => array(
          'name' => __( 'Ravioli Gebühr (' . get_woocommerce_currency() . ')', Ravioli::RAVIOLI_TEXT_DOMAIN ),
          'type' => 'number',
          'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_fee'],
          'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
          'css' => 'width: 11ch;',
          'desc' => __( 'Wieviel willst du von deinen Kund:innen für eine Ravioli-Verpackung verlangen?', 'ravioli_settings_tab' ),
          'id'   => 'ravioli_settings_tab_fee'
      ),
      'ravioli_weight' => array(
        'name' => __( 'Max. totales Gewicht (kg)', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'number',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_weight'],
        'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
        'css' => 'width: 11ch;',
        'desc' => __( "Kund:innen bekommen die Ravioli-Option nicht angezeigt, falls das Gesamtgewicht des Warenkorbs größer als dieser Wert ist. Für kein Limit, trage 0 ein. Vergesse nicht, Produktgewichte einzupflegen.", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_weight'
      ),
      'ravioli_volume' => array(
        'name' => __( 'Max. totales Volumen (cm³)', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'number',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_volume'],
        'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
        'css' => 'width: 11ch;',
        'desc' => __( "Kund:innen bekommen die Ravioli-Option nicht angezeigt, falls das Gesamtvolumen des Warenkorbs größer als dieser Wert ist. Für kein Limit, trage 0 ein. Vergesse nicht, Produktvolumen einzupflegen.", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_volume'
      ),
      'ravioli_image' => array(
        'name' => __( 'Ravioli Bild', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'css' => 'display: none;',
        'default' => $this->default_image_url(),
        'desc' => __( "Dieses Bild wird beim Ravioli Pop-up oder Banner angezeigt. Unsere Empfehlung: Schieß ein schönes Foto von deinen Produkten in einer geöffneten Ravioli Verpackung.", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_image'
      ),
      'section_constraints_end' => array(
        'type' => 'sectionend',
        'id' => 'wc_settings_ravioli_constraints_section_end'
      ),
      // section texts
      'section_texts_title' => array(
        'name'     => __( 'Texte', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type'     => 'title',
        'desc'     => 'Benutze den Tag ' .
                      '<span class="ravioli-code">[PREIS]</span> anstatt den Preis manuell hinzuschreiben ' .
                      'und halte den Text kurz und knackig.<br>' .
                      'Um einen Zeilenabstand zu machen, benutze den Tag <span class="ravioli-code">&lt;br&gt;</span>.<br>' .
                      'Der folgende Satz wird am Schluss automatisch hinzugefügt: ' .
                      '<i>Die Ravioli-Verpackung kann bequem und kostenlos bei allen DHL Paketshops, DHL Packstationen oder an allen Briefkästen zurückgebracht werden.</i>',
        'id'       => 'wc_settings_tab_ravioli_section_texts_title'
      ),
      'ravioli_body_text' => array(
        'name' => __( 'Anzeige-Text', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'textarea',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_body_text'],
        'custom_attributes' => array( 'rows' => '6', 'cols' => '30' ),
        'css' => 'width: 70ch;',
        'desc' => __( '<p class="description">Dieser Text erscheint bei der Ravioli-Anzeige.<br>' . 
                      'Er soll erklären, wie Ravioli funktioniert, was die Vorteil für deine Kunden sind, ' .
                      'und wie viel es zusätzlich kostet.' .
                      '</p>',
                      'ravioli_settings_tab'
                    ),
        'id'   => 'ravioli_settings_tab_body_text'
      ),
      'ravioli_teaser_text' => array(
        'name' => __( 'Teaser-Text', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'textarea',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_teaser_text'],
        'custom_attributes' => array( 'rows' => '6', 'cols' => '30' ),
        'css' => 'width: 70ch;',
        'desc' => __( '<p class="description">Dieser Text erscheint beim Vorschau-Banner der Ravioli-Anzeige ' .
                      '<b>auf mobilen Geräten</b>.<br>' .
                      'Er dient dazu, deine Kund:innen auf die Ravioli-Option aufmerksam zu machen.<br>' .
                      'Er sollte also nur ein Teaser sein. Kund:innen die interessiert sind können dann ' .
                      ' auf "Mehr erfahren" klicken.<br>',
                      'ravioli_settings_tab'
                    ),
        'id'   => 'ravioli_settings_tab_teaser_text'
      ),
      'section_texts_end' => array(
        'type' => 'sectionend',
        'id' => 'wc_settings_ravioli_texts_section_end'
      ),
      // section styling
      'section_styling_title' => array(
        'name'     => __( 'Styling', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type'     => 'title',
        'desc'     => 'Für erweiteres Styling kannst du dein eigenes CSS hinzufügen und '. 
                      'die Klassen <span class="ravioli-code">.ravioli-button</span>, ' .
                      '<span class="ravioli-code">.ravioli-button-yes</span>, ' .
                      '<span class="ravioli-code">.ravioli-button-no</span>, ' .
                      '<span class="ravioli-code">.ravioli-button-more</span>, ' .
                      '<span class="ravioli-code">.ravioli-title</span> und ' .
                      '<span class="ravioli-code">.ravioli-text</span> verwenden ',
        'id'       => 'wc_settings_tab_ravioli_section_styling_title'
      ),
      'ravioli_yes_button_bg_color' => array(
        'name' => __( 'Ja-Knopf Hintergrundfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_yes_button_bg_color'],
        'css' => '',
        'desc' => __( "z.B. #fec957 oder rgb(254, 201, 87)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_yes_button_bg_color'
      ),
      'ravioli_yes_button_font_color' => array(
        'name' => __( 'Ja-Knopf Textfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_yes_button_font_color'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_yes_button_font_color'
      ),
      'ravioli_yes_button_color_hover' => array(
        'name' => __( 'Ja-Knopf Hover-Textfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_yes_button_color_hover'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_yes_button_color_hover'
      ),
      'ravioli_yes_button_bg_color_hover' => array(
        'name' => __( 'Ja-Knopf Hover-Hintegrundfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_yes_button_bg_color_hover'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_yes_button_bg_color_hover'
      ),
      'ravioli_no_button_bg_color' => array(
        'name' => __( 'Nein-Knopf Hintergrundfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_no_button_bg_color'],
        'css' => '',
        'desc' => __( "z.B. #fec957 oder rgb(254, 201, 87)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_no_button_bg_color'
      ),
      'ravioli_no_button_font_color' => array(
        'name' => __( 'Nein-Knopf Textfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_no_button_font_color'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_no_button_font_color'
      ),
      'ravioli_no_button_color_hover' => array(
        'name' => __( 'Nein-Knopf Hover-Textfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_no_button_color_hover'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_no_button_color_hover'
      ),
      'ravioli_no_button_bg_color_hover' => array(
        'name' => __( 'Nein-Knopf Hover-Hintegrundfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_no_button_bg_color_hover'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_no_button_bg_color_hover'
      ),
      'ravioli_more_button_font_color' => array(
        'name' => __( 'Mehr-Knopf Textfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_more_button_font_color'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_more_button_font_color'
      ),
      'ravioli_more_button_color_hover' => array(
        'name' => __( 'Mehr-Knopf Hover-Textfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_more_button_color_hover'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_more_button_color_hover'
      ),
      'ravioli_text_color' => array(
        'name' => __( 'Textfarbe', Ravioli::RAVIOLI_TEXT_DOMAIN ),
        'type' => 'text',
        'default' => Ravioli_Admin::DEFAULT_ADMIN_SETTINGS['ravioli_text_color'],
        'css' => '',
        'desc' => __( "z.B. #ffffff oder rgb(255, 255, 255)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_ravioli_text_color'
      ),
      'section_styling_end' => array(
          'type' => 'sectionend',
          'id' => 'wc_settings_ravioli_section_styling_end'
      )
    );
  }
}
?>