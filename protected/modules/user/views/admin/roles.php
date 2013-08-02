<div class="page-header">
    <h1>Role Management <small><?php echo $user->profile->firstname; ?></small></h1>
</div>

<?php echo CHtml::beginForm('', 'post', array('name'=>'general', 'class' => '')); ?>
	<input type="hidden" name="user_id" value="<?php echo $user->id; ?>" />
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Module Name</th>
				<th>Assignment</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($roles)): ?>
				<?php foreach($roles as $taskKey => $taskValue): ?>
					<tr>
						<td><?php echo $taskKey; ?></td>
						<td>
							<?php if(!empty($taskValue) && is_array($taskValue)): ?>
								<?php foreach($taskValue as $opsKey => $opsValue): ?>
									<?php if($opsKey == 'Site.Index') continue; ?>
									<span class="assignments">
										<label class="checkbox">
											<input <?php echo (in_array($opsKey, $assign_role)) ? 'checked="checked"' : ''; ?> type="checkbox" name="user_ops[]" value="<?php echo $opsKey; ?>" />
											<?php echo $opsKey; ?>
										</label>
									</span>
								<?php endforeach; ?>
							<?php endif; ?>
						</td>
					</tr>				
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<button type="submit" class="btn btn-info">Save</button>
<?php echo CHtml::endForm(); ?>