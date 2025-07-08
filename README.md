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

## 👨‍💻 Usage Example

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
