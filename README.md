# 📝 Article Publishing Platform  

A simple PHP-based platform for publishing articles with user authentication and comment functionality.
The project implements best security practices to prevent common web vulnerabilities.
Articles must be approved by an admin before being published. 

## 🌟 Features  
✅ User authentication (login & registration)  
✅ Secure password hashing (`password_hash()`)  
✅ CSRF protection (`csrf_token`)  
✅ XSS prevention (`htmlspecialchars()`)  
✅ SQL Injection prevention (Prepared Statements)  
✅ Article publishing & moderation  
✅ Commenting system   
✅ Stored data in MySQL with XAMPP as the local development environment

## 🔧 Technologies  
- PHP (Backend)  
- MySQL (Database)  
- HTML, CSS (Frontend)
- CSS (Design)

## 🔒 Security Best Practices Implemented  
- **Session-based authentication** for user login  
- **CSRF protection** using a unique token per session  
- **SQL Injection prevention** with `bind_param()`  
- **XSS prevention** by sanitizing output (`htmlspecialchars()`)  
- **Article moderation** before displaying  


