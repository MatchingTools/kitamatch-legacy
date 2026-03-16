<?php
use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;
$iterator = Finder::create()
 ->files()
 ->name('*.php')
 ->exclude('node_modules')
 ->exclude('resources')
 ->exclude('database')
 ->exclude('config')
 ->exclude('routes')
 ->exclude('bootstrap')
 ->exclude('storage')
 ->exclude('vendor')
 ->in('/var/www/html/kitamatch/app');

return new Sami($iterator,[
 'theme' => 'default',
 'title' => 'KitaMatch',
 'build_dir' => '/var/www/html/kitamatch/public/docs/build',
 'cache_dir' => '/var/www/html/kitamatch/public/docs/cache',
]);
