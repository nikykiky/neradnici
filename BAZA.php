CREATE DATABASE gogstorg_zavrsni CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `stsl_ucenik` (
`id_uc` int(4) auto_increment primary key, 
`ime` char(15) NOT NULL,  
`prezime` char(30) NOT NULL,
`oib` int(11),  
`datum_rodenja` date,  
`adresa` char(30),
`grad` char(15),
`spol` enum('musko','zensko'),
`rjesenje` char(45),
`klasa` char(45)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_roditelj` (
`id_ro` int(4) auto_increment primary key, 
`ime` char(15),  
`prezime` char(30),
`telefon` char(11)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_roditelj_dijete` (
`id_ro` int(4) NOT NULL, 
`id_uc` int(4) NOT NULL
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_razrednik` (
`id_ra` int(4) auto_increment primary key, 
`ime` char(15),  
`prezime` char(30),
`telefon` char(11)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_razred` (
`id_raz` int(3) primary key auto_increment NOT NULL, 
`oznaka_raz` char(3)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_sk_godina` (
`id_skgod` int(5) primary key auto_increment NOT NULL, 
`sk_godina` char(10)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_ucenik_razred` (
`id_ra` int(20) NOT NULL, 
`id_uc` int(20) NOT NULL,  
`id_skgod` int(20) NOT NULL
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_korisnik` (
    `id_ko` INT(2) AUTO_INCREMENT PRIMARY KEY, 
    `ime` VARCHAR(50),  
    `prezime` VARCHAR(50),
    `titula` VARCHAR(50),
    `korisnicko_ime` VARCHAR(50) UNIQUE,
    `lozinka` VARCHAR(255)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `stsl_dnevnik_rada` (
`id_dr` int(4) auto_increment primary key, 
`id_ko` int(2), 
`opis` text,
`datum_unosa` datetime DEFAULT NOW()
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `stsl_dosje_ucenika` (
`id_do` int(10) auto_increment primary key, 
`id_uc` int(5), 
`id_ko` int(2), 
`opis` text,
`datum_unosa` datetime 
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;







INSERT INTO `stsl_ucenik` (`ime`, `prezime`, `oib`, `datum_rodenja`,  `adresa`, `grad`, `spol`, `rjesenje`, `klasa`) VALUES 
('Nikolina', 'Smilović', 1111111111, '2011-01-01', 'Matice Hrvatske 11', 'Split', 'zensko', 'rjesenje1', 'klasa1'),
('Ana Maria', 'Ugrin', 1111111111, '2011-01-01', 'Matice Hrvatske 11', 'Split', 'zensko', 'rjesenje1', 'klasa1'),
('Nataša', 'Burilović', 1111111111, '2011-01-01', 'Matice Hrvatske 11', 'Split', 'zensko', 'rjesenje1', 'klasa1'),
('Antonia', 'Borzić', 1111111111, '2011-01-01', 'Matice Hrvatske 11', 'Split', 'zensko', 'rjesenje1', 'klasa1');


INSERT INTO `stsl_ucenik_razred` (`id_ra`, `id_uc`, `id_skgod`) VALUES 
(1, 2, 1),(6, 3, 1),(19, 4, 1),(27, 1, 1),(28, 5, 1);

INSERT INTO `stsl_dosje_ucenika` (`id_uc`, `id_ko`, `opis`, `datum_unosa`) VALUES 
(2,2,'test - Ima mokraćnih problema','2024-11-18 15:45'),
(2,1,'test - Donio nalaze','2024-11-19 15:45'),
(3,1,'test - Želučane tegobe','2024-11-20 15:45');

INSERT INTO `stsl_sk_godina` (`sk_godina`) VALUES
('24/25');

INSERT INTO `stsl_razred` (`oznaka_raz`) VALUES
('1a'),('2a'),('3a'),('4a'),
('1b'),('2b'),('3b'),('4b'),
('g1a'),('g2a'),('g3a'),('g4a'),
('g1b'),('g2b'),('g3b'),('g4b'),
('g1m'),('g2m'),('g3m'),('g4m'),
('1or'),('2or'),('3or'),('4or'),
('g1p'),('g2p'),('g3p'),('g4p'),
('g1d'),('g2d'),('g3d'),
('1c'),('2c'),('3c'),
('1d'),('2d'),('3d');

INSERT INTO `stsl_korisnik` (`ime`, `prezime`, `titula`, `korisnicko_ime`, `lozinka`) 
VALUES 
('Ana Maria', 'Ugrin', 'pedagog', 'pedagog', '$2y$10$FyzrNJ0ET1E.MZVeayc8qu4juUenBNeYjg87lh/AdpWwll/jMKMLu'),
('Nataša', 'Burilović', 'psiholog', 'psiholog', '$2y$10$JBXoLeZdRg8qtISzXqwcvO22asZn2fHDsLQe2y0dkkls8Ee4CJ2Ey'),
('Antonia', 'Borzić', 'stped', 'stped', '$2y$10$0ewv.xPwyl5chV25lw7JjOJKJxyrodO/.WHiiY/P9drnR6ExmTgZ.');

INSERT INTO `stsl_dnevnik_rada` (`id_ko`, `opis`, `datum_unosa`) VALUES 
(1,'test - Dojava o bombi','2024-11-18 15:45'),
(1,'test - Policija zaprimila izvještaj','2024-11-19 16:10'),
(2,'test - Pronašli krivca: Ante Antic','2024-11-20 17:20');




INSERT INTO `stsl_roditelj` (`ime`, `prezime`, `telefon`) VALUES 
('Ante', 'Smilović','0917894516'),
('Ana', 'Balić', '0971452631'),
('Karlo', 'Balić', '0971452622'),
('Josip', 'Franković', '0981472536'),
('Jelena', 'Franković', '0981472511'),
('Anđelka', 'Lozert', '0917418520');

INSERT INTO `stsl_roditelj_dijete` (`id_ro`,`id_uc`) VALUES 
(1,1),(2,2),(3,2),(4,3),(5,3),(6,4);

INSERT INTO `stsl_razrednik` (`ime`, `prezime`, `telefon`) VALUES
('Nikolina', 'Smilović','0917894516'),
('Tomislav', 'Jukić', '0971452631'),
('Duška', 'Boban', '0971452622'),
('Nives', 'Škero', '0981472536'),
('Martina', 'Javorčić', '0981472511'),
('Jelena', 'Gluić', '0917418520');

