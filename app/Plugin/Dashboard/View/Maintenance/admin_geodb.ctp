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
			<td class="geodb-cell">
				<div class="geodb-idle">
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
							'class' => 'btn btn-info btn-small js-geodb-update',
							'data-type' => $info['type'],
						)
					);
					?>
				</div>
				<div class="geodb-busy" style="display:none;">
					<div class="progress progress-striped active" style="width:150px;margin-bottom:4px;">
						<div class="bar geodb-bar" style="width:100%;"></div>
					</div>
					<small class="muted geodb-text"><?php echo __('Starting…'); ?></small>
				</div>
				<div class="geodb-result" style="display:none;"></div>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<div>
	<small class="muted">
		Databases are stored in <code><?php echo $dbPath; ?></code>.
		The city database is about 120 MB compressed and may take a few minutes to download.<br />
		IP geolocation by
		<?php echo $this->Html->link('DB-IP.com', 'https://db-ip.com', array('target' => '_blank')); ?>
		(databases licensed under the
		<?php echo $this->Html->link('CC BY 4.0', 'https://creativecommons.org/licenses/by/4.0/', array('target' => '_blank')); ?>
		license).
	</small>
</div>

<?php
$urls = json_encode(array(
	'status' => $this->Html->url(array(
		'plugin' => 'dashboard',
		'admin' => true,
		'controller' => 'maintenance',
		'action' => 'admin_geoDbStatus',
	)),
	'update' => $this->Html->url(array(
		'plugin' => 'dashboard',
		'admin' => true,
		'controller' => 'maintenance',
		'action' => 'admin_geoDbUpdate',
	)),
));
?>
<script type="text/javascript">
var GEOIP_URLS = <?php echo $urls; ?>;
(function ($) {

	function formatBytes(bytes) {
		if (!bytes && bytes !== 0) {
			return '';
		}
		if (bytes < 1048576) {
			return Math.round(bytes / 1024) + ' KB';
		}
		return (bytes / 1048576).toFixed(1) + ' MB';
	}

	function showBusy(cell) {
		cell.find('.geodb-idle').hide();
		cell.find('.geodb-busy').show();
		cell.find('.geodb-result').hide();
	}

	function showIdle(cell) {
		cell.find('.geodb-busy').hide();
		cell.find('.geodb-idle').show();
	}

	function showResult(cell, ok, message) {
		var el = cell.find('.geodb-result');
		el.html('<small class="' + (ok ? 'text-success' : 'text-error') + '">' + message + '</small>').show();
	}

	function renderProgress(cell, p) {
		var text, pct = null;
		if (p.phase === 'downloading') {
			if (p.total > 0) {
				pct = Math.round(p.downloaded / p.total * 100);
				text = '<?php echo __('Downloading…'); ?> ' + pct + '% (' + formatBytes(p.downloaded) + ')';
			} else {
				text = '<?php echo __('Downloading…'); ?> ' + formatBytes(p.downloaded);
			}
		} else if (p.phase === 'extracting') {
			text = '<?php echo __('Installing database…'); ?> ' + formatBytes(p.downloaded);
		} else {
			text = '<?php echo __('Working…'); ?>';
		}
		cell.find('.geodb-bar').css('width', pct ? pct + '%' : '100%');
		cell.find('.geodb-text').text(text);
	}

	function pollProgress() {
		$.getJSON(GEOIP_URLS.status).done(function (res) {
			if (!res || !res.progress) {
				return;
			}
			$.each(res.progress, function (type, p) {
				var cell = $('.js-geodb-update[data-type="' + type + '"]').closest('.geodb-cell');
				if (cell.length && cell.is(':visible') && cell.find('.geodb-busy').is(':visible') && p) {
					renderProgress(cell, p);
				}
			});
		});
	}

	$('.js-geodb-update').on('click', function (e) {
		e.preventDefault();
		var btn = $(this),
			type = btn.data('type'),
			cell = btn.closest('.geodb-cell'),
			poller = setInterval(pollProgress, 1500);

		btn.prop('disabled', true);
		showBusy(cell);

		$.getJSON(GEOIP_URLS.update + '/' + type)
			.done(function (res) {
				if (res && res.success) {
					clearInterval(poller);
					showResult(cell, true, res.message);
					setTimeout(function () { window.location.reload(); }, 1200);
				} else {
					clearInterval(poller);
					showResult(cell, false, (res && res.message) ? res.message : '<?php echo __('Download failed.'); ?>');
					showIdle(cell);
					btn.prop('disabled', false);
				}
			})
			.fail(function () {
				clearInterval(poller);
				showResult(cell, false, '<?php echo __('Request failed. Please try again.'); ?>');
				showIdle(cell);
				btn.prop('disabled', false);
			});
	});

})(jQuery);
</script>
