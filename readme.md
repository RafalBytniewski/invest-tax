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

<img width="1137" height="684" alt="image" src="https://github.com/user-attachments/assets/332da728-bea5-4667-bce9-e98feeeecad6" />

<img width="1073" height="655" alt="image" src="https://github.com/user-attachments/assets/a26de448-3893-4b80-8c17-1fc87b6f70e7" />






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
