USE sgql_unittests_data_1;

CREATE TABLE `sgql_association_2` (
  `p_id` INT NOT NULL,
  `c_id` INT NOT NULL,
  PRIMARY KEY (`p_id`, `c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `sgql_association_3` (
  `p_id` INT NOT NULL,
  `c_id` INT NOT NULL,
  PRIMARY KEY (`p_id`, `c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `sgql_tables` (`name`, `primary_column`) VALUES
    ('passports', 'id');

INSERT INTO `sgql_associations` (`parent_id`, `child_id`, `type`) VALUES
    (2, 3, 2),
    (1, 4, 0);