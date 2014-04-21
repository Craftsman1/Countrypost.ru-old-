<? // Проверяем раздел происхождения запроса в рамках типа аккаунта пользователя
if ((empty($segment) OR empty($allowed_segments) OR in_array($segment, $allowed_segments))) :
	View::show("/{$this->user->user_group}/elements/auth/success");
else : ?>
<script>
$(function() {
	window.location = '<?= $this->config->item('base_url') ?>';
});
</script>
<? endif; ?>