# Laravel Wallet System

A flexible and customizable wallet system for Laravel applications.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

---

## 🚀 Features

- 💳 Credit, debit, and refund support
- 📄 Tracks transactions and transaction history
- 🔐 Uses Laravel Cache locks for safe concurrent updates
- ⚙️ Fully configurable
- 🧱 Extendable schema: you can modify the `transactions` table
- 💾 Compatible with relational and non-relational DBs
- 🧪 Easy integration with existing `users` table

---

## 📦 Installation

```bash
composer require aniket/laravel-wallet-system
## 👨‍💻 Usage Example

### In `User.php`:

```php
use Aniket\LaravelWalletSystem\Traits\HasWallet;

class User extends Authenticatable {
    use HasWallet;
}

## Use Like
$user = User::find(1);
$user->credit(1000, 'Signup Bonus');
$user->debit(250, 'Purchase');
$user->refund(100, 'Failed order refund');

 
