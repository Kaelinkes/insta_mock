# insta_mock

**InstaMock** is a custom-built social media platform designed to mimic key features of Instagram. It was developed as part of the Internet Programming 622 course at Richfield Graduate Institute of Technology. This project demonstrates a full-stack web application using PHP, MySQL, HTML, CSS, and JavaScript, without relying on frameworks or CMS platforms.

---

## Features

- User registration and login with secure password hashing (`password_hash()`)  
- Profile pages with editable display name and profile picture  
- Timeline/dashboard showing user posts with optional images in reverse chronological order  
- Private messaging system with threaded conversations  
- User search functionality  
- Form validation, image previews, and basic interactivity via JavaScript/jQuery  
- Security measures against SQL injection, XSS, and unauthorized access  

---

## Tech Stack

- **Backend:** PHP, MySQL  
- **Frontend:** HTML5, CSS3, JavaScript, jQuery  
- **Security:** Password hashing, prepared statements, session-based authentication  

---

## Installation

1. Clone the repository:  
   ```bash
   git clone https://github.com/Kaelinkes/insta_mock.git
   cd insta_mock
   Import the database:

2. Create a MySQL database (e.g., insta_mock)

3. Configure the database connection in includes/db.php

4. Serve the project using a local server (XAMPP/WAMP/LAMP or php -S localhost:8000)

5. Open http://localhost/insta_mock in your browser
   
##Usage

- Register a new user and login
- Create posts and view your dashboard
- Search for other users and view their profiles
- Send and receive private messages
- Edit your profile information and upload a profile picture
- 
##Project Structure
  insta_mock/
  
├── includes/# PHP files for DB connection and helpers

├── css/ # Stylesheets

├── js/ # JavaScript files

├── uploads/ # Uploaded images(if missing create the folder)

├── index.php   # Login/dashboard page

├── register.php

├── profile.php

└── messages.php

##License
MIT License – free to use, modify, and experiment with for educational purposes.
