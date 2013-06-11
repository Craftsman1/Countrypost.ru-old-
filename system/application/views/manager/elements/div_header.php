<? // Проверяем раздел происхождения запроса в рамках типа аккаунта пользователя
if ((empty($segment) OR empty($allowed_segments) OR in_array($segment, $allowed_segments)) AND isset($this->user->user_group)) :
	View::show("/{$this->user->user_group}/elements/auth/success");
else : ?>
<script>
$(function() {
	window.location = '<?= BASEURL ?>';
});
</script>
<? endif; ?>