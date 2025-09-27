# E-commerce Setup Commands

This document describes the custom Artisan commands available for setting up and managing your e-commerce application.

## Available Commands

### 1. Setup E-commerce Data
```bash
php artisan setup:ecommerce [--fresh]
```

**Description:** Sets up the complete e-commerce environment with admin user, categories, and sample products.

**Options:**
- `--fresh` : Deletes all existing data and creates fresh setup

**What it creates:**

#### Admin User
- **Email:** admin@ecommerce.com
- **Password:** admin123
- **Status:** Active

#### Categories (6 categories)
1. **Electronics** - Latest electronic gadgets and devices
2. **Clothing** - Fashion and clothing items
3. **Books** - Educational and entertainment books
4. **Home & Garden** - Home improvement and garden supplies
5. **Sports** - Sports equipment and accessories
6. **Beauty & Health** - Beauty products and health supplements

#### Sample Products (10 products)

**Electronics:**
- iPhone 15 Pro Max - $1,199.99 (Special: $1,099.99) - Featured
- MacBook Air M3 - $1,299.99 - Featured
- Samsung 55" QLED TV - $899.99 (Special: $799.99)
- Wireless Bluetooth Earbuds - $199.99 (Special: $159.99) - Featured

**Clothing/Sports:**
- Nike Air Max 270 - $149.99 - Featured
- Levi's 501 Original Jeans - $89.99

**Books:**
- The Art of Programming - $49.99 (Special: $39.99)

**Home & Garden:**
- Robot Vacuum Cleaner - $299.99 (Special: $249.99) - Featured

**Beauty & Health:**
- Skincare Essential Kit - $79.99

**Sports:**
- Yoga Mat Premium - $29.99

**Examples:**
```bash
# Regular setup (keeps existing data)
php artisan setup:ecommerce

# Fresh setup (WARNING: Deletes all existing data)
php artisan setup:ecommerce --fresh
```

### 2. Show Application Statistics
```bash
php artisan show:stats
```

**Description:** Displays comprehensive statistics about your e-commerce application data.

**Information shown:**
- Admin users (total and active)
- Categories (total and active)  
- Products (total, active, and featured)
- Customers (registered)
- Orders (total, pending, processing)
- Product price range (min, max, average)
- Recent activity (latest products and orders)

**Example Output:**
```
📊 E-commerce Application Statistics
=====================================

👥 Admins: 6 total (6 active)
📁 Categories: 7 total (7 active)  
📦 Products: 20 total (20 active, 11 featured)
👤 Customers: 8 registered
🛒 Orders: 6 total (2 pending, 1 processing)

💰 Product Price Range:
   Min: $29.99
   Max: $1,299.99
   Avg: $298.79

Recent Activity:
📦 Latest Products:
   • iPhone 15 Pro Max (2 minutes ago)
   • MacBook Air M3 (2 minutes ago)
   • Samsung 55" QLED TV (2 minutes ago)
🛒 Latest Orders:
   • Order #29 - $1,199.99 (processing) - 19 minutes ago
   • Order #28 - $149.99 (pending) - 37 minutes ago
   • Order #27 - $89.99 (pending) - 40 minutes ago
```

## Usage Scenarios

### Initial Setup
When setting up the application for the first time:
```bash
php artisan setup:ecommerce
```

### Development Environment
For a clean development environment:
```bash
php artisan setup:ecommerce --fresh
```

### Monitoring
To check application status and activity:
```bash
php artisan show:stats
```

## Safety Features

### Data Protection
- The setup command will NOT overwrite existing data unless `--fresh` flag is used
- The `--fresh` option requires explicit confirmation before deleting data
- Foreign key constraints are properly handled during data deletion

### Error Handling
- Commands include comprehensive error handling
- Validation ensures data integrity
- Progress feedback shows what's being created or already exists

## Database Structure

### Tables Affected
- `admins` - Admin user accounts
- `categories` - Product categories with hierarchy support
- `products` - Product catalog with pricing and inventory
- `product_categories` - Many-to-many relationship between products and categories

### Key Features
- **Categories:** Support parent-child relationships for subcategories
- **Products:** Include pricing, special pricing, inventory tracking, featured status
- **Relationships:** Products can belong to multiple categories
- **SEO:** Automatic slug generation for categories and products

## Troubleshooting

### Common Issues

**1. Foreign Key Constraint Errors**
```bash
SQLSTATE[42000]: Cannot truncate a table referenced in a foreign key constraint
```
**Solution:** The `--fresh` option automatically handles foreign key constraints by temporarily disabling them.

**2. Existing Data**
**Behavior:** Setup command skips existing records and shows warning messages.
**Solution:** Use `--fresh` flag to recreate all data.

**3. Memory Issues**
**Solution:** Increase PHP memory limit for large datasets:
```bash
php -d memory_limit=512M artisan setup:ecommerce
```

## Extending the Commands

The commands are located in:
- `app/Console/Commands/SetupEcommerceCommand.php`
- `app/Console/Commands/ShowStatsCommand.php`

You can modify these files to:
- Add more sample products
- Create additional categories  
- Include more detailed statistics
- Add custom validation or business logic

## Security Considerations

- Default admin password should be changed in production
- Consider using environment variables for sensitive data
- The commands respect Laravel's security features (password hashing, etc.)

---

**Note:** Always backup your database before using the `--fresh` option in production environments.