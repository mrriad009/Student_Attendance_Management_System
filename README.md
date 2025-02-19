
# Student Attendance Management System
**Prepared by**:  
Md Mahamudul Islam Riad  
ID : 11220320898

Suraya Khatun Hasiba  
ID : 11220321003  

Department of Computer Science and Engineering  
**Course Title:** Database Management Systems Lab  
**Course Code:** CSE 3104
**Date**: 02.19.2025


## Objective
The Student Attendance Management System aims to streamline the process of tracking and managing student attendance in educational institutions. It provides an efficient, automated, and user-friendly platform for recording attendance, generating reports, and analyzing student performance based on attendance data.

## Features of the Project

1. **Student Registration**: Allows to register new students with details like ID, name, email, and department.
2. **Attendance Tracking**: Records attendance data (present, absent, total classes) for each student.
3. **Search Functionality**: Enables searching for students by their unique ID to view their attendance details.
4. **Department Filter**: Filters students by department and displays their attendance sorted by percentage.
5. **Attendance Percentage Calculation**: Automatically calculates attendance percentage for each student.
6. **Status Indicator**: Displays attendance status (Warning, Normal, Impressive) based on attendance percentage.
7. **View Profile**: Provides a detailed profile of each student, including attendance history.
8. **User-Friendly Interface**: A clean, modern, and responsive design for easy navigation.

## Database Design
The system uses a MySQL database with the following tables and attributes:

### 1. Students Table
**Attributes**:
- `id (Primary Key)`: Unique student ID.
- `name`: Full name of the student.
- `email`: Email address of the student.
- `department`: Department of the student.

### 2. Attendance Table
**Attributes**:
- `student_id (Foreign Key)`: References the id in the students table.
- `total_classes`: Total number of classes held.
- `present`: Number of classes attended.
- `absent`: Number of classes missed.
- `percentage`: Attendance percentage (calculated as (present / total_classes) * 100).

## Real-Life Usage

- **Automate Attendance Tracking**: Replace manual attendance registers with a digital system, reducing errors and saving time.
- **Monitor Student Performance**: Analyze attendance trends to identify students who may need additional support.
- **Generate Reports**: Create attendance reports for faculty, administration, and parents.
- **Enhance Accountability**: Ensure transparency in attendance records and reduce absenteeism.
- **Support Decision-Making**: Use attendance data to make informed decisions about student promotions, scholarships, and disciplinary actions.

## Future Development
The future development of the Student Attendance Management System can focus on several key enhancements:

- **Mobile Application**: Develop a mobile app for students and faculty to access attendance data and notifications on the go.
- **Real-Time Attendance Tracking**: Integrate facial recognition or biometric systems to automate real-time attendance tracking during classes.
- **Automated Alerts**: Send automatic notifications to students and parents when attendance drops below a certain threshold.
- **Analytics Dashboard**: Implement an advanced analytics dashboard to visualize attendance trends, track student engagement, and generate predictive insights.
- **Multi-School Support**: Enable the system to handle attendance data for multiple educational institutions.

## Conclusion
The Student Attendance Management System is a robust, scalable, and user-friendly solution for tracking and managing student attendance. It leverages modern web technologies and a well-structured database to provide accurate and efficient attendance tracking. By digitalizing the attendance process, the system helps educational institutions focus on their core mission of delivering quality education.


## WorkFlow 

# Student Attendance Management System

This project is a web-based application for managing student attendance. It includes features for registering new students, searching student profiles, viewing profiles, checking department attendance, and an admin panel for managing the system.

## Features

1. **Home Page (index.php)**
   - The main landing page of the application.
   - Provides navigation to other sections of the application.

2. **Register New Student (Register form)**
   - Accessible via `register_profile.php`.
   - Allows users to register a new student by filling out a form.
   - The form includes fields for Student ID, Name, Email, and Department.
   - Upon submission, the data is stored in the `attendance` table.

3. **Register New Entity**
   - When a new student is registered, their data is stored in the `attendance` table.
   - The registration form validates inputs and provides success or error messages based on the outcome.

4. **Searching (Student Profile Searching)**
   - Allows users to search for student profiles.
   - Searches are performed on the `attendance_record` table.
   - Provides a user-friendly interface for searching and viewing student profiles.

5. **View Profile**
   - Combines data from two tables: `student` and `attendance_record`.
   - Provides a detailed view of a student's profile, including their attendance records.
   - Accessible via a search or direct link.

6. **Check Department**
   - Allows users to check attendance records by department.
   - Queries the `attendance_record` table to provide department-specific attendance data.

7. **Admin Panel**
   - Accessible via a secret code (pre-set to "12345").
   - Redirects to `login.php` upon successful code entry.
   - Provides administrative functionalities for managing the system.

## Installation

1. Clone the repository to your local machine.
   ```bash
   git clone https://github.com/yourusername/Student_Attendance_Management_System.git
   ```

2. Move the project to your web server's root directory (e.g., `c:/xampp/htdocs/` for XAMPP).

3. Import the database schema.
   - Open phpMyAdmin or any MySQL client.
   - Create a new database named `student_attendance`.
   - Import the provided SQL file (`database.sql`) into the `student_attendance` database.

4. Configure the database connection.
   - Open `register_profile.php`.
   - Update the database connection details if necessary:
     ```php
     $host = 'localhost';
     $username = 'root';
     $password = '';
     $database = 'student_attendance';
     ```

5. Start your web server and navigate to the application in your browser.

## Usage

1. **Home Page**
   - Navigate to `index.php` to access the home page.

2. **Register New Student**
   - Navigate to `register_profile.php` to register a new student.

3. **Search Student Profiles**
   - Use the search functionality to find student profiles in the `attendance_record` table.

4. **View Profiles**
   - View detailed student profiles by combining data from the `student` and `attendance_record` tables.

5. **Check Department Attendance**
   - Check attendance records by department using the provided interface.

6. **Admin Panel**
   - Access the admin panel by entering the secret code "12345".
   - Manage the system through the admin interface.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request with your changes.

## License

This project is licensed under the MIT License.

