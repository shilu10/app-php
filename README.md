# Job Listing App – Pure PHP MVC (No Framework)

This is a **Job Listing Application** built in **pure PHP** using a handcrafted **Model–View–Controller** architecture.  
No Laravel, Symfony, or CodeIgniter — **just raw PHP**, custom controllers, and a custom router.

---

## ✨ What’s Special About This Project?

### 1. **True MVC Without a Framework**
- **Model:** Handles database queries, data validation, and business logic.  
- **View:** Clean, reusable PHP/HTML templates with dynamic placeholders.  
- **Controller:** Connects Models and Views, processes requests, and returns responses.  
- Every piece of the MVC pipeline is **written from scratch**, no hidden framework logic.

---

### 2. **Custom Routing System**
- No `index.php?page=jobs` style — instead, **pretty URLs** like:
/jobs
/jobs/42
/jobs/create

- Routes map to controller actions using a **custom route matcher**.  
- Supports **GET**, **POST**, and other HTTP methods via manual request handling.  
- Dynamic parameters (e.g., `/jobs/{id}`) are parsed without external libraries.

---

### 3. **Custom Controllers**
- Each controller is a dedicated PHP class/method for a specific resource (e.g., `JobController`).  
- No magic — request parsing, validation, and response rendering are handled explicitly.  
- Controllers can return **HTML views** or **JSON responses** for API endpoints.

---

### 4. **Manual HTTP Request Handling**
- Uses PHP’s native `$_GET`, `$_POST`, and `$_SERVER` superglobals.  
- Custom logic for:
- Parsing query strings and form data  
- Setting HTTP status codes and headers  
- Handling redirects and error responses

---

### 5. **Lightweight & Zero Dependencies**
- No vendor lock-in — works on any standard PHP hosting.  
- Minimal footprint, easy to deploy.  
- Ideal for environments where installing large frameworks isn’t possible.

---

### 6. **Educational Value**
- Shows exactly how MVC works under the hood in PHP.  
- Great for learning core PHP architecture and routing concepts.  
- Builds strong fundamentals before using heavy frameworks.

---

## 🛠 Tech Stack
- **Language:** PHP (>= 7.4 recommended)  
- **Architecture:** MVC (Model-View-Controller)  
- **Routing:** Custom PHP router  
- **Views:** PHP + HTML templates  
- **Database:** MySQL (PDO)

---

