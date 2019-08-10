<form action="{{ route('startImport') }}"  method="post">
@csrf
<div class="container">
    <button type="submit">Запустить импорт</button>
</div>
</form>