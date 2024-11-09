
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
<br>
APP_URL=Your_Full_URL
<br>
GOOGLE_RECAPTCHA_KEY=Your_Google_Recaptcha_V3_Key
<br>
GOOGLE_RECAPTCHA_SECRET=Your_Google_Recaptcha_V3_Secret
<br>
QUEUE_CONNECTION= (database or redis, i prefer using redis)

and change database connection : 
<br>
DB_CONNECTION=mysql
<br>
DB_HOST=127.0.0.1
<br>
DB_PORT=3306
<br>
DB_DATABASE=laravel
<br>
DB_USERNAME=root
<br>
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


