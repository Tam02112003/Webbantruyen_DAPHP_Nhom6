<div align="center">  
  <img src="public/icon/file.png" alt="Web Comic Store Logo" width="200">  
  <h1>Website Comic Store</h1>  
  <p>  
    <strong>A full-featured e-commerce platform for comic book enthusiasts</strong>  
  </p>  
  <p>  
    <a href="#features">Features</a> •  
    <a href="#screenshots">Screenshots</a> •  
    <a href="#architecture">Architecture</a> •  
    <a href="#installation">Installation</a> •  
    <a href="#usage">Usage</a> •  
    <a href="#tech-stack">Tech Stack</a>  
  </p>  
    
  [![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)  
  [![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com/)  
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)  
</div>  
  
---  
  
## ✨ Overview  
  
Web Comic Store is a comprehensive e-commerce platform built with PHP using the MVC architecture. The application provides a seamless shopping experience for comic book enthusiasts while offering powerful management tools for administrators.  
  
### 🎯 Key Capabilities  
  
<table>  
  <tr>  
    <td width="50%">  
      <h4>For Customers</h4>  
      <ul>  
        <li>Browse comic listings with category filtering</li>  
        <li>Manage shopping cart with quantity controls</li>  
        <li>Secure checkout process</li>  
        <li>Track order history</li>  
      </ul>  
    </td>  
    <td width="50%">  
      <h4>For Administrators</h4>  
      <ul>  
        <li>Comprehensive comic inventory management</li>  
        <li>Category creation and organization</li>  
        <li>Order processing and fulfillment</li>  
        <li>User account management</li>  
      </ul>  
    </td>  
  </tr>  
</table>  
  
## 📸 Screenshots  
  
<div align="center">  
  <table>  
    <tr>  
      <td align="center">  
        <img src="docs/images/cart-screenshot.png" alt="Shopping Cart" width="400"/><br>  
        <em>Shopping Cart Interface</em>  
      </td>  
      <td align="center">  
        <img src="docs/images/admin-comics-screenshot.png" alt="Admin Comics Management" width="400"/><br>  
        <em>Admin Comics Management</em>  
      </td>  
    </tr>  
    <tr>  
      <td align="center">  
        <img src="docs/images/checkout-screenshot.png" alt="Checkout Process" width="400"/><br>  
        <em>Checkout Process</em>  
      </td>  
      <td align="center">  
        <img src="docs/images/comic-detail-screenshot.png" alt="Comic Detail" width="400"/><br>  
        <em>Comic Detail View</em>  
      </td>  
    </tr>  
  </table>  
</div>  
  
## 🏗️ Architecture  
  
The application follows the Model-View-Controller (MVC) architectural pattern for clear separation of concerns:  
  
```mermaid  
graph TD  
    subgraph "Client Browser"  
        User["User Web Browser"]  
    end  
  
    subgraph "Controllers (app/controllers)"  
        User --> |"HTTP Request"| IndexPHP["index.php (Router)"]  
        IndexPHP --> |"Routes to"| AuthController["AuthController"]  
        IndexPHP --> |"Routes to"| HomeController["HomeController"]  
        IndexPHP --> |"Routes to"| CartController["CartController"]  
        IndexPHP --> |"Routes to"| OrderController["OrderController"]  
        IndexPHP --> |"Routes to"| AdminControllers["Admin Controllers"]  
    end  
  
    subgraph "Views (app/views)"  
        AuthController --> |"Renders"| AuthViews["Authentication Views"]  
        HomeController --> |"Renders"| HomeViews["Customer Views"]  
        CartController --> |"Renders"| CartViews["Cart Views"]  
        OrderController --> |"Renders"| OrderViews["Order Views"]  
        AdminControllers --> |"Renders"| AdminViews["Admin Views"]  
          
        AuthViews --> CustomerLayout["Customer Layout"]  
        HomeViews --> CustomerLayout  
        CartViews --> CustomerLayout  
        OrderViews --> CustomerLayout  
        AdminViews --> AdminLayout["Admin Layout"]  
    end  
  
    subgraph "Models (app/models)"  
        AuthController --> |"Uses"| UserModel["UserModel"]  
        HomeController --> |"Uses"| ComicModel["ComicModel"]  
        HomeController --> |"Uses"| CategoryModel["CategoryModel"]  
        CartController --> |"Uses"| CartModel["CartModel"]  
        OrderController --> |"Uses"| OrderModel["OrderModel"]  
        AdminControllers --> |"Uses"| AllModels["All Models"]  
          
        UserModel --> |"Queries"| Database["MySQL Database"]  
        ComicModel --> |"Queries"| Database  
        CategoryModel --> |"Queries"| Database  
        CartModel --> |"Queries"| Database  
        OrderModel --> |"Queries"| Database  
    end  
  
    subgraph "External Services"  
        AuthController --> |"OAuth"| GoogleOAuth["Google OAuth API"]  
    end
