<div class="ravioli-modal-container" id="ravioli-modal-container">
  <div class="ravioli-modal" id="ravioli-modal">
    <h3>Mehrweg-Verpackung von Ravioli</h3>
    <img src="<?php echo esc_html(trim(get_option( "ravioli_settings_tab_image" ))) ?>" class="ravioli-pic"/>
    <p>
      <?php
        $allowed_tags = array(
            'br' => array(),
        );
        echo trim(
          str_replace(
            '[PREIS]',
            wc_price(esc_html(trim(get_option( 'ravioli_settings_tab_fee' )))),
            wp_kses(get_option( 'ravioli_settings_tab_body_text'), $allowed_tags)
          )
        )
      ?>
      Die Ravioli-Verpackung kann bequem und kostenlos bei allen DHL Paketshops, DHL Packstationen
      oder an allen Briefkästen zurückgebracht werden.
    </p>
    <div class="ravioli-button-container">
      <button class="ravioli-button ravioli-button-yes" id="ravioli-button-yes">Ja, gerne</button>
      <button class="ravioli-button ravioli-button-no" id="ravioli-button-no">Nein, danke</button>
    </div>
  </div>
</div>