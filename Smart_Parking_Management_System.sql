CREATE DATABASE Parking_System;

CREATE TABLE Parking_Lot (
    Lot_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Location VARCHAR(150),
    Capacity INT
);

CREATE TABLE Staff (
    Staff_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Role VARCHAR(50),
    Phone VARCHAR(15),
    Lot_ID INT,
    FOREIGN KEY (Lot_ID) REFERENCES Parking_Lot(Lot_ID)
);

CREATE TABLE Parking_Space (
    Space_ID INT AUTO_INCREMENT PRIMARY KEY,
    Type VARCHAR(50),
    Status VARCHAR(20),
    Lot_ID INT,
    FOREIGN KEY (Lot_ID) REFERENCES Parking_Lot(Lot_ID)
);

CREATE TABLE Space_Sensor (
    Sensor_ID INT AUTO_INCREMENT,
    Status VARCHAR(20),
    Space_ID INT,
    FOREIGN KEY (Space_ID) REFERENCES Parking_Space(Space_ID),
    PRIMARY KEY (Sensor_ID, Space_ID)
);

CREATE TABLE Owner (
    Owner_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Phone VARCHAR(15),
    Email VARCHAR(100),
    Password VARCHAR(20) UNIQUE
);

CREATE TABLE Vehicle (
    Vehicle_ID INT AUTO_INCREMENT PRIMARY KEY,
    Type VARCHAR(50),
    Plate_no VARCHAR(20) UNIQUE,
    Owner_ID INT,
    FOREIGN KEY (Owner_ID) REFERENCES Owner(Owner_ID)
);

CREATE TABLE Reservation (
    Reservation_ID INT AUTO_INCREMENT PRIMARY KEY,
    StartTime DATETIME,
    EndTime DATETIME,
    Status VARCHAR(20)
);

CREATE TABLE Parking_Ticket (
    Ticket_ID INT AUTO_INCREMENT PRIMARY KEY,
    EntryTime DATETIME,
    ExitTime DATETIME,
    Duration INT,
    Fee DECIMAL(10,2)
);


CREATE TABLE Payment (
    Payment_ID INT AUTO_INCREMENT PRIMARY KEY,
    Method VARCHAR(50),
    Status VARCHAR(20),
    Amount DECIMAL(10,2),
    Ticket_ID INT,
    Payment_Date DATETIME,
    FOREIGN KEY (Ticket_ID) REFERENCES Parking_Ticket(Ticket_ID)
);

CREATE TABLE Booking (
    Owner_ID INT,
    Space_ID INT,
    Reservation_ID INT,
    PRIMARY KEY (Owner_ID, Space_ID, Reservation_ID),
    FOREIGN KEY (Owner_ID) REFERENCES Owner(Owner_ID),
    FOREIGN KEY (Space_ID) REFERENCES Parking_Space(Space_ID),
    FOREIGN KEY (Reservation_ID) REFERENCES Reservation(Reservation_ID)
);

CREATE TABLE Issued_For (
    Ticket_ID INT,
    Vehicle_ID INT,
    Space_ID INT,
    PRIMARY KEY (Ticket_ID, Vehicle_ID, Space_ID),
    FOREIGN KEY (Ticket_ID) REFERENCES Parking_Ticket(Ticket_ID),
    FOREIGN KEY (Vehicle_ID) REFERENCES Vehicle(Vehicle_ID),
    FOREIGN KEY (Space_ID) REFERENCES Parking_Space(Space_ID)
);

CREATE TABLE Rate (
    Rate_ID INT AUTO_INCREMENT PRIMARY KEY,
    Vehicle_Type VARCHAR(50),
    Rate_Per_Hour DECIMAL(10,2)
);


INSERT INTO Parking_Lot VALUES ('Central Mall Lot', 'Downtown', 200);
INSERT INTO Parking_Lot VALUES ('Airport Parking', 'Airport Road', 500);
INSERT INTO Parking_Lot VALUES ('City Center Lot', 'MG Road', 300);
INSERT INTO Parking_Lot VALUES ('Tech Park Lot', 'IT Hub', 250);
INSERT INTO Parking_Lot VALUES ('Railway Station Lot', 'Station Road', 400);

INSERT INTO Staff (Name, Role, Phone, Lot_ID)
VALUES 
('Ramesh Kumar', 'Manager', '9380052105', 1),
('Sita Sharma', 'Attendant', '9123456780', 1),
('Ajay Singh', 'Security', '9988776655', 2),
('Meena Patel', 'Cleaner', '9871122334', 2),
('Vikram Rao', 'Supervisor', '9812345678', 3);


INSERT INTO Parking_Space VALUES (1001, 'Compact', 'Available', 1);
INSERT INTO Parking_Space VALUES (1002, 'SUV', 'Occupied', 1);
INSERT INTO Parking_Space VALUES (1003, 'Electric', 'Reserved', 2);
INSERT INTO Parking_Space VALUES (1004, 'Compact', 'Occupied', 2);
INSERT INTO Parking_Space VALUES (1005, 'Bike', 'Available', 3);

INSERT INTO Space_Sensor VALUES (501, 'Available', 1001);
INSERT INTO Space_Sensor VALUES (502, 'Occupied', 1002);
INSERT INTO Space_Sensor VALUES (503, 'Occupied', 1003);
INSERT INTO Space_Sensor VALUES (504, 'Occupied', 1004);
INSERT INTO Space_Sensor VALUES (505, 'Available', 1005);

INSERT INTO Owner (Name, Phone, Email, Password)
VALUES
('Darshan P Sajjana', '9876543210', 'darshan@example.com', 'owner123'),
('Anita Rao', '9123456780', 'anita@example.com', 'secure456'),
('Vikram Singh', '9988776655', 'vikram@example.com', 'pass789'),
('Meena Patel', '9871122334', 'meena@example.com', 'mypassword1'),
('Ramesh Kumar', '9812345678', 'ramesh@example.com', 'ramesh2025');


INSERT INTO Vehicle VALUES (201, 'Car', 'KA01AB1234', 1);
INSERT INTO Vehicle VALUES (202, 'Bike', 'KA05XY5678', 2);
INSERT INTO Vehicle VALUES (203, 'SUV', 'KA09MN4321', 3);
INSERT INTO Vehicle VALUES (204, 'Car', 'KA03CD8765', 4);
INSERT INTO Vehicle VALUES (205, 'Electric', 'KA07EV2025', 5);

INSERT INTO Reservation VALUES (301, '2025-09-14 10:00:00', '2025-09-14 12:00:00', 'Confirmed');
INSERT INTO Reservation VALUES (302, '2025-09-15 09:00:00', '2025-09-15 11:30:00', 'Pending');
INSERT INTO Reservation VALUES (303, '2025-09-15 18:00:00', '2025-09-15 20:00:00', 'Confirmed');
INSERT INTO Reservation VALUES (304, '2025-09-16 08:30:00', '2025-09-16 10:30:00', 'Cancelled');
INSERT INTO Reservation VALUES (305, '2025-09-16 12:00:00', '2025-09-16 14:00:00', 'Confirmed');


INSERT INTO Booking VALUES (1, 1001, 301);
INSERT INTO Booking VALUES (2, 1002, 302);
INSERT INTO Booking VALUES (3, 1003, 303);
INSERT INTO Booking VALUES (4, 1004, 304);
INSERT INTO Booking VALUES (5, 1005, 305);


INSERT INTO Parking_Ticket (EntryTime, ExitTime, Duration, Fee)
VALUES
('2025-11-03 08:00:00', '2025-11-03 10:00:00', 120, 50.00),
('2025-11-03 09:15:00', '2025-11-03 11:45:00', 150, 60.00),
('2025-11-02 18:30:00', '2025-11-02 21:30:00', 180, 75.00),
('2025-11-01 07:00:00', '2025-11-01 09:30:00', 150, 55.00),
('2025-10-31 19:00:00', '2025-10-31 22:00:00', 180, 70.50);

INSERT INTO Payment (Method, Status, Amount, Ticket_ID, Payment_Date)
VALUES
('Cash', 'Failed', 50.00, 1, '2025-11-03 09:50:00'),
('UPI', 'Completed', 60.00, 2, '2025-11-03 12:35:00'),
('Credit Card', 'Completed', 75.00, 3, '2025-11-03 15:05:00'),
('Debit Card', 'Failed', 55.00, 4, '2025-11-02 13:10:00'),
('UPI', 'Completed', 70.50, 5, '2025-11-02 19:30:00');

INSERT INTO Rate (Vehicle_Type, Rate_Per_Hour)
VALUES
('Bike', 20.00),
('Scooter', 25.00),
('Car', 50.00),
('SUV', 70.00),
('Van', 80.00),
('Electric Car', 40.00),
('Electric Bike', 15.00),
('Auto Rickshaw', 30.00);


-- Procedure
-- P1
DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CloseParkingTicket`(IN p_ticket_id INT)
BEGIN
    UPDATE Parking_Ticket
    SET ExitTime = NOW()
    WHERE Ticket_ID = p_ticket_id;
END $$

DELIMITER ;


-- P2
DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `create_ticket`(
    IN p_vehicle_id INT,
    IN p_space_id INT
)
BEGIN
    DECLARE v_ticket_id INT;
    DECLARE v_entry_time DATETIME;

    SET v_entry_time = NOW();

    -- Step 1: Create a new parking ticket
    INSERT INTO Parking_Ticket (EntryTime, Duration, Fee)
    VALUES (v_entry_time, 0, 0);
    SET v_ticket_id = LAST_INSERT_ID();

    -- Step 2: Insert into Issued_For table (link ticket, vehicle, space)
    INSERT INTO Issued_For (Ticket_ID, Vehicle_ID, Space_ID)
    VALUES (v_ticket_id, p_vehicle_id, p_space_id);

    -- Step 3: Return created Ticket ID
    SELECT v_ticket_id AS Ticket_ID;
END $$

DELIMITER ;


-- Functions
-- F1
DELIMITER $$

CREATE DEFINER=`root`@`localhost` FUNCTION `calculate_fee_by_type`(
    v_type VARCHAR(50),
    duration INT
) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    DECLARE rate DECIMAL(10,2);
    DECLARE total_fee DECIMAL(10,2);

    -- Get rate
    SELECT Rate_Per_Hour INTO rate
    FROM Rate_Config
    WHERE Vehicle_Type = v_type;

    -- Apply discount if it's an electric vehicle
    IF v_type LIKE '%Electric%' THEN
        SET rate = rate * 0.8;  -- 20% discount
    END IF;

    SET total_fee = CEIL(duration / 60) * rate;
    RETURN total_fee;
END $$

DELIMITER ;

-- F2
DELIMITER $$

CREATE DEFINER=`root`@`localhost` FUNCTION `is_vehicle_parked`(v_id INT) RETURNS tinyint(1)
    DETERMINISTIC
BEGIN
    DECLARE parked BOOLEAN;
    SELECT COUNT(*) > 0
    INTO parked
    FROM Parking_Ticket pt
    JOIN Issued_For ifr ON pt.Ticket_ID = ifr.Ticket_ID
    WHERE ifr.Vehicle_ID = v_id AND pt.ExitTime IS NULL;
    RETURN parked;
END $$

DELIMITER ;


-- Triggers
-- T1
DELIMITER $$

CREATE DEFINER=`root`@`localhost` TRIGGER `update_space_status` AFTER INSERT ON `issued_for` FOR EACH ROW BEGIN
    UPDATE Parking_Space
    SET Status = 'Occupied'
    WHERE Space_ID = NEW.Space_ID;
END $$

DELIMITER ;

-- T2
DELIMITER $$

CREATE DEFINER=`root`@`localhost` TRIGGER `calculate_parking_details` BEFORE UPDATE ON `parking_ticket` FOR EACH ROW BEGIN
    DECLARE v_vehicle_id INT;
    DECLARE v_vehicle_type VARCHAR(50);
    DECLARE v_rate DECIMAL(10,2);

    IF NEW.ExitTime IS NOT NULL THEN
        -- Duration
        SET NEW.Duration = TIMESTAMPDIFF(MINUTE, NEW.EntryTime, NEW.ExitTime);

        -- Fetch Vehicle Type
        SELECT ifr.Vehicle_ID INTO v_vehicle_id
        FROM Issued_For ifr WHERE ifr.Ticket_ID = NEW.Ticket_ID;

        SELECT Type INTO v_vehicle_type
        FROM Vehicle WHERE Vehicle_ID = v_vehicle_id;

        -- Fetch Rate
        SELECT Rate_Per_Hour INTO v_rate
        FROM Rate_Config WHERE Vehicle_Type = v_vehicle_type;

        -- Fee
        SET NEW.Fee = CEIL(NEW.Duration / 60) * v_rate;
    END IF;
END $$

DELIMITER ;

-- T3
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `free_space_after_exit` AFTER UPDATE ON `parking_ticket` FOR EACH ROW BEGIN
    DECLARE v_space_id INT;

    -- When ExitTime is set (meaning the vehicle left)
    IF NEW.ExitTime IS NOT NULL THEN
        SELECT Space_ID INTO v_space_id
        FROM Issued_For
        WHERE Ticket_ID = NEW.Ticket_ID;

        -- Mark the corresponding space as Available
        UPDATE Parking_Space
        SET Status = 'Available'
        WHERE Space_ID = v_space_id;
    END IF;
END $$

DELIMITER ;

CREATE USER 'admin'@'localhost' IDENTIFIED BY 'admin123';
GRANT ALL PRIVILEGES ON parking_system.* TO 'admin'@'localhost';
FLUSH PRIVILEGES;


