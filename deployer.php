<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'teste-laravel');

// Project repository
set('repository', 'git@github.com:frf/teste-laravel.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', ['storage']);

// Writable dirs by web server
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

host('teste.appr.dev.br')
    ->user('deployer')
    ->stage('prod')
    ->set('deploy_path', '/home/deployer/prod');

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

task('reload:php-fpm', function () {
    run('sudo /etc/init.d/php8.1-fpm restart'); // Using SysV Init scripts
});

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'deploy:symlink',
    'artisan:migrate',
    'artisan:route:cache',
    'artisan:cache:clear',
    'artisan:view:clear',
    'artisan:optimize:clear',
    'deploy:unlock',
    'cleanup',
]);

after('deploy:failed', 'deploy:unlock');
before('deploy:symlink', 'artisan:migrate');
after('deploy', 'reload:php-fpm');
