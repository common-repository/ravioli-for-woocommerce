<div class="ravioli-banner-full" id="ravioli-banner-full">
  <div class="ravioli-banner-inner-container" id="ravioli-banner-full-inner-container">
    <img src="<?php echo esc_html(trim(get_option( "ravioli_settings_tab_image" ))) ?>" class="ravioli-banner-pic"/>
    <div class="ravioli-banner-text-container">
      <h3 class="ravioli-title">Mehrweg-Verpackung von Ravioli</h3>
      <p class="ravioli-text">
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
</div>