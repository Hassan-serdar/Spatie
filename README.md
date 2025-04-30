# 📝 Mini Blog with Roles & Permissions (Laravel + Spatie)

This is a **mini blog application** built with Laravel for educational purposes.  
It demonstrates how to implement **Roles and Permissions** using the [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission) package.

## 🎯 Purpose

The main goal of this project is to learn and explore **role-based access control** in a real Laravel environment.  

This includes how to:

- Create and assign roles and permissions
- Restrict access to routes, views, and actions
- Manage users and their access levels

> 💡 While this app mimics a blogging system structure, its main focus is on **permissions**, not content management.

## 🔑 Features

- User authentication using Laravel Breeze
- Role & permission system powered by Spatie
- User management with role assignment
- Role creation with permission binding
- Permission management
- Middleware-based access restriction (e.g., only users with `Edit Roles` can access role edit routes)
- `SuperAdmin` is protected: cannot be assigned or altered through the UI
- Pagination for role and permission listing

## ⚠️ Note

- This project is **not meant for production use**.
- The logic around SuperAdmin and Admin roles is intentionally restricted to avoid privilege escalation.
