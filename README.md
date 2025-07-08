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
Then use like:
php
Copy
Edit
$user = User::find(1);
$user->credit(1000, 'Signup Bonus');
$user->debit(250, 'Purchase');
$user->refund(100, 'Failed order refund');
🔹 STEP 9: Versioning (for maintainers or contributors)
Under a new section:

md
Copy
Edit
## 🏷️ Versioning

Once your package is ready for release:

```bash
git tag v1.0.0
git push origin v1.0.0
