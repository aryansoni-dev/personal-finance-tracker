# Personal Finance Tracker

![FineTrack Preview](./imgs/hero.png)

A comprehensive, web-based personal finance management application built with PHP, MySQL, and Tailwind CSS. The platform allows users to track their income, monitor expenses, set budgets, and visualize their financial health.

## 🚀 Features

- **User Authentication:** Secure registration, login, and password reset functionality via email (powered by PHPMailer).
- **Dashboard & Analytics:** Quick overview of your wallet balance, total income, expenses, and savings, along with visual analytics.
- **Transaction Management:** Easily add, update, and delete income and expense transactions.
- **Budgeting:** Set category-wise budgets and automatically track your spending against them to reach your financial goals.
- **Profile Management:** Manage user personal details or securely delete the account.
- **Categorization:** Classify transactions accurately utilizing distinct income and expense categories.
- **Responsive UI:** Clean, modern, and mobile-friendly design powered by Tailwind CSS.

## 💻 Tech Stack

- **Frontend:** HTML5, CSS3, Tailwind CSS (^3.4)
- **Backend:** PHP (Object-Oriented syntax)
- **Database:** MySQL / MariaDB
- **Libraries/Packages:** PHPMailer (^6.9)
- **Dependency Management:** Composer (PHP), NPM (Tailwind)

## 🗄️ Database Structure

The unified relation schema consists of the following core tables:
- `users`: Stores user credentials and profile data. 
- `categories`: Global taxonomy for identifying whether a transaction is an income or expense.
- `transactions`: Records financial entries linked to specific categories and users.
- `budgets`: Stores expenditure limits set by users for specific categories.
- `totals`: Caches financial snapshots containing net balance, total income, expenses, and savings per user for quick dashboard loads.

*(See full schema queries in `DBschema.txt`)*

## 🛠️ Installation & Setup

### Prerequisites
- PHP server environment (e.g., XAMPP, LAMP, or WAMP stack)
- MySQL / MariaDB
- [Composer](https://getcomposer.org/) (for PHP libraries)
- [Node.js & npm](https://nodejs.org/en/) (for compiling Tailwind classes)

### Steps

1. **Clone the repository:**
   ```bash
   git clone <your-github-repo-url>
   cd personal-finance-tracker
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies:**
   ```bash
   npm install
   ```

4. **Compile Tailwind CSS:**
   ```bash
   npx tailwindcss build -i <source> -o <destination> --watch 
   ```
   *(Be sure to replace `<source>` and `<destination>` with your stylesheets input and output paths)*

5. **Environment Configuration:**
   - Copy the `.env.example` file to create a new `.env` file in the root directory:
     ```bash
     cp .env.example .env
     ```
   - Create a new MySQL database and run the SQL statements found in `DBschema.txt`.
   - Update your database access credentials inside your new `.env` file!
     ```
        DB_HOST=localhost
        DB_USER=root
        DB_PASS=your_db_password
        DB_NAME=finetrack

        MAIL_HOST=smtp.gmail.com
        MAIL_PORT=587
        MAIL_USERNAME=your_email@gmail.com
        MAIL_PASSWORD=your_app_password
        MAIL_FROM_ADDRESS=your_email@gmail.com
     ```
   
6. **Mail Server Configuration (Optional):**
   - For password reset functionality, edit the SMTP/Mail credentials (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`) inside your `.env` file.

7. **Run the Application:**
   - Start your Apache and MySQL services.
   - Serve the application locally and navigate to the root directory (e.g. `http://localhost/personal-finance-tracker`).

## 📁 Key Project Structure

```text
├── classes/          # Backend PHP entities and business logic (Budget, Category, User, etc.)
├── config/           # Database connection setup
├── mailer/           # Mailing scripts (Forgot password features using PHPMailer)
├── vendor/           # Composer dependencies
├── views/            # UI components, pages (dashboard, analytics, profiles etc.), and partials
├── DBschema.txt      # Master SQL blueprint for backend structure
├── index.html        # Welcome / Entry point
├── package.json      # Node tools (TailwindCSS dependency)
└── composer.json     # Backend tools (PHPMailer dependency)
```

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](../../issues).

## 📜 License

This project is open-source and available under the [MIT License](LICENSE).