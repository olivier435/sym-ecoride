
-- ======================================================
-- SCRIPT DE STRUCTURE DE BASE DE DONNEES : ECORIDE
-- ======================================================

-- Suppression des tables si elles existent déjà
DROP TABLE IF EXISTS testimonial, complaint, trip_passenger, trip, city, travel_preference,
    avatar, car, model, brand, contact, company, user;

-- Table: company
CREATE TABLE company (
    id INT AUTO_INCREMENT,
    CONSTRAINT company_PK PRIMARY KEY (id),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    adress VARCHAR(255) NOT NULL,
    postal_code VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(35) NOT NULL,
    siren VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    manager VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Table: contact
CREATE TABLE contact (
    id INT AUTO_INCREMENT,
    CONSTRAINT contact_PK PRIMARY KEY (id),
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(35) NOT NULL,
    content LONGTEXT NOT NULL,
    send_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: brand
CREATE TABLE brand (
    id INT AUTO_INCREMENT,
    CONSTRAINT brand_PK PRIMARY KEY (id),
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Table: model
CREATE TABLE model (
    id INT AUTO_INCREMENT,
    CONSTRAINT model_PK PRIMARY KEY (id),
    name VARCHAR(255) NOT NULL,
    brand_id INT NOT NULL,
    CONSTRAINT model_brand_FK FOREIGN KEY (brand_id) REFERENCES brand(id)
) ENGINE=InnoDB;

-- Table: user
CREATE TABLE user (
    id INT AUTO_INCREMENT,
    CONSTRAINT user_PK PRIMARY KEY (id),
    email VARCHAR(180) NOT NULL UNIQUE,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    adress VARCHAR(255) NOT NULL,
    postal_code VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    phone VARCHAR(35) NOT NULL,
    reset_token VARCHAR(255),
    created_token_at DATETIME,
    auth_code VARCHAR(255),
    pseudo VARCHAR(50) NOT NULL,
    credit INT NOT NULL,
    is_suspended BOOLEAN NOT NULL DEFAULT FALSE,
    must_change_password BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;

-- Table: car
CREATE TABLE car (
    id INT AUTO_INCREMENT,
    CONSTRAINT car_PK PRIMARY KEY (id),
    energy VARCHAR(255) NOT NULL,
    color VARCHAR(255) NOT NULL,
    firstregistration_at DATETIME NOT NULL,
    registration VARCHAR(255) NOT NULL UNIQUE,
    model_id INT NOT NULL,
    brand_id INT NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT car_model_FK FOREIGN KEY (model_id) REFERENCES model(id),
    CONSTRAINT car_brand_FK FOREIGN KEY (brand_id) REFERENCES brand(id),
    CONSTRAINT car_user_FK FOREIGN KEY (user_id) REFERENCES user(id)
) ENGINE=InnoDB;

-- Table: avatar
CREATE TABLE avatar (
    id INT AUTO_INCREMENT,
    CONSTRAINT avatar_PK PRIMARY KEY (id),
    image_name VARCHAR(255) NOT NULL,
    updated_at DATETIME NOT NULL,
    user_id INT NOT NULL UNIQUE,
    CONSTRAINT avatar_user_FK FOREIGN KEY (user_id) REFERENCES user(id)
) ENGINE=InnoDB;

-- Table: travel_preference
CREATE TABLE travel_preference (
    id INT AUTO_INCREMENT,
    CONSTRAINT travel_preference_PK PRIMARY KEY (id),
    discussion VARCHAR(255) NOT NULL,
    music VARCHAR(255) NOT NULL,
    smoking VARCHAR(255) NOT NULL,
    pets VARCHAR(255) NOT NULL,
    user_id INT NOT NULL UNIQUE,
    CONSTRAINT travel_preference_user_FK FOREIGN KEY (user_id) REFERENCES user(id)
) ENGINE=InnoDB;

-- Table: city
CREATE TABLE city (
    id INT AUTO_INCREMENT,
    CONSTRAINT city_PK PRIMARY KEY (id),
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Table: trip
CREATE TABLE trip (
    id INT AUTO_INCREMENT,
    CONSTRAINT trip_PK PRIMARY KEY (id),
    departure_date DATE NOT NULL,
    arrival_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    departure_address VARCHAR(255) NOT NULL,
    arrival_address VARCHAR(255) NOT NULL,
    status ENUM('à venir', 'en cours', 'effectué', 'annulé') NOT NULL,
    seats_available INT NOT NULL,
    price_per_person INT NOT NULL,
    departure_city_id INT NOT NULL,
    arrival_city_id INT NOT NULL,
    car_id INT NOT NULL,
    driver_id INT NOT NULL,
    CONSTRAINT trip_city_departure_FK FOREIGN KEY (departure_city_id) REFERENCES city(id),
    CONSTRAINT trip_city_arrival_FK FOREIGN KEY (arrival_city_id) REFERENCES city(id),
    CONSTRAINT trip_car_FK FOREIGN KEY (car_id) REFERENCES car(id),
    CONSTRAINT trip_driver_FK FOREIGN KEY (driver_id) REFERENCES user(id)
) ENGINE=InnoDB;

-- Table: trip_passenger
CREATE TABLE trip_passenger (
    id INT AUTO_INCREMENT,
    CONSTRAINT trip_passenger_PK PRIMARY KEY (id),
    validation_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    validation_at DATETIME NOT NULL,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT trip_passenger_trip_FK FOREIGN KEY (trip_id) REFERENCES trip(id),
    CONSTRAINT trip_passenger_user_FK FOREIGN KEY (user_id) REFERENCES user(id)
) ENGINE=InnoDB;

-- Table: complaint
CREATE TABLE complaint (
    id INT AUTO_INCREMENT,
    CONSTRAINT complaint_PK PRIMARY KEY (id),
    type VARCHAR(255) NOT NULL,
    comment VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    ticket_closed BOOLEAN NOT NULL DEFAULT FALSE,
    ticket_resolved BOOLEAN NOT NULL DEFAULT FALSE,
    trip_passenger_id INT NOT NULL UNIQUE,
    CONSTRAINT complaint_trip_passenger_FK FOREIGN KEY (trip_passenger_id) REFERENCES trip_passenger(id)
) ENGINE=InnoDB;

-- Table: testimonial
CREATE TABLE testimonial (
    id INT AUTO_INCREMENT,
    CONSTRAINT testimonial_PK PRIMARY KEY (id),
    rating INT NOT NULL,
    content LONGTEXT NOT NULL,
    is_approved BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL,
    trip_id INT NOT NULL,
    author_id INT NOT NULL,
    CONSTRAINT testimonial_trip_FK FOREIGN KEY (trip_id) REFERENCES trip(id),
    CONSTRAINT testimonial_user_FK FOREIGN KEY (author_id) REFERENCES user(id)
) ENGINE=InnoDB;

CREATE INDEX user_email_IDX ON user(email);
CREATE INDEX car_registration_IDX ON car(registration);