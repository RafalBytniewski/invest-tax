# INSTALATION

git clone https://github.com/RafalBytniewski/InvestTax.git

cp .env.example .env
php artisan key:generate

composer install
npm install
npm run build/npm run dev

php artisan migrate