# 💰 Invest-Tax 

# 🚧⏳🛠️🔜

An application for managing stock and cryptocurrency transactions, allowing transaction filtering and generating tax reports.

## 🛠 Tech Stack

| Layer          | Tool                        |
|----------------|-----------------------------|
| Framework      | Laravel 12                  |
| Admin          | Filament 3                  |
| Interactivity  | Livewire 3                  |
| Styling        | Tailwind CSS 4              |
| Authentication | Laravel Breeze              |

## 🚀 Features

### 👤 Frontend (User)

- Add stock and cryptocurrency transactions  
- Filter transactions by date, type, exchange, or asset  
- View transaction history  
- Generate tax reports including payable tax  
- Register/login (email)  

### 🔧 Admin Panel

- User management  
- Management of assets, wallets or exchanges (optional)  
- View statistics (optional: number of transactions, amounts, taxes)  

### 🎨 UI/UX

- Responsive user interface  
- Dynamic filters and tables using Livewire (no full-page reloads)  
- Built with Tailwind CSS and Preline UI for a clean and intuitive design  

<img width="1611" height="444" alt="image" src="https://github.com/user-attachments/assets/4db51242-b1b7-4aa2-908e-f67d6e9b8948" />
<img width="1392" height="658" alt="image" src="https://github.com/user-attachments/assets/517555db-f58b-4ed1-bc1b-76ed195e1f2d" />




## 📦 Installation

```bash
git clone https://github.com/your-username/invest-tax.git
cd invest-tax

composer install
npm install && npm run dev

cp .env.example .env
php artisan key:generate

# Configure your database connection in the .env file

php artisan migrate --seed

php artisan serve
