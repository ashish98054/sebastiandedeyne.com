@setup
    $dev = $dev ?? false;
    $root = $dev ? '/home/forge/dev.sebastiandedeyne.com' : '/home/forge/sebastiandedeyne.com';
    $branch = $dev ? 'dev' : 'master';
    $releaseDir = "{$root}/releases/".date('Y-m-d-His');
@endsetup

@servers(['web' => ['forge@sebastiandedeyne.com']])

@task('deploy', ['on' => 'web'])
    echo "Deploying new release: {{ $releaseDir }}"

    mkdir -p releases
	git clone -b {{ $branch }} git@github.com:sebastiandedeyne/sebastiandedeyne.com.git {{ $releaseDir }}

    cd {{ $releaseDir }}

    composer install --no-dev --no-interaction --prefer-dist --quiet
    yarn --frozen-lockfile --silent
    yarn production

    rm -f current
	ln -s {{ $releaseDir }} current
	ln -s {{ $releaseDir }}/../../.env {{ $releaseDir }}/.env
	sudo service php7.1-fpm restart

    php artisan cache:clear
    php artisan responsecache:flush
    php artisan optimize
    php artisan config:cache
    php artisan route:cache

    ls -dt {{ $root }}/releases/* | tail -n +3 | xargs -d "\n" rm -rf;
@endtask
