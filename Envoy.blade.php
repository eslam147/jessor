@servers(['jessor_main' => ['root@173.212.241.22']])

@story('testing')
    fetch_last_update_from_main
    fetch_last_update_from_main_to_beta
@endstory

@task('fetch_last_update_from_main', ['on' => 'jessor_main'])
    cd /home/jesoor.online/testing.jesoor.online/
    git pull origin main
    composer dumpautoload
    php artisan optimize:clear
@endtask

@task('fetch_last_update_from_main_to_beta', ['on' => 'jessor_main'])
    cd /home/jesoor.online/beta.jesoor.online/
    git pull origin main
    composer dumpautoload
    php artisan optimize:clear
@endtask
