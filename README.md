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
```
---


# ⚙️ Wallet Configuration

You can edit the configuration in `config/wallet.php`:

```php
return [
    'users_table' => 'users',                    // Table name for users
    'wallet_balance_column' => 'wallet_balance',// Column for storing wallet balance
    'lock_duration' => 10,                       // Cache lock duration in seconds
    'default_balance' => 0.00,                   // Default balance for new users
];
```
## ⚙️ Configuration Options

- **users_table**  
  Specify your users table name.

- **wallet_balance_column**  
  Column in the users table for wallet balance.

- **lock_duration**  
  Time in seconds to lock a wallet during updates (prevents race conditions).

- **default_balance**  
  Initial balance for new users.
---

## 👨‍💻 Usage Example

For migrating the tables.

```bash
php artisan migrate
```

### In `User.php`:

```php
use Aniket\LaravelWalletSystem\Traits\HasWallet;

class User extends Authenticatable {
    use HasWallet;
}
```
## 💳 Wallet Operations

---

### ✅ Credit Wallet

Credits money to the user's wallet.

```php
$user = User::find(1);
$user->credit(1000, 'Signup Bonus');
```
### 🛒 Debit Wallet
Debits money from the user's wallet if sufficient balance is available.

```php
$user = User::find(1);
$user->debit(250, 'Purchase');
```
### 💸 Refund Wallet
Refunds money back into the wallet (acts like a credit, but marked with metadata).

```php
$user = User::find(1);
$user->refund(100, 'Failed order refund');
```
