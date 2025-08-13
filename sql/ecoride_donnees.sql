
-- ======================================================
-- SCRIPT D'INSERTION DE DONNÉES : ECORIDE
-- Données de démonstration pour tests initiaux
-- ======================================================

-- Insertion company
INSERT INTO company (name, slug, adress, postal_code, city, email, phone, siren, url, type, manager) VALUES
('Ecoride', 'ecoride', '1 Chemin de Fernandez', '75000', 'PARIS', 'contact@ecoride.fr', '+33699067699', '191 240 407 00111', 'https://www.ecoride.fr', 'SAS', 'Albert CAMUS');

-- Insertion marques
INSERT INTO brand (name) VALUES 
('Peugeot'), ('Renault'), ('Citroën'), ('BMW'), ('Mercedes-Benz'), ('Volkswagen'), ('Tesla'), ('Toyota'), ('Audi'), ('Ford'), ('Opel'), ('Hyundai'), ('Kia'), ('Dacia'), ('Nissan');

-- Insertion modèles
INSERT INTO model (name, brand_id) VALUES 
('208', 1), 
('308', 1), 
('3008', 1), 
('5008', 1), 
('Clio', 2), 
('Captur', 2), 
('Mégane', 2), 
('Scénic', 2), 
('C3', 3), 
('C4', 3), 
('C5 Aircross', 3), 
('Série 1', 4), 
('Série 3', 4), 
('X1', 4), 
('i3', 4), 
('Classe A', 5), 
('Classe C', 5), 
('GLA', 5), 
('EQC', 5), 
('Golf', 6), 
('Polo', 6), 
('Tiguan', 6), 
('ID.3', 6), 
('Model 3', 7), 
('Model Y', 7), 
('Model S', 7), 
('Model X', 7), 
('Yaris', 8), 
('Corolla', 8), 
('RAV4', 8), 
('Prius', 8), 
('A3', 9), 
('A4', 9), 
('Q3', 9), 
('e-tron', 9), 
('Fiesta', 10), 
('Focus', 10), 
('Puma', 10), 
('Kuga', 10), 
('Corsa', 11), 
('Astra', 11), 
('Mokka', 11), 
('i20', 12), 
('i30', 12), 
('Kona', 12), 
('Ioniq', 12), 
('Picanto', 13), 
('Ceed', 13), 
('Sportage', 13), 
('EV6', 13), 
('Sandero', 14), 
('Duster', 14), 
('Spring', 14), 
('Micra', 15), 
('Juke', 15), 
('Qashqai', 15), 
('Leaf', 15);

-- Insertion de villes
INSERT INTO city (name) VALUES 
('PARIS'), ('NANTES'), ('LYON'), ('ANGERS'), ('LES SABLES D''OLONNE'), ('LILLE'), ('RENNES'), ('MARSEILLE'), ('NICE'), ('BORDEAUX'), ('TOULOUSE'), ('MONTPELLIER'), ('STRASBOURG'), ('REIMS');

-- Insertion utilisateur
INSERT INTO user (email, roles, password, firstname, lastname, adress, postal_code, city, phone, reset_token, created_token_at, auth_code, pseudo, credit, is_suspended, must_change_password) VALUES
('admin@gmail.com', '["ROLE_ADMIN"]', '$2y$13$g2V2eQnhDYwyPgEHI/a2HefUX463GHdD6y2KUNmmIS/sVZATKAajC', 'Admin', 'Admin', '1 rue de l''écologie', '75000', 'Paris', '+33123456789', NULL, NULL, NULL, 'admin', 100, 0, 0), 
('user0@gmail.com', '[]', '$2y$13$nP6Gg4ElD3BxgLOEcyIur.z2h6K4onmmgslw2Njtx2LxAaWhWmGB.', 'Michelle', 'Hubert', '25 rue de la République', '84000', 'Avignon', '+33630601414', NULL, NULL, NULL, 'colette640', 20, 0, 0), 
('user1@gmail.com', '["ROLE_EMPLOYE"]', '$2y$13$ZupVOqIY25BCJXK8OS8Z6.jgsXe8pDbq9/D3vAtC2z5jIzn6ML2rW', 'Mathilde', 'ROYER', '1 rue de la Reine Jeanne', '04100', 'Manosque', '+33738117953', NULL, NULL, NULL, 'lguilbert1', 20, 0, 0), 
('user2@gmail.com', '[]', '$2y$13$EMojeNpEuzU4HlvyfyQf7OWUogzpCVzjpUDnZuIkz9KXVDIoqBIZa', 'Mathilde', 'Garcia', '22 rue Noé', '56000', 'Vannes', '+33744479010', NULL, NULL, NULL, 'bernard.delmas2', 20, 0, 0), 
('user3@gmail.com', '[]', '$2y$13$wt3xyTOJnZ5QMgEEdm4AMuS6BSuYlRyTFq654yAM.RcrF7bWOvn.i', 'Aimé', 'David', '13 boulevard de la Corderie', '13007', 'Marseille', '+33769022439', NULL, NULL, NULL, 'frobin3', 20, 0, 0), 
('user4@gmail.com', '[]', '$2y$13$dSIrQkjO.abQvPS/MQICcObfugLh1Hlrj.a/y69BF2pu7UGE/pgE.', 'Georges', 'Valentin', '15 rue des Bahutiers', '33000', 'Bordeaux', '+33635376707', NULL, NULL, NULL, 'jlegrand4', 20, 0, 0);

-- Insertion avatar
INSERT INTO avatar (user_id, image_name, updated_at) VALUES
(1, '68945eafd38fc.png', '2025-07-08 13:42:10'), 
(2, '68945eb06f51d.png', '2025-07-08 13:42:11'), 
(3, '68945eb0e6860.png', '2025-07-13 09:43:30'), 
(4, '68945eb16f2de.png', '2025-07-08 13:42:13'), 
(5, '68945eb200e60.png', '2025-07-08 13:42:14'), 
(6, '68945eb27f028.png', '2025-07-08 13:42:14');

-- Insertion véhicule
INSERT INTO car (energy, color, firstregistration_at, registration, model_id, brand_id, user_id) VALUES
('Essence', 'Argenté', '2017-02-26 23:24:28', 'SS-174-FJ', 33, 9, 1),
('Essence', 'Gris Métalisé', '2025-01-01 00:00:00', 'SB-235-CB', 21, 6, 1),
('Electrique', 'Olive', '2020-04-28 23:13:59', 'KS-797-QD', 51, 14, 1),
('Hybride', 'Olive', '2020-05-14 20:51:24', 'IN-601-JG', 26, 7, 2),
('Electrique', 'Blanc', '2016-04-28 21:25:44', 'MR-126-DQ', 26, 7, 2),
('E85 (Bioéthanol)', 'Vert', '2015-10-10 14:19:38', 'BV-650-TJ', 26, 7, 2),
('Diesel', 'Marron', '2022-09-06 04:55:49', 'BF-604-TY', 55, 15, 2),
('Gaz Naturel (GNV) et GPL', 'Noir', '2023-01-23 03:49:18', 'QM-990-EW', 7, 2, 3),
('Hybride', 'Jaune', '2019-08-11 18:38:14', 'RV-978-XL', 35, 9, 3),
('Essence', 'Blanc', '2020-04-04 12:36:51', 'HM-152-JF', 44, 12, 4),
('Diesel', 'Argenté', '2016-11-21 19:36:47', 'DD-295-KC', 4, 1, 5),
('Diesel', 'Citron', '2024-12-09 04:25:31', 'BV-281-QB', 37, 10, 5),
('Hybride', 'Vert', '2016-02-12 15:47:58', 'FV-473-AA', 55, 15, 5),
('Essence', 'Fuchsia', '2019-12-29 03:40:47', 'DR-923-TX', 55, 15, 6),
('E85 (Bioéthanol)', 'Marron', '2023-11-02 21:59:01', 'XA-585-EG', 20, 6, 6),
('Hybride', 'Marron', '2021-03-28 06:49:07', 'NF-376-CR', 19, 5, 6),
('Electrique', 'Noir Métalisé', '2025-01-01 00:00:00', 'GH-256-KL', 25, 7, 6);

-- Insertion trajet
INSERT INTO trip (departure_date, arrival_date, departure_time, arrival_time, departure_address, arrival_address, status, seats_available, price_per_person, departure_city_id, arrival_city_id, car_id, driver_id) VALUES
('2025-08-18', '2025-08-18', '21:00:00', '23:30:00', '4 Quai Du Marché Neuf, 75004 PARIS', '1 Place De La Petite Hollande, 44000 NANTES', 'à venir', 3, 16, 1, 2, 2, 1), 
('2025-08-18', '2025-08-18', '21:00:00', '23:30:00', '85 Rue De Turenne, 75003 PARIS', '1 Rue Gresset, 44000 NANTES', 'à venir', 3, 17, 1, 2, 1, 1), 
('2025-08-18', '2025-08-18', '21:00:00', '23:30:00', '24 Boulevard Pablo Picasso, 49000 ANGERS', '7 Place du Général Colineau, 85000 LES SABLES D''OLONNE', 'à venir', 3, 12, 4, 5, 5, 2), 
('2025-08-01', '2025-08-01', '18:00:00', '21:30:00', '5 Quai Malaquais, 75006 PARIS', '12 Rue de l''Héronnière, 44000 NANTES', 'effectué', 3, 15, 1, 2, 2, 1), 
('2025-08-20', '2025-08-20', '10:00:00', '13:30:00', '115 Boulevard Exelmans, 75016 PARIS', '14 Rue des Petites Ecuries, 44000 NANTES', 'à venir', 3, 15, 1, 2, 17, 6), 
('2025-08-20', '2025-08-20', '14:00:00', '17:30:00', '46 Rue de Turenne, 75003 PARIS', '1 Place Alexis-Ricordeau, 44000 NANTES', 'à venir', 3, 16, 1, 2, 5, 2), 
('2025-08-20', '2025-08-20', '14:30:00', '18:00:00', '13 Rue Pierre Fontaine, 75009 PARIS', '3 Allee de la Cité, 44200 NANTES', 'à venir', 3, 15, 1, 2, 3, 1), 
('2025-08-20', '2025-08-20', '18:00:00', '22:00:00', '60 Avenue de Gravelle, 75012 PARIS', '2 Cours Olivier de Clisson, 44000 NANTES', 'à venir', 3, 18, 1, 2, 12, 5),
('2025-08-22', '2025-08-22', '10:00:00', '14:30:00', '60 Avenue de Gravelle, 75012 PARIS', '43 Rue Michel Gachet, 13007 MARSEILLE', 'à venir', 3, 18, 1, 8, 12, 5);

-- Insertion passager
INSERT INTO trip_passenger (validation_status, validation_at, trip_id, user_id) VALUES
('reported', '2025-08-01 22:20:16', 4, 3),
('validated', '2025-08-01 22:23:06', 4, 2), 
('pending', '2025-08-01 10:23:06', 7, 3);

-- Insertion travel_preference
INSERT INTO travel_preference (discussion, music, smoking, pets, user_id) VALUES
('depends', 'depends', 'allowed', 'some_pets', 1), 
('depends', 'depends', 'outside_only', 'no_pets', 2), 
('depends', 'depends', 'allowed', 'some_pets', 3), 
('depends', 'depends', 'outside_only', 'some_pets', 4), 
('depends', 'depends', 'outside_only', 'some_pets', 5), 
('depends', 'depends', 'forbidden', 'some_pets', 6);

-- Insertion avis
INSERT INTO testimonial (rating, content, is_approved, created_at, trip_id, author_id) VALUES
(5, 'Super trajet avec conducteur ponctuel !', 1, NOW(), 4, 2);

-- Insertion litiges
INSERT INTO complaint (type, comment, created_at, ticket_closed, ticket_resolved, trip_passenger_id) VALUES
('problem_on_trip', 'Test, test, test', '2025-08-01 22:25:16', 0, 0, 1);
