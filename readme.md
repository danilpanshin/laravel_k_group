1. git clone https://github.com/danilpanshin/laravel_k_group.git
2. cd laravel_k_group
3. composer install
4. php artisan migrate
5. php artisan serve

Механизм импорта скорее всего нужно было реализовать 
отдельным сервисом, а не методом модели, дабы не нарушать 
принципы SRP, но отставил как есть для простоты   