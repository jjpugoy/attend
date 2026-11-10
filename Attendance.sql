CREATE DATABASE IF NOT EXISTS Attendance;
USE Attendance;

DROP TABLE IF EXISTS AttendanceTbl;
DROP TABLE IF EXISTS Employees;
DROP TABLE IF EXISTS Departments;

CREATE TABLE Departments (
    depCode INT PRIMARY KEY,
    depName VARCHAR(50)  NOT NULL,
    depHead VARCHAR(50)  NOT NULL,
    depTelNo VARCHAR(20) NOT NULL
);

CREATE TABLE Employees (
    empID INT PRIMARY KEY,
    depCode INT NOT NULL,
    empFName VARCHAR(50) NOT NULL,
    empLName VARCHAR(50) NOT NULL,
    empRPH DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (depCode) REFERENCES Departments(depCode)
);

CREATE TABLE AttendanceTbl (
    attRN INT AUTO_INCREMENT PRIMARY KEY,
    empID INT NOT NULL,
    attDate DATE NOT NULL,
    attTimeIn DATETIME NOT NULL,
    attTimeOut DATETIME NOT NULL,
    attStat VARCHAR(20) NOT NULL DEFAULT 'Active',  
    FOREIGN KEY (empID) REFERENCES Employees(empID)
);



