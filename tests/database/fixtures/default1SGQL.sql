USE sgql_unittests_data_1;

INSERT INTO `sgql_tables` (`name`, `primary_column`) VALUES ('customers', 'id'), ('orders', 'id'), ('products', 'id');

INSERT INTO `sgql_associations` (`parent_id`, `child_id`, `type`) VALUES (1, 2, 1);

CREATE TABLE `sgql_association_1` (
  `p_id` INT NOT NULL,
  `c_id` INT NOT NULL,
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE UNIQUE INDEX child_id ON `sgql_association_1` (`c_id`);

INSERT INTO `sgql_association_1` (`p_id`, `c_id`) VALUES (1, 1);