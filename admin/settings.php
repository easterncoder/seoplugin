<div class="wrap">
  <h1><?php _e( 'Settings', 'seoplugin' ); ?></h1>
  <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="action" value="seoplugin-save-settings">
    <input type="hidden" name="redirect" value="<?php echo htmlentities( $_GET['page'] ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <tr>
          <th scope="row">
            <label for="sample-setting"><?php _e( 'Sample Setting', 'seo-plugin' ); ?></label>
          </th>
          <td>
            <input id="sample-setting" name="settings[sample-setting]" value="<?php echo htmlentities( self::get( 'sample-setting' ) ); ?>" class="regular-text">
            <p class="description"><?php _e( 'Setting description', 'seo-plugin' ); ?></p>
          </td>
        </tr>
      </tbody>
    </table>
    <p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'seo-plugin' ); ?>"></p>
  </form>
</div>