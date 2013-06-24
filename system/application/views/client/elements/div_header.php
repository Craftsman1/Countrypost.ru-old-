<? // Проверяем раздел происхождения запроса в рамках типа аккаунта пользователя
if (!isset($this->user->user_group)) $path = "main"; else $path = $this->user->user_group;
if ((empty($segment) OR empty($allowed_segments) OR in_array($segment, $allowed_segments))) :
    View::show("/{$path}/elements/auth/success");
else : ?>
<script>
$(function() {
	window.location = '<?= $this->config->item('base_url') ?>';
});
</script>
<? endif; ?>