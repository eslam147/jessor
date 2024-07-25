@servers(['main' => ['root@173.212.241.22']])

@story('testing')
    testing
@endstory
@story('deploy')
    deploy
@endstory

@task('testing', ['on' => 'main'])
    cd /home/jesoor.online/testing.jesoor.online/
    git pull origin main
    composer dumpautoload
    php artisan optimize:clear
@endtask
@task('deploy', ['on' => 'main'])
    cd /home/jesoor.online/public_html/
    git pull origin main
    composer install --optimize-autoloader --no-dev

    php artisan optimize:clear
    php artisan optimize
@endtask
