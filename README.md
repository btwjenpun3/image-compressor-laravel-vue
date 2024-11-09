
## About

This is a simple Image Compressor made with Laravel, Inertia, Vue, and Tailwind. For now its only support for JPG and JPEG only.  

And support ReCAPTCHA V3 for avoid bot uploader.

## How to Install

First you need to clone this repo :

    git clone https://github.com/btwjenpun3/image-compressor-laravel-vue.git

Then you need to install requirement packages using Composer :

    composer install

After that, please rename .env.example into .env

Open .env file and edit :
APP_URL=Your_Full_URL
GOOGLE_RECAPTCHA_KEY=Your_Google_Recaptcha_V3_Key
GOOGLE_RECAPTCHA_SECRET=Your_Google_Recaptcha_V3_Secret
QUEUE_CONNECTION= (database or redis, i prefer using redis)

and change database connection : 
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

Save your .env file

Back to Terminal again and type :

    php artisan migrate
    php artisan key:generate

Then install requirement packages using NPM :

    npm install

Then build :

    npm run build

And its done.


