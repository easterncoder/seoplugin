<div class="wrap">
  <h1><?php echo $config['name']; ?></h1>
  <h2><?php echo $config['description']; ?></h2>
  <?php if ( $sitemaps_enabled_post_types || $sitemaps_enabled_taxonomies ) : ?>
  <p><a href="<?php echo $sitemap_url; ?>" target="_blank"><?php _e( 'View sitemap', 'seoplugin' ); ?></a></p>
  <?php endif; ?>
  <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="action" value="seoplugin-save-settings">
    <input type="hidden" name="redirect" value="<?php echo htmlentities( $_GET['page'] ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <tr>
          <th scope="row">
            <?php _e( 'Post Types', 'seoplugin' ); ?>
          </th>
          <td>
            <p><?php _e( 'Select post types to include in the sitemap', 'seoplugin' ); ?></p>
            <?php foreach ( $custom_post_types as $cpt ) : ?>
            <label>
              <input type="checkbox" name="settings[sitemaps-enabled-post-types][]" value="<?php echo $cpt->name; ?>" <?php echo in_array( $cpt->name, $sitemaps_enabled_post_types ) ? 'checked="checked"' : ''; ?>>
              <?php echo $cpt->label; ?>
            </label><br>
            <?php endforeach; ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Taxonomies', 'seoplugin' ); ?>
          </th>
          <td>
            <p><?php _e( 'Select taxonomies to include in the sitemap', 'seoplugin' ); ?></p>
            <?php foreach ( $taxonomies as $tax ) : ?>
            <label>
              <input type="checkbox" name="settings[sitemaps-enabled-taxonomies][]" value="<?php echo $tax->name; ?>" <?php echo in_array( $tax->name, $sitemaps_enabled_taxonomies ) ? 'checked="checked"' : ''; ?>>
              <?php echo $tax->label; ?>
            </label><br>
            <?php endforeach; ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Other', 'seoplugin' ); ?>
          </th>
          <td>
            <label>
              <input type="hidden" name="settings[sitemaps-enable-authors]" value="">
              <input type="checkbox" name="settings[sitemaps-enable-authors]" value="1" <?php echo $sitemaps_enable_authors ? 'checked="checked"' : ''; ?>>
              <?php _e( 'Authors', 'seoplugin' ); ?>
            </label><br>
          </td>
        </tr>
      </tbody>
    </table>
    <p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'seo-plugin' ); ?>"></p>
  </form>
</div>