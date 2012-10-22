<div class='content'>
	<h2>F.A.Q.</h2>
	<form class='admin-inside' method="POST">
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<? if (count($faq_sections)) : 
					foreach($faq_sections as $section) : ?>
				<tr class='last-row'>
					<td>
						<span>
							<b style="font-size:1.1em;">
								<?= $section->faq_section_name ?>
							</b>
							<br />
							<br />
						</span>
					</td>
				</tr>
				<? foreach ($section->questions as $item) : ?>
				<tr class='last-row'>
					<td style='padding-left:20px;'>
						<a href='#faq<?= $item->faq_id ?>'><?= $item->faq_question ?></a>
					</td>
				</tr>
				<? endforeach; endforeach; endif; ?>
			</table>
			<br />
			<table>
				<? if (count($faq_sections)) : 
					foreach($faq_sections as $section) : 
						foreach ($section->questions as $item) : ?>
				<tr>
					<th>
						<span>
							<b style="font-size:1.1em;">
								<a name="faq<?= $item->faq_id ?>"></a>
								<?= $item->faq_question ?>
							</b>
						</span>
					</th>
				</tr>
				<tr>
					<td>
						<span>
							<?= html_entity_decode($item->faq_answer) ?>
						</span>
					</td>
				</tr>
				<? endforeach; endforeach; endif; ?>
			</table>
		</div>
	</form>
</div>