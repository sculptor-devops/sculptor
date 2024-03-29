<?php
namespace Deployer;

/* Available contextual variables use this format {PARAMETER}
DOMAINS Server names as specified in web server configuration file
URL The domain url
NAME The domain name
PATH Domain root path
PUBLIC Domain public path visible from the web
CURRENT The path of the current code version
HOME The domain home path
USER The impersonated user
PHP The absolute path of the php interpreter
PHP_VERSION The version of the php */

require 'recipe/common.php';
require 'contrib/npm.php';

set('application', '{NAME}');

set('repository', '{REPOSITORY}');

set('git_tty', false);
set('http_user', '{USER}');
set('http_group', '{USER}');
set('writable_mode', 'chown');
set('branch', '{BRANCH}');
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

set('env', [
    #'GIT_SSH_COMMAND' => 'ssh -F {PATH}/ssh_config'
]);

set('bin/php', function () {
    return '{PHP}';
});

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
    'deploy:npm_packages',
    'deploy:npm_prod',    
    'deploy:owner',
    'deploy:migrate',
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
    'deploy:migrate',
    'deploy:owner',
    'deploy:npm_packages',
    'deploy:npm_prod',
    'cleanup',
    'success'
]);

task('deploy:key', function () {
    run("{PHP} {PATH}/current/artisan key:generate");
});

task('deploy:migrate', function () {
    run("{PHP} {PATH}/current/artisan migrate --force");
});

task('deploy:npm_packages', function () {
    run("cd {{release_path}} && npm install");
});

task('deploy:npm_prod', function () {
    run("cd {{release_path}} && npm run prod");
});

task('deploy:owner', function () {
    run("chown -R {USER}:{USER} {PATH}/shared");
});

after('deploy:failed', 'deploy:unlock');
