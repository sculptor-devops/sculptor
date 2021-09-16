<?php
namespace Deployer;

/* Available contextual variables
{DOMAINS} Server names as specified in web server configuration file
{URL} The domain url
{NAME} The domain name
{PATH} Domain root path
{PUBLIC} Domain public path visible from the web
{CURRENT} The path of the current code version
{HOME} The domain home path
{USER} The impersonated user
{PHP} The absolute path of the php interpreter
{PHP_VERSION} The version of the php */

require 'recipe/common.php';

// Project name
set('application', '{NAME}');

// Project repository
set('repository', '{REPOSITORY}');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

// Shared files/dirs between deploys
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server
set('writable_dirs', []);

set('allow_anonymous_stats', false);

set('env', [
    #'GIT_SSH_COMMAND' => 'ssh -F {PATH}/ssh_config'
]);

set('bin/php', function () {
    return '{PHP}';
});

// Hosts
localhost()
    ->set('deploy_path', '{PATH}')
    ->set('http_user', '{USER}');

// Tasks
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
    'deploy:owner',
    'cleanup',
    'success'
]);

task('deploy:owner', function () {
    run("chown -R {USER}:{USER} {PATH}/shared");
});

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
