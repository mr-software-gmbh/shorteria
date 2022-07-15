CREATE TABLE `migration`
(
    `id`          INT      NOT NULL AUTO_INCREMENT,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

INSERT INTO `migration` (`id`, `executed_at`)
VALUES (1, current_timestamp());

CREATE TABLE log
(
    `id`         bigint(20) UNSIGNED NOT NULL,
    `url_id`     int(10) UNSIGNED NOT NULL,
    `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` datetime NOT NULL               DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE url
(
    `id`             int(10) UNSIGNED NOT NULL,
    `short`          varbinary(50) NOT NULL,
    `redirect_to`    text COLLATE utf8mb4_unicode_ci NOT NULL,
    `comment`        text COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `created_at`     datetime                        NOT NULL DEFAULT current_timestamp(),
    `deactivated_at` datetime                                 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `log`
    ADD PRIMARY KEY (`id`),
  ADD KEY `FK_url` (`url_id`);

ALTER TABLE `url`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `short` (`short`);

ALTER TABLE `log`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `url`
    MODIFY `id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `log`
    ADD CONSTRAINT `FK_url` FOREIGN KEY (`url_id`) REFERENCES `url` (`id`) ON DELETE CASCADE;

