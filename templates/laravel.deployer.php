<?php
namespace Deployer;

require 'recipe/common.php';

set('application', '{NAME}');

set('repository', '{REPOSITORY}');

set('git_tty', false);
set('http_user', '{USER}');
set('http_group', '{USER}');
set('writable_mode', 'chown');
set('branch', 'master');
set('writable_recursive', true);
set('writable_chmod_mode', '0755');
set('writable_chmod_recursive', true);

set('shared_dirs', ['storage']);
set('shared_files', ['.env']);
set('writable_dirs', [
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

set('log_files', 'storage/logs/*.log');

set('allow_anonymous_stats', false);

localhost()
    ->set('deploy_path', '{PATH}')
    ->set('http_user', '{USER}');

desc('Deploy');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:owner',
    'cleanup',
    'success'
]);

desc('Installation');
task('deploy:install', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:key',
    'deploy:owner',
    'cleanup',
    'success'
]);

task('deploy:key', function () {
    run("php {PATH}/current/artisan key:generate");
});

task('deploy:migrate', function () {
    run("php {PATH}/current/artisan migrate --force");
});

task('deploy:owner', function () {
    run("chown -R {USER}:{USER} /var/www/html");
});

after('deploy:failed', 'deploy:unlock');
