<div class="wrap">
  <h1><?php _e( 'Features', 'seoplugin' ); ?></h1>
  <p><?php _e( 'Enable/disable features by selecting/deselecting them below.', 'seo-plugin' ); ?></p>
  <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="action" value="seoplugin-toggle-features">
    <input type="hidden" name="redirect" value="<?php echo htmlentities( $_GET['page'] ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <?php foreach ( $features as $feature ) : ?>
        <tr>
          <th scope="row">
            <label>
              <input type="checkbox" name="enabled_features[]" value="<?php echo $feature['id']; ?>" <?php echo in_array( $feature['id'], $enabled_features ) ? 'checked="checked"' : ''; ?>>
              <?php echo $feature['name']; ?>
            </label>
          </th>
          <td>
            <p class="description"><?php echo $feature['description']; ?></p>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'seo-plugin' ); ?>"></p>
  </form>
</div>