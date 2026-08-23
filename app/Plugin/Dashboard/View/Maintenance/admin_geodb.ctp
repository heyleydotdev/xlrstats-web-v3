<?php
/**
 * XLRstats : Real Time Player Stats (http://www.xlrstats.com)
 * (CC) BY-NC-SA 2005-2013, Mark Weirath, Özgür Uysal
 *
 * Licensed under the Creative Commons BY-NC-SA 3.0 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          http://www.xlrstats.com
 * @license       Creative Commons BY-NC-SA 3.0 License (http://creativecommons.org/licenses/by-nc-sa/3.0/)
 * @package       app.Plugin.Dashboard.View.Maintenance
 * @since         XLRstats v3.0
 * @version       0.1
 */
?>
<div class="page-header">
	<h1>Geo IP Databases</h1>
</div>

<div>
	<blockquote><?php
		echo '<i>';
		echo __('These free DB-IP Lite databases resolve player and server IP addresses to countries and cities.');
		echo '</i>';
		?>
	</blockquote>
</div>

<table class="table table-striped">
	<thead>
	<tr>
		<th><?php echo __('Database'); ?></th>
		<th><?php echo __('Status'); ?></th>
		<th><?php echo __('Details'); ?></th>
		<th><?php echo __('Action'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($status as $info): ?>
		<tr>
			<td><?php echo $info['label']; ?></td>
			<td>
				<?php if ($info['available']): ?>
					<span class="label label-success"><?php echo __('Installed'); ?></span>
				<?php else: ?>
					<span class="label label-important"><?php echo $info['error']; ?></span>
				<?php endif; ?>
			</td>
			<td>
				<?php if ($info['available']): ?>
					<small>
						<?php echo $info['sizeReadable']; ?> •
						<?php echo $info['databaseType']; ?> •
						<?php echo __('built') . ' ' . $info['buildDate']; ?>
					</small>
				<?php else: ?>
					<small><?php echo $info['path']; ?></small>
				<?php endif; ?>
			</td>
			<td>
				<?php
				echo $this->Html->link(
					$info['available'] ? __('Update') : __('Download'),
					array(
						'plugin' => 'dashboard',
						'admin' => true,
						'controller' => 'maintenance',
						'action' => 'admin_geoDbUpdate',
						$info['type'],
					),
					array(
						'class' => 'btn btn-info btn-small',
						'onclick' => 'this.innerHTML = \'Downloading…\'; return true;',
					)
				);
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<div>
	<small class="muted">
		Databases are stored in <code><?php echo APP; ?>Vendor<?php echo DS; ?>dbip</code>.
		The city database is about 120 MB compressed and may take a few minutes to download.<br />
		IP geolocation by
		<?php echo $this->Html->link('DB-IP.com', 'https://db-ip.com', array('target' => '_blank')); ?>
		(databases licensed under the
		<?php echo $this->Html->link('CC BY 4.0', 'https://creativecommons.org/licenses/by/4.0/', array('target' => '_blank')); ?>
		license).
	</small>
</div>
