@servers(['production' => 'sam@bemo.makiba.et'])

@setup
    $base_dir = '/home/sam';

    $app_dir = $base_dir . '/app';
    $repo_dir = $base_dir . '/bemo.git';
    $deploy_dir = $base_dir . '/bemo';

    $release_dir = $base_dir . '/releases/release_' . date('Ymdhis');
    $release_tmp = '/tmp/release';
@endsetup

@story('deploy')
    clone
    symlinks
    composer
    assets
    live
@endstory

@task('clone', ['on' => 'production'])
    cd {{ $repo_dir }}

    git worktree prune

    rm -rf {{ $release_tmp }}
    git worktree add {{ $release_tmp }} {{ $branch ?? 'master'}}

    mv {{ $release_tmp }} {{ $release_dir }}
    git worktree prune
@endtask

@task('symlinks', ['on' => 'production'])
    cd {{ $release_dir }}
    ln -nfs {{ $app_dir }}/.env .env

    cd {{ $release_dir }}/storage

    rm -r {{ $release_dir }}/storage/logs
    ln -nfs {{ $app_dir }}/logs logs
@endtask

@task('composer', ['on' => 'production'])
    cd {{ $release_dir }}
    echo "{{ $password }}" | sudo -S composer self-update
    composer install --no-dev --prefer-dist --no-scripts

    php artisan clear-compiled --env=production
    php artisan optimize --env=production
    php artisan migrate --force
@endtask

@task('assets', ['on' => 'production'])
    cd {{ $release_dir }}
    npm install
    npm run production

    rm -rf node_modules/

    find ./public/js ./public/css ./public/fonts ./public/images -type f ! -name "*.gz" ! -name "*.br" -exec gzip   -f -r -k -9 {} \;
    find ./public/js ./public/css ./public/fonts ./public/images -type f ! -name "*.gz" ! -name "*.br" -exec brotli -f    -k -Z {} \;
@endtask

@task('live', ['on' => 'production'])
    echo "{{ $password }}" | sudo -S rm -rf {{ $deploy_dir }}
    ln -nfs {{ $release_dir }} {{ $deploy_dir }}

    echo "{{ $password }}" | sudo -S chgrp -h www-data {{ $release_dir }}
    echo "{{ $password }}" | sudo -S chmod -R 777 {{ $release_dir }}/bootstrap/cache {{ $release_dir }}/storage
    echo "{{ $password }}" | sudo -S supervisorctl reread
@endtask
