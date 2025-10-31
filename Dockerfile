# PHP 8.4-cliの公式イメージを使用
FROM php:8.4-cli

# 作業ディレクトリを設定
WORKDIR /var/www/html

# 必要なパッケージをインストール (git, unzip, 依存関係管理用のlibzip-dev)
RUN apt-get update && apt-get install -y \
    zip \ 
    unzip \
    libzip-dev

# Composerのインストール (ライブラリ管理に必須)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# タイムゾーンの設定（オプションだが推奨）
RUN echo "date.timezone=Asia/Tokyo" > /usr/local/etc/php/conf.d/timezone.ini

# (必要に応じて) 拡張機能のインストール
# 例：pdo_mysql拡張など
# RUN docker-php-ext-install pdo_mysql

# プロジェクトの依存関係をインストール
# Dockerfile内に記述せず、コンテナ起動後にホスト側から実行する方が開発効率は良い
# COPY composer.json .
# RUN composer install --no-dev --optimize-autoloader