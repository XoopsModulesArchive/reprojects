CREATE TABLE `projects` (
    `project_id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_name`        VARCHAR(255) NOT NULL,
    `project_description` TEXT         NOT NULL,
    `project_type`        INT UNSIGNED NOT NULL,
    `project_location`    VARCHAR(255) NOT NULL,
    `project_details`     TEXT         NOT NULL,
    `project_status`      INT UNSIGNED NOT NULL,
    `project_salesinfo`   TEXT         NOT NULL,
    `project_rentinfo`    TEXT         NOT NULL,
    `project_enrollment`  TEXT         NOT NULL,
    `project_areacode`    VARCHAR(10)  NOT NULL,
    PRIMARY KEY (`project_id`),
    INDEX (`project_name`, `project_type`)
);

CREATE TABLE project_images (
    image_id   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (image_id),
    KEY project_id (project_id)
)
    ENGINE = ISAM;
