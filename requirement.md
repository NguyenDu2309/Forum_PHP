# Student Forum - Web Programming Project Overview

## Overview
This project is a prototype system where students can post questions amongst themselves to get help with their coursework. It functions as a simple self-contained student version of Stack Overflow.

## Features

### Core Features (Implemented)
- **User Authentication**: Login system for students.
- **Post Management**: Create, read, update, and delete posts.
- **Comment System**: Add, edit, and delete comments on posts.
- **Module Organization**: Posts categorized by modules/courses.
- **Registration System**: Allow students to create accounts.
- **Password Encryption**: Enhanced security for user credentials.


### Planned Features
- **User Management**: Complete CRUD operations for user accounts.
- **Module Management**: Admin interface for managing course modules.
- **Contact Form**: Email functionality to contact administrators.
- **Admin Dashboard**: Special interface for administrators.
- **Input Validation**: Client and server-side validation.
- **Pagination**: For post and comment listings.
- **Search Functionality**: Find posts by keywords.

## Technical Implementation
- **Architecture**: Model-View-Controller (MVC) pattern for clean code organization.
- **Database Operations**: PHP PDO for database interactions with prepared statements.
- **Data Storage**: MySQL relational database.
- **Responsive Design**: Bootstrap framework for mobile-friendly UI.

## Database Structure
- **Users**: Store user information and credentials.
- **Posts**: Store questions/discussions created by users.
- **Comments**: Store responses to posts.
- **Modules**: Store course/module information.

## Security Measures
- **Prepared Statements**: Prevent SQL injection.
- **Input Sanitization**: Prevent XSS attacks.
- **Authentication Checks**: Ensure protected operations.
- **Form Validation**: Maintain data integrity.

## Installation and Setup
1. Clone the repository to your web server directory.
2. Import the database schema from `database/schema.sql`.
3. Configure the database connection in `config/database.php`.
4. Ensure your web server has PHP 7.4+ and MySQL 5.7+.

## Usage
### For Students
- Login with your credentials.
- Browse questions by module.
- Post new questions.
- Comment on existing questions.
- Edit or delete your own posts and comments.

### For Administrators
- Manage users, posts, and modules.
- Monitor system activity.
- Respond to contact form submissions.

## Development Roadmap
### Phase 1 (Current)
- Core authentication.
- Basic CRUD for posts and comments.
- Simple UI implementation.

### Phase 2 (Next Steps)
- Complete user management.
- Module management.
- Contact form implementation.
- Enhanced security features.

### Phase 3 (Future Enhancements)
- Advanced search functionality.
- Real-time notifications.
- User profile customization.
- Performance optimizations.

## Testing
- **Functional Testing**: Ensure all CRUD operations work as expected.
- **Cross-Browser Compatibility**: Validate on different web browsers.
- **Mobile Responsiveness**: Optimize for mobile devices.
- **Security Testing**: Identify and fix vulnerabilities.

## Legal and Ethical Considerations
- **GDPR Compliance**: Handle user data responsibly.
- **Accessibility Standards (WCAG 2.1)**: Improve usability for all users.
- **Content Ownership Policies**: Ensure users own their content.
- **Code of Conduct**: Maintain respectful interactions among users.

## Contributors
- [Your Name] - Lead Developer

## License
This project is developed as part of COMP1841 Web Programming coursework and is not licensed for public distribution.

