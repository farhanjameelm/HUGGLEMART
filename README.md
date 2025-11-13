# HugglingMart - eCommerce Platform with Price Negotiation

A modern, responsive eCommerce platform built with PHP and MDBootstrap that allows customers to negotiate prices with sellers through an interactive bargaining system.

## 🌟 Features

### Customer Features
- **Product Browsing**: Browse products by categories with advanced filtering
- **Price Negotiation**: Interactive bargaining system with real-time chat
- **Shopping Cart**: Add products with negotiated prices
- **User Dashboard**: Track orders, bargains, and wishlist
- **Responsive Design**: Optimized for desktop, tablet, and mobile

### Admin Features
- **Dashboard**: Overview of sales, products, and bargaining activity
- **Product Management**: Add, edit, and manage product inventory
- **Bargain Management**: Respond to customer negotiations
- **User Management**: View and manage customer accounts
- **Analytics**: Track bargaining trends and success rates

### Bargaining System
- **Real-time Chat**: Chat-like interface for negotiations
- **Counter Offers**: Both parties can make counter offers
- **Auto-accept Rules**: Set automatic acceptance thresholds
- **Expiration System**: Bargains expire after set time periods
- **Status Tracking**: Track pending, accepted, rejected, and expired bargains

## 🛠️ Tech Stack

- **Backend**: PHP 8.x with PDO for database operations
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **UI Framework**: MDBootstrap 7.x (Material Design for Bootstrap)
- **Database**: MySQL/MariaDB
- **Icons**: Font Awesome 6.x
- **Charts**: Chart.js (for admin analytics)

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or MariaDB 10.3+
- Apache/Nginx web server
- XAMPP/WAMP/LAMP (for local development)

## 🚀 Installation

### 1. Clone/Download the Project
```bash
# If using Git
git clone <repository-url> HUGGLINGMART

# Or download and extract to your web server directory
```

### 2. Database Setup
1. Start your MySQL server (XAMPP/WAMP)
2. Create a new database named `hugglingmart`
3. Import the database schema:
   ```sql
   -- Run the SQL files in order:
   source database/schema.sql
   source database/sample_data.sql
   ```

### 3. Configuration
1. Update database credentials in `config/database.php` if needed:
   ```php
   private $host = 'localhost';
   private $db_name = 'hugglingmart';
   private $username = 'root';
   private $password = '';
   ```

2. Update site URL in `config/config.php`:
   ```php
   define('SITE_URL', 'http://localhost/HUGGLINGMART');
   ```

### 4. File Permissions
Ensure the following directories are writable:
- `assets/uploads/` (for product images)
- `logs/` (if implementing logging)

## 🔐 Default Login Credentials

### Admin Account
- **Email**: admin@hugglingmart.com
- **Password**: password

### Test User Account
- **Email**: user@example.com
- **Password**: password

## 📁 Project Structure

```
HUGGLINGMART/
├── admin/                  # Admin panel files
│   ├── index.php          # Admin dashboard
│   ├── bargains.php       # Bargain management
│   └── products.php       # Product management
├── api/                   # API endpoints
│   ├── create-bargain.php # Create new bargain
│   ├── add-to-cart.php    # Add items to cart
│   └── get-counts.php     # Get cart/bargain counts
├── assets/                # Static assets
│   ├── css/              # Custom stylesheets
│   ├── js/               # Custom JavaScript
│   └── uploads/          # Uploaded files
├── classes/               # PHP classes
│   ├── User.php          # User management
│   ├── Product.php       # Product operations
│   └── Bargain.php       # Bargaining system
├── config/                # Configuration files
│   ├── config.php        # Main configuration
│   └── database.php      # Database connection
├── database/              # Database files
│   ├── schema.sql        # Database structure
│   └── sample_data.sql   # Sample data
├── includes/              # Shared components
│   ├── header.php        # Site header
│   └── footer.php        # Site footer
├── index.php             # Homepage
├── product.php           # Product detail page
├── bargain-chat.php      # Bargaining interface
├── bargains.php          # User bargains page
├── login.php             # User login
├── register.php          # User registration
└── README.md             # This file
```

## 🎯 Key Features Explained

### Bargaining System
1. **Initiate Bargain**: Customers click "Negotiate" on product pages
2. **Set Offer**: Enter desired price with optional message
3. **Real-time Chat**: Chat-like interface for negotiations
4. **Admin Response**: Sellers can accept, reject, or counter-offer
5. **Final Decision**: Accepted bargains can be added to cart at negotiated price

### Product Management
- Add/edit products with multiple images
- Set bargaining rules per product
- Manage inventory and stock levels
- Category organization

### User Experience
- Responsive design works on all devices
- Real-time notifications for bargain updates
- Intuitive navigation and search
- Modern Material Design interface

## 🔧 Customization

### Styling
- Modify CSS variables in `includes/header.php` for color scheme
- Add custom styles in `assets/css/custom.css`
- Update MDBootstrap theme if needed

### Bargaining Rules
- Adjust default discount thresholds in `config/config.php`
- Modify bargain expiration times
- Customize auto-accept rules per product

### Features
- Add payment gateway integration
- Implement email notifications
- Add product reviews and ratings
- Extend admin analytics

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check MySQL server is running
   - Verify database credentials in `config/database.php`
   - Ensure database `hugglingmart` exists

2. **Images Not Loading**
   - Check file permissions on `assets/uploads/`
   - Verify image URLs in sample data
   - Ensure web server can serve static files

3. **Session Issues**
   - Check PHP session configuration
   - Ensure cookies are enabled in browser
   - Verify session directory is writable

4. **CSRF Token Errors**
   - Clear browser cache and cookies
   - Check session is properly started
   - Verify CSRF token generation

## 📈 Future Enhancements

- [ ] Payment gateway integration (Stripe, PayPal)
- [ ] Email notification system
- [ ] Advanced search and filtering
- [ ] Product reviews and ratings
- [ ] Wishlist functionality
- [ ] Order tracking system
- [ ] Multi-vendor support
- [ ] Mobile app (React Native/Flutter)
- [ ] Advanced analytics dashboard
- [ ] Inventory management system

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For support and questions:
- Create an issue in the repository
- Email: support@hugglingmart.com
- Documentation: [Project Wiki]

---

**HugglingMart** - Where Smart Shopping Meets Smart Negotiation! 🛍️💬
