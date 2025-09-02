DROP TABLE IF EXISTS `verification`;
CREATE TABLE IF NOT EXISTS `verification` (
    `verification_id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `type` varchar(20) COLLATE utf8mb4_czech_ci NOT NULL,
    `datetime_add` datetime NOT NULL,
    `user_id_add` int NOT NULL,
    PRIMARY KEY (`verification_id`),
    KEY `user_id` (`user_id`,`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
COMMIT;