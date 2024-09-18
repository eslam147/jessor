@servers(['main' => ['admin@149.102.141.93']])

@story('production')
    pull_from_main
@endstory
@story('testing')
    pull_from_main_to_beta
@endstory

@task('pull_from_main', ['on' => 'main'])
    cd /home/admin/public_html/
    git pull origin main
    composer dumpautoload
    php artisan optimize:clear
    php artisan optimize
@endtask

@task('pull_from_main_to_beta', ['on' => 'main'])
    cd /home/jesoor.online/beta.jesoor.online/
    git pull origin main
    composer dumpautoload
    php artisan optimize:clear
@endtask
