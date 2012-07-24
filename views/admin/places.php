<section class="title">
	<h4><? echo lang('places:places'); ?></h4>
</section>

<section class="item">
	<? echo form_open('admin/places/delete');?>
	
	<? if (!empty($places)): ?>
	
		<table>
			<thead>
			    <!-- Header row -->
				<tr>
					<th><? echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                    <th><? echo lang('places:id'); ?></th>
					<th><? echo lang('places:name'); ?></th>
					<th><? echo lang('places:address'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">
						<div class="inner"><? $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?
				    // Loop through each team here.
				    foreach( $places as $location ):
			    ?>
				<tr>
					<td><? echo form_checkbox('action_to[]', $location->id); ?></td>
					<td><? echo $location->id; ?></td>
					<td><? echo $location->name; ?></td>
					<td><? echo $location->address; ?></td>
					<td class="actions">
						<? echo
						anchor('http://maps.googleapis.com/maps/api/staticmap?
							center='.$location->address.'&
							markers='.$location->address.'&
							zoom='.$settings->zoom_level->value.'&
							size='.$settings->image_size->value.'&
							sensor=false&
							key='.$settings->api_key->value, lang('global:view'), 'class="button" target="_blank"').' '.
						anchor('admin/places/edit/'.$location->id, lang('global:edit'), 'class="button"').' '.
						anchor('admin/places/delete/'.$location->id, 	lang('global:delete'), array('class'=>'button')); ?>
					</td>
				</tr>
				<? endforeach; ?>
			</tbody>
		</table>
		
		<div class="table_action_buttons">
			<? $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
		</div>
		
	<? else: ?>
		<div class="no_data"><? echo lang('sports:no_items'); ?></div>
	<? endif;?>
	
	<? echo form_close(); ?>
</section>
