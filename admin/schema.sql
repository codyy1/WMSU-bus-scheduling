
CREATE DATABASE IF NOT EXISTS wmsu_transport;
USE wmsu_transport;


CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    WMSUID VARCHAR(50) NOT NULL UNIQUE,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    UserType ENUM('Admin', 'Student', 'Staff') NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE Routes (
    RouteID INT PRIMARY KEY AUTO_INCREMENT,
    RouteName VARCHAR(100) NOT NULL,
    StartLocation VARCHAR(100) NOT NULL,
    EndLocation VARCHAR(100) NOT NULL,
    IsActive BOOLEAN DEFAULT TRUE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE Stops (
    StopID INT PRIMARY KEY AUTO_INCREMENT,
    StopName VARCHAR(100) NOT NULL,
    Description TEXT,
    Latitude DECIMAL(10, 8),
    Longitude DECIMAL(11, 8),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE RouteStops (
    RouteStopID INT PRIMARY KEY AUTO_INCREMENT,
    RouteID INT NOT NULL,
    StopID INT NOT NULL,
    StopOrder INT NOT NULL,
    ScheduledTime TIME,
    FOREIGN KEY (RouteID) REFERENCES Routes(RouteID),
    FOREIGN KEY (StopID) REFERENCES Stops(StopID)
);


CREATE TABLE Vehicles (
    VehicleID INT PRIMARY KEY AUTO_INCREMENT,
    PlateNumber VARCHAR(20) NOT NULL,
    VehicleType ENUM('WMSU Bus', 'Van', 'Jeepney') NOT NULL,
    Status ENUM('Operational', 'Maintenance', 'Out of Service') DEFAULT 'Operational'
);


CREATE TABLE Schedules (
    ScheduleID INT PRIMARY KEY AUTO_INCREMENT,
    RouteID INT NOT NULL,
    VehicleID INT NOT NULL,
    DriverName VARCHAR(100) NOT NULL,
    DateOfService DATE NOT NULL,
    Status ENUM('On Time', 'Delayed', 'Canceled') DEFAULT 'On Time',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (RouteID) REFERENCES Routes(RouteID),
    FOREIGN KEY (VehicleID) REFERENCES Vehicles(VehicleID)
);


CREATE TABLE Announcements (
    AnnouncementID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(200) NOT NULL,
    Content TEXT NOT NULL,
    CreatedBy INT NOT NULL,
    PublishDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CreatedBy) REFERENCES Users(UserID)
);


INSERT INTO Users (WMSUID, FirstName, LastName, PasswordHash, UserType)
VALUES 
    ('admin2025', 'System', 'Administrator', 'admin123', 'Admin'),
    ('20250001', 'Test', 'User', 'test123', 'Student');


INSERT INTO Vehicles (PlateNumber, VehicleType, Status)
VALUES 
    ('WMSU-BUS-001', 'WMSU Bus', 'Operational'),
    ('WMSU-VAN-001', 'Van', 'Operational'),
    ('WMSU-JEP-001', 'Jeepney', 'Operational');


INSERT INTO Routes (RouteName, StartLocation, EndLocation)
VALUES 
    ('Main Campus Loop', 'WMSU Main Gate', 'WMSU Main Gate'),
    ('City Route', 'WMSU Main Gate', 'City Hall'),
    ('Normal Complex Route', 'WMSU Main Gate', 'Normal Complex');


INSERT INTO Stops (StopName, Description)
VALUES 
    ('Main Gate', 'WMSU Main Entrance Gate'),
    ('CLA Building', 'College of Liberal Arts'),
    ('Engineering', 'College of Engineering'),
    ('Normal Hall', 'College of Education'),
    ('City Hall Stop', 'In front of City Hall'),
    ('Grandstand', 'City Grandstand Area');


INSERT INTO RouteStops (RouteID, StopID, StopOrder, ScheduledTime)
VALUES 
    (1, 1, 1, '07:00:00'),  
    (1, 2, 2, '07:10:00'),
    (1, 3, 3, '07:20:00'),
    (1, 4, 4, '07:30:00'),
    (2, 1, 1, '08:00:00'),  
    (2, 5, 2, '08:20:00'),
    (2, 6, 3, '08:40:00');