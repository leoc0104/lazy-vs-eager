# ORM and Performance: Comparison, Optimization and Impact on Data Manipulation in Web Applications

This repository contains the source code and performance tests for my undergraduate thesis in Database Technology at Fatec Bauru. The project analyzes and compares different data loading strategies in ORMs, evaluating their impact on web application performance.

## 📋 Project Overview

This research focuses on comparing the performance differences between **Eloquent ORM** and **Query Builder** in Laravel, with special attention to:

- CRUD operations performance comparison
- Lazy Loading vs Eager Loading strategies
- Database query optimization
- Performance impact analysis in web applications

## 🎯 Objectives

### Main Objective
Analyze and compare different data loading strategies in ORMs, evaluating their impact on web application performance.

### Specific Objectives
- Identify the most suitable scenarios for each loading approach
- Quantify performance impact in different operations
- Propose optimizations to improve application efficiency
- Compare Eloquent ORM vs Query Builder in various CRUD operations

## 🏗️ Project Structure

```
lazy-vs-eager/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   └── Post.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── tests/
│   └── Feature/
│       └── PerformanceTest.php
└── README.md
```

## 🛠️ Technology Stack

- **Framework**: Laravel 12.x
- **Database**: SQLite (for testing)
- **Testing**: PHPUnit
- **Language**: PHP 8.2+

## 📊 Performance Tests

The project includes comprehensive performance tests covering:

### CRUD Operations
- **Create**: Mass insertion comparison (1,000 records)
- **Read**: All records, filtered queries, and pagination
- **Update**: Individual vs batch updates
- **Delete**: Mass deletion with foreign key constraints

### Loading Strategies
- **Lazy Loading**: N+1 query problem demonstration
- **Eager Loading**: Optimized relationship loading
- **Query Builder vs Raw SQL**: Performance comparison

### Test Database Setup
- 10,000 users
- 100,000 posts (10 posts per user)
- Foreign key constraints with cascade delete

## 🚀 Installation & Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd lazy-vs-eager
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   ```

## 🧪 Running Performance Tests

### Run All Performance Tests
```bash
php artisan test tests/Feature/PerformanceTest.php --env=testing
```

### Run Specific Test Groups

**CRUD Operations:**
```bash
# Select All Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=selectAll

# Filtering Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=selectFilter

# Pagination Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=selectPaginate

# Insert Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=insert

# Update Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=update

# Delete Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=delete
```

**Loading Strategies:**
```bash
# Lazy vs Eager Loading
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=lazyVsEagerLoading
```

**Eloquent vs Query Builder:**
```bash
# Eloquent Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=eloquent

# Query Builder Operations
php artisan test tests/Feature/PerformanceTest.php --env=testing --group=querybuilder
```

## 📈 Expected Test Results

Based on our research, you can expect the following performance patterns:

- **Query Builder** generally outperforms Eloquent in raw operations
- **Eloquent** provides better integration with Laravel features (events, relationships)
- **Eager Loading** significantly reduces query count compared to Lazy Loading
- **Mass operations** show the most dramatic performance differences

## 🔍 Key Findings

### Query Builder Advantages
- Faster execution times for simple operations
- Lower memory usage
- Direct SQL generation without ORM overhead

### Eloquent Advantages
- Rich feature set (events, observers, mutators)
- Better code maintainability
- Automatic relationship management
- Built-in optimizations for complex operations

### Performance Metrics
The tests measure execution time in seconds and output results to STDERR for analysis.

## 📝 Research Methodology

1. **Controlled Environment**: All tests run in isolated transactions
2. **Consistent Data**: Same dataset for all comparative tests
3. **Multiple Iterations**: Tests can be run multiple times for consistency
4. **Precise Measurements**: Using PHP's microtime() for accurate timing

### Research Questions
- How do different ORM approaches impact web application performance?
- When should developers choose Query Builder over Eloquent ORM?
- What are the trade-offs between developer productivity and application performance?

## 📚 References

- Laravel Documentation
- Eloquent ORM Performance Best Practices
- Database Query Optimization Techniques
- Web Application Performance Analysis

## 📄 License

This project is developed for educational purposes as part of an undergraduate thesis.

---

**Author**: Leonardo Camargo  
**Institution**: Fatec Bauru - Database Technology Course
**Year**: 2025
