<div class="wrap">
  <h1><?php echo $config['name']; ?></h1>
  <h2><?php echo $config['description']; ?></h2>
  <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="action" value="seoplugin-save-settings">
    <input type="hidden" name="redirect" value="<?php echo htmlentities( $_GET['page'] ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <?php $index = 0; foreach( $settings AS $robot ) : ?>
        <tr>
          <th scope="row">
            <?php _e( 'User Agent', 'seoplugin' ); ?>
          </th>
          <td>
            <input type="text" name="settings[robots-txt][<?php echo $index; ?>][user-agent]" value="<?php echo htmlentities( $robot['user-agent'] ); ?>">
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Allow', 'seoplugin' ); ?>
          </th>
          <td>
            <textarea name="settings[robots-txt][<?php echo $index; ?>][allow]"><?php echo $robot['allow']; ?></textarea>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Disallow', 'seoplugin' ); ?>
          </th>
          <td>
            <textarea name="settings[robots-txt][<?php echo $index; ?>][disallow]"><?php echo $robot['disallow']; ?></textarea>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e( 'Crawl Delay', 'seoplugin' ); ?>
          </th>
          <td>
            <input name="settings[robots-txt][<?php echo $index; ?>][crawl-delay]" type="number" min="0" value="<?php echo (int) $robot['crawl-delay']; ?>">
          </td>
        </tr>
        <th scope="row">
        </th>
        <td>
          <label>
            <input type="checkbox" name="settings[robots-txt][<?php echo $index; ?>][include-sitemap-index]" value="1" <?php echo $robot['include-sitemap-index'] ? 'checked="checked"' : ''; ?>>
            <?php _e( 'Include sitemap index in robots.txt', 'seoplugin' ); ?>
          </label>
        </td>
        <?php $index++; endforeach; ?>
      </tbody>
    </table>
    <p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'seo-plugin' ); ?>"></p>
  </form>
</div>